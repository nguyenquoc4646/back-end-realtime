<?php

namespace App\Http\Controllers\Admin;

use App\Events\ForgotPasswordEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UploadFileRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Traits\AuthenticatesLogin;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use AuthenticatesLogin;
    public function index()
    {
        $users = User::all();
        if (!empty($users)) {
            return response()->json([
                'success' => 'Success get data account',
                'message' => 'lấy dữ liệu thành công',
                'data' => $users
            ], 200);
        } else {
            return response()->json([
                'error' => "Error get data account",
                'message' => 'Danh sách tài khoản trống',
                'data' => []
            ], 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $fileName = 'avatar_default.jpg';
        $user = User::create([
            'name' => trim($request->name),
            'email' => trim($request->email),
            'password' => trim(Hash::make($request->password)),
            'email_verified_at' => Carbon::now(),
            'avatar' => $fileName
        ]);
        if (!empty($user)) {
            return response()->json([
                'success' => 'Success create account',
                'message' => 'Thêm tài khoản thành công',
                'user' => $user,
            ], 200);
        } else {
            return response()->json([
                'success' => 'Error create account faile',
                'message' => 'Thêm tài khoản thất bại',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if (!empty($user)) {
            return response()->json([
                'success' => "Success get data",
                'message' => 'Thành công',
                'user' => $user
            ], 200);
        } else {
            return response()->json([
                'success' => "Error get data",
                'message' => 'Thất bại ! không tồn tại dữ liệu',
                'user' => $user
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::find($id);
        if (!empty($user)) {
            return response()->json([
                'success' => "Success get data",
                'message' => 'Lấy dữ liệu thành công',
                'user' => $user
            ], 200);
        } else {
            return response()->json([
                'error' => 'Error get data',
                'message' => 'Không tồn tại dữ liệu'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'error' => 'Error get account data',
                'message' => "Không tồn tại tài khoản"
            ], 404);
        }

        $user->update([
            'name' => trim($request->name),
            'email' => trim($request->email),
            'password' => trim(Hash::make($request->password))
        ]);

        return response()->json([
            'success' => 'Success account update',
            'message' => "Cập nhật thành công"
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user =  User::find($id);
        if (!empty($user)) {
            $user->delete();
            return response()->json([
                'success' => "Success delete account",
                'message' => "Xóa tài khoản thành công",
            ], 200);
        } else {
            return response()->json([
                'error' => false,
                'message' => "Không tồn tại tài khoản",
            ], 404);
        }
    }

    public function uploadFile(UploadFileRequest $request)
    {
        $account = User::find($request->id);
        $fileName = "avatar_default.jpg";
        if (!empty($request->avatar)) {
            $file = $request->file('avatar');
            $ext = $file->getClientOriginalExtension();
            $randomStr = Str::random(20);
            $fileName = strtolower($randomStr) . '.' . $ext;
            $file->move('upload/', $fileName);
            if (!empty($account)) {
                $account->avatar = $fileName;
                $account->save();
                return response()->json([
                    'success' => 'Success upload file',
                    'message' => "Cập nhật ảnh đại diện thành công",
                    'account' => $account
                ], 200);
            } else {
                return response()->json([
                    'error' => "Error upload file",
                    'message' => "Không tồn tại tài khoản",
                ], 404);
            }
        }
    }
    public function activateMail($id)
    {
        $id = base64_decode($id);
        $user = User::find($id);
        if (!empty($user)) {
            $user->email_verified_at =  Carbon::now();
            $user->save();
            return response()->json([
                'successs' => 'Success verify mail',
                'message' => 'Xác minh thành công'
            ], 200);
        } else {
            return response()->json(
                [
                    'error' => 'Error verify mail',
                    'message' => "Không tồn tại tài khoản"
                ],
                404
            );
        }
    }
    public function forgotPassword(Request $request)
    {
        $account = User::checkEmail($request->email);
        if (!empty($account)) {
            $account->remember_token = Str::random(50);
            $account->save();
            $sendMailUpdatePassword = ForgotPasswordEvent::dispatch($account->email);
            if ($sendMailUpdatePassword) {
                return response()->json([
                    'success' => 'Success send mail update password',
                    'message' => 'Vui lòng kiểm tra mail để cập nhật mật khẩu'
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Error!Cant not be send mail',
                    'message' => 'Lỗi hệ thống - vui lòng thử lại sau'
                ], 500);
            }
        } else {
            return response()->json([
                'error' => 'Error!Not found account',
                'message' => 'Tài khoản không tồn tại'
            ], 404);
        }
    }

    public function updatePassword(Request $request){
        $user = User::where('remember_token',$request->remember_token)->get();
        if(!empty($user)){
            if($request->confirm_password === $request->password){
                $user->password = trim(Hash::make($request->password));
                return response()->json([
                    'success'=>'Success update password account',
                    'message'=>'Cập nhật mật khẩu thành công'
                ],200);
            }else{
                return response()->json([
                    'error'=>'Error confirm password mismatched',
                    'message'=>'Mật khẩu không khớp'
                ],400);
            }
        }else{
            return response()->json([
                'error'=>'Error! not found account',
                'message'=>'Tài khoản không tồn tại'
            ],404);
        }
    }
}
