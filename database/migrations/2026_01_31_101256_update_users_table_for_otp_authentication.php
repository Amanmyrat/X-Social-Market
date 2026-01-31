<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Make password nullable for OTP-only authentication
            $table->string('password')->nullable()->change();
            
            // Track when user verified their phone via OTP
            $table->timestamp('phone_verified_at')->nullable();
        });
        
        // Set phone_verified_at for existing users who have passwords
        DB::table('users')
            ->whereNotNull('password')
            ->update(['phone_verified_at' => DB::raw('created_at')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
            $table->dropColumn('phone_verified_at');
        });
    }
};
