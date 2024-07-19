<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupRequest;
use App\Models\GroupChatModel;
use App\Models\GroupMemberModel;
use App\Models\User;
use App\Traits\AuthenticatesLogin;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use AuthenticatesLogin;
    public function index()
    {
        $groupChats = GroupChatModel::getAll();
        if (!empty($groupChats)) {
            return response()->json([
                'success' => 'Success get list data account',
                'groupChats' => $groupChats
            ]);
        } else {
            return response()->json([
                'error' => 'Data not found',
                'message' => 'Không có dữ liệu'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GroupRequest $request)
    {
        $groupChat = new GroupChatModel;

        if (!empty($request->leader_id)) {

            $groupChat->name_group = $request->name_group;
            $groupChat->leader_id = $request->leader_id;
            $groupChat->save();
        }

        $newGroupId = $groupChat->id;
        foreach ($request->members as $member_id) {
            $groupMembers = new GroupMemberModel;
            $groupMembers->group_chat_id = $newGroupId;
            $groupMembers->member_id = $member_id;
            $groupMembers->save();
        }
        if ($groupMembers) {
            return response()->json([
                'success' => 'Success create group',
                'message' => "Tạo nhóm thành công",
                'groupChat' => $groupChat
            ], 200);
        } else {
            return response()->json([
                'error' => 'Error create group',
                'message' => 'Tạo nhóm không thành công'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $groupChat = GroupChatModel::with('leader') // Load thông tin của leader
            ->findOrFail($id); // Tìm theo ID của nhóm
        // Lấy danh sách các thành viên trong nhóm
        $members = $groupChat->members()->with('user')->get();

        // Tạo một mảng response JSON
        return response()->json([
            'success' => 'Success get data',
            'groupChat' => $groupChat,
            'members' => $members,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $groupChat = GroupChatModel::with('leader') // Load thông tin của leader
            ->findOrFail($id); // Tìm theo ID của nhóm
     
        if (empty($groupChat)) {
            return response()->json([
                'error' => "Group Not found",
                'message' => "Nhóm không tồn tại",
                'groupChat' => [],
            ],404);
        } else {
            $members = $groupChat->members()->with('user')->get();
            return response()->json([
                'success' => 'Success get data account',
                'message' => "Lấy dữ liệu thành công",
                'groupChat' => $groupChat,
                'members' => $members,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GroupRequest $request, string $id)
    {
        $groupChat = GroupChatModel::find($id);

        if (!empty($groupChat)) {
            if (!empty($request->leader_id)) {
                $groupChat->name_group = $request->name_group;
                $groupChat->leader_id = $request->leader_id;
                $groupChat->save();
            }
            $newGroupId = $groupChat->id;
        }
        foreach ($request->members as $member_id) {
            $groupMembers = new GroupMemberModel;
            $groupMembers->group_chat_id = $newGroupId;
            $groupMembers->member_id = $member_id;
            $groupMembers->save();
        }
        if ($groupMembers) {
            return response()->json([
                'success' => 'Success update group',
                'message' => "Cập nhật thành công",
                'groupChat' => $groupChat
            ], 200);
        } else {
            return response()->json([
                'error' => 'Error update group',
                'message' => 'Cập nhật không thành công'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $groupChat = GroupChatModel::find($id);
        if (!empty($groupChat)) {
            $groupChat->delete();
            return response()->json([
                'success' => 'Success delete account',
                'message' => 'Xóa tài khoản thành công'
            ], 200);
        } else {
            return response()->json([
                'error' => 'Error not found',
                'message' => 'Tài khoản không tồn tại'
            ], 404);
        }
    }
}
