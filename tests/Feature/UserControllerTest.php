<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Sarpras dapat mengakses halaman kelola akun.
     */
    public function test_sarpras_can_access_user_management_page(): void
    {
        $sarpras = User::create([
            'name' => 'Admin Sarpras',
            'email' => 'sarpras@sekolah.sch.id',
            'password' => Hash::make('password'),
            'role' => 'sarpras',
        ]);

        $response = $this->actingAs($sarpras)->get('/users');

        $response->assertStatus(200);
        $response->assertSee('Kelola Akun Pengguna');
    }

    /**
     * Akun jurusan ditolak saat mengakses halaman kelola akun.
     */
    public function test_jurusan_is_forbidden_from_user_management_page(): void
    {
        $jurusan = Jurusan::create([
            'kode' => 'TKR',
            'nama' => 'Teknik Kendaraan Ringan',
        ]);

        $userJurusan = User::create([
            'name' => 'Akun TKR',
            'email' => 'tkr@sekolah.sch.id',
            'password' => Hash::make('password'),
            'role' => 'jurusan',
            'jurusan_id' => $jurusan->id,
        ]);

        $response = $this->actingAs($userJurusan)->get('/users');

        $response->assertStatus(403);
    }

    /**
     * Paginasi kelola akun ditampilkan dengan rapi ketika pengguna melebihi batas per halaman.
     */
    public function test_user_management_pagination_renders_cleanly(): void
    {
        $sarpras = User::firstOrCreate(
            ['email' => 'sarpras@sekolah.sch.id'],
            [
                'name' => 'Admin Sarpras',
                'password' => Hash::make('password'),
                'role' => 'sarpras',
            ]
        );

        // Buat user tambahan sehingga total melebihi 10
        User::factory()->count(12)->create();

        $response = $this->actingAs($sarpras)->get('/users');

        $response->assertStatus(200);
        $response->assertSee('Menampilkan');
        $response->assertSee('total hasil');
        $response->assertDontSee('pagination::bootstrap-5');
    }
}
