<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupRequest;
use App\Models\GroupChatModel;
use App\Models\GroupMemberModel;
use App\Models\User;
use App\Traits\AuthenticatesLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                'success' => 'Success get list data groups',
                'groupChats' => $groupChats
            ], 200);
        } else {
            return response()->json([
                'error' => 'Error group not found',
                'message' => 'Danh sách nhóm trống'
            ], 404);
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

        DB::beginTransaction();

        try {
            $groupChat = new GroupChatModel;
            if ($request->has('leader_id') && !empty($request->leader_id)) {

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
            DB::commit();
            return response()->json([
                'success' => 'Success create group',
                'message' => "Tạo nhóm thành công",
                'groupChat' => $groupChat
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error cannot create group. Lỗi' . $e->getMessage(),
                'message' => "Lỗi, không thể tạo nhóm",
                'groupChat' => $groupChat
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $groupChat = GroupChatModel::with('leader') // Load thông tin của leader
            ->find($id); // Tìm theo ID của nhóm
        if (empty($groupChat)) {
            return response()->json([
                'error' => 'Error not found group',
                'groupChat' => [],
                'members' => [],
                'message' => "Nhóm không tồn tại"

            ], 404);
        } else {
            $members = $groupChat->members()->with('user')->get();
            if (!$members->isEmpty()) {
                return response()->json([
                    'success' => 'Success get data infor group and group members ',
                    'groupChat' => $groupChat,
                    'members' => $members,
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Error not found group members',
                    'groupChat' => $groupChat,
                    'members' => [],
                ], 404);
            }
        }
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
                'error' => "Error group Not found",
                'message' => "Nhóm không tồn tại",
                'groupChat' => [],
            ], 404);
        } else {
            $members = $groupChat->members()->with('user')->get();
            return response()->json([
                'success' => 'Success get data account',
                'message' => "Lấy dữ liệu thành công",
                'groupChat' => $groupChat,
                'members' => $members,
            ], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $groupChat = GroupChatModel::find($id);
        $errors = [];
        if (empty($groupChat)) {
            return response()->json([
                'error' => 'Error update group',
                'message' => 'Không tìm thấy nhóm để cập nhật'
            ], 404);
        }
        if (!empty($request->nameGroup)) {
            $groupChat->name_group = $request->nameGroup;
        } else {
            $errors[] = 'Tên nhóm bắt buộc nhập';
        }

        if (!empty($request->leaderId)) {
            $leader = User::where('id',$request->leaderId)->get();
            if (!empty($leader)) {
                $groupChat->leader_id = $request->leaderId;
            } else {
                $errors[] = 'Nhóm trưởng không tồn tại';
            }
        } else {
            $errors[] = 'Nhóm trưởng không để trống';
        };

        $groupChat->save();
        // Lấy id của nhóm chat mới sau khi cập nhật
        $newGroupId = $groupChat->id;
        if (!empty($request->members)) {
            foreach ($request->members as $member_id) {
                $member = User::find($member_id);
                if (!$member) {
                    $errors[] = 'Người dùng không tồn tại: ' . $member_id;
                    continue; // Bỏ qua và tiếp tục vòng lặp
                }
                $groupMembers = new GroupMemberModel;
                $groupMembers->group_chat_id = $newGroupId;
                $groupMembers->member_id = $member_id;
                $groupMembers->save();
            }
        } else {
            $errors[] = 'Cần ít nhất 1 thành viên';
        }
        if (!empty($errors)) {
            return response()->json([
                'error' => 'Error update group',
                'message' => implode(', ', $errors)
            ], 400);
        }
        return response()->json([
            'success' => 'Success update group',
            'message' => 'Cập nhật thành công',
            'groupChat' => $groupChat
        ], 200);
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
