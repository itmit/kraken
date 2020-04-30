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
use App\Http\Controllers\DistanceController;

class MasterApiController extends ApiBaseController
{
    public $successStatus = 200;

    public function updateLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 400);            
        }

        try {
            DB::transaction(function () use ($request) {
                MasterInfo::where('master_id', auth('api')->user()->id)->update([
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json(['error'=>'Произошла ошибка при обновлении координат'], 500);  
        }

        return $this->sendResponse([],
            'Координаты успешно обновлены');
    }

    public function changeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'status' => [
                'required',
                Rule::in(['free', 'offline', 'busy']), // свободен или оффлайн
            ],
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 400);            
        }

        try {
            DB::transaction(function () use ($request) {
                MasterInfo::where('master_id', auth('api')->user()->id)->update([
                    'status' => $request->status,
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json(['error'=>'Произошла ошибка при обновлении статуса'], 500);  
        }

        return $this->sendResponse([],
            'Статус успешно обновлен');
    }

    public function getInquiryList()
    {
        $master = MasterInfo::where('master_id', auth('api')->user()->id)->first();
        $works = explode(';', $master->work);
        $inquiries = Inquiry::whereNull('master_id')
        ->join('inquiry_details', 'inquiries.id', '=', 'inquiry_details.inquiry_id')
        ->select('inquiries.uuid', 'inquiries.client_id', 'inquiry_details.work', 'inquiry_details.urgency', 'inquiry_details.description', 'inquiry_details.address', 'inquiry_details.created_at')
        ->get();
        $result = [];
        foreach ($inquiries as $inquiry) {
            $type = $inquiry->getInquiryDetail()->getWork()->work;
            foreach ($works as $work) {
                if($work == $type)
                {
                    $time = new DistanceController();
                    if($time->getTime($inquiry->getInquiryDetail()->address, $master->latitude . ', ' . $master->longitude, $master->way, 600, $master->radius))
                    {
                        $result[] = $inquiry;
                    }
                }
            }
        }
        return $this->sendResponse($result, 'Список подходящих запросов');
    }

    public function getInquiryToMasterList()
    {
        return $this->sendResponse(MasterToInquiry::join('inquiries', 'master_to_inquiries.inquiry_id', '=', 'inquiries.id')
        ->join('inquiry_details', 'inquiries.id', '=', 'inquiry_details.inquiry_id')
        ->select('inquiries.uuid', 'inquiries.client_id', 'inquiry_details.work', 'inquiry_details.urgency', 'inquiry_details.description', 'inquiry_details.address', 'inquiry_details.created_at')
        ->where('master_to_inquiries.master_id', auth('api')->user()->id)
        ->get()
        ->toArray(), 'Список запросов');
    }

    public function applyInquiry(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'uuid' => 'required|uuid|exists:inquiries',
        ]);

        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 400);            
        }
        $id = auth('api')->user()->id;
        Inquiry::where('uuid', $request->uuid)->update([
            'master_id' => $id
        ]);
        $i = Inquiry::where('uuid', $request->uuid)->first();
        MasterInfo::where('master_id', $id)->update([
            'status' => 'busy',
        ]);
        $client = Client::where('id', $i->client_id)->first();
        $test = new PushController();
        $test->sendPush('Вам заявка', 'Вам заявка', $client->device_token);

        return $this->sendResponse([], 'Заявка принята мастером');
    }

    public function finishInquiry(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'uuid' => 'required|uuid|exists:inquiries',
        ]);

        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 400);            
        }

        Inquiry::where('uuid', $request->uuid)->update([
            'is_finished' => 1
        ]);
        $id = auth('api')->user()->id;
        $rating = MasterInfo::where('master_id', $id)->first(['rating']);
        $newRating = $rating->rating + 1;
        MasterInfo::where('master_id', $id)->update([
            'rating' => $newRating,
            'status' => 'free',
        ]);

        return $this->sendResponse([], 'Заявка завершена');
    }

    public function cancelInquiry(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'uuid' => 'required|uuid|exists:inquiries',
        ]);

        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 400);            
        }

        Inquiry::where('uuid', $request->uuid)->update([
            'master_id' => null
        ]);
        $id = auth('api')->user()->id;
        $rating = MasterInfo::where('master_id', $id)->first(['rating']);
        $newRating = $rating->rating - 1;
        MasterInfo::where('master_id', $id)->update([
            'rating' => $newRating,
            'status' => 'free',
        ]);

        return $this->sendResponse([], 'Заявка отменена');
    }

    public function changeWayToTravel(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'way' => [
                'required',
                Rule::in(['driving', 'walking']),
            ],
        ]);

        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 400);            
        }

        MasterInfo::where('master_id', auth('api')->user()->id)->update([
            'way' => $request->way,
        ]);

        return $this->sendResponse([], 'Способ перемещения изменен');
    }
}
