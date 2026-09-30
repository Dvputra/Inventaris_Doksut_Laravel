<?php

use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $units = [
            [
                'kode' => 'KUR',
                'nama' => 'Kurikulum',
                'kepala_bengkel' => 'Waka Bidang Kurikulum',
                'deskripsi' => 'Unit kerja bidang kurikulum, perencanaan pembelajaran, dan asesmen akademik sekolah.',
                'email' => 'kurikulum@sekolah.sch.id',
                'account_name' => 'Akun Unit Kurikulum',
            ],
            [
                'kode' => 'WMM',
                'nama' => 'Wakil Manajemen Mutu (WMM)',
                'kepala_bengkel' => 'Ketua WMM',
                'deskripsi' => 'Unit kerja penjaminan mutu pendidikan, audit mutu internal, standarisasi ISO, dan tata kelola mutu sekolah.',
                'email' => 'wmm@sekolah.sch.id',
                'account_name' => 'Akun Unit WMM',
            ],
            [
                'kode' => 'KES',
                'nama' => 'Kesiswaan',
                'kepala_bengkel' => 'Waka Bidang Kesiswaan',
                'deskripsi' => 'Unit kerja pembinaan karakter, kedisiplinan, OSIS/ekstrakurikuler, dan layanan kesiswaan.',
                'email' => 'kesiswaan@sekolah.sch.id',
                'account_name' => 'Akun Unit Kesiswaan',
            ],
            [
                'kode' => 'HUM',
                'nama' => 'Hubungan Masyarakat (Humas)',
                'kepala_bengkel' => 'Waka Bidang Humas',
                'deskripsi' => 'Unit kerja kerjasama industri, prakerin/PKL, bursa kerja khusus (BKK), dan kemitraan eksternal.',
                'email' => 'humas@sekolah.sch.id',
                'account_name' => 'Akun Unit Humas',
            ],
            [
                'kode' => 'TU',
                'nama' => 'Tata Usaha (TU)',
                'kepala_bengkel' => 'Kepala Tata Usaha',
                'deskripsi' => 'Unit tata usaha administrasi kepegawaian, persuratan, kearsipan, dan operasional kantor.',
                'email' => 'tu@sekolah.sch.id',
                'account_name' => 'Akun Unit Tata Usaha (TU)',
            ],
        ];

        foreach ($units as $u) {
            $jurusan = Jurusan::firstOrCreate(
                ['kode' => $u['kode']],
                [
                    'nama' => $u['nama'],
                    'kepala_bengkel' => $u['kepala_bengkel'],
                    'deskripsi' => $u['deskripsi'],
                ]
            );

            User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['account_name'],
                    'password' => Hash::make('password'),
                    'role' => 'jurusan',
                    'jurusan_id' => $jurusan->id,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $kodes = ['KUR', 'WMM', 'KES', 'HUM', 'TU'];
        $emails = [
            'kurikulum@sekolah.sch.id',
            'wmm@sekolah.sch.id',
            'kesiswaan@sekolah.sch.id',
            'humas@sekolah.sch.id',
            'tu@sekolah.sch.id',
        ];

        User::whereIn('email', $emails)->delete();
        Jurusan::whereIn('kode', $kodes)->delete();
    }
};
