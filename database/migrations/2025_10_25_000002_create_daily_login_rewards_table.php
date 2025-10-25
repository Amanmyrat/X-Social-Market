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
        Schema::create('daily_login_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('current_streak')->default(0);
            $table->integer('highest_streak')->default(0);
            $table->date('last_login_date')->nullable();
            $table->decimal('total_earned', 15, 2)->default(0);
            $table->integer('total_claims')->default(0);
            $table->timestamps();

            $table->unique('user_id');
            $table->index(['user_id', 'last_login_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_login_rewards');
    }
};

