<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('group_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_chat_id');
            $table->foreign('group_chat_id')->references('id')->on('group_chats'); // Đảm bảo 'group_chats' là tên chính xác của bảng
            $table->unsignedBigInteger('user_send_id');
            $table->foreign('user_send_id')->references('id')->on('users');
            $table->longText('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_messages');
    }
};
