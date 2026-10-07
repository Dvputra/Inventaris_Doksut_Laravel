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
            ['kode' => 'KBS'],
            [
                'nama' => 'Alat Kebersihan',
                'keterangan' => 'Sapu, pel, tempat sampah, ember, pengki, sikat, dan perlengkapan sanitasi',
            ]
        );

        \App\Models\Category::firstOrCreate(
            ['kode' => 'ATK'],
            [
                'nama' => 'Alat Tulis Kantor (ATK)',
                'keterangan' => 'Kertas, spidol, pulpen, stapler, binder, buku, dan perlengkapan tulis',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\Category::whereIn('kode', ['KBS', 'ATK'])->delete();
    }
};
