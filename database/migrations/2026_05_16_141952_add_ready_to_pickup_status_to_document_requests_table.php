<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE document_requests MODIFY COLUMN status ENUM('pending', 'processing', 'ready_to_pickup', 'released', 'rejected') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE document_requests MODIFY COLUMN status ENUM('pending', 'processing', 'released', 'rejected') DEFAULT 'pending'");
    }
};
