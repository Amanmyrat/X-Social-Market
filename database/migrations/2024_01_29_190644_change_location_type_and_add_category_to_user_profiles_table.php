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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()
                ->constrained()
                ->onDelete('cascade');

            $table->dropColumn('location');
            $table->foreignId('location_id')->nullable()
                ->constrained()
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');

            $table->dropConstrainedForeignId('location_id');
            $table->string('location')->nullable();
        });
    }
};
