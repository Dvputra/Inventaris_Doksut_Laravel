<?php

namespace Tests\Feature;

use App\Models\Complaint;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PublicComplaintTest extends TestCase
{
    use RefreshDatabase;

    protected Jurusan $jurusan;

    protected User $sarprasUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jurusan = Jurusan::create([
            'kode' => 'TP',
            'nama' => 'Teknik Pemesinan',
        ]);

        $this->sarprasUser = User::create([
            'name' => 'Admin Sarpras',
            'email' => 'sarpras@sekolah.sch.id',
            'password' => Hash::make('password'),
            'role' => 'sarpras',
        ]);
    }

    /**
     * Guru dan publik dapat mengakses halaman portal pengaduan tanpa login.
     */
    public function test_public_user_can_view_welcome_page_and_complaint_form(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Layanan Kendala Fasilitas Sekolah');
        $response->assertSee('Lapor Kendala Sekarang');
        $response->assertSee('Formulir Lapor Kendala Fasilitas');
    }

    /**
     * Guru dapat mengirimkan pengaduan kendala tanpa perlu login.
     */
    public function test_teacher_can_submit_complaint_without_login(): void
    {
        $payload = [
            'nama_pelapor' => 'Bpk. Ahmad Fauzi, S.Pd',
            'kontak' => '081234567899',
            'jurusan_id' => $this->jurusan->id,
            'lokasi_ruang' => 'Lab Komputer CAD Meja 05',
            'kategori' => 'komputer_it',
            'judul_kendala' => 'Monitor PC tidak menyala',
            'deskripsi' => 'Layar monitor mati total meskipun kabel power sudah terpasang kencang.',
            'tingkat_urgensi' => 'sedang',
        ];

        $response = $this->post('/lapor', $payload);

        $response->assertRedirect();

        $complaint = Complaint::where('kontak', '081234567899')->first();
        $this->assertNotNull($complaint);
        $this->assertEquals('Bpk. Ahmad Fauzi, S.Pd', $complaint->nama_pelapor);
        $this->assertEquals('menunggu', $complaint->status);
        $this->assertStringStartsWith('ADU-', $complaint->ticket_code);
    }

    /**
     * Pelapor dapat melacak status penanganan kendala menggunakan kode tiket.
     */
    public function test_teacher_can_track_complaint_with_ticket_code(): void
    {
        $complaint = Complaint::create([
            'ticket_code' => 'ADU-2609-TEST',
            'nama_pelapor' => 'Ibu Siti',
            'kontak' => '0899999999',
            'lokasi_ruang' => 'Ruang Teori',
            'kategori' => 'kelistrikan',
            'judul_kendala' => 'Lampu Neon Mati',
            'deskripsi' => 'Ruangan gelap, lampu berkedip lalu mati.',
            'tingkat_urgensi' => 'rendah',
            'status' => 'diproses',
            'teknisi_penanganan' => 'Pak Bambang',
        ]);

        $response = $this->get('/lacak-status?ticket='.$complaint->ticket_code);

        $response->assertStatus(200);
        $response->assertSee('ADU-2609-TEST');
        $response->assertSee('Lampu Neon Mati');
        $response->assertSee('Pak Bambang');
        $response->assertSee('Sedang Ditangani');
    }

    /**
     * Petugas Sarpras yang login dapat melihat daftar dan memperbarui status pengaduan.
     */
    public function test_staff_can_view_and_update_complaint(): void
    {
        $complaint = Complaint::create([
            'ticket_code' => 'ADU-2609-UPDT',
            'nama_pelapor' => 'Bpk. Dani',
            'kontak' => '0877777777',
            'lokasi_ruang' => 'Lab Komputer 2',
            'kategori' => 'komputer_it',
            'judul_kendala' => 'Koneksi LAN Putus',
            'deskripsi' => 'Kabel LAN lepas konektor RJ45.',
            'tingkat_urgensi' => 'sedang',
            'status' => 'menunggu',
        ]);

        // Akses daftar pengaduan
        $indexResponse = $this->actingAs($this->sarprasUser)->get('/complaints');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('ADU-2609-UPDT');

        // Update status pengaduan menjadi selesai
        $updateResponse = $this->actingAs($this->sarprasUser)->put('/complaints/'.$complaint->id, [
            'status' => 'selesai',
            'teknisi_penanganan' => 'Tim Jaringan Sarpras',
            'tindak_lanjut' => 'Konektor RJ45 telah dikrimping ulang dan koneksi internet telah stabil.',
        ]);

        $updateResponse->assertRedirect('/complaints/'.$complaint->id);

        $complaint->refresh();
        $this->assertEquals('selesai', $complaint->status);
        $this->assertEquals('Tim Jaringan Sarpras', $complaint->teknisi_penanganan);
        $this->assertNotNull($complaint->tanggal_selesai);
    }

    /**
     * Akun non-sarpras (jurusan) dilarang mengakses dan melihat menu pengaduan guru.
     */
    public function test_jurusan_account_cannot_access_or_see_complaints_menu(): void
    {
        $jurusanUser = User::create([
            'name' => 'Kaprogli Mesin',
            'email' => 'mesin@sekolah.sch.id',
            'password' => Hash::make('password'),
            'role' => 'jurusan',
            'jurusan_id' => $this->jurusan->id,
        ]);

        $complaint = Complaint::create([
            'ticket_code' => 'ADU-TEST-AUTH',
            'nama_pelapor' => 'Guru Penguji',
            'kontak' => '0812345678',
            'jurusan_id' => $this->jurusan->id,
            'lokasi_ruang' => 'Ruang Teori',
            'kategori' => 'kelistrikan',
            'judul_kendala' => 'Stop kontak rusak',
            'deskripsi' => 'Perlu ganti stop kontak baru',
            'status' => 'menunggu',
        ]);

        // 1. Akun jurusan diblokir 403 saat mengakses route complaints dan cetak pengaduan
        $this->actingAs($jurusanUser)->get(route('complaints.index'))->assertForbidden();
        $this->actingAs($jurusanUser)->get(route('complaints.show', $complaint))->assertForbidden();
        $this->actingAs($jurusanUser)->get(route('reports.complaints.print'))->assertForbidden();

        // 2. Akun jurusan tidak melihat menu Pengaduan Guru di sidebar
        $jurusanDashboard = $this->actingAs($jurusanUser)->get(route('dashboard'));
        $jurusanDashboard->assertStatus(200);
        $jurusanDashboard->assertDontSee('Pengaduan Guru');

        // 3. Akun Sarpras dapat melihat menu Pengaduan Guru di sidebar
        $sarprasDashboard = $this->actingAs($this->sarprasUser)->get(route('dashboard'));
        $sarprasDashboard->assertStatus(200);
        $sarprasDashboard->assertSee('Pengaduan Guru');
    }
}
