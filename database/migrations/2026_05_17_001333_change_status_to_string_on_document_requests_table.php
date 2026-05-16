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
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            // Drop the old ENUM check constraint in PostgreSQL
            DB::statement('ALTER TABLE document_requests DROP CONSTRAINT IF EXISTS document_requests_status_check');
        }

        Schema::table('document_requests', function (Blueprint $table) {
            $table->string('status', 50)->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->enum('status', ['pending', 'processing', 'ready_to_pickup', 'released', 'rejected'])->default('pending')->change();
        });
    }
};
