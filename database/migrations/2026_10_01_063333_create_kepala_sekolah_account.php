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
        \App\Models\User::firstOrCreate(
            ['email' => 'kepala.sekolah@sekolah.sch.id'],
            [
                'name' => 'Bpk. Kepala Sekolah, M.Pd',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'kepala_sekolah',
                'jurusan_id' => null,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\User::where('email', 'kepala.sekolah@sekolah.sch.id')->delete();
    }
};
