<?php

namespace Database\Seeders;

use App\Models\GroupChatModel;
use App\Models\GroupMemberModel;
use App\Models\GroupMessageModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groupChats = GroupChatModel::pluck('id')->toArray(); // lấy danh sách id group từ bảng group
        $groupMessages = [];
        foreach ($groupChats as $groupId) {
            $userIds = GroupMemberModel::where('group_chat_id', $groupId)->pluck('member_id')->toArray();
            if (!empty($userIds)) {
                for ($i = 1; $i <= 10; $i++) {
                    $groupMessages[] = [
                        'group_chat_id' => $groupId,
                        'user_send_id' => $userIds[array_rand($userIds)],
                        'message' => 'Tin nhắn gửi từ ' . $i . ' đến nhóm ' . $groupId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

        }
        GroupMessageModel::insert($groupMessages);
    }
}
