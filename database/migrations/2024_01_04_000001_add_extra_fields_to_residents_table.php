<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('residents', function (Blueprint $table) {
            $table->string('middle_name', 100)->nullable()->after('last_name');
            $table->enum('gender', ['Male', 'Female'])->nullable()->after('contact_number');
            $table->enum('civil_status', ['Single', 'Married', 'Widowed', 'Separated'])->nullable()->after('gender');
        });
    }

    public function down(): void {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn(['middle_name', 'gender', 'civil_status']);
        });
    }
};
