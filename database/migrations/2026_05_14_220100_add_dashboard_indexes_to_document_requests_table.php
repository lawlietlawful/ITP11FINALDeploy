<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->index('status');
            $table->index('created_at');
            $table->index('released_at');
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['released_at']);
            $table->dropIndex(['status', 'created_at']);
        });
    }
};
