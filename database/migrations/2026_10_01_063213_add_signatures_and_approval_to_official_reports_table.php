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
            $table->string('status_approval')->default('menunggu_acc')->after('status_dokumen'); // menunggu_acc, disetujui, ditolak
            $table->foreignId('approved_by')->nullable()->after('status_approval')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('catatan_approval')->nullable()->after('approved_at');
            
            // Tanda tangan elektronik (Disimpan sebagai path gambar PNG transparan)
            $table->string('ttd_pihak_pertama')->nullable()->after('catatan_approval');
            $table->timestamp('ttd_pihak_pertama_at')->nullable()->after('ttd_pihak_pertama');
            $table->string('ttd_mengetahui')->nullable()->after('ttd_pihak_pertama_at');
            $table->timestamp('ttd_mengetahui_at')->nullable()->after('ttd_mengetahui');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('official_reports', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'status_approval',
                'approved_by',
                'approved_at',
                'catatan_approval',
                'ttd_pihak_pertama',
                'ttd_pihak_pertama_at',
                'ttd_mengetahui',
                'ttd_mengetahui_at',
            ]);
        });
    }
};
