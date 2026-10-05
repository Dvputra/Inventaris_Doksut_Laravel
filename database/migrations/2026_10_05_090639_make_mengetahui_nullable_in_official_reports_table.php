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
            $table->string('mengetahui_nama')->nullable()->change();
            $table->string('mengetahui_jabatan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('official_reports', function (Blueprint $table) {
            $table->string('mengetahui_nama')->nullable(false)->change();
            $table->string('mengetahui_jabatan')->nullable(false)->change();
        });
    }
};
