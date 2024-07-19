<?php

namespace App\Http\Controllers;

use App\Events\UserRegisterSuccessEvent;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
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
    public function register(RegisterRequest $request)
    {
        $fileName = 'avatar_default.jpg';
        if ($request->confirm_password === $request->password) {
            $user = new User;
            $user->name = trim($request->name);
            $user->email = trim($request->email);
            $user->password = trim(Hash::make($request->password));
            $user->avatar = $fileName;
            $user->save();
            UserRegisterSuccessEvent::dispatch($user);
            return response()->json([
                'success' => "Success register",
                'message' => 'Đăng kí thành công',
            ],200);
        } else {
            return response()->json([
                'error' => "Error register",
                'message' => 'Mật khẩu không khớp',
            ],404);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
     auth()->logout();
        return response()->json([
            'success' => "Success logged out",
            'message' => 'Đăng xuất thành công'
        ], 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function refresh()
    // {
    //     return $this->respondWithToken(auth()->refresh());
    // }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'success'=>'Success login',
            'message'=>'Đăng nhập thành công',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expiry_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

  

    public function updateInfor(UserRequest $request, $id)
    {

        $fileName = 'avatar_default.jpg';
        if (!empty($request->avatar)) {
            $file = $request->file('avatar');
            $ext = $file->getClientOriginalExtension();
            $randomStr = Str::random(20);
            $fileName = strtolower($randomStr) . '.' . $ext;
            $file->move('upload/', $fileName);
        }
        $accountInfor = User::find($id);
        if (!empty($accountInfor)) {
            $accountInfor->name = trim($request->name);
            $accountInfor->email = trim($request->email);
            $accountInfor->password = trim(Hash::make($request->password));
            $accountInfor->avatar = $fileName;
            $accountInfor->save();

            return response()->json([
                'Success' => 'Success updated account',
                'message' => "Cập nhật tài khoản thành công",
                'accountInfor' => $accountInfor
            ], 200);
        } else {
            return response()->json([
                'error' => 'Error updated account',
                'message' => "Lỗi! Không tồn tại tài khoản",
                'accountInfor' => []
            ], 404);
        }
    }

    public function chatPrivate($idUser){
        return response()->json('Đây là chat private');
    }

    
}
