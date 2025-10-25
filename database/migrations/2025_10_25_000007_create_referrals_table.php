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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referee_id')->constrained('users')->onDelete('cascade');
            $table->decimal('reward_amount', 15, 2)->default(0);
            $table->boolean('reward_claimed')->default(false);
            $table->timestamp('reward_claimed_at')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->unique(['referrer_id', 'referee_id']);
            $table->index(['referrer_id', 'status']);
            $table->index('reward_claimed');
        });

        // Add referral reward setting
        DB::table('app_settings')->insert([
            'key' => 'referral_reward',
            'value' => '10',
            'type' => 'decimal',
            'description' => 'Reward amount for successful referral (TNT coins)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('app_settings')->where('key', 'referral_reward')->delete();
        Schema::dropIfExists('referrals');
    }
};

