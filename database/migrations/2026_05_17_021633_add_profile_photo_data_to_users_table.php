<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('profile_photo_data')->nullable()->after('profile_photo_path');
            $table->string('profile_photo_mime', 50)->nullable()->after('profile_photo_data');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_photo_data', 'profile_photo_mime']);
        });
    }
};
