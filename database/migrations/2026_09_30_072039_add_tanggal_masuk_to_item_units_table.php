<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('item_units', function (Blueprint $table) {
            $table->date('tanggal_masuk')->nullable()->after('catatan');
        });

        // Backfill existing units: set tanggal_masuk from created_at
        DB::table('item_units')
            ->whereNull('tanggal_masuk')
            ->update(['tanggal_masuk' => DB::raw('DATE(created_at)')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_units', function (Blueprint $table) {
            $table->dropColumn('tanggal_masuk');
        });
    }
};
