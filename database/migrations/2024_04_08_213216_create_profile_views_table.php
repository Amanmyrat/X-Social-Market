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
        Schema::create('profile_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viewer_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('user_profile_id')
                ->constrained('user_profiles')
                ->onDelete('cascade');
            $table->date('viewed_at');
            $table->timestamps();

            // Unique constraint to ensure a user can only view a profile once per day
            $table->unique(['user_profile_id', 'viewer_id', 'viewed_at'], 'profile_viewer_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_views');
    }
};
