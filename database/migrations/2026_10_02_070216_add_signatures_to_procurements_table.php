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
        Schema::table('procurements', function (Blueprint $table) {
            // TTD Pengusul / Pemohon (Kepala Program / Unit Kerja)
            $table->string('ttd_pemohon')->nullable()->after('alasan');
            $table->timestamp('ttd_pemohon_at')->nullable()->after('ttd_pemohon');

            // TTD Verifikasi Sarpras
            $table->foreignId('verified_by')->nullable()->after('catatan_sarpras')->constrained('users')->nullOnDelete();
            $table->string('ttd_sarpras')->nullable()->after('verified_by');
            $table->timestamp('ttd_sarpras_at')->nullable()->after('ttd_sarpras');

            // TTD & Persetujuan Mengetahui Kepala Sekolah
            $table->string('status_kepsek')->default('menunggu')->after('ttd_sarpras_at'); // menunggu, disetujui, ditolak
            $table->foreignId('kepsek_by')->nullable()->after('status_kepsek')->constrained('users')->nullOnDelete();
            $table->timestamp('kepsek_at')->nullable()->after('kepsek_by');
            $table->text('catatan_kepsek')->nullable()->after('kepsek_at');
            $table->string('ttd_kepsek')->nullable()->after('catatan_kepsek');
            $table->timestamp('ttd_kepsek_at')->nullable()->after('ttd_kepsek');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('procurements', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropForeign(['kepsek_by']);
            $table->dropColumn([
                'ttd_pemohon',
                'ttd_pemohon_at',
                'verified_by',
                'ttd_sarpras',
                'ttd_sarpras_at',
                'status_kepsek',
                'kepsek_by',
                'kepsek_at',
                'catatan_kepsek',
                'ttd_kepsek',
                'ttd_kepsek_at',
            ]);
        });
    }
};
