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
        Schema::table('categories', function (Blueprint $table) {
            // Ensure empty strings are NULL to avoid conversion errors
            DB::statement("UPDATE categories SET title = NULL WHERE title = ''");
            DB::statement("UPDATE categories SET description = NULL WHERE description = ''");

            // Convert existing text values into JSON format with 'tk' as default locale
            DB::statement("UPDATE categories SET title = jsonb_build_object('tk', title::text) WHERE title IS NOT NULL");
            DB::statement("UPDATE categories SET description = jsonb_build_object('tk', description::text) WHERE description IS NOT NULL");

            // Now, change column type to JSONB
            DB::statement("ALTER TABLE categories ALTER COLUMN title SET DATA TYPE JSONB USING title::jsonb");
            DB::statement("ALTER TABLE categories ALTER COLUMN description SET DATA TYPE JSONB USING description::jsonb");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            DB::statement("ALTER TABLE categories ALTER COLUMN title SET DATA TYPE TEXT USING title->>'tk'");
            DB::statement("ALTER TABLE categories ALTER COLUMN description SET DATA TYPE TEXT USING description->>'tk'");
        });
    }
};
