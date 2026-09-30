<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\Jurusan;
use App\Models\MaintenanceLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MaintenanceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $sarpras;

    protected User $jurusanUser;

    protected User $otherJurusanUser;

    protected Jurusan $jurusan;

    protected Jurusan $otherJurusan;

    protected Category $category;

    protected Item $item;

    protected ItemUnit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jurusan = Jurusan::create([
            'kode' => 'TKJ',
            'nama' => 'Teknik Komputer dan Jaringan',
        ]);

        $this->otherJurusan = Jurusan::create([
            'kode' => 'TKR',
            'nama' => 'Teknik Kendaraan Ringan',
        ]);

        $this->sarpras = User::create([
            'name' => 'Sarpras Pusat',
            'email' => 'sarpras@sekolah.sch.id',
            'password' => Hash::make('password'),
            'role' => 'sarpras',
        ]);

        $this->jurusanUser = User::create([
            'name' => 'Akun TKJ',
            'email' => 'tkj@sekolah.sch.id',
            'password' => Hash::make('password'),
            'role' => 'jurusan',
            'jurusan_id' => $this->jurusan->id,
        ]);

        $this->otherJurusanUser = User::create([
            'name' => 'Akun TKR',
            'email' => 'tkr@sekolah.sch.id',
            'password' => Hash::make('password'),
            'role' => 'jurusan',
            'jurusan_id' => $this->otherJurusan->id,
        ]);

        $this->category = Category::create([
            'kode' => 'KOM',
            'nama' => 'Komputer & Jaringan',
        ]);

        $this->item = Item::create([
            'jurusan_id' => $this->jurusan->id,
            'category_id' => $this->category->id,
            'kode_barang' => 'TKJ-KOM-001',
            'nama_barang' => 'PC Lab Komputer 01',
            'jenis' => 'alat',
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jumlah' => 1,
            'lokasi' => 'Lab Komputer 1',
        ]);

        $this->unit = ItemUnit::create([
            'item_id' => $this->item->id,
            'jurusan_id' => $this->jurusan->id,
            'unit_code' => 'TKJ-KOM-001-01',
            'nomor_meja' => 'Meja-01',
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'tanggal_masuk' => now()->toDateString(),
        ]);
    }

    public function test_recording_maintenance_in_process_updates_unit_status(): void
    {
        $response = $this->actingAs($this->jurusanUser)->post(route('units.maintenance.store', $this->unit), [
            'tanggal' => now()->toDateString(),
            'gejala_kerusakan' => 'Layar bergaris dan kipas berbunyi keras',
            'tindakan_perbaikan' => 'Pemeriksaan PSU dan konektor kabel LCD',
            'biaya' => 50000,
            'teknisi_pelaksana' => 'Ahmad Teknisi',
            'status' => 'proses',
        ]);

        $response->assertRedirect(route('items.show', $this->item->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('maintenance_logs', [
            'item_unit_id' => $this->unit->id,
            'status' => 'proses',
            'gejala_kerusakan' => 'Layar bergaris dan kipas berbunyi keras',
        ]);

        $this->unit->refresh();
        $this->assertEquals('dalam_perbaikan', $this->unit->status);
        $this->assertEquals('rusak_ringan', $this->unit->kondisi);
    }

    public function test_completing_maintenance_unit_restores_status_to_tersedia(): void
    {
        // First put in repair
        $log = MaintenanceLog::create([
            'item_unit_id' => $this->unit->id,
            'jurusan_id' => $this->jurusan->id,
            'user_id' => $this->jurusanUser->id,
            'tanggal' => now()->toDateString(),
            'gejala_kerusakan' => 'Kipas mati',
            'tindakan_perbaikan' => 'Sedang dicek',
            'status' => 'proses',
        ]);
        $this->unit->update(['status' => 'dalam_perbaikan', 'kondisi' => 'rusak_ringan']);

        // Selesaikan servis
        $response = $this->actingAs($this->jurusanUser)->patch(route('units.maintenance.complete', $this->unit), [
            'tanggal' => now()->toDateString(),
            'tindakan_perbaikan' => 'Penggantian kipas DC 12V dan pembersihan debu',
            'biaya' => 75000,
            'teknisi_pelaksana' => 'Budi Toolman',
            'kondisi' => 'baik',
        ]);

        $response->assertRedirect(route('items.show', $this->item->id));
        $response->assertSessionHas('success');

        $this->unit->refresh();
        $this->assertEquals('tersedia', $this->unit->status);
        $this->assertEquals('baik', $this->unit->kondisi);

        $log->refresh();
        $this->assertEquals('selesai', $log->status);
        $this->assertEquals('Penggantian kipas DC 12V dan pembersihan debu', $log->tindakan_perbaikan);
        $this->assertEquals(75000, $log->biaya);
        $this->assertEquals('Budi Toolman', $log->teknisi_pelaksana);
    }

    public function test_completing_maintenance_unit_as_unfixable_marks_afkir(): void
    {
        $log = MaintenanceLog::create([
            'item_unit_id' => $this->unit->id,
            'jurusan_id' => $this->jurusan->id,
            'user_id' => $this->jurusanUser->id,
            'tanggal' => now()->toDateString(),
            'gejala_kerusakan' => 'Motherboard terbakar',
            'tindakan_perbaikan' => 'Sedang dicek',
            'status' => 'proses',
        ]);
        $this->unit->update(['status' => 'dalam_perbaikan', 'kondisi' => 'rusak_ringan']);

        $response = $this->actingAs($this->jurusanUser)->patch(route('units.maintenance.complete', $this->unit), [
            'tanggal' => now()->toDateString(),
            'tindakan_perbaikan' => 'Jalur chipset korsleting parah, tidak ekonomis untuk diperbaiki',
            'kondisi' => 'rusak_berat',
        ]);

        $response->assertRedirect(route('items.show', $this->item->id));

        $this->unit->refresh();
        $this->assertEquals('afkir', $this->unit->status);
        $this->assertEquals('rusak_berat', $this->unit->kondisi);

        $log->refresh();
        $this->assertEquals('tidak_dapat_diperbaiki', $log->status);
    }

    public function test_cancelling_maintenance_unit_deletes_process_log_and_reverts_unit(): void
    {
        $log = MaintenanceLog::create([
            'item_unit_id' => $this->unit->id,
            'jurusan_id' => $this->jurusan->id,
            'user_id' => $this->jurusanUser->id,
            'tanggal' => now()->toDateString(),
            'gejala_kerusakan' => 'Salah klik servis',
            'tindakan_perbaikan' => 'Belum ada',
            'status' => 'proses',
        ]);
        $this->unit->update(['status' => 'dalam_perbaikan', 'kondisi' => 'rusak_ringan']);

        $response = $this->actingAs($this->jurusanUser)->patch(route('units.maintenance.cancel', $this->unit));

        $response->assertRedirect(route('items.show', $this->item->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('maintenance_logs', ['id' => $log->id]);

        $this->unit->refresh();
        $this->assertEquals('tersedia', $this->unit->status);
        $this->assertEquals('baik', $this->unit->kondisi);
    }

    public function test_destroy_maintenance_log_reverts_unit_status_if_in_process(): void
    {
        $log = MaintenanceLog::create([
            'item_unit_id' => $this->unit->id,
            'jurusan_id' => $this->jurusan->id,
            'user_id' => $this->jurusanUser->id,
            'tanggal' => now()->toDateString(),
            'gejala_kerusakan' => 'Uji coba servis',
            'tindakan_perbaikan' => 'Pengecekan',
            'status' => 'proses',
        ]);
        $this->unit->update(['status' => 'dalam_perbaikan', 'kondisi' => 'rusak_ringan']);

        $response = $this->actingAs($this->jurusanUser)->delete(route('maintenance.destroy', $log));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('maintenance_logs', ['id' => $log->id]);

        $this->unit->refresh();
        $this->assertEquals('tersedia', $this->unit->status);
        $this->assertEquals('baik', $this->unit->kondisi);
    }

    public function test_jurusan_cannot_modify_maintenance_of_other_jurusan(): void
    {
        $this->unit->update(['status' => 'dalam_perbaikan']);

        $response = $this->actingAs($this->otherJurusanUser)->patch(route('units.maintenance.cancel', $this->unit));
        $response->assertStatus(403);

        $responseComplete = $this->actingAs($this->otherJurusanUser)->patch(route('units.maintenance.complete', $this->unit), [
            'tindakan_perbaikan' => 'Hacking test',
            'kondisi' => 'baik',
        ]);
        $responseComplete->assertStatus(403);
    }

    public function test_item_show_displays_service_action_buttons(): void
    {
        $this->unit->update(['status' => 'dalam_perbaikan']);
        MaintenanceLog::create([
            'item_unit_id' => $this->unit->id,
            'jurusan_id' => $this->jurusan->id,
            'user_id' => $this->jurusanUser->id,
            'tanggal' => now()->toDateString(),
            'gejala_kerusakan' => 'RAM kotor',
            'tindakan_perbaikan' => 'Sedang dibersihkan',
            'status' => 'proses',
        ]);

        $response = $this->actingAs($this->jurusanUser)->get(route('items.show', $this->item));

        $response->assertStatus(200);
        $response->assertSee('Dalam Perbaikan');
        $response->assertSee('completeLogModal-'.$this->unit->id);
        $response->assertSee('cancelMaintenanceModal');
        $response->assertSee('Selesaikan Servis');
        $response->assertSee('Batalkan Status Servis');
    }
}
