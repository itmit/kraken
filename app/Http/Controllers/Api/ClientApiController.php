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
        $clientType = Client::where('id', $id)->first()->type;
        if($clientType == 'master')
        {
            $client = Client::where('id', $id)
            ->join('master_infos', 'clients.id', '=', 'master_infos.master_id')
            ->select('clients.id', 'clients.email', 'master_infos.department_id', 'master_infos.name', 'master_infos.qualification', 'master_infos.work', 'master_infos.phone', 'master_infos.rating', 'master_infos.status')
            ->first()
            ->toArray();
        }
        if($clientType == 'customer')
        {
            $client = Client::where('id', $id)
            ->join('client_infos', 'clients.id', '=', 'client_infos.client_id')
            ->select('clients.id', 'clients.email', 'client_infos.name', 'client_infos.organization', 'client_infos.address', 'client_infos.phone')
            ->first()
            ->toArray();
        }

        return $this->sendResponse($client,
            'Координаты успешно обновлены');
    }
}
