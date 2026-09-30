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
        Schema::create('item_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('jurusan_id')->constrained('jurusans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->string('satuan', 30)->default('unit');
            $table->date('tanggal_pemakaian');
            $table->string('nama_guru'); // Guru Pengampu Praktik
            $table->string('kelas', 100); // e.g. "XI TKI 1", "XII TKR 2"
            $table->string('keperluan_jobsheet'); // e.g. "Praktik Bubut Ulir Segitiga Jobsheet 3"
            $table->integer('stok_sebelum');
            $table->integer('stok_sesudah');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_usages');
    }
};
