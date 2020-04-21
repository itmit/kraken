<?php

namespace App\Http\Controllers;

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

class PushController extends Controller
{

    public function test()
    {
        return response()->json(200);
    }

    public function sendPush(string $body, string $title, string $token = null)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        if($token == null) return;
        $fields = array (
            'to' => $token,
            "notification" => [
                "body" => $body,
                "title" => $title
            ]
        );
        $fields = json_encode ( $fields );

        $headers = array (
                'Authorization: key=AAAAtqGqksY:APA91bGIoYDhyweSfziOCLoVQ0GS04y48yEQenyA4YqEg0dMjDoPUpxMXu5HpCn6TjtjvroCh8Y4Td9X1m-azfTzE1TF_i7_cqdypJkXAuls_akdaZPfwSvZeZfc6gRRh2utRQTkUcSY',
                'Content-Type: application/json'
        );

        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

        curl_exec ( $ch );

        curl_close ( $ch );
        
        return response()->json(200);
    }
}
