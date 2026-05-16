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
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            // PostgreSQL: Drop enum constraint and change column type to varchar
            DB::statement('ALTER TABLE document_requests DROP CONSTRAINT IF EXISTS document_requests_status_check');
            DB::statement("ALTER TABLE document_requests ALTER COLUMN status TYPE VARCHAR(50)");
        } else {
            // MySQL: Change enum to varchar
            DB::statement("ALTER TABLE document_requests MODIFY COLUMN status VARCHAR(50) DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: safely leave as varchar
    }
};
