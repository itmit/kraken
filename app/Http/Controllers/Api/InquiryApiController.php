<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use App\Models\ClientInfo;
use App\Models\MasterInfo;
use App\Models\Inquiry;
use App\Models\InquiryDetail;
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

class InquiryApiController extends ApiBaseController
{
    public $successStatus = 200;

    private $user;
    private $userInfo;

    public function store(Request $request)
    {

    }

    public function getTypeOfWork()
    {
        $list = TypeOfWork::select('id', 'work')->get()->toArray();
        return $this->sendResponse($list,
            'Список родов работ');
    }
}
