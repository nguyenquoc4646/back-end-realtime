<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMemberModel extends Model
{
    use HasFactory;
    protected $table = 'group_members';
    protected $fillable = [
        'name_group|required|unique',
        'leader_id',
        'members'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'member_id', 'id')->select('id','name');
    }
}
