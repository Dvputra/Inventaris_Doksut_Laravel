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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->foreignId('jurusan_id')->constrained('jurusans')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->integer('jumlah')->default(1);
            $table->string('satuan', 30)->default('unit'); // unit, set, pcs, botol, roll, lembar
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->string('lokasi')->nullable(); // e.g. Bengkel Mesin 1, Lab Otomotif A
            $table->enum('jenis', ['alat', 'bahan'])->default('alat'); // alat/mesin vs bahan praktik habis pakai
            $table->string('sumber_dana')->nullable(); // BOS, DAK, BPOPP, Komite
            $table->year('tahun_pengadaan')->nullable();
            $table->text('spesifikasi')->nullable();
            $table->string('foto')->nullable();

            // Kolom Khusus Komputer & Perangkat IT (Opsional / Tidak Wajib)
            $table->boolean('is_computer')->default(false);
            $table->string('processor')->nullable(); // misal: Core i5-11400 / Ryzen 5
            $table->string('ram')->nullable(); // misal: 8 GB / 16 GB DDR4
            $table->string('storage')->nullable(); // misal: SSD 512 GB / HDD 1 TB
            $table->string('gpu_vga')->nullable(); // misal: NVIDIA GTX 1650 / Intel UHD
            $table->string('monitor')->nullable(); // misal: LG 24 Inch IPS
            $table->string('sistem_operasi')->nullable(); // misal: Windows 11 Pro / Ubuntu (opsional)

            // Peringatan Stok Minimum untuk Bahan Habis Pakai
            $table->integer('min_stok')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
