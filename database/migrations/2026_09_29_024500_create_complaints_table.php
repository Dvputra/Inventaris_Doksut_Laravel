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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code', 30)->unique();
            $table->string('nama_pelapor', 150);
            $table->string('kontak', 50);
            $table->foreignId('jurusan_id')->nullable()->constrained('jurusans')->nullOnDelete();
            $table->string('lokasi_ruang', 255);
            $table->enum('kategori', [
                'komputer_it',
                'kelistrikan',
                'mesin_peralatan',
                'sarana_gedung',
                'lainnya',
            ])->default('komputer_it');
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->string('judul_kendala', 255);
            $table->text('deskripsi');
            $table->string('foto')->nullable();
            $table->enum('tingkat_urgensi', ['rendah', 'sedang', 'tinggi_darurat'])->default('sedang');
            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'ditolak'])->default('menunggu');
            $table->text('tindak_lanjut')->nullable();
            $table->string('teknisi_penanganan', 150)->nullable();
            $table->timestamp('tanggal_selesai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
