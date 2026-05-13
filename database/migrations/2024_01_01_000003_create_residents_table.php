<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->text('address');
            $table->string('contact_number', 20)->nullable();
            $table->date('birthdate')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('residents');
    }
};
