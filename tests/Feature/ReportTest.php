<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\ItemUsage;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected Jurusan $jurusanTP;

    protected Jurusan $jurusanDKV;

    protected Category $category;

    protected User $sarprasUser;

    protected User $jurusanUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jurusanTP = Jurusan::create([
            'kode' => 'TP',
            'nama' => 'Teknik Pemesinan',
        ]);

        $this->jurusanDKV = Jurusan::create([
            'kode' => 'DKV',
            'nama' => 'Desain Komunikasi Visual',
        ]);

        $this->category = Category::create([
            'nama' => 'Komputer & Jaringan',
            'kode' => 'KOM',
            'deskripsi' => 'Alat dan perangkat workstation',
        ]);

        $this->sarprasUser = User::create([
            'name' => 'Waka Sarpras',
            'email' => 'sarpras@doksut.sch.id',
            'password' => Hash::make('password'),
            'role' => 'sarpras',
        ]);

        $this->jurusanUser = User::create([
            'name' => 'Kaprogli TP',
            'email' => 'tp@doksut.sch.id',
            'password' => Hash::make('password'),
            'role' => 'jurusan',
            'jurusan_id' => $this->jurusanTP->id,
        ]);
    }

    public function test_guest_cannot_access_reports(): void
    {
        $this->get(route('reports.index'))->assertRedirect(route('login'));
        $this->get(route('reports.items.print'))->assertRedirect(route('login'));
        $this->get(route('reports.usages.print'))->assertRedirect(route('login'));
        $this->get(route('reports.complaints.print'))->assertRedirect(route('login'));
    }

    public function test_sarpras_can_view_reports_index_page(): void
    {
        $response = $this->actingAs($this->sarprasUser)->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertSeeText('Pusat Laporan & Cetak Rekapitulasi');
        $response->assertSeeText('Laporan Inventaris Aset & Barang');
        $response->assertSeeText('Laporan Pemakaian Bahan');
        $response->assertSeeText('Laporan Pengaduan & Servis');
        // Memastikan banner petunjuk filter awal muncul sebelum filter diterapkan
        $response->assertSeeText('Tentukan Periode Waktu & Tanggal Laporan');
    }

    public function test_can_print_item_inventory_report(): void
    {
        $item = Item::create([
            'kode_barang' => 'DKV-KOM-001',
            'nama_barang' => 'PC Workstation DKV Rendering',
            'category_id' => $this->category->id,
            'jurusan_id' => $this->jurusanDKV->id,
            'jumlah' => 5,
            'min_stok' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jenis' => 'alat',
            'is_computer' => true,
            'processor' => 'Intel Core i7-13700',
            'ram' => '32GB DDR5',
            'storage' => '1TB NVMe Gen4',
            'gpu_vga' => 'NVIDIA RTX 4060 8GB',
        ]);

        $response = $this->actingAs($this->sarprasUser)->get(route('reports.items.print', [
            'jurusan_id' => $this->jurusanDKV->id,
            'is_computer' => '1',
        ]));

        $response->assertStatus(200);
        $response->assertSeeText('SMK DR. SUTOMO TEMANGGUNG');
        $response->assertSeeText('LAPORAN REKAPITULASI INVENTARIS ASET & SARANA PRASARANA');
        $response->assertSee('PC Workstation DKV Rendering');
        $response->assertSee('Intel Core i7-13700');
        $response->assertSee('DKV-KOM-001');
    }

    public function test_can_print_item_usages_report(): void
    {
        $item = Item::create([
            'kode_barang' => 'TP-MSN-002',
            'nama_barang' => 'Besi Pejal ST41 Dia 25mm',
            'category_id' => $this->category->id,
            'jurusan_id' => $this->jurusanTP->id,
            'jumlah' => 40,
            'satuan' => 'batang',
            'kondisi' => 'baik',
            'jenis' => 'bahan',
        ]);

        ItemUsage::create([
            'item_id' => $item->id,
            'jurusan_id' => $this->jurusanTP->id,
            'user_id' => $this->sarprasUser->id,
            'tanggal_pemakaian' => now()->format('Y-m-d'),
            'nama_guru' => 'Bpk. Bambang Pamungkas, S.T.',
            'kelas' => 'XII TP 1',
            'keperluan_jobsheet' => 'Bubut Poros Bertingkat & Ulir Metris',
            'jumlah' => 10,
            'satuan' => 'batang',
            'stok_sebelum' => 50,
            'stok_sesudah' => 40,
        ]);

        $response = $this->actingAs($this->sarprasUser)->get(route('reports.usages.print', [
            'jurusan_id' => $this->jurusanTP->id,
            'tahun' => now()->format('Y'),
        ]));

        $response->assertStatus(200);
        $response->assertSeeText('BERITA ACARA & REKAPITULASI PEMAKAIAN BAHAN / ATK');
        $response->assertSee('Besi Pejal ST41 Dia 25mm');
        $response->assertSee('Bpk. Bambang Pamungkas, S.T.');
        $response->assertSeeText('Bubut Poros Bertingkat & Ulir Metris');
        $response->assertSee('-10');
    }

    public function test_can_print_complaints_report(): void
    {
        Complaint::create([
            'ticket_code' => 'ADU-2609-TEST',
            'nama_pelapor' => 'Ibu Siti Aminah, S.Kom',
            'kontak' => '081234567890',
            'jurusan_id' => $this->jurusanDKV->id,
            'lokasi_ruang' => 'Lab Mac DKV',
            'kategori' => 'komputer_it',
            'judul_kendala' => 'Proyektor HDMI Lab DKV Mati Total',
            'deskripsi' => 'Lampu indikator berkedip merah dan tidak mau menyala.',
            'tingkat_urgensi' => 'tinggi_darurat',
            'status' => 'selesai',
            'teknisi_penanganan' => 'Pak Joko Sarpras',
            'tindak_lanjut' => 'Ganti lampu proyektor dan kabel power baru.',
            'tanggal_selesai' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->sarprasUser)->get(route('reports.complaints.print', [
            'status' => 'selesai',
        ]));

        $response->assertStatus(200);
        $response->assertSeeText('REKAPITULASI PENGADUAN, SERVIS & PEMELIHARAAN SARANA PRASARANA');
        $response->assertSee('ADU-2609-TEST');
        $response->assertSee('Proyektor HDMI Lab DKV Mati Total');
        $response->assertSee('Pak Joko Sarpras');
    }

    public function test_jurusan_user_is_scoped_to_their_jurusan(): void
    {
        $response = $this->actingAs($this->jurusanUser)->get(route('reports.items.print'));

        $response->assertStatus(200);
        $response->assertSee('Teknik Pemesinan (TP)');
    }

    public function test_can_export_items_to_excel(): void
    {
        Item::create([
            'kode_barang' => 'DKV-KOM-888',
            'nama_barang' => 'Laptop Multimedia DKV',
            'category_id' => $this->category->id,
            'jurusan_id' => $this->jurusanDKV->id,
            'jumlah' => 2,
            'min_stok' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jenis' => 'alat',
        ]);

        $response = $this->actingAs($this->sarprasUser)->get(route('reports.items.export-excel'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Laptop Multimedia DKV', $response->streamedContent());
        $this->assertStringContainsString('DKV-KOM-888', $response->streamedContent());
    }

    public function test_can_export_usages_to_excel(): void
    {
        $item = Item::create([
            'kode_barang' => 'TP-BHN-888',
            'nama_barang' => 'Elektroda Las RB-26',
            'category_id' => $this->category->id,
            'jurusan_id' => $this->jurusanTP->id,
            'jumlah' => 50,
            'min_stok' => 5,
            'satuan' => 'dus',
            'kondisi' => 'baik',
            'jenis' => 'bahan',
        ]);

        ItemUsage::create([
            'item_id' => $item->id,
            'jurusan_id' => $this->jurusanTP->id,
            'user_id' => $this->jurusanUser->id,
            'nama_guru' => 'Pak Sigit Las',
            'kelas' => 'XII TP 2',
            'keperluan_jobsheet' => 'Las Sambungan Tumpul',
            'jumlah' => 3,
            'stok_sebelum' => 50,
            'stok_sesudah' => 47,
            'tanggal_pemakaian' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->sarprasUser)->get(route('reports.usages.export-excel'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Elektroda Las RB-26', $response->streamedContent());
        $this->assertStringContainsString('Pak Sigit Las', $response->streamedContent());
    }

    public function test_can_export_detailed_item_units_with_condition_to_excel(): void
    {
        $item = Item::create([
            'kode_barang' => 'DKV-KOM-999',
            'nama_barang' => 'PC Lab Multimedia Editing',
            'category_id' => $this->category->id,
            'jurusan_id' => $this->jurusanDKV->id,
            'jumlah' => 2,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jenis' => 'alat',
            'is_computer' => true,
        ]);

        ItemUnit::create([
            'item_id' => $item->id,
            'jurusan_id' => $this->jurusanDKV->id,
            'unit_code' => 'DKV-KOM-999-01',
            'nomor_seri' => 'SN-PC-01-OK',
            'nomor_meja' => 'Meja 01',
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'lokasi_penempatan' => 'Lab DKV 1',
        ]);

        ItemUnit::create([
            'item_id' => $item->id,
            'jurusan_id' => $this->jurusanDKV->id,
            'unit_code' => 'DKV-KOM-999-02',
            'nomor_seri' => 'SN-PC-02-DMG',
            'nomor_meja' => 'Meja 02',
            'kondisi' => 'rusak_ringan',
            'status' => 'dalam_perbaikan',
            'lokasi_penempatan' => 'Lab DKV 1',
            'catatan' => 'Port USB depan longgar',
        ]);

        $response = $this->actingAs($this->sarprasUser)->get(route('reports.items.export-excel', ['mode' => 'detail']));

        $response->assertStatus(200);
        $content = $response->streamedContent();
        $this->assertStringContainsString('Kode Unit Fisik', $content);
        $this->assertStringContainsString('Kondisi Unit', $content);
        $this->assertStringContainsString('DKV-KOM-999-01', $content);
        $this->assertStringContainsString('SN-PC-01-OK', $content);
        $this->assertStringContainsString('DKV-KOM-999-02', $content);
        $this->assertStringContainsString('Rusak Ringan', $content);
        $this->assertStringContainsString('Port USB depan longgar', $content);
    }

    public function test_can_filter_items_report_by_period(): void
    {
        // Barang yang dibuat hari ini
        $itemToday = Item::create([
            'kode_barang' => 'DKV-KOM-TODAY',
            'nama_barang' => 'PC Hari Ini',
            'category_id' => $this->category->id,
            'jurusan_id' => $this->jurusanDKV->id,
            'jumlah' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jenis' => 'alat',
            'tahun_pengadaan' => now()->year,
            'created_at' => now(),
        ]);

        // Barang yang dibuat 2 bulan lalu (di luar hari ini dan minggu ini)
        $itemOld = Item::create([
            'kode_barang' => 'DKV-KOM-OLD',
            'nama_barang' => 'PC Masa Lalu',
            'category_id' => $this->category->id,
            'jurusan_id' => $this->jurusanDKV->id,
            'jumlah' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jenis' => 'alat',
            'tahun_pengadaan' => now()->year - 2,
        ]);
        $itemOld->timestamps = false;
        $itemOld->created_at = now()->subMonths(2);
        $itemOld->save();

        // 1. Filter index preview dengan periode hari_ini
        $responseToday = $this->actingAs($this->sarprasUser)->get(route('reports.index', [
            'tab' => 'items',
            'periode' => 'hari_ini',
        ]));
        $responseToday->assertStatus(200);
        $responseToday->assertSee('DKV-KOM-TODAY');
        $responseToday->assertDontSee('DKV-KOM-OLD');

        // 2. Filter cetak printItems dengan periode hari_ini
        $responsePrint = $this->actingAs($this->sarprasUser)->get(route('reports.items.print', [
            'periode' => 'hari_ini',
        ]));
        $responsePrint->assertStatus(200);
        $responsePrint->assertSee('DKV-KOM-TODAY');
        $responsePrint->assertDontSee('DKV-KOM-OLD');
        $responsePrint->assertSee('Harian (Hari Ini');

        // 3. Filter export excel dengan periode hari_ini
        $responseExcel = $this->actingAs($this->sarprasUser)->get(route('reports.items.export-excel', [
            'periode' => 'hari_ini',
        ]));
        $responseExcel->assertStatus(200);
        $content = $responseExcel->streamedContent();
        $this->assertStringContainsString('DKV-KOM-TODAY', $content);
        $this->assertStringNotContainsString('DKV-KOM-OLD', $content);
    }

    public function test_can_filter_items_report_by_custom_date_range(): void
    {
        $item1 = Item::create([
            'kode_barang' => 'RANGE-01',
            'nama_barang' => 'Item Tanggal 10',
            'category_id' => $this->category->id,
            'jurusan_id' => $this->jurusanDKV->id,
            'jumlah' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jenis' => 'alat',
        ]);
        $item1->timestamps = false;
        $item1->created_at = '2026-05-10 10:00:00';
        $item1->save();

        $item2 = Item::create([
            'kode_barang' => 'RANGE-02',
            'nama_barang' => 'Item Tanggal 25',
            'category_id' => $this->category->id,
            'jurusan_id' => $this->jurusanDKV->id,
            'jumlah' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jenis' => 'alat',
        ]);
        $item2->timestamps = false;
        $item2->created_at = '2026-05-25 10:00:00';
        $item2->save();

        // Filter rentang 2026-05-01 s/d 2026-05-15 (hanya RANGE-01 yang masuk)
        $response = $this->actingAs($this->sarprasUser)->get(route('reports.index', [
            'tab' => 'items',
            'tgl_mulai' => '2026-05-01',
            'tgl_selesai' => '2026-05-15',
        ]));
        $response->assertStatus(200);
        $response->assertSee('RANGE-01');
        $response->assertDontSee('RANGE-02');

        // Cetak dokumen juga memuat RANGE-01 dan label rentang tanggal
        $printRes = $this->actingAs($this->sarprasUser)->get(route('reports.items.print', [
            'tgl_mulai' => '2026-05-01',
            'tgl_selesai' => '2026-05-15',
        ]));
        $printRes->assertStatus(200);
        $printRes->assertSee('RANGE-01');
        $printRes->assertDontSee('RANGE-02');
        $printRes->assertSee('Rentang Tanggal: 01/05/2026 s/d 15/05/2026');
    }
}
