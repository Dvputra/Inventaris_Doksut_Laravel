<?php

namespace Tests\Feature;

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BorrowingEditAndDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected User $sarprasUser;

    protected User $jurusanUserA;

    protected User $jurusanUserB;

    protected Jurusan $jurusanA;

    protected Jurusan $jurusanB;

    protected Item $alatA;

    protected ItemUnit $unitA1;

    protected ItemUnit $unitA2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jurusanA = Jurusan::firstOrCreate(
            ['kode' => 'TKR'],
            [
                'nama' => 'Teknik Kendaraan Ringan',
                'kepala_bengkel' => 'Budi Santoso, S.T.',
                'deskripsi' => 'Bengkel TKR',
            ]
        );

        $this->jurusanB = Jurusan::firstOrCreate(
            ['kode' => 'TKJ'],
            [
                'nama' => 'Teknik Komputer & Jaringan',
                'kepala_bengkel' => 'Siti Aminah, M.Kom.',
                'deskripsi' => 'Laboratorium TKJ',
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

        $this->alatA = Item::create([
            'kode_barang' => 'ALT-TKR-001',
            'nama_barang' => 'Multitester Digital',
            'jenis' => 'alat',
            'satuan' => 'unit',
            'jumlah' => 5,
            'jurusan_id' => $this->jurusanA->id,
            'kondisi' => 'baik',
        ]);

        $this->unitA1 = ItemUnit::create([
            'item_id' => $this->alatA->id,
            'jurusan_id' => $this->jurusanA->id,
            'unit_code' => 'ALT-TKR-001-U01',
            'nomor_meja' => 'Meja 01',
            'kondisi' => 'baik',
            'status' => 'tersedia',
        ]);

        $this->unitA2 = ItemUnit::create([
            'item_id' => $this->alatA->id,
            'jurusan_id' => $this->jurusanA->id,
            'unit_code' => 'ALT-TKR-001-U02',
            'nomor_meja' => 'Meja 02',
            'kondisi' => 'baik',
            'status' => 'tersedia',
        ]);
    }

    public function test_jurusan_can_access_edit_borrowing_page(): void
    {
        $borrowing = Borrowing::create([
            'item_id' => $this->alatA->id,
            'item_unit_id' => $this->unitA1->id,
            'jurusan_id' => $this->jurusanA->id,
            'nama_peminjam' => 'Ahmad Dani',
            'kelas_atau_jabatan' => 'XII TKR 1',
            'kontak' => '08123456789',
            'jumlah' => 1,
            'tanggal_pinjam' => now()->toDateString(),
            'status' => 'dipinjam',
        ]);
        $this->unitA1->update(['status' => 'dipinjam']);

        $response = $this->actingAs($this->jurusanUserA)->get(route('borrowings.edit', $borrowing));
        $response->assertOk();
        $response->assertSee('Ahmad Dani');
        $response->assertSee('Multitester Digital');
    }

    public function test_jurusan_can_update_borrowing_and_switch_units(): void
    {
        $borrowing = Borrowing::create([
            'item_id' => $this->alatA->id,
            'item_unit_id' => $this->unitA1->id,
            'jurusan_id' => $this->jurusanA->id,
            'nama_peminjam' => 'Ahmad Dani',
            'kelas_atau_jabatan' => 'XII TKR 1',
            'kontak' => '08123456789',
            'jumlah' => 1,
            'tanggal_pinjam' => '2026-09-25',
            'status' => 'dipinjam',
        ]);
        $this->unitA1->update(['status' => 'dipinjam']);

        // Update: ganti peminjam dan ganti unit fisik dari unitA1 ke unitA2
        $response = $this->actingAs($this->jurusanUserA)->put(route('borrowings.update', $borrowing), [
            'item_id' => $this->alatA->id,
            'item_unit_id' => $this->unitA2->id,
            'nama_peminjam' => 'Ahmad Dani Pratama',
            'kelas_atau_jabatan' => 'XII TKR 2',
            'kontak' => '08987654321',
            'jumlah' => 1,
            'tanggal_pinjam' => '2026-09-25',
            'status' => 'dipinjam',
            'catatan' => 'Pindah meja ke Meja 02',
        ]);

        $response->assertRedirect(route('borrowings.index'));
        $response->assertSessionHas('success');

        $borrowing->refresh();
        $this->assertEquals('Ahmad Dani Pratama', $borrowing->nama_peminjam);
        $this->assertEquals($this->unitA2->id, $borrowing->item_unit_id);

        // Unit A1 harus kembali menjadi 'tersedia'
        $this->unitA1->refresh();
        $this->assertEquals('tersedia', $this->unitA1->status);

        // Unit A2 sekarang menjadi 'dipinjam'
        $this->unitA2->refresh();
        $this->assertEquals('dipinjam', $this->unitA2->status);
    }

    public function test_updating_borrowing_status_to_kembali_frees_unit(): void
    {
        $borrowing = Borrowing::create([
            'item_id' => $this->alatA->id,
            'item_unit_id' => $this->unitA1->id,
            'jurusan_id' => $this->jurusanA->id,
            'nama_peminjam' => 'Bambang',
            'jumlah' => 1,
            'tanggal_pinjam' => '2026-09-20',
            'status' => 'dipinjam',
        ]);
        $this->unitA1->update(['status' => 'dipinjam']);

        $response = $this->actingAs($this->jurusanUserA)->put(route('borrowings.update', $borrowing), [
            'item_id' => $this->alatA->id,
            'item_unit_id' => $this->unitA1->id,
            'nama_peminjam' => 'Bambang',
            'jumlah' => 1,
            'tanggal_pinjam' => '2026-09-20',
            'status' => 'kembali',
            'tanggal_kembali' => '2026-09-22',
        ]);

        $response->assertRedirect(route('borrowings.index'));

        $borrowing->refresh();
        $this->assertEquals('kembali', $borrowing->status);
        $this->assertEquals('2026-09-22', $borrowing->tanggal_kembali->format('Y-m-d'));

        // Unit fisik harus kembali 'tersedia'
        $this->unitA1->refresh();
        $this->assertEquals('tersedia', $this->unitA1->status);
    }

    public function test_jurusan_can_delete_borrowing_and_revert_unit(): void
    {
        $borrowing = Borrowing::create([
            'item_id' => $this->alatA->id,
            'item_unit_id' => $this->unitA1->id,
            'jurusan_id' => $this->jurusanA->id,
            'nama_peminjam' => 'Siswa Salah Catat',
            'jumlah' => 1,
            'tanggal_pinjam' => now()->toDateString(),
            'status' => 'dipinjam',
        ]);
        $this->unitA1->update(['status' => 'dipinjam']);

        $response = $this->actingAs($this->jurusanUserA)->delete(route('borrowings.destroy', $borrowing));

        $response->assertRedirect(route('borrowings.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('borrowings', ['id' => $borrowing->id]);

        // Unit fisik harus kembali 'tersedia'
        $this->unitA1->refresh();
        $this->assertEquals('tersedia', $this->unitA1->status);
    }

    public function test_authorization_protects_other_jurusan_from_editing_or_deleting(): void
    {
        $borrowing = Borrowing::create([
            'item_id' => $this->alatA->id,
            'item_unit_id' => $this->unitA1->id,
            'jurusan_id' => $this->jurusanA->id,
            'nama_peminjam' => 'Peminjam TKR',
            'jumlah' => 1,
            'tanggal_pinjam' => now()->toDateString(),
            'status' => 'dipinjam',
        ]);

        // Jurusan B mencoba edit -> 403 Forbidden
        $responseEdit = $this->actingAs($this->jurusanUserB)->get(route('borrowings.edit', $borrowing));
        $responseEdit->assertForbidden();

        // Jurusan B mencoba update -> 403 Forbidden
        $responseUpdate = $this->actingAs($this->jurusanUserB)->put(route('borrowings.update', $borrowing), [
            'item_id' => $this->alatA->id,
            'nama_peminjam' => 'Hacker TKJ',
            'jumlah' => 1,
            'tanggal_pinjam' => now()->toDateString(),
            'status' => 'dipinjam',
        ]);
        $responseUpdate->assertForbidden();

        // Jurusan B mencoba delete -> 403 Forbidden
        $responseDelete = $this->actingAs($this->jurusanUserB)->delete(route('borrowings.destroy', $borrowing));
        $responseDelete->assertForbidden();

        $this->assertDatabaseHas('borrowings', ['id' => $borrowing->id]);

        // Sarpras berwenang edit dan delete
        $sarprasEdit = $this->actingAs($this->sarprasUser)->get(route('borrowings.edit', $borrowing));
        $sarprasEdit->assertOk();

        $sarprasDelete = $this->actingAs($this->sarprasUser)->delete(route('borrowings.destroy', $borrowing));
        $sarprasDelete->assertRedirect(route('borrowings.index'));
        $this->assertDatabaseMissing('borrowings', ['id' => $borrowing->id]);
    }

    public function test_borrowing_create_preselects_item_from_query_parameter(): void
    {
        $response = $this->actingAs($this->jurusanUserA)
            ->get(route('borrowings.create', ['item_id' => $this->alatA->id]));

        $response->assertOk();
        $response->assertViewHas('selectedItemId', (string) $this->alatA->id);
        $response->assertViewHas('selectedItem');
        $response->assertSee('Multitester Digital');
        $response->assertSee('Alat ini otomatis terintegrasi dari halaman barang');
        $response->assertSee(route('items.show', $this->alatA));
    }

    public function test_borrowing_create_preselects_specific_unit_from_query_parameter(): void
    {
        $response = $this->actingAs($this->jurusanUserA)
            ->get(route('borrowings.create', [
                'item_id' => $this->alatA->id,
                'unit_id' => $this->unitA1->id,
            ]));

        $response->assertOk();
        $response->assertViewHas('selectedItemId', (string) $this->alatA->id);
        $response->assertViewHas('selectedUnitId', (string) $this->unitA1->id);
        $response->assertSee('data-selected="'.$this->unitA1->id.'"', false);
    }

    public function test_items_show_page_contains_integrated_borrowing_links(): void
    {
        $response = $this->actingAs($this->jurusanUserA)
            ->get(route('items.show', $this->alatA));

        $response->assertOk();
        // Link di header aksi barang
        $response->assertSee(route('borrowings.create', ['item_id' => $this->alatA->id]));
        // Link di aksi tabel unit fisik spesifik
        $response->assertSee(route('borrowings.create', ['item_id' => $this->alatA->id, 'unit_id' => $this->unitA1->id]));
    }

    public function test_borrowing_store_validates_unit_belongs_to_selected_item(): void
    {
        // Buat alat dan unit lain di jurusan B
        $alatB = Item::create([
            'kode_barang' => 'ALT-TKJ-999',
            'nama_barang' => 'Crimping Tool',
            'jenis' => 'alat',
            'satuan' => 'unit',
            'jumlah' => 2,
            'jurusan_id' => $this->jurusanB->id,
            'kondisi' => 'baik',
        ]);
        $unitB = ItemUnit::create([
            'item_id' => $alatB->id,
            'jurusan_id' => $this->jurusanB->id,
            'unit_code' => 'ALT-TKJ-999-U01',
            'kondisi' => 'baik',
            'status' => 'tersedia',
        ]);

        // Coba pinjam alatA tapi menyertakan unitB milik alatB
        $response = $this->actingAs($this->sarprasUser)->post(route('borrowings.store'), [
            'item_id' => $this->alatA->id,
            'item_unit_id' => $unitB->id,
            'nama_peminjam' => 'Siswa Tes',
            'jumlah' => 1,
            'tanggal_pinjam' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('item_unit_id');
    }
}
