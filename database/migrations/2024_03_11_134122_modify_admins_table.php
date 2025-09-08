<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('surname')->nullable()->after('name');
            $table->string('phone')->nullable()->after('surname');

            $table->boolean('is_active')->default(true)->after('password');
            $table->text('profile_image')->nullable()->after('is_active');
            $table->text('last_activity')->nullable()->after('profile_image');
            if (Schema::hasColumn('admins', 'is_super')) {
                $table->dropColumn('is_super');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['surname', 'phone', 'is_active', 'profile_image']);
            $table->boolean('is_super')->default(false)->after('password');
        });
    }
};
