<?php

namespace Database\Seeders;

use App\Models\GroupChatModel;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::pluck('id')->toArray();
        $groupChats = [];
        for ($i = 1; $i <= 10; $i++) {
            $groupChats[] = [
                'name_group' => 'Nhóm ' . $i,
                'leader_id' => $userIds[array_rand($userIds)],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        GroupChatModel::insert($groupChats);
    }
}
