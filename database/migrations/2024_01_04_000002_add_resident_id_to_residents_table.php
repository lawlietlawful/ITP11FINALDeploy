<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('residents', function (Blueprint $table) {
            $table->string('resident_id', 20)->nullable()->unique()->after('id');
        });

        // Backfill existing residents with IDs based on their creation order
        $residents = DB::table('residents')->orderBy('id')->get();
        foreach ($residents as $index => $resident) {
            $year = date('Y', strtotime($resident->created_at));
            $seq  = str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            DB::table('residents')
                ->where('id', $resident->id)
                ->update(['resident_id' => "RES-{$year}-{$seq}"]);
        }
    }

    public function down(): void {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn('resident_id');
        });
    }
};
