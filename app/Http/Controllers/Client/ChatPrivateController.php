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
        if (empty($userReceiver)) {
            return response()->json([
                'error' => "Error: User receiver not found",
                'userReciever' => null
            ], 404);
        }

        $users = User::where('id', '<>', Auth::user()->id)->get();

        return response()->json([
            'success' => "Success: Data chat retrieved successfully",
            'listUsers' => $users,
            'messagePrivate' => $messagePrivate,
        ], 200);
    }

    public function messagePrivate(Request $request)
    {
        $messagePrivate = new PrivateMessageModel;
        $userReceiver = User::find($request->idUserReciever);
        if (!empty($userReceiver) && !empty($request->message)) {
            $messagePrivate->user_send_id  = Auth::user()->id;
            $messagePrivate->user_receiver_id = $request->idUserReciever;
            $messagePrivate->message = $request->message;
            $messagePrivate->save();
            broadcast(new ChatPrivateEvent($request->user(), User::find($request->idUserReciever), $request->message));
            return response()->json([
                'success' => "Success send message",
                'message' => 'Gửi tin nhắn thành công'
            ], 200);
        } else {
            return response()->json([
                'error' => "Error: Not found receiver or empty content",
                'message' => "Người nhận không tồn tại hoặc nội dung tin nhắn"
            ], 404);
        }
    }
}
