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
        Schema::table('report_types', function (Blueprint $table) {
            // Ensure empty strings are NULL to avoid conversion errors
            DB::statement("UPDATE report_types SET title = NULL WHERE title = ''");

            // Convert existing text values into JSON format with 'tk' as default locale
            DB::statement("UPDATE report_types SET title = jsonb_build_object('tk', title::text) WHERE title IS NOT NULL");

            // Now, change column type to JSONB
            DB::statement("ALTER TABLE report_types ALTER COLUMN title SET DATA TYPE JSONB USING title::jsonb");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_types', function (Blueprint $table) {
            DB::statement("ALTER TABLE report_types ALTER COLUMN title SET DATA TYPE TEXT USING title->>'tk'");
        });
    }
};
