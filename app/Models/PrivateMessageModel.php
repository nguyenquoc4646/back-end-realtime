<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PrivateMessageModel extends Model
{
    use HasFactory;
    protected $table = 'private_messages';
    static public function getMessagesPrivate($idUserReciever)
    {

        return self::select(
            'user_send.id as user_send_id',
            'user_send.avatar as user_send_avatar',
            'user_send.name as user_send_name',
            'user_receiver.id as user_receiver_id',
            'user_receiver.avatar as user_reciever_avatar',
            'user_receiver.name as user_receiver_name',
            'private_messages.message',
            'private_messages.created_at'
        )
            ->leftJoin('users as user_send', 'user_send.id', '=', 'private_messages.user_send_id')
            ->leftJoin('users as user_receiver', 'user_receiver.id', '=', 'private_messages.user_receiver_id')
            ->where(function ($query) use ($idUserReciever) {
                $query->where('private_messages.user_send_id', Auth::user()->id)
                    ->where('private_messages.user_receiver_id', $idUserReciever);
            })
            ->orderBy('private_messages.created_at', 'desc')
            ->limit(50)
            ->orderBy('private_messages.created_at', 'asc')
            // Đảm bảo tin nhắn được sắp xếp theo thời gian
            ->get();
    }
}
