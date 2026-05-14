<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label')->nullable();
            $table->string('type')->default('string'); // string, boolean, text
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'system_name', 'value' => 'VistáBarangay', 'label' => 'System Name', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_email', 'value' => 'contact@vistabarangay.com', 'label' => 'Contact Email', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_phone', 'value' => '09171234567', 'label' => 'Contact Phone', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_mode', 'value' => 'false', 'label' => 'Maintenance Mode (Disable Portal)', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'allow_online_requests', 'value' => 'true', 'label' => 'Allow Online Document Requests', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
