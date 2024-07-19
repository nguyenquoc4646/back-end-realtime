<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupChatModel extends Model
{
    use HasFactory;
    protected $table = "group_chats";
    protected $fillable = [
        'name_group',
        'leader_id '
    ];
    static public function getAll()
    {
        return self::select(
            'group_chats.id',
            'group_chats.name_group',
            'users.name as name_leader',

        )->join('users', 'users.id', '=', 'group_chats.leader_id')
            ->get();
    }
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id', 'id')->select('id','name');
    }
    public function members()
    {
        return $this->hasMany(GroupMemberModel::class, 'group_chat_id', 'id')->select('id','group_chat_id','member_id');
    }
}
