<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('official_reports', function (Blueprint $table) {
            $table->string('pihak_kedua_peran', 30)->nullable()->default('pembeli')->after('pihak_kedua_jabatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('official_reports', function (Blueprint $table) {
            $table->dropColumn('pihak_kedua_peran');
        });
    }
};
