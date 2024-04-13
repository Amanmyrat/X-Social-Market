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
        Schema::table('followers', function (Blueprint $table) {
            $table->renameColumn('following_user_id', 'user_id');
            $table->renameColumn('followed_user_id', 'follow_user_id');

            // Drop old foreign keys
            $table->dropForeign(['following_user_id']);
            $table->dropForeign(['followed_user_id']);

            // Add new foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('follow_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('followers', function (Blueprint $table) {
            // Revert column names
            $table->renameColumn('user_id', 'following_user_id');
            $table->renameColumn('follow_user_id', 'followed_user_id');

            // Drop the new foreign keys
            $table->dropForeign(['user_id']);
            $table->dropForeign(['follow_user_id']);

            // Re-add the original foreign keys
            $table->foreign('following_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('followed_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
