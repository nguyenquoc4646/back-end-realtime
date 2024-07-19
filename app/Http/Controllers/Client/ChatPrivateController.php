<?php

namespace App\Http\Controllers\Client;

use App\Events\ChatPrivateEvent;
use App\Http\Controllers\Controller;
use App\Models\ChatPrivateModel;
use App\Models\PrivateMessageModel;
use App\Models\User;
use App\Traits\AuthenticatesLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatPrivateController extends Controller
{
    use AuthenticatesLogin;

    public function chatPrivate($idUserReciever)
    {
        $messagePrivate = PrivateMessageModel::getMessagesPrivate($idUserReciever);
        $userReceiver = User::find($idUserReciever);
        $users = User::where('id', '<>', Auth::user()->id)->get();
        if (!empty($userReceiver)) {
            return response()->json([
                'success' => "Success get data chat",
                
                'listUsers'=>$users,
                'messagePrivate' => $messagePrivate,
            ],200);
        } else {
            return response()->json([
                'error' => "Error not found user receiver",
                'userReciever' => []
            ],404);
        }
    }

    public function messagePrivate(Request $request)
    {
        $messagePrivate = new PrivateMessageModel;
        if (!empty($request->idUserReciever) && !empty($request->message)) {
            $messagePrivate->user_send_id  = Auth::user()->id;
            $messagePrivate->user_receiver_id = $request->idUserReciever;
            $messagePrivate->message = $request->message;
            $messagePrivate->save();
            if (broadcast(new ChatPrivateEvent($request->user(), User::find($request->idUserReciever), $request->message))) {
                return response()->json([
                    'success' => "Success send message",
                    'message' => 'Gửi tin nhắn thành công'
                ], 200);
            } else {
                return response()->json([
                    'error' => "error server or not connection",
                    'message' => "Lỗi hệ thống, vui lòng thử lại sau"
                ], 500);
            }
        } else {
            return response()->json([
                'error' => "Error not found receiver or dont exits content",
                'message' => "Người nhận không tồn tại hoặc không có nội dung"
            ], 404);
        }
    }
}
