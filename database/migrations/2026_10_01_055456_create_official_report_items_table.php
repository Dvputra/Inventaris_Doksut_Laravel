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
        Schema::create('official_report_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('official_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('item_unit_id')->nullable()->constrained()->nullOnDelete();
            
            // Snapshot data barang agar jika barang di database diedit/dihapus, dokumen BA tetap utuh & valid
            $table->string('kode_barang')->nullable();
            $table->string('nama_barang');
            $table->string('unit_code')->nullable();
            $table->string('nomor_seri')->nullable();
            $table->integer('jumlah')->default(1);
            $table->string('satuan')->default('unit');
            $table->string('kondisi_saat_lapor')->nullable(); // rusak_berat, rusak_total, bekas_layak, dll
            $table->decimal('harga_satuan', 15, 2)->default(0); // Khusus penjualan
            $table->decimal('subtotal', 15, 2)->default(0); // Khusus penjualan
            $table->text('keterangan')->nullable(); // Alasan rusak / rincian kondisi

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_report_items');
    }
};
