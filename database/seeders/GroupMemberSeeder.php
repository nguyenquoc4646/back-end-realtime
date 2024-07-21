<?php

namespace Database\Seeders;

use App\Models\GroupChatModel;
use App\Models\GroupMemberModel;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = GroupChatModel::select('id')->get();
        $users = User::select('id')->get();
        $groupMembers = [];
        foreach ($groups as $group) {
            // Lấy ngẫu nhiên một thành viên từ danh sách users
            $randomUserId = $users->random()->id;

            // Thêm vào mảng dữ liệu insert
            $groupMembers[] = [
                'group_chat_id' => $group->id,
                'member_id' => $randomUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        GroupMemberModel::insert($groupMembers);
    }
}
