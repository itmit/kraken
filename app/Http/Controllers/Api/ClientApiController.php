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

class ClientApiController extends ApiBaseController
{
    public $successStatus = 200;

    public function index()
    {
        $id = auth('api')->user()->id;
        $clientType = Client::where('id', $id)->first();
        if($clientType->type == 'master')
        {
            // $client = Client::where('id', $id)
            // ->join('master_infos', 'clients.id', '=', 'master_infos.master_id')
            // ->select('clients.id', 'clients.email', 'master_infos.department_id', 'master_infos.name', 'master_infos.qualification', 'master_infos.work', 'master_infos.phone', 'master_infos.rating', 'master_infos.status')
            // ->first()
            // ->toArray();
            $client = $clientType->getMasterInfo()->toArray();
        }
        if($clientType->type == 'customer')
        {
            // $client = Client::where('id', $id)
            // ->join('client_infos', 'clients.id', '=', 'client_infos.client_id')
            // ->select('clients.id', 'clients.email', 'client_infos.name', 'client_infos.organization', 'client_infos.address', 'client_infos.phone')
            // ->first()
            // ->toArray();
            $client = $clientType->getClientInfo()->toArray();
        }

        return $this->sendResponse($client,
            '');
    }

    public function changeRadius(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'radius' => [
                'required',
                Rule::in(['0', '1', '5', '10']),
            ],
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 400);            
        }
        $id = auth('api')->user()->id;
        $client = Client::where('id', $id)->first();
        if($client->type == 'master')
        {
            ClientInfo::where('client_id', $id)->update([
                'radius' => $request->radius
            ]);
        }
        if($client->type == 'customer')
        {
            MasterInfo::where('master_id', $id)->update([
                'radius' => $request->radius
            ]);
        }
        return $this->sendResponse([],
            'Radius updated');
    }
}
