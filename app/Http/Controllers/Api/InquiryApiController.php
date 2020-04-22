<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use App\Models\ClientInfo;
use App\Models\MasterInfo;
use App\Models\Inquiry;
use App\Models\InquiryDetail;
use App\Models\InquiryFile;
use App\Models\TypeOfWork;
use App\Models\MasterToInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Http\Controllers\PushController;

class InquiryApiController extends ApiBaseController
{
    public $successStatus = 200;

    private $user;
    private $userInfo;
    private $inquiry;

    public function show($uuid)
    {
        $inquiry = Inquiry::where('uuid', $uuid)
        ->join('inquiry_details', 'inquiries.id', '=', 'inquiry_details.inquiry_id')
        ->select('inquiry_details.work', 'inquiry_details.urgency', 'inquiry_details.description', 'inquiry_details.address', 'inquiry_details.status', 'inquiry_details.started_at')
        ->first()
        ->toArray();
        return $this->sendResponse($inquiry, 'Запрос');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'work' => 'required|exists:type_of_works,id', // id роды работ полученного методом getTypeOfWork
            'urgency' => [
                'required',
                Rule::in(['urgent', 'now', 'scheduled']), // срочно, сейчас, заданное время
            ],
            'description' => 'required|min:2|max:191',
            'address' => 'required|string|min:2|max:191',
            'files' => 'array'
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 400);            
        }

        $authClientId = auth('api')->user()->id;

        try {
            DB::transaction(function () use ($request, $authClientId) {
                $inquiry = Inquiry::create([
                    'uuid' => Str::uuid(),
                    'client_id' => $authClientId,
                ]);

                $this->inquiry = $inquiry;
        
                InquiryDetail::create([
                    'inquiry_id' => $inquiry->id,
                    'work' => $request->work,
                    'urgency' => $request->urgency,
                    'description' => $request->description,
                    'address' => $request->address,
                    'status' => 'created',
                ]);

                foreach ($request->files as $file) {
                    $path = $file->store('public/inquiry/'.$inquiry->uuid);
                    $url = Storage::url($path);
                    InquiryFile::create([
                        'inquiry_id' => $inquiry->id,
                        'file' => $url,
                    ]);
                }
            });
        } catch (\Throwable $th) {
            return response()->json(['error'=>'Произошла ошибка'], 500);  
        }

        if($request->urgency == 'urgent')
        {
            $test = new PushController();
            $request->request->add(['uuid' => $this->inquiry->uuid]);
            $masters = self::getMasterList($request);
            $masters = $masters->getData();
            $i = 0;
            foreach($masters->data as $master)
            {
                $request->request->add(['uuid_inquiry' => $this->inquiry->uuid]);
                $request->request->add(['uuid_master' => $master->uuid]);
                self::selectMaster($request);
                $test->sendPush('Текст тела оповещения', 'Текст заголовка оповещения', $master->device_token);
                $i++;
                if($i >= 10) break;
            }
        }

        return $this->sendResponse([],
            'Запрос успешно создан');
    }

    /**
     * получить список ближайщих подходящих запросов
     */

    public function getNearInquiryList()
    {
        // $master = MasterInfo::where('master_id', auth('api')->user()->id)->first();

        // $works = explode(';', $master->work);

        // $result = [];

        // foreach ($works as $work) {
        //     InquiryDetail::
        // }

        // return $this->sendResponse([],
        //     'Запрос успешно создан');
    }

    public function getTypeOfWork()
    {
        $list = TypeOfWork::select('id', 'work')->get()->toArray();
        return $this->sendResponse($list,
            'Список родов работ');
    }

    public function getMasterList(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'uuid' => 'required|exists:inquiries',
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 400);            
        }

        $inquiry = Inquiry::where('uuid', $request->uuid)->first();
        $type = $inquiry->getInquiryDetail()->getWork()->work;

        $masters = Client::join('master_infos', 'clients.id', '=', 'master_infos.master_id')
        ->where('master_infos.status', 'free')
        ->select('clients.uuid', 'clients.device_token', 'master_infos.name', 'master_infos.qualification', 'master_infos.work', 'master_infos.phone', 'master_infos.rating')
        ->get();

        $result = [];

        foreach ($masters as $master) {
            $works = explode(';', $master->work);
            foreach ($works as $work) {
                if($work == $type) $result[] = $master;
            }
        };

        return $this->sendResponse($result, 'Список подходящих мастеров');
    }

    public function selectMaster(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'uuid_inquiry' => 'required|exists:inquiries,uuid',
            'uuid_master' => 'required|exists:clients,uuid',
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 400);            
        }

        $inquiry = Inquiry::where('uuid', $request->uuid_inquiry)->first();
        $master = Client::where('uuid', $request->uuid_master)->first();

        MasterToInquiry::create([
            'inquiry_id' => $inquiry->id,
            'master_id' => $master->id
        ]);

        return $this->sendResponse([], 'Запрос отправлен мастеру');
    }

    public function index()
    {
        return $this->sendResponse(Inquiry::where('client_id', auth('api')->user()->id)->where('is_finished', 0)
        ->join('inquiry_details', 'inquiries.id', '=', 'inquiry_details.inquiry_id')
        ->join('type_of_works', 'inquiry_details.work', '=', 'type_of_works.id')
        ->get()->toArray(), 'Список запросов клиента');
    }

    public function test()
    {
        // $from = "Череповец Наседкина 12";
        // $to = "59.091803, 37.925015";

        // $from = urlencode($from);
        // $to = urlencode($to);

        // $data = file_get_contents("http://dev.virtualearth.net/REST/v1/Routes/DistanceMatrix?origins=$from&destinations=$to&travelMode=driving&key=AoQ1_RhiXbz8RQ36RbFTnPkRLu6yNFAfLaKKp-_kK6mrk_fm0yEA3pd-bEltlGl1");

        // $data = json_decode($data);
        // // return "Откуда: ".$data->destination_addresses[0] . "<br/>" .
        // //     "Куда: ". $data->origin_addresses[0] . "<br/>" .
        // //     "Время: ". $data->rows[0]->elements[0]->distance->text . "<br/>" .
        // //     "Путь: ".$data->rows[0]->elements[0]->duration->text;
     
        // Store the query in a PHP variable (assuming you obtained it from the form)  
        $query = str_ireplace(" ","%20",'Россия, Вологодская область, Череповец, Наседкина 12');  
        
        // Construct the final Locations API URI  
        $findURL = $baseURL."/".$query."?output=xml&key=AoQ1_RhiXbz8RQ36RbFTnPkRLu6yNFAfLaKKp-_kK6mrk_fm0yEA3pd-bEltlGl1";  
        
        // get the response from the Locations API and store it in a string  
        $output = file_get_contents($findURL);  
        
        // create an XML element based on the XML string  
        $response = new SimpleXMLElement($output);  
        
        // Extract data (e.g. latitude and longitude) from the results  
        $latitude =  
        $response->ResourceSets->ResourceSet->Resources->Location->Point->Latitude;  
        $longitude =  
        $response->ResourceSets->ResourceSet->Resources->Location->Point->Longitude;

        return $this->sendResponse([$latitude], 'Адрес');
    }
}
