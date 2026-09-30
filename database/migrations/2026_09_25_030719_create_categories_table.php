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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique(); // KOM, MSN, TLS, UKR, ELK, BHN, APD
            $table->string('nama'); // e.g. Komputer & Jaringan, Mesin & Peralatan Bengkel, Alat Ukur, Hand Tools, Bahan Praktik
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
