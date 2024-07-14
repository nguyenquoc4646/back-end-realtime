<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
    public function index()
    {
        return response()->json(User::get());
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
    public function store(Request $request)
    {
        $validatorUser = Validator::make(
            $request->all(),
            [
                'name' => 'required|unique:users,name',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
            ]
        );

        if ($validatorUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validatorUser->errors()
            ]);
        };
        $fileName = 'avatar_default.jpg';
        // if (!empty($request->avatar)) {
        //     $file = $request->file('avatar');
        //     $ext = $file->getClientOriginalExtension();
        //     $randomStr = Str::random(20);
        //     $fileName = strtolower($randomStr) . '.' . $ext;
        //     $file->move('upload/', $fileName);
        // }

        $user = User::create([
            'name' => trim($request->name),
            'email' => trim($request->email),
            'password' => trim(Hash::make($request->password)),
            'email_verified_at' => Carbon::now(),
            'avatar' => $fileName
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Thêm người dùng thành công',
            'user' => $user,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json(User::find($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
      
        return response()->json(User::find($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        $user->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>trim(Hash::make($request->password))
        ]);
        return response()->json('success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::find($id)->delete();
        return response()->json('success');
    }
}
