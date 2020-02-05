<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use App\Models\ClientInfo;
use App\Models\MasterInfo;
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
use libphonenumber;

class AuthApiController extends ApiBaseController
{
    public $successStatus = 200;

    private $user;
    private $userInfo;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'email' => 'required|unique:clients|email|max:191',
            'name' => 'required|max:191|min:2',
            'organization' => 'required|min:2|max:191',
            'address' => 'required',
            'phone' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 400);            
        }

        if(Client::where('email', '=', $request->email)->exists())
        {
            return response()->json(['error'=>'Аккаунт с таким email-адресом уже зарегистрирован'], 500);     
        }

        try {
            $phone = request('phone');

            $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
            $phoneNumberObject = $phoneNumberUtil->parse($phone, 'RU');
            $phone = $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);

            $request->phone = $phone;
        } catch (\Throwable $th) {
            return response()->json(['error'=>'Не удалось преобразовать номер телефона'], 500);     
        }
        

        DB::transaction(function () use ($request) {
            $this->user = Client::create([
                'uuid' => Str::uuid(),
                'password' => Hash::make($request->password),
                'email' => $request->email,
                'type' => 'customer'
            ]);

            $this->userInfo = ClientInfo::create([
                'client_id' => $this->user->id,
                'name' => $request->name,
                'organization' => $request->organization,
                'address' => $request->address,
                'phone' => $request->phone,
            ]);

            if($request->device_token)
            {
                ClientInfo::where('client_id', '=', $this->user->id)->update([
                    'device_token' => $this->device_token
                ]);
            };

        });

        Auth::login($this->user);     

        if (Auth::check()) {
            $tokenResult = $this->user->createToken(config('app.name'));
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            return $this->sendResponse([
                'client_type' => $client->type,
                'client_info' => $this->userInfo,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ],
                'Authorization is successful');
        }
        
        return response()->json(['error'=>'Не удалось авторизоваться'], 500);     
    }

    /** 
     * login api 
     * 
     * @return Response 
     */ 
    public function login(Request $request) { 

        $validator = Validator::make($request->all(), [ 
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 401);            
        }

        $client = Client::where('email', '=', $request->email)->first();

        if(!$client)
        {
            return response()->json(['error'=>'Такого пользователя не существует'], 500); 
        }    
        
        if($client->type == 'customer')
        {
            $clientInfo = ClientInfo::where('client_id', '=', $client->id)->first();
        }
        if($client->type == 'master')
        {
            $clientInfo = MasterInfo::where('master_id', '=', $client->id)->first();
        }

        if(Hash::check($request->password, $client->password))
        {
            Auth::login($client);
            if (Auth::check()) {
                $tokenResult = $client->createToken(config('app.name'));
                $token = $tokenResult->token;
                $token->expires_at = Carbon::now()->addWeeks(1);
                $token->save();

                
                if($request->deviceToken)
                {
                    self::updateDeviceToken($client, $request->deviceToken);
                }

                return $this->sendResponse([
                    'client_type' => $client->type,
                    'client_info' => $clientInfo,
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse(
                        $tokenResult->token->expires_at
                    )->toDateTimeString()
                ],
                    'Authorization is successful');
            }
        }
        else
        {
            return response()->json(['error'=>'Неверный пароль'], 500); 
        }
        return response()->json(['error'=>'Авторизация не удалась'], 401); 
    }

    private function updateDeviceToken($client, $deviceToken)
    {
        if($client->type == 'customer')
        {
            ClientInfo::where('client_id', '=', $client->id)->update([
                'device_token' => $deviceToken
            ]);
        }
        if($client->type == 'master')
        {
            MasterInfo::where('master_id', '=', $client->id)->update([
                'device_token' => $deviceToken
            ]);
        }
    }
    
}
