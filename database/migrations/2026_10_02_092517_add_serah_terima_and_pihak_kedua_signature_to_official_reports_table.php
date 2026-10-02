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
            $table->string('jenis')->change(); // ubah dari enum ke string agar fleksibel mendukung serah_terima
            $table->string('pihak_kedua_nip')->nullable()->after('pihak_kedua_jabatan');
            $table->string('ttd_pihak_kedua')->nullable()->after('ttd_pihak_pertama_at');
            $table->timestamp('ttd_pihak_kedua_at')->nullable()->after('ttd_pihak_kedua');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('official_reports', function (Blueprint $table) {
            $table->dropColumn([
                'pihak_kedua_nip',
                'ttd_pihak_kedua',
                'ttd_pihak_kedua_at',
            ]);
        });
    }
};
