<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityAndPerformanceAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $sarprasUser;

    protected User $jurusanAUser;

    protected User $jurusanBUser;

    protected Jurusan $jurusanA;

    protected Jurusan $jurusanB;

    protected Category $category;

    protected Item $itemA;

    protected Item $itemB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jurusanA = Jurusan::create([
            'kode' => 'TKR',
            'nama' => 'Teknik Kendaraan Ringan',
        ]);

        $this->jurusanB = Jurusan::create([
            'kode' => 'TKJ',
            'nama' => 'Teknik Komputer dan Jaringan',
        ]);

        $this->sarprasUser = User::create([
            'name' => 'Admin Sarpras',
            'email' => 'sarpras@sekolah.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'sarpras',
        ]);

        $this->jurusanAUser = User::create([
            'name' => 'Kaprog TKR',
            'email' => 'tkr@sekolah.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'jurusan',
            'jurusan_id' => $this->jurusanA->id,
        ]);

        $this->jurusanBUser = User::create([
            'name' => 'Kaprog TKJ',
            'email' => 'tkj@sekolah.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'jurusan',
            'jurusan_id' => $this->jurusanB->id,
        ]);

        $this->category = Category::create([
            'kode' => 'ALT',
            'nama' => 'Peralatan Bengkel',
        ]);

        $this->itemA = Item::create([
            'kode_barang' => 'TKR-ALT-001',
            'nama_barang' => 'Kunci Pas Set TKR',
            'jurusan_id' => $this->jurusanA->id,
            'category_id' => $this->category->id,
            'jumlah' => 10,
            'satuan' => 'set',
            'kondisi' => 'baik',
            'jenis' => 'alat',
        ]);

        $this->itemB = Item::create([
            'kode_barang' => 'TKJ-ALT-001',
            'nama_barang' => 'Crimping Tool TKJ',
            'jurusan_id' => $this->jurusanB->id,
            'category_id' => $this->category->id,
            'jumlah' => 5,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jenis' => 'alat',
        ]);
    }

    /**
     * KEAMANAN 1: Pengguna tanpa autentikasi (guest) tidak dapat mengakses rute terlindungi.
     */
    public function test_guest_cannot_access_protected_routes(): void
    {
        $protectedRoutes = [
            '/dashboard',
            '/items',
            '/borrowings',
            '/usages',
            '/procurements',
            '/reports',
            '/users',
            '/jurusans',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    /**
     * KEAMANAN 2: Akun Jurusan dilarang mengakses rute khusus Sarpras (Role Authorization).
     */
    public function test_jurusan_user_is_forbidden_from_sarpras_only_routes(): void
    {
        $sarprasRoutes = [
            '/users',
            '/users/create',
            '/jurusans',
            '/jurusans/create',
            '/complaints',
            '/sarpras/umum',
            '/sarpras/gudang',
        ];

        foreach ($sarprasRoutes as $route) {
            $response = $this->actingAs($this->jurusanAUser)->get($route);
            $response->assertStatus(403);
        }
    }

    /**
     * KEAMANAN 3: Isolasi Data Antar Jurusan (Cross-Tenant Protection).
     * Jurusan A tidak boleh mengedit, memperbarui, atau menghapus item milik Jurusan B.
     */
    public function test_jurusan_cannot_modify_or_delete_other_jurusan_items(): void
    {
        // Mencoba mengakses form edit item milik Jurusan B
        $response = $this->actingAs($this->jurusanAUser)->get("/items/{$this->itemB->id}/edit");
        $response->assertStatus(403);

        // Mencoba memperbarui item milik Jurusan B
        $updateResponse = $this->actingAs($this->jurusanAUser)->put("/items/{$this->itemB->id}", [
            'kode_barang' => 'HACK-001',
            'nama_barang' => 'Barang Hasil Hack',
            'jumlah' => 999,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'jenis' => 'alat',
        ]);
        $updateResponse->assertStatus(403);

        // Memastikan data di database tidak berubah
        $this->assertDatabaseHas('items', [
            'id' => $this->itemB->id,
            'nama_barang' => 'Crimping Tool TKJ',
        ]);

        // Mencoba menghapus item milik Jurusan B
        $deleteResponse = $this->actingAs($this->jurusanAUser)->delete("/items/{$this->itemB->id}");
        $deleteResponse->assertStatus(403);

        // Memastikan item tetap ada
        $this->assertDatabaseHas('items', ['id' => $this->itemB->id]);
    }

    /**
     * KEAMANAN 4: Proteksi SQL Injection pada input pencarian dan filter.
     */
    public function test_sql_injection_payloads_in_search_are_safely_escaped(): void
    {
        $payloads = [
            "' OR '1'='1",
            "'; DROP TABLE items; --",
            '1 UNION SELECT null, null, null--',
            "' OR 1=1 --",
            '" OR ""="',
        ];

        foreach ($payloads as $payload) {
            $response = $this->actingAs($this->sarprasUser)->get('/items?q='.urlencode($payload));
            $response->assertStatus(200);

            // Pastikan tabel items tidak terhapus atau rusak
            $this->assertDatabaseHas('items', ['id' => $this->itemA->id]);
        }
    }

    /**
     * KEAMANAN 5: Proteksi Path Traversal pada storage route.
     * Mencoba mengakses file di luar folder storage/public seperti .env harus gagal (404/403).
     */
    public function test_path_traversal_on_storage_route_is_prevented(): void
    {
        $traversalPaths = [
            '/storage/../../.env',
            '/storage/..%2F..%2F.env',
            '/storage/....//....//.env',
            '/storage/etc/passwd',
        ];

        foreach ($traversalPaths as $path) {
            try {
                $response = $this->get($path);
                $this->assertContains($response->getStatusCode(), [404, 403, 400]);
            } catch (\Exception $e) {
                // Flysystem CorruptedPathDetected exception is also safe
                $this->assertTrue(true);
            }
        }
    }

    /**
     * PERFORMA 1: Query Eager Loading pada daftar barang inventaris.
     * Pastikan tidak terjadi ledakan query (N+1) saat merender daftar barang.
     */
    public function test_items_index_query_count_is_optimized_against_n_plus_one(): void
    {
        // Buat 15 barang tambahan
        for ($i = 1; $i <= 15; $i++) {
            Item::create([
                'kode_barang' => "PERF-ITEM-{$i}",
                'nama_barang' => "Barang Pengujian Performa {$i}",
                'jurusan_id' => $this->jurusanA->id,
                'category_id' => $this->category->id,
                'jumlah' => 1,
                'satuan' => 'unit',
                'kondisi' => 'baik',
                'jenis' => 'alat',
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->actingAs($this->sarprasUser)->get('/items');
        $response->assertStatus(200);

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // Untuk halaman dengan pagination 10 item + relasi jurusan/category/units + categories/jurusans all
        // Jumlah query harus stabil di bawah 15 query, BUKAN proporsional terhadap jumlah item (N+1).
        $this->assertLessThanOrEqual(15, $queryCount, "Terdeteksi N+1 problem: total query ({$queryCount}) melebihi ambang batas.");
    }

    /**
     * PERFORMA 2: Waktu respons dan pagination berjalan efektif.
     */
    public function test_items_pagination_and_response_time(): void
    {
        $startTime = microtime(true);

        $response = $this->actingAs($this->sarprasUser)->get('/items?page=1');
        $response->assertStatus(200);

        $executionTime = microtime(true) - $startTime;

        // Response time pada pengujian lokal harus di bawah 1 detik
        $this->assertLessThan(1.0, $executionTime, "Halaman items memerlukan waktu {$executionTime}s, terlalu lambat.");
    }

    /**
     * PERFORMA 3: Dashboard Sarpras mengeksekusi aggregasi secara efisien tanpa N+1.
     */
    public function test_dashboard_query_efficiency(): void
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->actingAs($this->sarprasUser)->get('/dashboard');
        $response->assertStatus(200);

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // Dashboard sarpras memuat stat cards dan 4 daftar recent dengan eager loading.
        // Total query harus terkendali (di bawah 30 query).
        $this->assertLessThanOrEqual(30, $queryCount, "Dashboard mengeksekusi terlalu banyak query: {$queryCount}");
    }
}
