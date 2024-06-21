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
        Schema::table('post_notifications', function (Blueprint $table) {
            $table->foreignId('comment_id')->nullable()->after('reason')
                ->references('id')->on('post_comments')
                ->onDelete('cascade');
            $table->foreignId('post_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_notifications', function (Blueprint $table) {
            $table->foreignId('post_id')->nullable(false)->change();
            $table->dropForeign(['comment_id']);
            $table->dropColumn('comment_id');
        });
    }
};
