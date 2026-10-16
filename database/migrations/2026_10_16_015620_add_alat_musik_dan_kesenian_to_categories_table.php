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
        \App\Models\Category::firstOrCreate(
            ['kode' => 'MSK'],
            [
                'nama' => 'Alat Musik & Kesenian',
                'keterangan' => 'Gitar, keyboard, drum, gamelan, sound amplifier musik, mikrofon, dan perlengkapan kesenian',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\Category::where('kode', 'MSK')->delete();
    }
};
