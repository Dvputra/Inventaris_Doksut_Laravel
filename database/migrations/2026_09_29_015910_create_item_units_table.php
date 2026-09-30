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
        Schema::create('item_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('jurusan_id')->constrained('jurusans')->cascadeOnDelete();
            $table->string('unit_code', 60)->unique(); // e.g. TP-MSN-001-01, TKR-KOM-001-PC01
            $table->string('nomor_seri')->nullable(); // Serial number pabrik / SN monitor
            $table->string('nomor_meja')->nullable(); // Khusus Lab Komputer, misal: "Meja PC-01"
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->enum('status', ['tersedia', 'dipinjam', 'dalam_perbaikan', 'afkir'])->default('tersedia');
            $table->string('lokasi_penempatan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_units');
    }
};
