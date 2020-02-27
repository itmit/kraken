<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use App\Models\ClientInfo;
use App\Models\MasterInfo;
use App\Models\Inquiry;
use App\Models\InquiryDetail;
use App\Models\InquiryFile;
use App\Models\TypeOfWork;
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

class InquiryApiController extends ApiBaseController
{
    public $successStatus = 200;

    private $user;
    private $userInfo;

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
}
