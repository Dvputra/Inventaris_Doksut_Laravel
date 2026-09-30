<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemRestock;
use App\Models\ItemUnit;
use App\Models\ItemUsage;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemAndUsageSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $sarpras;

    protected User $jurusanUser;

    protected Jurusan $jurusan;

    protected Category $catMesin;

    protected Category $catKomputer;

    protected Category $catBahan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jurusan = Jurusan::create([
            'kode' => 'TP',
            'nama' => 'Teknik Pemesinan',
        ]);

        $this->sarpras = User::create([
            'name' => 'Sarpras Pusat',
            'email' => 'sarpras@sekolah.sch.id',
            'password' => Hash::make('password'),
            'role' => 'sarpras',
        ]);

        $this->jurusanUser = User::create([
            'name' => 'Akun TP',
            'email' => 'tp@sekolah.sch.id',
            'password' => Hash::make('password'),
            'role' => 'jurusan',
            'jurusan_id' => $this->jurusan->id,
        ]);

        $this->catMesin = Category::create([
            'kode' => 'MSN',
            'nama' => 'Mesin & Peralatan Berat',
        ]);

        $this->catKomputer = Category::create([
            'kode' => 'KOM',
            'nama' => 'Komputer & Perangkat IT',
        ]);

        $this->catBahan = Category::create([
            'kode' => 'BHN',
            'nama' => 'Bahan Praktik Habis Pakai',
        ]);
    }

    /**
     * Uji generate otomatis kode barang berdasarkan jurusan dan kategori, serta pembuatan unit fisik.
     */
    public function test_auto_generation_of_item_code_and_physical_units(): void
    {
        $response = $this->actingAs($this->jurusanUser)->post('/items', [
            'nama_barang' => 'Mesin Bubut Presisi',
            'category_id' => $this->catMesin->id,
            'jenis' => 'alat',
            'jumlah' => 2,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'lokasi' => 'Bengkel Bubut 1',
            'auto_generate_units' => '1',
        ]);

        $response->assertRedirect();

        $item = Item::where('nama_barang', 'Mesin Bubut Presisi')->first();
        $this->assertNotNull($item);
        $this->assertEquals('TP-MSN-001', $item->kode_barang);

        // Verifikasi 2 unit fisik dibuat
        $this->assertCount(2, $item->units);
        $this->assertDatabaseHas('item_units', [
            'unit_code' => 'TP-MSN-001-01',
            'item_id' => $item->id,
        ]);
        $this->assertDatabaseHas('item_units', [
            'unit_code' => 'TP-MSN-001-02',
            'item_id' => $item->id,
        ]);
    }

    /**
     * Uji inventarisasi komputer dengan spesifikasi hardware dan nomor meja lab.
     */
    public function test_computer_inventory_specs_and_lab_workstation(): void
    {
        $response = $this->actingAs($this->jurusanUser)->post('/items', [
            'nama_barang' => 'Workstation PC CAD/CAM',
            'category_id' => $this->catKomputer->id,
            'jenis' => 'alat',
            'jumlah' => 3,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'lokasi' => 'Lab CAD Pemesinan',
            'is_computer' => '1',
            'processor' => 'AMD Ryzen 5 5600G',
            'ram' => '16 GB DDR4',
            'storage' => 'SSD NVMe 512 GB',
            'gpu_vga' => 'GTX 1650 4GB',
            'monitor' => 'LG 24 Inch IPS',
            'sistem_operasi' => 'Windows 11 Pro',
            'auto_generate_units' => '1',
        ]);

        $response->assertRedirect();

        $pc = Item::where('nama_barang', 'Workstation PC CAD/CAM')->first();
        $this->assertNotNull($pc);
        $this->assertTrue((bool) $pc->is_computer);
        $this->assertEquals('TP-KOM-001', $pc->kode_barang);
        $this->assertEquals('AMD Ryzen 5 5600G', $pc->processor);

        // Verifikasi nomor meja dibuat untuk PC
        $units = $pc->units;
        $this->assertCount(3, $units);
        $this->assertEquals('Meja PC-01', $units[0]->nomor_meja);
        $this->assertEquals('Meja PC-02', $units[1]->nomor_meja);
        $this->assertEquals('Meja PC-03', $units[2]->nomor_meja);
    }

    /**
     * Uji pemakaian bahan praktikum yang otomatis memotong stok barang dan mencatat riwayat.
     */
    public function test_consumable_usage_deducts_stock_and_records_history(): void
    {
        $bahan = Item::create([
            'kode_barang' => 'TP-BHN-001',
            'nama_barang' => 'Mata Pisau Bubut HSS',
            'jurusan_id' => $this->jurusan->id,
            'category_id' => $this->catBahan->id,
            'jumlah' => 10,
            'satuan' => 'batang',
            'kondisi' => 'baik',
            'jenis' => 'bahan',
            'min_stok' => 3,
        ]);

        $response = $this->actingAs($this->jurusanUser)->post('/usages', [
            'item_id' => $bahan->id,
            'jumlah' => 4,
            'tanggal_pemakaian' => '2026-09-29',
            'nama_guru' => 'Bpk. Agus Setiawan, S.T',
            'kelas' => 'XII TP 1',
            'keperluan_jobsheet' => 'Jobsheet Bubut Ulir Metrik',
            'catatan' => 'Dipakai untuk praktikum kelompok A',
        ]);

        $response->assertRedirect('/usages');

        // Verifikasi stok bahan berkurang dari 10 menjadi 6
        $bahan->refresh();
        $this->assertEquals(6, $bahan->jumlah);

        // Verifikasi record log pemakaian
        $usage = ItemUsage::where('item_id', $bahan->id)->first();
        $this->assertNotNull($usage);
        $this->assertEquals(4, $usage->jumlah);
        $this->assertEquals(10, $usage->stok_sebelum);
        $this->assertEquals(6, $usage->stok_sesudah);
        $this->assertEquals('XII TP 1', $usage->kelas);
    }

    /**
     * Uji validasi pemakaian bahan jika jumlah yang diminta melebihi stok yang ada.
     */
    public function test_consumable_usage_fails_when_exceeding_available_stock(): void
    {
        $bahan = Item::create([
            'kode_barang' => 'TP-BHN-002',
            'nama_barang' => 'Coolant Pemotongan Logam 5L',
            'jurusan_id' => $this->jurusan->id,
            'category_id' => $this->catBahan->id,
            'jumlah' => 2,
            'satuan' => 'galon',
            'kondisi' => 'baik',
            'jenis' => 'bahan',
        ]);

        $response = $this->actingAs($this->jurusanUser)->post('/usages', [
            'item_id' => $bahan->id,
            'jumlah' => 5, // melebihi stok 2
            'tanggal_pemakaian' => '2026-09-29',
            'nama_guru' => 'Bpk. Agus Setiawan, S.T',
            'kelas' => 'XI TP 2',
            'keperluan_jobsheet' => 'Praktik Bubut Muka',
        ]);

        $response->assertSessionHasErrors('jumlah');

        $bahan->refresh();
        $this->assertEquals(2, $bahan->jumlah);
        $this->assertEquals(0, ItemUsage::where('item_id', $bahan->id)->count());
    }

    /**
     * Uji filter kategori KOM baik dengan ID angka maupun string 'KOM', serta pencarian kata kunci.
     */
    public function test_category_kom_filtering_and_search_in_items_index(): void
    {
        $pcItem = Item::create([
            'kode_barang' => 'TP-KOM-002',
            'nama_barang' => 'PC Lab Simulasi Otomasi',
            'jurusan_id' => $this->jurusan->id,
            'category_id' => $this->catKomputer->id,
            'jumlah' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jenis' => 'alat',
            'is_computer' => true,
            'processor' => 'Intel Core i7-12700',
        ]);

        $nonPcItem = Item::create([
            'kode_barang' => 'TP-MSN-005',
            'nama_barang' => 'Mesin CNC Milling 3-Axis',
            'jurusan_id' => $this->jurusan->id,
            'category_id' => $this->catMesin->id,
            'jumlah' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jenis' => 'alat',
            'is_computer' => false,
        ]);

        // Filter dengan category_id numerik
        $responseNum = $this->actingAs($this->sarpras)->get('/items?category_id='.$this->catKomputer->id);
        $responseNum->assertOk();
        $responseNum->assertSee('PC Lab Simulasi Otomasi');
        $responseNum->assertDontSee('Mesin CNC Milling 3-Axis');

        // Filter dengan category string kode 'KOM'
        $responseCode = $this->actingAs($this->sarpras)->get('/items?category_id=KOM');
        $responseCode->assertOk();
        $responseCode->assertSee('PC Lab Simulasi Otomasi');
        $responseCode->assertDontSee('Mesin CNC Milling 3-Axis');

        // Pencarian dengan keyword 'KOM'
        $responseQ = $this->actingAs($this->sarpras)->get('/items?q=KOM');
        $responseQ->assertOk();
        $responseQ->assertSee('PC Lab Simulasi Otomasi');

        // Pencarian dengan keyword 'Komputer' (nama kategori)
        $responseQName = $this->actingAs($this->sarpras)->get('/items?q=Komputer');
        $responseQName->assertOk();
        $responseQName->assertSee('PC Lab Simulasi Otomasi');
    }

    /**
     * Uji bahwa memilih kategori KOM otomatis menandai is_computer dan menyimpan sistem_operasi pada update.
     */
    public function test_category_kom_automatically_marks_is_computer_and_saves_os_on_update(): void
    {
        // Buat item baru dengan category KOM tanpa mengirim is_computer
        $response = $this->actingAs($this->jurusanUser)->post('/items', [
            'nama_barang' => 'PC Lab Jaringan Baru',
            'category_id' => $this->catKomputer->id,
            'jenis' => 'alat',
            'jumlah' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'lokasi' => 'Lab Jaringan',
        ]);

        $response->assertRedirect();
        $item = Item::where('nama_barang', 'PC Lab Jaringan Baru')->first();
        $this->assertNotNull($item);
        $this->assertTrue($item->is_computer);
        $this->assertEquals($this->catKomputer->id, $item->category_id);

        // Update item dengan sistem operasi
        $updateResponse = $this->actingAs($this->jurusanUser)->put('/items/'.$item->id, [
            'kode_barang' => $item->kode_barang,
            'nama_barang' => 'PC Lab Jaringan Baru (Updated)',
            'category_id' => $this->catKomputer->id,
            'jenis' => 'alat',
            'jumlah' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'processor' => 'Intel Core i5',
            'sistem_operasi' => 'Linux Ubuntu 24.04 LTS',
        ]);

        $updateResponse->assertRedirect();
        $item->refresh();
        $this->assertTrue($item->is_computer);
        $this->assertEquals('Linux Ubuntu 24.04 LTS', $item->sistem_operasi);
    }

    /**
     * Uji bahwa kategori KOM mengizinkan memilih 'Bukan Komputer' (is_computer = 0) dan filter PC Lab bekerja akurat.
     */
    public function test_category_kom_allows_choosing_non_computer_and_filtering(): void
    {
        // 1. Buat barang kategori KOM yang dipilih sebagai 'Bukan Komputer' (misal: Proyektor)
        $response = $this->actingAs($this->jurusanUser)->post('/items', [
            'nama_barang' => 'Proyektor Epson EB-X500',
            'category_id' => $this->catKomputer->id,
            'is_computer' => 0,
            'jenis' => 'alat',
            'jumlah' => 2,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'lokasi' => 'Lab Komputer',
            'processor' => 'Tidak Ada CPU', // harus otomatis dinullkan
        ]);

        $response->assertRedirect();
        $proyektor = Item::where('nama_barang', 'Proyektor Epson EB-X500')->first();
        $this->assertNotNull($proyektor);
        $this->assertFalse((bool) $proyektor->is_computer);
        $this->assertNull($proyektor->processor);

        // 2. Filter PC Lab = Semua ('') -> Proyektor & PC harus muncul
        $responseAll = $this->actingAs($this->jurusanUser)->get('/items?category_id='.$this->catKomputer->id.'&is_computer=');
        $responseAll->assertOk();
        $responseAll->assertSee('Proyektor Epson EB-X500');

        // 3. Filter PC Lab = Ya (PC) ('1') -> Proyektor tidak muncul
        $responsePcOnly = $this->actingAs($this->jurusanUser)->get('/items?category_id='.$this->catKomputer->id.'&is_computer=1');
        $responsePcOnly->assertOk();
        $responsePcOnly->assertDontSee('Proyektor Epson EB-X500');

        // 4. Filter PC Lab = Bukan PC ('0') -> Proyektor muncul
        $responseNonPc = $this->actingAs($this->jurusanUser)->get('/items?category_id='.$this->catKomputer->id.'&is_computer=0');
        $responseNonPc->assertOk();
        $responseNonPc->assertSee('Proyektor Epson EB-X500');
    }

    /**
     * Uji bahwa saat input komputer dalam jumlah banyak, unit otomatis mewarisi spek dan dapat diedit per unit.
     */
    public function test_computer_units_inherit_specs_and_can_be_edited_per_unit(): void
    {
        // 1. Input komputer dengan jumlah 3 unit dan spek baseline
        $response = $this->actingAs($this->jurusanUser)->post('/items', [
            'nama_barang' => 'PC Lab Jaringan Batch A',
            'category_id' => $this->catKomputer->id,
            'is_computer' => 1,
            'jenis' => 'alat',
            'jumlah' => 3,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'lokasi' => 'Lab Jaringan Komputer',
            'processor' => 'Intel Core i5-12400',
            'ram' => '16 GB DDR4',
            'storage' => 'SSD NVMe 512 GB',
            'gpu_vga' => 'Intel UHD 730',
            'monitor' => 'LG 24 Inch IPS',
            'sistem_operasi' => 'Windows 11 Pro',
            'auto_generate_units' => 1,
        ]);

        $response->assertRedirect();
        $item = Item::where('nama_barang', 'PC Lab Jaringan Batch A')->first();
        $this->assertNotNull($item);
        $this->assertCount(3, $item->units);

        // Pastikan setiap unit mewarisi spesifikasi baseline
        foreach ($item->units as $unit) {
            $this->assertEquals('Intel Core i5-12400', $unit->processor);
            $this->assertEquals('16 GB DDR4', $unit->ram);
            $this->assertEquals('SSD NVMe 512 GB', $unit->storage);
            $this->assertEquals('Intel UHD 730', $unit->gpu_vga);
            $this->assertEquals('LG 24 Inch IPS', $unit->monitor);
            $this->assertEquals('Windows 11 Pro', $unit->sistem_operasi);
        }

        // 2. Edit spesifikasi khusus pada Unit ke-2 (upgrade RAM & VGA)
        $unit2 = $item->units[1];
        $updateUnitResponse = $this->actingAs($this->jurusanUser)->put('/units/'.$unit2->id, [
            'nomor_meja' => 'Meja PC-02 (Server Lab)',
            'processor' => 'Intel Core i7-12700',
            'ram' => '32 GB DDR4',
            'storage' => 'SSD NVMe 1 TB',
            'gpu_vga' => 'NVIDIA RTX 3060 12GB',
            'monitor' => 'Dell 27 Inch 4K',
            'sistem_operasi' => 'Ubuntu Server 24.04 LTS',
            'kondisi' => 'baik',
            'status' => 'tersedia',
        ]);

        $updateUnitResponse->assertRedirect();
        $unit2->refresh();

        $this->assertEquals('Intel Core i7-12700', $unit2->processor);
        $this->assertEquals('32 GB DDR4', $unit2->ram);
        $this->assertEquals('SSD NVMe 1 TB', $unit2->storage);
        $this->assertEquals('NVIDIA RTX 3060 12GB', $unit2->gpu_vga);
        $this->assertEquals('Ubuntu Server 24.04 LTS', $unit2->sistem_operasi);

        // Pastikan Unit ke-1 tetap mempertahankan spesifikasi awal (tidak ikut berubah)
        $unit1 = $item->units[0]->fresh();
        $this->assertEquals('Intel Core i5-12400', $unit1->processor);
        $this->assertEquals('16 GB DDR4', $unit1->ram);

        // 3. Halaman detail barang menampilkan rincian spek per unit
        $showResponse = $this->actingAs($this->jurusanUser)->get('/items/'.$item->id);
        $showResponse->assertOk();
        $showResponse->assertSee('Intel Core i7-12700');
        $showResponse->assertSee('32 GB DDR4');
        $showResponse->assertDontSee('Bisa diedit per unit');
    }

    /**
     * Uji input komputer tanpa spesifikasi hardware di form barang, lalu spek diisi per unit.
     */
    public function test_computer_created_without_specs_can_have_specs_configured_per_unit(): void
    {
        // 1. Buat komputer hanya dengan jumlah stok tanpa rincian hardware di tingkat barang
        $response = $this->actingAs($this->jurusanUser)->post('/items', [
            'nama_barang' => 'PC Lab Multimedia',
            'category_id' => $this->catKomputer->id,
            'is_computer' => 1,
            'jenis' => 'alat',
            'jumlah' => 2,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'lokasi' => 'Lab Multimedia',
            'auto_generate_units' => 1,
        ]);

        $response->assertRedirect();
        $item = Item::where('nama_barang', 'PC Lab Multimedia')->first();
        $this->assertNotNull($item);
        $this->assertCount(2, $item->units);
        $this->assertNull($item->processor);

        // 2. Isi spek unit 1
        $unit1 = $item->units[0];
        $this->actingAs($this->jurusanUser)->put('/units/'.$unit1->id, [
            'nomor_meja' => 'Meja PC-01',
            'processor' => 'Intel Core i5-13400',
            'ram' => '16 GB DDR5',
            'storage' => 'NVMe 1 TB',
            'gpu_vga' => 'RTX 3050',
            'monitor' => 'ASUS 24 Inch',
            'sistem_operasi' => 'Windows 11 Home',
            'kondisi' => 'baik',
            'status' => 'tersedia',
        ])->assertRedirect();

        $unit1->refresh();
        $this->assertEquals('Intel Core i5-13400', $unit1->processor);
        $this->assertEquals('16 GB DDR5', $unit1->ram);

        // 3. Pastikan halaman detail menampilkan spesifikasi unit dan tidak ada badge 'Bisa diedit per unit'
        $showResponse = $this->actingAs($this->jurusanUser)->get('/items/'.$item->id);
        $showResponse->assertOk();
        $showResponse->assertSee('Intel Core i5-13400');
        $showResponse->assertSee('16 GB DDR5');
        $showResponse->assertDontSee('Bisa diedit per unit');
        $showResponse->assertDontSee('Bisa disesuaikan per unit');
    }

    /**
     * Uji unggah foto barang saat create, akses via /storage, dan update/hapus foto.
     */
    public function test_item_photo_upload_display_and_storage_fallback(): void
    {
        Storage::fake('public', ['lock' => 0]);

        $file = UploadedFile::fake()->image('pc_server.jpg', 600, 600)->size(300);

        // 1. Simpan item beserta upload foto
        $response = $this->actingAs($this->jurusanUser)->post('/items', [
            'nama_barang' => 'Server CBT Utama',
            'category_id' => $this->catKomputer->id,
            'is_computer' => 1,
            'jenis' => 'alat',
            'jumlah' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'lokasi' => 'Ruang Server',
            'foto' => $file,
        ]);

        $response->assertRedirect();
        $item = Item::where('nama_barang', 'Server CBT Utama')->first();
        $this->assertNotNull($item);
        $this->assertNotNull($item->foto);
        Storage::disk('public')->assertExists($item->foto);

        // 2. Akses foto via endpoint /storage/{path}
        $imageResponse = $this->get('/storage/'.$item->foto);
        $imageResponse->assertOk();

        // 3. Akses halaman index dan show, foto harus dirender
        $indexResponse = $this->actingAs($this->jurusanUser)->get('/items');
        $indexResponse->assertOk();
        $indexResponse->assertSee('/storage/'.$item->foto);

        $showResponse = $this->actingAs($this->jurusanUser)->get('/items/'.$item->id);
        $showResponse->assertOk();
        $showResponse->assertSee('/storage/'.$item->foto);

        // 4. Update item dengan opsi hapus foto
        $updateResponse = $this->actingAs($this->jurusanUser)->put('/items/'.$item->id, [
            'nama_barang' => 'Server CBT Utama',
            'kode_barang' => $item->kode_barang,
            'category_id' => $this->catKomputer->id,
            'is_computer' => 1,
            'jenis' => 'alat',
            'jumlah' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'lokasi' => 'Ruang Server',
            'hapus_foto' => 1,
        ]);

        $updateResponse->assertRedirect();
        $item->refresh();
        $this->assertNull($item->foto);
    }

    public function test_batch_restok_creates_sequential_units_with_tanggal_masuk(): void
    {
        // 1. Buat item alat dengan 2 unit awal
        $item = Item::create([
            'nama_barang' => 'Laptop Siswa',
            'kode_barang' => 'TKJ-KOM-001',
            'category_id' => $this->catKomputer->id,
            'jurusan_id' => $this->jurusan->id,
            'jenis' => 'alat',
            'jumlah' => 2,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'is_computer' => true,
        ]);

        ItemUnit::create([
            'item_id' => $item->id,
            'jurusan_id' => $item->jurusan_id,
            'unit_code' => 'TKJ-KOM-001-01',
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'tanggal_masuk' => '2026-01-15',
        ]);
        ItemUnit::create([
            'item_id' => $item->id,
            'jurusan_id' => $item->jurusan_id,
            'unit_code' => 'TKJ-KOM-001-02',
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'tanggal_masuk' => '2026-01-15',
        ]);

        // 2. Batch re-stok: tambah 3 unit baru
        $response = $this->actingAs($this->jurusanUser)->post(
            route('items.units.store-batch', $item),
            [
                'jumlah_unit' => 3,
                'tanggal_masuk' => '2026-09-30',
                'kondisi' => 'baik',
                'lokasi_penempatan' => 'Lab TKJ',
                'catatan' => 'Pengadaan semester ganjil 2026',
            ]
        );

        $response->assertRedirect(route('items.show', $item));
        $response->assertSessionHas('success');

        // 3. Verifikasi unit baru tercipta dengan kode berurutan
        $item->refresh();
        $this->assertEquals(5, $item->jumlah);
        $this->assertEquals(5, $item->units()->count());

        $this->assertDatabaseHas('item_units', [
            'item_id' => $item->id,
            'unit_code' => 'TKJ-KOM-001-03',
            'catatan' => 'Pengadaan semester ganjil 2026',
        ]);
        $this->assertDatabaseHas('item_units', [
            'item_id' => $item->id,
            'unit_code' => 'TKJ-KOM-001-04',
        ]);
        $this->assertDatabaseHas('item_units', [
            'item_id' => $item->id,
            'unit_code' => 'TKJ-KOM-001-05',
        ]);

        // 4. Verifikasi tanggal masuk tersimpan benar via model
        $newUnit = ItemUnit::where('unit_code', 'TKJ-KOM-001-03')->first();
        $this->assertEquals('2026-09-30', $newUnit->tanggal_masuk->toDateString());

        $oldUnit = ItemUnit::where('unit_code', 'TKJ-KOM-001-01')->first();
        $this->assertEquals('2026-01-15', $oldUnit->tanggal_masuk->toDateString());

        // 5. Verifikasi halaman detail menampilkan tanggal masuk dan re-stok batch button
        $showResponse = $this->actingAs($this->jurusanUser)->get(route('items.show', $item));
        $showResponse->assertSee('Re-stok Batch');
        $showResponse->assertSee('30/09/2026');
        $showResponse->assertSee('TKJ-KOM-001-05');
    }

    public function test_bahan_restock_and_cancel_adjusts_stock_and_shows_audit_log(): void
    {
        // 1. Buat barang bahan habis pakai dengan stok awal 5
        $item = Item::create([
            'nama_barang' => 'Kabel UTP Cat6',
            'kode_barang' => 'TKJ-BAH-001',
            'category_id' => $this->catBahan->id,
            'jurusan_id' => $this->jurusan->id,
            'jenis' => 'bahan',
            'jumlah' => 5,
            'satuan' => 'roll',
            'kondisi' => 'baik',
            'min_stok' => 2,
        ]);

        // 2. Lakukan Re-stok Masuk sebanyak 10 roll
        $restockResponse = $this->actingAs($this->jurusanUser)->post(
            route('items.restock.store', $item),
            [
                'jumlah' => 10,
                'tanggal_masuk' => '2026-09-30',
                'sumber_dana' => 'BOS Reguler',
                'pemasok' => 'CV Sumber Kabel',
                'catatan' => 'Nota No. 123/IX/2026',
            ]
        );

        $restockResponse->assertRedirect(route('items.show', $item));
        $restockResponse->assertSessionHas('success');

        // 3. Verifikasi stok bertambah 5 + 10 = 15
        $item->refresh();
        $this->assertEquals(15, $item->jumlah);

        // Verifikasi audit log tersimpan
        $this->assertDatabaseHas('item_restocks', [
            'item_id' => $item->id,
            'jumlah' => 10,
            'satuan' => 'roll',
            'sumber_dana' => 'BOS Reguler',
            'pemasok' => 'CV Sumber Kabel',
            'stok_sebelum' => 5,
            'stok_sesudah' => 15,
            'catatan' => 'Nota No. 123/IX/2026',
        ]);

        $restock = ItemRestock::where('item_id', $item->id)->first();
        $this->assertNotNull($restock);
        $this->assertEquals('2026-09-30', $restock->tanggal_masuk->toDateString());

        // 4. Verifikasi tampilan detail barang
        $showResponse = $this->actingAs($this->jurusanUser)->get(route('items.show', $item));
        $showResponse->assertSee('Riwayat Re-stok Masuk');
        $showResponse->assertSee('CV Sumber Kabel');
        $showResponse->assertSee('Nota No. 123/IX/2026');
        $showResponse->assertSee('+10 roll');

        // 5. Batalkan / hapus transaksi re-stok
        $deleteResponse = $this->actingAs($this->jurusanUser)->delete(
            route('restocks.destroy', $restock)
        );

        $deleteResponse->assertRedirect(route('items.show', $item));
        $deleteResponse->assertSessionHas('success');

        // 6. Verifikasi stok kembali ke 5 dan log terhapus
        $item->refresh();
        $this->assertEquals(5, $item->jumlah);
        $this->assertDatabaseMissing('item_restocks', ['id' => $restock->id]);
    }

    public function test_items_index_can_be_sorted_by_columns(): void
    {
        // Buat 2 barang dengan urutan nama dan stok berbeda
        Item::create([
            'nama_barang' => 'Alpha Tester',
            'kode_barang' => 'TKJ-MES-001',
            'category_id' => $this->catMesin->id,
            'jurusan_id' => $this->jurusan->id,
            'jenis' => 'alat',
            'jumlah' => 10,
            'satuan' => 'unit',
            'kondisi' => 'baik',
        ]);

        Item::create([
            'nama_barang' => 'Zeta Scanner',
            'kode_barang' => 'TKJ-MES-002',
            'category_id' => $this->catMesin->id,
            'jurusan_id' => $this->jurusan->id,
            'jenis' => 'alat',
            'jumlah' => 2,
            'satuan' => 'unit',
            'kondisi' => 'baik',
        ]);

        // 1. Sort by nama_barang asc
        $responseAsc = $this->actingAs($this->jurusanUser)->get(route('items.index', [
            'sort' => 'nama_barang',
            'direction' => 'asc',
        ]));
        $responseAsc->assertOk();
        $responseAsc->assertSeeInOrder(['Alpha Tester', 'Zeta Scanner']);

        // 2. Sort by nama_barang desc
        $responseDesc = $this->actingAs($this->jurusanUser)->get(route('items.index', [
            'sort' => 'nama_barang',
            'direction' => 'desc',
        ]));
        $responseDesc->assertOk();
        $responseDesc->assertSeeInOrder(['Zeta Scanner', 'Alpha Tester']);

        // 3. Sort by jumlah asc
        $responseStockAsc = $this->actingAs($this->jurusanUser)->get(route('items.index', [
            'sort' => 'jumlah',
            'direction' => 'asc',
        ]));
        $responseStockAsc->assertOk();
        $responseStockAsc->assertSeeInOrder(['Zeta Scanner', 'Alpha Tester']);

        // 4. Sort by jumlah desc
        $responseStockDesc = $this->actingAs($this->jurusanUser)->get(route('items.index', [
            'sort' => 'jumlah',
            'direction' => 'desc',
        ]));
        $responseStockDesc->assertOk();
        $responseStockDesc->assertSeeInOrder(['Alpha Tester', 'Zeta Scanner']);
    }

    public function test_consumable_usage_allows_optional_kelas_and_jobsheet(): void
    {
        $bahan = Item::create([
            'kode_barang' => 'TP-BHN-999',
            'nama_barang' => 'Kertas Amplas Halus',
            'jurusan_id' => $this->jurusan->id,
            'category_id' => $this->catBahan->id,
            'jumlah' => 20,
            'satuan' => 'lembar',
            'kondisi' => 'baik',
            'jenis' => 'bahan',
        ]);

        // Input pemakaian tanpa mengisi kelas & keperluan_jobsheet
        $response = $this->actingAs($this->jurusanUser)->post('/usages', [
            'item_id' => $bahan->id,
            'jumlah' => 5,
            'tanggal_pemakaian' => '2026-09-30',
            'nama_guru' => 'Ibu Siti Aminah',
            'kelas' => null,
            'keperluan_jobsheet' => null,
            'catatan' => 'Dipakai untuk pembersihan meja praktik',
        ]);

        $response->assertRedirect('/usages');
        $response->assertSessionHas('success');

        $bahan->refresh();
        $this->assertEquals(15, $bahan->jumlah);

        $usage = ItemUsage::where('item_id', $bahan->id)->first();
        $this->assertNotNull($usage);
        $this->assertNull($usage->kelas);
        $this->assertNull($usage->keperluan_jobsheet);
        $this->assertEquals(5, $usage->jumlah);

        // Pastikan tampilan index usages dan item detail bisa memuat tanpa error
        $indexResponse = $this->actingAs($this->jurusanUser)->get('/usages');
        $indexResponse->assertOk();
        $indexResponse->assertSee('Ibu Siti Aminah');

        $showResponse = $this->actingAs($this->jurusanUser)->get(route('items.show', $bahan));
        $showResponse->assertOk();
        $showResponse->assertSee('Ibu Siti Aminah');
    }

    public function test_consumable_usage_can_be_edited_and_adjusts_stock(): void
    {
        $bahan = Item::create([
            'kode_barang' => 'TP-BHN-888',
            'nama_barang' => 'Minyak Pelumas Mesin',
            'jurusan_id' => $this->jurusan->id,
            'category_id' => $this->catBahan->id,
            'jumlah' => 20,
            'satuan' => 'liter',
            'kondisi' => 'baik',
            'jenis' => 'bahan',
        ]);

        // 1. Buat transaksi awal pemakaian 8 liter -> sisa stok 12
        $this->actingAs($this->jurusanUser)->post('/usages', [
            'item_id' => $bahan->id,
            'jumlah' => 8,
            'tanggal_pemakaian' => '2026-09-30',
            'nama_guru' => 'Pak Joko',
            'kelas' => 'XI TP 1',
            'keperluan_jobsheet' => 'Perawatan Berkala Mesin Bubut',
        ]);

        $bahan->refresh();
        $this->assertEquals(12, $bahan->jumlah);

        $usage = ItemUsage::where('item_id', $bahan->id)->first();
        $this->assertNotNull($usage);

        // 2. Akses halaman edit
        $editResponse = $this->actingAs($this->jurusanUser)->get(route('usages.edit', $usage));
        $editResponse->assertOk();
        $editResponse->assertSee('Edit Pemakaian Bahan');
        $editResponse->assertSee('Minyak Pelumas Mesin');

        // 3. Edit pemakaian menjadi 5 liter (berkurang 3 liter) -> sisa stok bertambah 3 jadi 15
        $updateResponse = $this->actingAs($this->jurusanUser)->put(route('usages.update', $usage), [
            'jumlah' => 5,
            'tanggal_pemakaian' => '2026-09-30',
            'nama_guru' => 'Pak Joko Widodo, M.Pd',
            'kelas' => 'XI TP 2',
            'keperluan_jobsheet' => 'Perawatan Mesin Bubut & Frais',
            'catatan' => 'Revisi jumlah pemakaian riil',
        ]);

        $updateResponse->assertRedirect(route('usages.index'));
        $updateResponse->assertSessionHas('success');

        $bahan->refresh();
        $this->assertEquals(15, $bahan->jumlah);

        $usage->refresh();
        $this->assertEquals(5, $usage->jumlah);
        $this->assertEquals('Pak Joko Widodo, M.Pd', $usage->nama_guru);
        $this->assertEquals('XI TP 2', $usage->kelas);
        $this->assertEquals('Revisi jumlah pemakaian riil', $usage->catatan);

        // 4. Coba edit pemakaian melebihi sisa stok (saat ini stok 15, coba tambah jadi 25 -> butuh 20 tambahan, tidak cukup)
        $invalidUpdateResponse = $this->actingAs($this->jurusanUser)->put(route('usages.update', $usage), [
            'jumlah' => 25,
            'tanggal_pemakaian' => '2026-09-30',
            'nama_guru' => 'Pak Joko Widodo, M.Pd',
        ]);

        $invalidUpdateResponse->assertSessionHasErrors('jumlah');

        // Stok tetap tidak berubah
        $bahan->refresh();
        $this->assertEquals(15, $bahan->jumlah);
    }

    public function test_non_sarpras_user_only_sees_and_accesses_own_jurusan_items(): void
    {
        $sarJurusan = Jurusan::where('kode', 'SAR')->first();

        // 1. Buat barang milik sarpras
        $sarItem = Item::create([
            'kode_barang' => 'SAR-UMM-001',
            'nama_barang' => 'Proyektor Aula Sarpras',
            'jurusan_id' => $sarJurusan->id,
            'category_id' => $this->catMesin->id,
            'jumlah' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jenis' => 'alat',
            'lokasi' => 'Aula Utama',
        ]);

        // 2. Buat barang milik jurusan user (TP)
        $tpItem = Item::create([
            'kode_barang' => 'TP-MSN-777',
            'nama_barang' => 'Mesin Bubut Presisi TP',
            'jurusan_id' => $this->jurusan->id,
            'category_id' => $this->catMesin->id,
            'jumlah' => 2,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jenis' => 'alat',
            'lokasi' => 'Bengkel TP',
        ]);

        // 3. User jurusan mengakses items.index -> hanya melihat barang TP, tidak ada barang Sarpras
        $indexResponse = $this->actingAs($this->jurusanUser)->get(route('items.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Mesin Bubut Presisi TP');
        $indexResponse->assertDontSee('Proyektor Aula Sarpras');

        // 4. User jurusan mengakses detail barang miliknya -> OK
        $showOwnResponse = $this->actingAs($this->jurusanUser)->get(route('items.show', $tpItem));
        $showOwnResponse->assertOk();
        $showOwnResponse->assertSee('Mesin Bubut Presisi TP');

        // 5. User jurusan mencoba mengakses detail barang sarpras -> 403 Forbidden
        $showSarResponse = $this->actingAs($this->jurusanUser)->get(route('items.show', $sarItem));
        $showSarResponse->assertForbidden();

        // 6. User sarpras mengakses items.index -> bisa melihat barang sarpras
        $sarIndexResponse = $this->actingAs($this->sarpras)->get(route('items.index'));
        $sarIndexResponse->assertOk();
        $sarIndexResponse->assertSee('Proyektor Aula Sarpras');
    }

    /**
     * Uji upload gambar barang hingga 3 MB dan memastikan gambar terkompresi dengan baik.
     */
    public function test_item_image_upload_up_to_3mb_and_compressed(): void
    {
        Storage::fake('public', ['lock' => 0]);

        $file = UploadedFile::fake()->image('foto_mesin.jpg', 1600, 1200)->size(2500); // 2.5 MB

        $response = $this->actingAs($this->jurusanUser)->post('/items', [
            'nama_barang' => 'Mesin CNC Frais 3-Axis',
            'category_id' => $this->catMesin->id,
            'jenis' => 'alat',
            'jumlah' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'lokasi' => 'Bengkel CNC',
            'foto' => $file,
        ]);

        $response->assertRedirect();

        $item = Item::where('nama_barang', 'Mesin CNC Frais 3-Axis')->first();
        $this->assertNotNull($item);
        $this->assertNotNull($item->foto);

        Storage::disk('public')->assertExists($item->foto);

        // Pastikan ukuran file hasil kompresi jauh lebih kecil dari 2.5 MB (kurang dari 500 KB)
        $compressedSize = Storage::disk('public')->size($item->foto);
        $this->assertLessThan(500 * 1024, $compressedSize);
    }
}
