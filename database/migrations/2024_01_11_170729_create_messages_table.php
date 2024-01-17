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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('sender_user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('receiver_user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('type');
            $table->text('body')->nullable();
            $table->json('extra')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('sender_deleted_at')->nullable();
            $table->timestamp('receiver_deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
