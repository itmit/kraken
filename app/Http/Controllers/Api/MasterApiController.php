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
                Rule::in(['free', 'offline']), // свободен или оффлайн
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
}
