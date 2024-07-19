<?php

namespace App\Traits;

use App\Events\UserRegisterSuccessEvent;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


trait AuthenticatesLogin{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $token = auth()->attempt($credentials);
        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'error' => 'Error account or password faile',
                'message' => 'Tài khoản hoặc mật khẩu sai'
            ], 404);
        }
        $user = auth()->user();
        if (!$user->email_verified_at) {
            auth()->logout();
            UserRegisterSuccessEvent::dispatch($user);
            return response()->json([
                'error' => "Error account not verify",
                'message' => 'Tài khoản chưa được xác minh, vui lòng kiểm tra email',
            ], 404);
        }
        
            return $this->respondWithToken($token);
     
       
        // return response()->json($token);
    }
}