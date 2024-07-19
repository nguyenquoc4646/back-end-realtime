<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMessageModel extends Model
{
    use HasFactory;
    protected $table = 'group_messages';
    public static function messageGroup($idGroup){
        return self::select('group_messages.user_send_id','users.avatar','group_messages.message','group_messages.created_at')
        ->join('users','group_messages.user_send_id','=','users.id')
        ->where('group_messages.group_chat_id','=',$idGroup)
        ->limit(40)
        ->get();
    }
}
