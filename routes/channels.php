<?php

use App\Models\GroupchatModel;
use App\Models\GroupMemberModel;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('chat.private.{userSend}.{userReciever}',function($user,$userSend,$userReciever){
    if($user != null){
        if($user->id == $userSend || $user->id == $userReciever){
            return true;
        }
    }
    return false;
});
Broadcast::channel('chat.group.{groupChatId}',function($user,$groupChatId){
    if($user){
        $groupChat = GroupchatModel::find($groupChatId);
        $member_id = GroupMemberModel::where('group_chat_id',$groupChatId)->pluck('member_id')->toArray();
        if($user->id == $groupChat->leader_id || in_array($user->id,$member_id)){
            return true;
        }
    }
    return false;
});