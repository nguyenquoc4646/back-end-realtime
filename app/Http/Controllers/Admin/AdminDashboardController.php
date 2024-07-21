<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(){
        $data['totalNumberOfAccount'] = User::count(); // tổng số tài khoản
        
        return response()->json([
            $data
        ]);
    }
}
