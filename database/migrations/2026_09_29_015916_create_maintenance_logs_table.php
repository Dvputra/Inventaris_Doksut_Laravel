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
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_unit_id')->constrained('item_units')->cascadeOnDelete();
            $table->foreignId('jurusan_id')->constrained('jurusans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal');
            $table->text('gejala_kerusakan'); // Keluhan / Gejala awal
            $table->text('tindakan_perbaikan'); // Solusi perbaikan / penggantian part
            $table->decimal('biaya', 15, 2)->nullable();
            $table->string('teknisi_pelaksana')->nullable();
            $table->enum('status', ['proses', 'selesai', 'tidak_dapat_diperbaiki'])->default('selesai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};
