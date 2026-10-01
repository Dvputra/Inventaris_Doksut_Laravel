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
        Schema::create('official_reports', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->enum('jenis', ['barang_rusak', 'penjualan']);
            $table->string('judul');
            $table->date('tanggal');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jurusan_id')->nullable()->constrained()->nullOnDelete();
            
            // Pihak-pihak terkait
            $table->string('pihak_pertama_nama');
            $table->string('pihak_pertama_jabatan');
            $table->string('pihak_pertama_nip')->nullable();
            
            $table->string('pihak_kedua_nama');
            $table->string('pihak_kedua_jabatan');
            $table->string('pihak_kedua_instansi')->nullable();
            $table->string('pihak_kedua_kontak')->nullable();

            $table->string('mengetahui_nama')->default('Kepala SMK Dr. Sutomo Temanggung');
            $table->string('mengetahui_jabatan')->default('Kepala Sekolah');
            $table->string('mengetahui_nip')->nullable();

            // Rincian Tambahan
            $table->text('latar_belakang')->nullable(); // Alasan penghapusan / kronologi / dasar penjualan
            $table->decimal('total_nominal', 15, 2)->default(0); // Khusus penjualan (bisa 0 jika barang rusak)
            $table->string('status_dokumen')->default('selesai'); // draft, selesai
            $table->string('file_lampiran')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_reports');
    }
};
