<?php

use App\Models\Jurusan;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Jurusan::firstOrCreate(
            ['kode' => 'SAR'],
            [
                'nama' => 'Sarpras Pusat & Fasilitas Umum',
                'kepala_bengkel' => 'Waka Bidang Sarana & Prasarana',
                'deskripsi' => 'Gudang penyimpanan sarpras, Lab Komputer CBT/ANBK umum, ruang guru, ruang TU, aula, dan sarana umum sekolah.',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Jurusan::where('kode', 'SAR')->delete();
    }
};
