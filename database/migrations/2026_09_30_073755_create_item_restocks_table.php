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
        Schema::create('item_restocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('jurusan_id')->constrained('jurusans')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('jumlah');
            $table->string('satuan', 30);
            $table->date('tanggal_masuk');
            $table->string('sumber_dana')->nullable(); // BOS, APBD, Komite, Bantuan Industri, dll
            $table->string('pemasok')->nullable(); // Toko / Supplier
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
        Schema::dropIfExists('item_restocks');
    }
};
