<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemUsage;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SarprasInventoryTest extends TestCase
{
    use RefreshDatabase;

    protected Jurusan $sarJurusan;

    protected Jurusan $tpJurusan;

    protected Category $komputerCat;

    protected Category $bahanCat;

    protected User $sarprasUser;

    protected User $tpUser;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup Jurusan Kejuruan dan Unit Sarpras Pusat
        $this->sarJurusan = Jurusan::firstOrCreate(
            ['kode' => 'SAR'],
            [
                'nama' => 'Sarpras Pusat & Fasilitas Umum',
                'kepala_bengkel' => 'Waka Bidang Sarana & Prasarana',
                'deskripsi' => 'Gudang penyimpanan sarpras, Lab Komputer CBT/ANBK umum, TU, aula, dan sarana umum sekolah.',
            ]
        );

        $this->tpJurusan = Jurusan::firstOrCreate(
            ['kode' => 'TP'],
            [
                'nama' => 'Teknik Pemesinan',
                'kepala_bengkel' => 'Bpk. Agus Setiawan, S.T',
            ]
        );

        // 2. Setup Kategori
        $this->komputerCat = Category::create([
            'kode' => 'KOM',
            'nama' => 'Komputer & Perangkat IT',
        ]);

        $this->bahanCat = Category::create([
            'kode' => 'BHN',
            'nama' => 'Bahan Praktik Habis Pakai',
        ]);

        // 3. Setup Users
        $this->sarprasUser = User::create([
            'name' => 'Waka Sarpras',
            'email' => 'sarpras@doksut.sch.id',
            'password' => Hash::make('password'),
            'role' => 'sarpras',
            'jurusan_id' => null,
        ]);

        $this->tpUser = User::create([
            'name' => 'Kaprogli TP',
            'email' => 'tp@doksut.sch.id',
            'password' => Hash::make('password'),
            'role' => 'jurusan',
            'jurusan_id' => $this->tpJurusan->id,
        ]);
    }

    /**
     * Sarpras dapat menginput barang inventaris untuk Lab Komputer CBT Umum (SAR-KOM-001).
     */
    public function test_sarpras_can_create_common_cbt_lab_computer_item(): void
    {
        $response = $this->actingAs($this->sarprasUser)->post(route('items.store'), [
            'nama_barang' => 'PC Client Workstation Lab CBT / ANBK Umum',
            'jurusan_id' => $this->sarJurusan->id,
            'category_id' => $this->komputerCat->id,
            'kode_barang' => 'SAR-KOM-001',
            'jumlah' => 5,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jenis' => 'alat',
            'lokasi' => 'Lab Komputer CBT 1 (Umum)',
            'is_computer' => 1,
            'processor' => 'Intel Core i5-11400',
            'ram' => '16 GB DDR4',
            'storage' => 'SSD 512 GB NVMe',
            'gpu_vga' => 'Intel UHD 730',
            'monitor' => 'Acer 21.5 Inch FHD',
            'sistem_operasi' => 'Windows 11 Pro Edu',
            'auto_generate_units' => 1,
        ]);

        $createdItem = Item::where('kode_barang', 'SAR-KOM-001')->first();
        $response->assertRedirect(route('items.show', $createdItem));
        $this->assertDatabaseHas('items', [
            'kode_barang' => 'SAR-KOM-001',
            'jurusan_id' => $this->sarJurusan->id,
            'nama_barang' => 'PC Client Workstation Lab CBT / ANBK Umum',
            'is_computer' => 1,
            'jumlah' => 5,
        ]);

        // Verifikasi 5 unit fisik ter-generate otomatis dengan kode SAR-KOM-001-01 s/d 05
        $this->assertDatabaseHas('item_units', [
            'unit_code' => 'SAR-KOM-001-01',
            'jurusan_id' => $this->sarJurusan->id,
            'nomor_meja' => 'Meja PC-01',
        ]);
        $this->assertDatabaseHas('item_units', [
            'unit_code' => 'SAR-KOM-001-05',
            'jurusan_id' => $this->sarJurusan->id,
            'nomor_meja' => 'Meja PC-05',
        ]);
    }

    /**
     * Endpoint generateCode otomatis menghasilkan prefix SAR-[KATEGORI]-[NO] untuk unit Sarpras.
     */
    public function test_api_generate_code_returns_sar_prefix_for_sarpras_unit(): void
    {
        $response = $this->actingAs($this->sarprasUser)->getJson(route('items.generate-code', [
            'jurusan_id' => $this->sarJurusan->id,
            'category_id' => $this->komputerCat->id,
        ]));

        $response->assertStatus(200);
        $response->assertJson(['code' => 'SAR-KOM-001']);
    }

    /**
     * Sarpras dapat mencatat pemakaian material gedung dari Gudang Sarpras (stok berkurang).
     */
    public function test_sarpras_can_record_warehouse_material_usage_and_deduct_stock(): void
    {
        $lampu = Item::create([
            'kode_barang' => 'SAR-BHN-001',
            'nama_barang' => 'Lampu LED Bulb Philips 14W Putih',
            'jurusan_id' => $this->sarJurusan->id,
            'category_id' => $this->bahanCat->id,
            'jumlah' => 20,
            'satuan' => 'pcs',
            'kondisi' => 'baik',
            'jenis' => 'bahan',
            'lokasi' => 'Gudang Sarpras Rak Elektrik B1',
            'min_stok' => 5,
        ]);

        $response = $this->actingAs($this->sarprasUser)->post(route('usages.store'), [
            'item_id' => $lampu->id,
            'jumlah' => 4,
            'tanggal_pemakaian' => now()->format('Y-m-d'),
            'nama_guru' => 'Pak Joko (Teknisi Sarpras)',
            'kelas' => 'Gedung Teori Lantai 1',
            'keperluan_jobsheet' => 'Pergantian lampu mati di Ruang Kelas X TP 1 dan Selasar',
            'catatan' => 'Pemasangan langsung oleh teknisi sarpras.',
        ]);

        $response->assertRedirect(route('usages.index'));

        // Stok awal 20 dikurangi 4 menjadi 16
        $this->assertEquals(16, $lampu->fresh()->jumlah);

        // Verifikasi mutasi tercatat pada ItemUsage
        $this->assertDatabaseHas('item_usages', [
            'item_id' => $lampu->id,
            'jurusan_id' => $this->sarJurusan->id,
            'jumlah' => 4,
            'stok_sebelum' => 20,
            'stok_sesudah' => 16,
            'keperluan_jobsheet' => 'Pergantian lampu mati di Ruang Kelas X TP 1 dan Selasar',
        ]);
    }

    /**
     * Daftar barang inventaris dapat difilter khusus barang milik Sarpras Pusat (SAR).
     */
    public function test_items_index_can_be_filtered_by_sarpras_unit(): void
    {
        Item::create([
            'kode_barang' => 'SAR-MSN-001',
            'nama_barang' => 'Genset Silent Perkins 15 kVA',
            'jurusan_id' => $this->sarJurusan->id,
            'category_id' => $this->komputerCat->id,
            'jumlah' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jenis' => 'alat',
            'lokasi' => 'Rumah Genset Belakang Lapangan',
        ]);

        Item::create([
            'kode_barang' => 'TP-MSN-001',
            'nama_barang' => 'Mesin Bubut Konvensional Krisbow',
            'jurusan_id' => $this->tpJurusan->id,
            'category_id' => $this->komputerCat->id,
            'jumlah' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jenis' => 'alat',
            'lokasi' => 'Bengkel Pemesinan',
        ]);

        $response = $this->actingAs($this->sarprasUser)->get(route('items.index', [
            'jurusan_id' => $this->sarJurusan->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Genset Silent Perkins 15 kVA');
        $response->assertSee('SAR-MSN-001');
        $response->assertDontSee('Mesin Bubut Konvensional Krisbow');
    }

    /**
     * Guru/publik dapat mengajukan kendala dengan memilih unit SAR (Fasilitas Umum).
     */
    public function test_public_complaint_can_target_sarpras_common_facilities(): void
    {
        $response = $this->post(route('public.complaint.store'), [
            'nama_pelapor' => 'Bpk. Agus Santoso, S.Kom',
            'kontak' => '081234567800',
            'jurusan_id' => $this->sarJurusan->id,
            'lokasi_ruang' => 'Lab Komputer CBT 1 Meja PC-03',
            'kategori' => 'komputer_it',
            'judul_kendala' => 'PC Meja 03 Blue Screen saat Ujian Simulasi',
            'deskripsi' => 'Komputer mendadak restart dan muncul kode BSOD memory management.',
            'tingkat_urgensi' => 'tinggi_darurat',
        ]);

        $response->assertSessionHas('complaint_success');
        $this->assertDatabaseHas('complaints', [
            'nama_pelapor' => 'Bpk. Agus Santoso, S.Kom',
            'jurusan_id' => $this->sarJurusan->id,
            'lokasi_ruang' => 'Lab Komputer CBT 1 Meja PC-03',
            'judul_kendala' => 'PC Meja 03 Blue Screen saat Ujian Simulasi',
        ]);
    }

    /**
     * Sarpras dapat mengakses halaman khusus Inventaris Fasilitas Umum.
     */
    public function test_sarpras_can_view_inventaris_umum_page(): void
    {
        // Barang umum terpasang di lab CBT
        Item::create([
            'kode_barang' => 'SAR-KOM-001',
            'nama_barang' => 'PC Client Workstation Lab CBT',
            'jurusan_id' => $this->sarJurusan->id,
            'category_id' => $this->komputerCat->id,
            'jumlah' => 10,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jenis' => 'alat',
            'lokasi' => 'Lab Komputer CBT 1',
            'is_computer' => true,
        ]);

        // Barang tersimpan di gudang sarpras (tidak boleh masuk tabel fasilitas umum terpasang)
        Item::create([
            'kode_barang' => 'SAR-BHN-001',
            'nama_barang' => 'Lampu LED Cadangan Gudang',
            'jurusan_id' => $this->sarJurusan->id,
            'category_id' => $this->bahanCat->id,
            'jumlah' => 15,
            'satuan' => 'pcs',
            'kondisi' => 'baik',
            'jenis' => 'bahan',
            'lokasi' => 'Gudang Sarpras Lantai 1',
        ]);

        $response = $this->actingAs($this->sarprasUser)->get(route('sarpras.umum'));

        $response->assertStatus(200);
        $response->assertSee('Inventaris Fasilitas Umum Sekolah');
        $response->assertSee('PC Client Workstation Lab CBT');
        $response->assertSee('Lab Komputer CBT 1');
        $response->assertDontSee('Lampu LED Cadangan Gudang');
    }

    /**
     * Sarpras dapat mengakses halaman khusus Stok di Gudang Sarpras.
     */
    public function test_sarpras_can_view_stok_di_gudang_page(): void
    {
        Item::create([
            'kode_barang' => 'SAR-BHN-001',
            'nama_barang' => 'Lampu LED Bulb Philips 14W',
            'jurusan_id' => $this->sarJurusan->id,
            'category_id' => $this->bahanCat->id,
            'jumlah' => 20,
            'satuan' => 'pcs',
            'kondisi' => 'baik',
            'jenis' => 'bahan',
            'lokasi' => 'Gudang Sarpras Rak B1',
            'min_stok' => 5,
        ]);

        Item::create([
            'kode_barang' => 'SAR-TLS-001',
            'nama_barang' => 'Mesin Bor Tangan Bosch',
            'jurusan_id' => $this->sarJurusan->id,
            'category_id' => $this->komputerCat->id,
            'jumlah' => 2,
            'satuan' => 'set',
            'kondisi' => 'baik',
            'jenis' => 'alat',
            'lokasi' => 'Gudang Sarpras Lemari Perkakas',
        ]);

        $response = $this->actingAs($this->sarprasUser)->get(route('sarpras.gudang'));

        $response->assertStatus(200);
        $response->assertSee('Stok & Logistik di Gudang Sarpras', false);
        $response->assertSee('Lampu LED Bulb Philips 14W');
        $response->assertSee('Gudang Sarpras Rak B1');
        $response->assertSee('Mesin Bor Tangan Bosch');
    }

    /**
     * Sarpras dapat menambahkan barang dengan penempatan Stok di Gudang dan otomatis masuk ke halaman gudang.
     */
    public function test_sarpras_can_create_item_with_gudang_placement_and_appears_in_gudang_page(): void
    {
        $response = $this->actingAs($this->sarprasUser)->post(route('items.store'), [
            'nama_barang' => 'Kabel Listrik NYM 2x1.5 Roll',
            'jurusan_id' => $this->sarJurusan->id,
            'category_id' => $this->bahanCat->id,
            'kode_barang' => 'SAR-BHN-050',
            'jumlah' => 10,
            'satuan' => 'roll',
            'kondisi' => 'baik',
            'jenis' => 'bahan',
            'lokasi' => 'Rak B3',
            'min_stok' => 2,
            'penempatan_sarpras' => 'gudang',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('items', [
            'kode_barang' => 'SAR-BHN-050',
            'nama_barang' => 'Kabel Listrik NYM 2x1.5 Roll',
            'jurusan_id' => $this->sarJurusan->id,
            'lokasi' => 'Gudang Sarpras - Rak B3',
        ]);

        // Verifikasi barang muncul di halaman Gudang Sarpras
        $gudangResponse = $this->actingAs($this->sarprasUser)->get(route('sarpras.gudang'));
        $gudangResponse->assertStatus(200);
        $gudangResponse->assertSee('Kabel Listrik NYM 2x1.5 Roll');
        $gudangResponse->assertSee('Gudang Sarpras - Rak B3');

        // Verifikasi barang TIDAK muncul di halaman Fasilitas Umum
        $umumResponse = $this->actingAs($this->sarprasUser)->get(route('sarpras.umum'));
        $umumResponse->assertStatus(200);
        $umumResponse->assertDontSee('Kabel Listrik NYM 2x1.5 Roll');
    }

    /**
     * Sarpras dapat menambahkan aset fasilitas umum dan otomatis masuk ke halaman umum.
     */
    public function test_sarpras_can_create_item_with_umum_placement_and_appears_in_umum_page(): void
    {
        $response = $this->actingAs($this->sarprasUser)->post(route('items.store'), [
            'nama_barang' => 'Speaker Active Aula Utama',
            'jurusan_id' => $this->sarJurusan->id,
            'category_id' => $this->komputerCat->id,
            'kode_barang' => 'SAR-ELK-010',
            'jumlah' => 2,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jenis' => 'alat',
            'lokasi' => 'Aula Utama Sekolah',
            'penempatan_sarpras' => 'umum',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('items', [
            'kode_barang' => 'SAR-ELK-010',
            'nama_barang' => 'Speaker Active Aula Utama',
            'jurusan_id' => $this->sarJurusan->id,
            'lokasi' => 'Aula Utama Sekolah',
        ]);

        // Verifikasi barang muncul di halaman Fasilitas Umum
        $umumResponse = $this->actingAs($this->sarprasUser)->get(route('sarpras.umum'));
        $umumResponse->assertStatus(200);
        $umumResponse->assertSee('Speaker Active Aula Utama');
        $umumResponse->assertSee('Aula Utama Sekolah');

        // Verifikasi barang TIDAK muncul di halaman Gudang
        $gudangResponse = $this->actingAs($this->sarprasUser)->get(route('sarpras.gudang'));
        $gudangResponse->assertStatus(200);
        $gudangResponse->assertDontSee('Speaker Active Aula Utama');
    }

    /**
     * Formulir tambah barang menerima query param penempatan_sarpras=gudang dengan benar.
     */
    public function test_items_create_form_handles_penempatan_sarpras_query_param(): void
    {
        $response = $this->actingAs($this->sarprasUser)->get(route('items.create', [
            'jurusan_id' => $this->sarJurusan->id,
            'penempatan_sarpras' => 'gudang',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Klasifikasi & Peruntukan Barang Sarpras', false);
        $response->assertSee('Stok Logistik di Gudang');
        $response->assertSee('opt_penempatan_gudang');
        $response->assertSee('checked');
    }

    /**
     * Akun non-sarpras (jurusan) dilarang mengakses halaman Stok di Gudang dan Inventaris Umum,
     * serta tidak melihat menu tersebut pada sidebar.
     */
    public function test_non_sarpras_cannot_access_or_see_sarpras_pages(): void
    {
        // 1. Akun Jurusan diblokir dengan 403 saat mengakses route sarpras.umum dan sarpras.gudang
        $this->actingAs($this->tpUser)->get(route('sarpras.umum'))->assertForbidden();
        $this->actingAs($this->tpUser)->get(route('sarpras.gudang'))->assertForbidden();

        // 2. Akun Jurusan tidak melihat menu Sarpras & Sekolah di sidebar
        $jurusanDashboard = $this->actingAs($this->tpUser)->get(route('dashboard'));
        $jurusanDashboard->assertStatus(200);
        $jurusanDashboard->assertDontSee('Inventaris Umum');
        $jurusanDashboard->assertDontSee('Stok di Gudang');
        $jurusanDashboard->assertDontSee('Sarpras & Sekolah', false);

        // 3. Sebaliknya, akun Sarpras melihat menu tersebut di sidebar
        $sarprasDashboard = $this->actingAs($this->sarprasUser)->get(route('dashboard'));
        $sarprasDashboard->assertStatus(200);
        $sarprasDashboard->assertSee('Inventaris Umum');
        $sarprasDashboard->assertSee('Stok di Gudang');
        $sarprasDashboard->assertSee('Sarpras & Sekolah', false);
    }
}
