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
        Schema::table('colors', function (Blueprint $table) {
            // Ensure empty strings are NULL to avoid conversion errors
            DB::statement("UPDATE colors SET title = NULL WHERE title = ''");

            // Convert existing text values into JSON format with 'tk' as default locale
            DB::statement("UPDATE colors SET title = jsonb_build_object('tk', title::text) WHERE title IS NOT NULL");

            // Now, change column type to JSONB
            DB::statement("ALTER TABLE colors ALTER COLUMN title SET DATA TYPE JSONB USING title::jsonb");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colors', function (Blueprint $table) {
            DB::statement("ALTER TABLE colors ALTER COLUMN title SET DATA TYPE TEXT USING title->>'tk'");
        });
    }
};
