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
        Schema::table('messages', function (Blueprint $table) {
            $table->index('read_at');
            $table->index('sender_deleted_at');
            $table->index('receiver_deleted_at');
        });

        Schema::table('chats', function (Blueprint $table) {
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['read_at']); // Drop additional indexes
            $table->dropIndex(['sender_deleted_at']);
            $table->dropIndex(['receiver_deleted_at']);
        });

        Schema::table('chats', function (Blueprint $table) {
            $table->dropIndex(['deleted_at']);
        });
    }
};
