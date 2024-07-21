<?php

namespace App\Http\Controllers\Client;

use App\Events\ChatGroupEvent;
use App\Http\Controllers\Controller;
use App\Models\GroupChatModel;
use App\Models\GroupMemberModel;
use App\Models\GroupMessageModel;
use App\Models\User;
use App\Traits\AuthenticatesLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatGroupController extends Controller
{
    use AuthenticatesLogin;
    public function chatGroup($idGroup)
    {

        $users = User::where('id', '<>', Auth::user()->id)->get(); // lấy ra danh sách tài khoản, khác tài khoản đang đăng nhập
        $groupChat = GroupChatModel::find($idGroup); // lấy ra group chat
        if (!empty($groupChat)) {
            $leader = User::find($groupChat->leader_id); // lấy ra thông tin của nhóm trưởng
            $member_id = GroupMemberModel::where('group_chat_id', '=', $idGroup)
                ->pluck('member_id')->toArray(); // lấy ra id thành viên của nhóm
            $member = User::whereIn('id', $member_id)->where('id', '<>', Auth::user()->id)->get(); // lấy ra thông tin user theo id
            $count = GroupMemberModel::select('*')
                ->join('group_chats', 'group_chats.id', '=', 'group_members.group_chat_id')
                ->where('group_chat_id', '=', $idGroup)
                ->count();
            $messageGroup = GroupMessageModel::messageGroup($idGroup);
            return response()->json([
                'success'=>"Success get data group chat",
                'message'=> 'Thành công lấy dữ liệu nhóm',
                'users' => $users, // danh sách user khác user đăng nhập hiện tại
                'groupChat' => $groupChat, // lấy ra nhóm đang chat
                'leader' => $leader, // lấy ra trường nhóm
                'member_id' => $member_id, // id của các thành viên
                'member' => $member, // lấy ra thông tin của thành viên trong nhóm theo id và khác với tài khoản hiện tại
                'count' => $count, // đếm số thành viên trong nhóm
                'messageGroup' => $messageGroup
            ]);
        }else{
            return response()->json([
                'error'=>"Error: Not found group",
                'message'=>"Không tìm thấy nhóm",
                'users' => null, // danh sách user khác user đăng nhập hiện tại
                'groupChat' => null, // lấy ra nhóm đang chat
                'leader' =>null , // lấy ra trường nhóm
                'member_id' => null, // id của các thành viên
                'member' =>null , // lấy ra thông tin của thành viên trong nhóm theo id và khác với tài khoản hiện tại
                'count' => null, // đếm số thành viên trong nhóm
                'messageGroup' =>null
            ]);    
        }
    }

    public function messageGroup(Request $request)
    {
        $messageGroup = new GroupMessageModel();
        $groupChat = GroupChatModel::find($request->groupChatId);
        if (!empty($groupChat) && !empty($request->message)) {
            $messageGroup->group_chat_id = $request->groupChatId;
            $messageGroup->message = $request->message;
            $messageGroup->user_send_id = Auth::user()->id;

            // Lưu tin nhắn
            $messageGroup->save();

            // phát sóng sự kiện - chạy độc lập
            broadcast(new ChatGroupEvent(GroupchatModel::find($request->groupChatId), $request->user(), $request->message));

            return response()->json([
                'success' => 'Message sent successfully',
                'message' => "Gửi tin nhắn thành công"
            ], 200);
        } else {
            return response()->json([
                'error' => 'Missing group or message content',
                'message' => "Nhóm không tồn tại hoặc không có nội dung tin nhắn"
            ], 404);
        }
    }
}
