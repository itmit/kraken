<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use App\Models\ClientInfo;
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
            'organization' => 'required|max:191',
            'address' => 'required|max:191',
            'phone' => 'required|max:18|min:17',
            'password' => 'required|min:6|confirmed',
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 401);            
        }

        if(Client::where('email', '=', $request->email)->exists())
        {
            return response()->json(['error'=>'Аккаунт с таким email-адресом уже зарегистрирован'], 500);     
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

        if(!ClientInfo::where('email', '=', $request->email)->exists())
        {
            return response()->json(['error'=>'Такого пользователя не существует'], 500); 
        }       

        $clientInfo = ClientInfo::where('email', '=', $request->email)->first();
        $client = Client::where('id', '=', $clientInfo->id)->first();

        if(Hash::check($request->password, $client->password))
        {
            Auth::login($client);
            if (Auth::check()) {
                $tokenResult = $client->createToken(config('app.name'));
                $token = $tokenResult->token;
                $token->expires_at = Carbon::now()->addWeeks(1);
                $token->save();

                if($request->device_token)
                {
                    clientInfo::where('client_id', '=', $client->id)->update([
                        'device_token' => $request->device_token
                    ]);
                }

                return $this->sendResponse([
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
    
}
