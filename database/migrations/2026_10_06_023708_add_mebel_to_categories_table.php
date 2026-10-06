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
        \Illuminate\Support\Facades\DB::table('categories')->updateOrInsert(
            ['kode' => 'MBL'],
            [
                'nama' => 'Mebel & Furnitur Ruangan',
                'keterangan' => 'Meja guru/siswa, kursi, lemari arsip, rak buku, loker, kabinet dan perabot ruang',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('categories')->where('kode', 'MBL')->delete();
    }
};
