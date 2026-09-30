<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class JurusanManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $sarprasUser;

    protected User $kurikulumUser;

    protected Jurusan $kurikulumUnit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sarprasUser = User::firstOrCreate(
            ['email' => 'sarpras@sekolah.sch.id'],
            [
                'name' => 'Admin Sarpras',
                'password' => Hash::make('password'),
                'role' => 'sarpras',
                'jurusan_id' => null,
            ]
        );

        $this->kurikulumUnit = Jurusan::firstOrCreate(
            ['kode' => 'KUR'],
            [
                'nama' => 'Kurikulum',
                'kepala_bengkel' => 'Waka Bidang Kurikulum',
                'deskripsi' => 'Unit pengembangan kurikulum dan pembelajaran',
            ]
        );

        $this->kurikulumUser = User::firstOrCreate(
            ['email' => 'kurikulum@sekolah.sch.id'],
            [
                'name' => 'Akun Unit Kurikulum',
                'password' => Hash::make('password'),
                'role' => 'jurusan',
                'jurusan_id' => $this->kurikulumUnit->id,
            ]
        );
    }

    /**
     * Sarpras dapat mengakses halaman kelola unit kerja & jurusan.
     */
    public function test_sarpras_can_view_jurusan_management_page(): void
    {
        $response = $this->actingAs($this->sarprasUser)->get(route('jurusans.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Unit Kerja & Jurusan');
        $response->assertSee('Kurikulum');
        $response->assertSee('Waka Bidang Kurikulum');
    }

    /**
     * Akun jurusan/unit kerja dilarang mengakses halaman kelola jurusan admin.
     */
    public function test_jurusan_user_is_forbidden_from_jurusan_management_index(): void
    {
        $response = $this->actingAs($this->kurikulumUser)->get(route('jurusans.index'));

        $response->assertForbidden();
    }

    /**
     * Sarpras dapat menambah unit kerja baru dengan akun pengguna otomatis.
     */
    public function test_sarpras_can_create_new_work_unit(): void
    {
        $response = $this->actingAs($this->sarprasUser)->post(route('jurusans.store'), [
            'kode' => 'BKK',
            'nama' => 'Bursa Kerja Khusus (BKK)',
            'kepala_bengkel' => 'Ketua BKK Sekolah',
            'deskripsi' => 'Penyaluran tamatan dan kemitraan dunia usaha dunia industri.',
            'create_user_account' => '1',
            'user_email' => 'bkk@sekolah.sch.id',
            'user_password' => 'password123',
        ]);

        $response->assertRedirect(route('jurusans.index'));
        $this->assertDatabaseHas('jurusans', [
            'kode' => 'BKK',
            'nama' => 'Bursa Kerja Khusus (BKK)',
            'kepala_bengkel' => 'Ketua BKK Sekolah',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'bkk@sekolah.sch.id',
            'role' => 'jurusan',
        ]);
    }

    /**
     * Sarpras dapat mengubah nama unit kerja dan nama kepala bengkel / kepala unit.
     */
    public function test_sarpras_can_update_unit_kerja_name_and_kepala_bengkel(): void
    {
        $response = $this->actingAs($this->sarprasUser)->put(route('jurusans.update', $this->kurikulumUnit), [
            'kode' => 'KUR',
            'nama' => 'Bidang Kurikulum & Pembelajaran',
            'kepala_bengkel' => 'Dr. H. Muhammad Ilyas, M.Pd',
            'deskripsi' => 'Deskripsi kurikulum baru',
        ]);

        $response->assertRedirect(route('jurusans.index'));
        $this->assertDatabaseHas('jurusans', [
            'id' => $this->kurikulumUnit->id,
            'nama' => 'Bidang Kurikulum & Pembelajaran',
            'kepala_bengkel' => 'Dr. H. Muhammad Ilyas, M.Pd',
            'deskripsi' => 'Deskripsi kurikulum baru',
        ]);
    }

    /**
     * Akun unit kerja dapat memperbarui nama kepala unit / bengkel mandiri via dashboard.
     */
    public function test_jurusan_user_can_update_their_own_unit_kepala_bengkel(): void
    {
        $response = $this->actingAs($this->kurikulumUser)->patch(route('jurusan.my-unit.update'), [
            'kepala_bengkel' => 'Waka Kurikulum Baru, M.Pd',
            'deskripsi' => 'Deskripsi diperbarui oleh unit',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('jurusans', [
            'id' => $this->kurikulumUnit->id,
            'kepala_bengkel' => 'Waka Kurikulum Baru, M.Pd',
            'deskripsi' => 'Deskripsi diperbarui oleh unit',
        ]);
    }

    /**
     * Akun unit kerja dapat login dan melihat header nama unit dan kepala bengkel.
     */
    public function test_unit_user_sees_kepala_bengkel_on_dashboard(): void
    {
        $response = $this->actingAs($this->kurikulumUser)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Kurikulum (KUR)');
        $response->assertSee('Waka Bidang Kurikulum');
    }
}
