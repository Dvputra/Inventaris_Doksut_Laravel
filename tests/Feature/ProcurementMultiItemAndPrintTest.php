<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\Procurement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProcurementMultiItemAndPrintTest extends TestCase
{
    use RefreshDatabase;

    protected User $sarprasUser;

    protected User $jurusanUserA;

    protected User $jurusanUserB;

    protected Jurusan $jurusanA;

    protected Jurusan $jurusanB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jurusanA = Jurusan::firstOrCreate(
            ['kode' => 'TKR'],
            [
                'nama' => 'Teknik Kendaraan Ringan',
                'kepala_bengkel' => 'Budi Santoso, S.T.',
                'deskripsi' => 'Bengkel Otomotif TKR',
            ]
        );

        $this->jurusanB = Jurusan::firstOrCreate(
            ['kode' => 'TKJ'],
            [
                'nama' => 'Teknik Komputer & Jaringan',
                'kepala_bengkel' => 'Siti Aminah, M.Kom.',
                'deskripsi' => 'Laboratorium Jaringan Komputer',
            ]
        );

        $this->sarprasUser = User::firstOrCreate(
            ['email' => 'sarpras@sekolah.sch.id'],
            [
                'name' => 'Admin Sarpras',
                'password' => Hash::make('password'),
                'role' => 'sarpras',
                'jurusan_id' => null,
            ]
        );

        $this->jurusanUserA = User::firstOrCreate(
            ['email' => 'tkr@sekolah.sch.id'],
            [
                'name' => 'Kajur TKR',
                'password' => Hash::make('password'),
                'role' => 'jurusan',
                'jurusan_id' => $this->jurusanA->id,
            ]
        );

        $this->jurusanUserB = User::firstOrCreate(
            ['email' => 'tkj@sekolah.sch.id'],
            [
                'name' => 'Kajur TKJ',
                'password' => Hash::make('password'),
                'role' => 'jurusan',
                'jurusan_id' => $this->jurusanB->id,
            ]
        );
    }

    public function test_jurusan_can_submit_multi_item_procurement(): void
    {
        $response = $this->actingAs($this->jurusanUserA)->post(route('procurements.store'), [
            'judul_pengadaan' => 'Usulan Alat Praktik Perkakas Mesin Gasal',
            'alasan' => 'Dibutuhkan untuk kegiatan praktikum bengkel mesin dan persiapan UKK.',
            'items' => [
                [
                    'nama_barang' => 'Kunci Torsi Digital',
                    'spesifikasi' => 'Rentang 20-200 Nm Merk Tekiro',
                    'jumlah' => 2,
                    'satuan' => 'unit',
                    'harga_satuan' => 1500000,
                    'perkiraan_biaya' => 3000000,
                    'keterangan' => 'Untuk kalibrasi mesin',
                ],
                [
                    'nama_barang' => 'Kunci Pas Ring Set 8-24mm',
                    'spesifikasi' => 'Chrome Vanadium 14 pcs',
                    'jumlah' => 5,
                    'satuan' => 'set',
                    'harga_satuan' => 350000,
                    'perkiraan_biaya' => 1750000,
                    'keterangan' => 'Tambahan meja praktik',
                ],
            ],
        ]);

        $response->assertRedirect(route('procurements.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('procurements', [
            'jurusan_id' => $this->jurusanA->id,
            'judul_pengadaan' => 'Usulan Alat Praktik Perkakas Mesin Gasal',
            'nama_barang' => 'Kunci Torsi Digital',
            'perkiraan_biaya' => 4750000,
            'status' => 'menunggu',
        ]);

        $procurement = Procurement::where('jurusan_id', $this->jurusanA->id)->first();
        $this->assertNotNull($procurement->nomor_usulan);
        $this->assertStringStartsWith('UP-', $procurement->nomor_usulan);
        $this->assertCount(2, $procurement->items);

        $this->assertDatabaseHas('procurement_items', [
            'procurement_id' => $procurement->id,
            'nama_barang' => 'Kunci Torsi Digital',
            'jumlah' => 2,
        ]);
        $this->assertDatabaseHas('procurement_items', [
            'procurement_id' => $procurement->id,
            'nama_barang' => 'Kunci Pas Ring Set 8-24mm',
            'jumlah' => 5,
        ]);
    }

    public function test_legacy_single_item_submission_backward_compatibility(): void
    {
        $response = $this->actingAs($this->jurusanUserA)->post(route('procurements.store'), [
            'nama_barang' => 'Mesin Las Inverter 450W',
            'spesifikasi' => 'Merk Lakoni Falcon 120e',
            'jumlah' => 1,
            'satuan' => 'unit',
            'perkiraan_biaya' => 1200000,
            'alasan' => 'Penggantian mesin las lama yang rusak terbakar.',
        ]);

        $response->assertRedirect(route('procurements.index'));

        $procurement = Procurement::where('nama_barang', 'Mesin Las Inverter 450W')->first();
        $this->assertNotNull($procurement);
        $this->assertCount(1, $procurement->items);
        $this->assertEquals(1200000, (float) $procurement->perkiraan_biaya);
    }

    public function test_procurement_edit_screen_and_update(): void
    {
        // Create initial procurement
        $procurement = Procurement::create([
            'nomor_usulan' => 'UP-202609-0099',
            'user_id' => $this->jurusanUserA->id,
            'jurusan_id' => $this->jurusanA->id,
            'judul_pengadaan' => 'Usulan Awal',
            'nama_barang' => 'Obeng Plus Minus',
            'jumlah' => 10,
            'satuan' => 'pcs',
            'perkiraan_biaya' => 200000,
            'alasan' => 'Kebutuhan awal',
            'status' => 'menunggu',
        ]);
        $procurement->items()->create([
            'nama_barang' => 'Obeng Plus Minus',
            'jumlah' => 10,
            'satuan' => 'pcs',
            'harga_satuan' => 20000,
            'perkiraan_biaya' => 200000,
        ]);

        // Access edit view
        $editResponse = $this->actingAs($this->jurusanUserA)->get(route('procurements.edit', $procurement));
        $editResponse->assertOk();
        $editResponse->assertSee('Obeng Plus Minus');

        // Update with 2 items
        $updateResponse = $this->actingAs($this->jurusanUserA)->put(route('procurements.update', $procurement), [
            'judul_pengadaan' => 'Usulan Diperbarui: Obeng & Tang',
            'alasan' => 'Disesuaikan dengan tambahan revisi guru produktif.',
            'items' => [
                [
                    'nama_barang' => 'Obeng Set Presisi',
                    'spesifikasi' => 'Magnetic tip',
                    'jumlah' => 15,
                    'satuan' => 'set',
                    'harga_satuan' => 30000,
                    'perkiraan_biaya' => 450000,
                ],
                [
                    'nama_barang' => 'Tang Potong Kabel',
                    'spesifikasi' => '6 inch',
                    'jumlah' => 5,
                    'satuan' => 'unit',
                    'harga_satuan' => 50000,
                    'perkiraan_biaya' => 250000,
                ],
            ],
        ]);

        $updateResponse->assertRedirect(route('procurements.index'));

        $procurement->refresh();
        $this->assertEquals('Usulan Diperbarui: Obeng & Tang', $procurement->judul_pengadaan);
        $this->assertEquals(700000, (float) $procurement->perkiraan_biaya);
        $this->assertCount(2, $procurement->items);
        $this->assertEquals('Obeng Set Presisi', $procurement->nama_barang);
    }

    public function test_procurement_print_view(): void
    {
        $procurement = Procurement::create([
            'nomor_usulan' => 'UP-202609-0100',
            'user_id' => $this->jurusanUserA->id,
            'jurusan_id' => $this->jurusanA->id,
            'judul_pengadaan' => 'Pengadaan Toolset Standar',
            'nama_barang' => 'Toolset Lengkap',
            'jumlah' => 1,
            'satuan' => 'box',
            'perkiraan_biaya' => 5000000,
            'alasan' => 'Peralatan utama bengkel otomotif.',
            'status' => 'menunggu',
        ]);
        $procurement->items()->create([
            'nama_barang' => 'Toolset Lengkap',
            'spesifikasi' => '120 pcs toolbox mekanik',
            'jumlah' => 1,
            'satuan' => 'box',
            'harga_satuan' => 5000000,
            'perkiraan_biaya' => 5000000,
        ]);

        $printResponse = $this->actingAs($this->jurusanUserA)->get(route('procurements.print', $procurement));
        $printResponse->assertOk();
        $printResponse->assertSee('SURAT USULAN PENGADAAN BARANG');
        $printResponse->assertSee('UP-202609-0100');
        $printResponse->assertSee('Toolset Lengkap');
        $printResponse->assertSee('Budi Santoso, S.T.');
    }

    public function test_authorization_prevents_other_jurusan_from_editing_or_printing(): void
    {
        $procurement = Procurement::create([
            'nomor_usulan' => 'UP-202609-0101',
            'user_id' => $this->jurusanUserA->id,
            'jurusan_id' => $this->jurusanA->id,
            'judul_pengadaan' => 'Milik TKR',
            'nama_barang' => 'Barang TKR',
            'jumlah' => 1,
            'satuan' => 'unit',
            'perkiraan_biaya' => 100000,
            'alasan' => 'Rahasia TKR',
            'status' => 'menunggu',
        ]);

        // Jurusan B attempts to edit TKR's procurement -> 403 Forbidden
        $responseEdit = $this->actingAs($this->jurusanUserB)->get(route('procurements.edit', $procurement));
        $responseEdit->assertForbidden();

        // Jurusan B attempts to print TKR's procurement -> 403 Forbidden
        $responsePrint = $this->actingAs($this->jurusanUserB)->get(route('procurements.print', $procurement));
        $responsePrint->assertForbidden();

        // Sarpras can edit and print any procurement
        $sarprasEdit = $this->actingAs($this->sarprasUser)->get(route('procurements.edit', $procurement));
        $sarprasEdit->assertOk();

        $sarprasPrint = $this->actingAs($this->sarprasUser)->get(route('procurements.print', $procurement));
        $sarprasPrint->assertOk();
    }

    public function test_jurusan_can_delete_own_procurement(): void
    {
        $procurement = Procurement::create([
            'nomor_usulan' => 'UP-202609-0102',
            'user_id' => $this->jurusanUserA->id,
            'jurusan_id' => $this->jurusanA->id,
            'judul_pengadaan' => 'Usulan Akan Dihapus',
            'nama_barang' => 'Kabel Roll 50m',
            'jumlah' => 2,
            'satuan' => 'roll',
            'perkiraan_biaya' => 600000,
            'alasan' => 'Dibatalkan karena stok masih ada.',
            'status' => 'menunggu',
        ]);
        $procurement->items()->create([
            'nama_barang' => 'Kabel Roll 50m',
            'jumlah' => 2,
            'satuan' => 'roll',
            'harga_satuan' => 300000,
            'perkiraan_biaya' => 600000,
        ]);

        $response = $this->actingAs($this->jurusanUserA)->delete(route('procurements.destroy', $procurement));

        $response->assertRedirect(route('procurements.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('procurements', ['id' => $procurement->id]);
        $this->assertDatabaseMissing('procurement_items', ['procurement_id' => $procurement->id]);
    }

    public function test_jurusan_cannot_delete_other_jurusan_procurement(): void
    {
        $procurement = Procurement::create([
            'nomor_usulan' => 'UP-202609-0103',
            'user_id' => $this->jurusanUserA->id,
            'jurusan_id' => $this->jurusanA->id,
            'judul_pengadaan' => 'Usulan TKR Terlindungi',
            'nama_barang' => 'Mesin Kompresor',
            'jumlah' => 1,
            'satuan' => 'unit',
            'perkiraan_biaya' => 4500000,
            'alasan' => 'Penting untuk bengkel.',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->jurusanUserB)->delete(route('procurements.destroy', $procurement));
        $response->assertForbidden();

        $this->assertDatabaseHas('procurements', ['id' => $procurement->id]);
    }

    public function test_sarpras_can_delete_any_procurement(): void
    {
        $procurement = Procurement::create([
            'nomor_usulan' => 'UP-202609-0104',
            'user_id' => $this->jurusanUserA->id,
            'jurusan_id' => $this->jurusanA->id,
            'judul_pengadaan' => 'Usulan Dihapus Sarpras',
            'nama_barang' => 'Bor Duduk',
            'jumlah' => 1,
            'satuan' => 'unit',
            'perkiraan_biaya' => 2500000,
            'alasan' => 'Salah input.',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->sarprasUser)->delete(route('procurements.destroy', $procurement));

        $response->assertRedirect(route('procurements.index'));
        $this->assertDatabaseMissing('procurements', ['id' => $procurement->id]);
    }

    public function test_jurusan_cannot_delete_approved_procurement(): void
    {
        $procurement = Procurement::create([
            'nomor_usulan' => 'UP-202609-0105',
            'user_id' => $this->jurusanUserA->id,
            'jurusan_id' => $this->jurusanA->id,
            'judul_pengadaan' => 'Usulan Sudah Disetujui',
            'nama_barang' => 'Kamera DSLR Lab',
            'jumlah' => 1,
            'satuan' => 'unit',
            'perkiraan_biaya' => 8000000,
            'alasan' => 'Untuk dokumentasi lab.',
            'status' => 'disetujui',
        ]);

        // Akun selain Sarpras (Jurusan pemilik) mencoba menghapus usulan yang sudah disetujui -> 403 Forbidden
        $response = $this->actingAs($this->jurusanUserA)->delete(route('procurements.destroy', $procurement));
        $response->assertForbidden();

        $this->assertDatabaseHas('procurements', ['id' => $procurement->id]);

        // Tombol hapus tidak boleh muncul di UI untuk akun jurusan ketika status usulan disetujui
        $indexResponse = $this->actingAs($this->jurusanUserA)->get(route('procurements.index'));
        $indexResponse->assertOk();
        $indexResponse->assertDontSee('action="'.route('procurements.destroy', $procurement).'"', false);
    }

    public function test_sarpras_can_delete_approved_procurement(): void
    {
        $procurement = Procurement::create([
            'nomor_usulan' => 'UP-202609-0106',
            'user_id' => $this->jurusanUserA->id,
            'jurusan_id' => $this->jurusanA->id,
            'judul_pengadaan' => 'Usulan Disetujui Dihapus Sarpras',
            'nama_barang' => 'Server Rack 24U',
            'jumlah' => 1,
            'satuan' => 'unit',
            'perkiraan_biaya' => 12000000,
            'alasan' => 'Dibatalkan oleh Sarpras.',
            'status' => 'disetujui',
        ]);

        // Sarpras tetap berwenang menghapus usulan yang disetujui
        $response = $this->actingAs($this->sarprasUser)->delete(route('procurements.destroy', $procurement));
        $response->assertRedirect(route('procurements.index'));

        $this->assertDatabaseMissing('procurements', ['id' => $procurement->id]);
    }
}
