<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\Jurusan;
use App\Services\ImageOptimizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ItemController extends Controller
{
    /**
     * Tampilkan daftar barang inventaris.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Item::with(['jurusan', 'category', 'units']);

        $sarJurusan = Jurusan::where('kode', 'SAR')->first();
        $sarJurusanId = $sarJurusan?->id;

        // Jika user adalah jurusan, batasi hanya data inventaris jurusannya sendiri
        if ($user->isJurusan()) {
            $query->where('jurusan_id', $user->jurusan_id);
        } elseif ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        // Filter kategori (Mendukung ID angka maupun kode 'KOM' atau nama kategori)
        $categoryFilter = $request->input('category_id') ?? $request->input('category') ?? $request->input('kategori');
        $komCategory = Category::where('kode', 'KOM')->first();
        $isKomFilter = false;

        if (! empty($categoryFilter)) {
            if (is_numeric($categoryFilter)) {
                $catId = (int) $categoryFilter;
                if ($komCategory && $catId === $komCategory->id) {
                    $isKomFilter = true;
                } else {
                    $query->where('category_id', $catId);
                }
            } else {
                $codeOrName = strtoupper(trim((string) $categoryFilter));
                if ($codeOrName === 'KOM' || str_contains(strtolower((string) $categoryFilter), 'komputer')) {
                    $isKomFilter = true;
                } else {
                    $query->whereHas('category', function ($q) use ($categoryFilter) {
                        $q->where('kode', strtoupper($categoryFilter))
                            ->orWhere('nama', 'like', "%{$categoryFilter}%");
                    });
                }
            }

            if ($isKomFilter) {
                // Kategori KOM mencakup semua item dengan category KOM atau ditandai sebagai komputer
                $query->where(function ($q) use ($komCategory) {
                    if ($komCategory) {
                        $q->where('category_id', $komCategory->id);
                    }
                    $q->orWhere('is_computer', true);
                });
            }
        }

        // Filter kondisi
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        // Filter jenis (alat/bahan)
        if ($request->filled('jenis')) {
            $bhnCategory = Category::where('kode', 'BHN')->first();
            $isBhnCategory = $bhnCategory && ($categoryFilter == $bhnCategory->id || strtoupper((string) $categoryFilter) === 'BHN');

            // Jika user memilih kategori non-bahan (seperti KOM, MSN, UKR) tetapi jenis=bahan masih tersisa dari navigasi sebelumnya, abaikan agar tidak 0 item
            if ($request->jenis === 'bahan' && ! empty($categoryFilter) && ! $isBhnCategory) {
                // Abaikan jenis=bahan untuk kategori non-bahan
            } else {
                $query->where('jenis', $request->jenis);
            }
        }

        // Filter khusus komputer / PC
        if ($request->has('is_computer') && $request->is_computer !== null && $request->is_computer !== '') {
            $query->where('is_computer', filter_var($request->is_computer, FILTER_VALIDATE_BOOLEAN));
        }

        // Pencarian nama, kode, lokasi, spesifikasi, atau kategori
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%")
                    ->orWhere('processor', 'like', "%{$search}%")
                    ->orWhere('spesifikasi', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($cq) use ($search) {
                        $cq->where('kode', 'like', "%{$search}%")
                            ->orWhere('nama', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting / Pengurutan kolom
        $allowedSorts = [
            'kode_barang' => 'kode_barang',
            'nama_barang' => 'nama_barang',
            'lokasi' => 'lokasi',
            'jumlah' => 'jumlah',
            'kondisi' => 'kondisi',
            'created_at' => 'created_at',
        ];

        $sort = $request->query('sort');
        $direction = strtolower($request->query('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        if ($sort && isset($allowedSorts[$sort])) {
            $query->orderBy($allowedSorts[$sort], $direction);
        } elseif ($sort === 'jurusan') {
            $query->join('jurusans', 'items.jurusan_id', '=', 'jurusans.id')
                ->orderBy('jurusans.nama', $direction)
                ->select('items.*');
        } elseif ($sort === 'kategori') {
            $query->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                ->orderBy('categories.nama', $direction)
                ->select('items.*');
        } elseif ($sort === 'units_count') {
            $query->withCount('units')->orderBy('units_count', $direction);
        } else {
            $query->latest();
        }

        $items = $query->paginate(10)->withQueryString();
        $categories = Category::all();
        $jurusans = Jurusan::all();

        return view('items.index', compact('items', 'categories', 'jurusans', 'sarJurusan'));
    }

    /**
     * Tampilkan formulir tambah barang baru.
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $categories = Category::all();
        $jurusans = Jurusan::all();
        $sarJurusan = Jurusan::where('kode', 'SAR')->first();
        $penempatanSarpras = $request->query('penempatan_sarpras', 'umum');
        $preselectedJurusanId = $request->query('jurusan_id');

        if (! $preselectedJurusanId && $request->has('penempatan_sarpras') && $sarJurusan) {
            $preselectedJurusanId = $sarJurusan->id;
        }

        return view('items.create', compact('categories', 'jurusans', 'user', 'sarJurusan', 'penempatanSarpras', 'preselectedJurusanId'));
    }

    /**
     * Endpoint API untuk menghasilkan kode barang otomatis sesuai pola [JURUSAN]-[KATEGORI]-[NO_URUT].
     */
    public function generateCode(Request $request): JsonResponse
    {
        $user = $request->user();
        $jurusanId = $user->isJurusan() ? $user->jurusan_id : $request->input('jurusan_id');
        $categoryId = $request->input('category_id');

        $code = $this->createItemCode($jurusanId, $categoryId);

        return response()->json(['code' => $code]);
    }

    /**
     * Helper internal untuk membangkitkan kode barang standar.
     */
    protected function createItemCode(?int $jurusanId, ?int $categoryId): string
    {
        $jurusanKode = 'UMUM';
        if ($jurusanId) {
            $jurusan = Jurusan::find($jurusanId);
            if ($jurusan) {
                $jurusanKode = strtoupper(trim($jurusan->kode));
            }
        }

        $categoryKode = 'BRG';
        if ($categoryId) {
            $category = Category::find($categoryId);
            if ($category) {
                $categoryKode = strtoupper(trim($category->kode ?: substr($category->nama, 0, 3)));
            }
        }

        $prefix = "{$jurusanKode}-{$categoryKode}-";

        // Cari nomor urut terakhir yang ada
        $existingCodes = Item::where('kode_barang', 'like', "{$prefix}%")
            ->pluck('kode_barang')
            ->toArray();

        $maxNumber = 0;
        foreach ($existingCodes as $c) {
            $suffix = substr($c, strlen($prefix));
            if (is_numeric($suffix)) {
                $num = (int) $suffix;
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
        }

        $nextNumber = $maxNumber + 1;

        return sprintf('%s%03d', $prefix, $nextNumber);
    }

    /**
     * Simpan data barang baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($request->has('jumlah')) {
            $request->merge([
                'jumlah' => str_replace(',', '.', (string) $request->input('jumlah')),
            ]);
        }
        if ($request->has('min_stok')) {
            $request->merge([
                'min_stok' => str_replace(',', '.', (string) $request->input('min_stok')),
            ]);
        }

        $rules = [
            'kode_barang' => ['nullable', 'string', 'max:50', 'unique:items,kode_barang'],
            'nama_barang' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'jumlah' => ['required', 'numeric', 'min:0'],
            'satuan' => ['required', 'string', 'max:30'],
            'kondisi' => ['required', 'in:baik,rusak_ringan,rusak_berat'],
            'jenis' => ['required', 'in:alat,bahan'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'sumber_dana' => ['nullable', 'string', 'max:100'],
            'tahun_pengadaan' => ['nullable', 'integer', 'min:1990', 'max:'.(date('Y') + 1)],
            'spesifikasi' => ['nullable', 'string'],
            'min_stok' => ['nullable', 'numeric', 'min:0'],

            // Spesifikasi Perangkat Komputer (Opsional / Tidak Wajib)
            'is_computer' => ['nullable', 'boolean'],
            'processor' => ['nullable', 'string', 'max:255'],
            'ram' => ['nullable', 'string', 'max:100'],
            'storage' => ['nullable', 'string', 'max:100'],
            'gpu_vga' => ['nullable', 'string', 'max:100'],
            'monitor' => ['nullable', 'string', 'max:100'],
            'sistem_operasi' => ['nullable', 'string', 'max:100'],

            // Opsi Pembuatan Unit Fisik Otomatis
            'auto_generate_units' => ['nullable', 'boolean'],

            // Foto Fisik Barang
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
        ];

        if ($user->isSarprasOrKepalaSekolah() || ! $user->jurusan_id) {
            $rules['jurusan_id'] = ['required', 'exists:jurusans,id'];
            $rules['penempatan_sarpras'] = ['nullable', 'in:gudang,umum'];
        }

        $validated = $request->validate($rules, [
            'foto.image' => 'File foto harus berupa gambar (JPG, PNG, atau WEBP).',
            'foto.max' => 'Ukuran file foto maksimal 3 MB.',
        ]);

        if ($user->isJurusan()) {
            $validated['jurusan_id'] = $user->jurusan_id;
        }

        // Jika kode_barang kosong, generate otomatis
        if (empty($validated['kode_barang'])) {
            $validated['kode_barang'] = $this->createItemCode($validated['jurusan_id'], $validated['category_id'] ?? null);
        }

        $komCategory = Category::where('kode', 'KOM')->first();
        $isKomCategory = $komCategory && ! empty($validated['category_id']) && (int) $validated['category_id'] === $komCategory->id;

        // User dapat memilih apakah barang kategori KOM adalah Komputer (true) atau Perangkat IT Non-Komputer (false)
        $isComputerChoice = $request->has('is_computer') ? $request->boolean('is_computer') : true;
        $validated['is_computer'] = $isKomCategory && $isComputerChoice;

        // Jika bukan komputer, kosongkan spesifikasi hardware komputer
        if (! $validated['is_computer']) {
            $validated['processor'] = null;
            $validated['ram'] = null;
            $validated['storage'] = null;
            $validated['gpu_vga'] = null;
            $validated['monitor'] = null;
            $validated['sistem_operasi'] = null;
        }
        $validated['min_stok'] = $validated['min_stok'] ?? 0;

        $autoGenerateUnits = $request->boolean('auto_generate_units');
        unset($validated['auto_generate_units']);

        // Upload dan kompres foto secara otomatis
        if ($request->hasFile('foto')) {
            $validated['foto'] = ImageOptimizer::optimizeAndStore($request->file('foto'), 'items');
        }

        $sarJurusan = Jurusan::where('kode', 'SAR')->first();
        $isSarUnit = $sarJurusan && $validated['jurusan_id'] == $sarJurusan->id;
        $penempatanSarpras = $request->input('penempatan_sarpras');

        if ($isSarUnit && $penempatanSarpras === 'gudang') {
            if (empty($validated['lokasi'])) {
                $validated['lokasi'] = 'Gudang Sarpras';
            } elseif (! str_contains(strtolower($validated['lokasi']), 'gudang')) {
                $validated['lokasi'] = 'Gudang Sarpras - '.$validated['lokasi'];
            }
        } elseif ($isSarUnit && $penempatanSarpras === 'umum') {
            if (empty($validated['lokasi'])) {
                $validated['lokasi'] = 'Fasilitas Umum Sekolah';
            }
        }
        unset($validated['penempatan_sarpras']);

        $item = Item::create($validated);

        // Jika jenis alat & opsi generate unit dipilih, buat item units fisik otomatis
        if ($item->jenis === 'alat' && $autoGenerateUnits && $item->jumlah > 0) {
            for ($i = 1; $i <= $item->jumlah; $i++) {
                $unitCode = sprintf('%s-%02d', $item->kode_barang, $i);
                $nomorMeja = $item->is_computer ? sprintf('Meja PC-%02d', $i) : null;

                ItemUnit::create([
                    'item_id' => $item->id,
                    'jurusan_id' => $item->jurusan_id,
                    'unit_code' => $unitCode,
                    'nomor_meja' => $nomorMeja,
                    'processor' => $item->is_computer ? $item->processor : null,
                    'ram' => $item->is_computer ? $item->ram : null,
                    'storage' => $item->is_computer ? $item->storage : null,
                    'gpu_vga' => $item->is_computer ? $item->gpu_vga : null,
                    'monitor' => $item->is_computer ? $item->monitor : null,
                    'sistem_operasi' => $item->is_computer ? $item->sistem_operasi : null,
                    'kondisi' => $item->kondisi,
                    'status' => 'tersedia',
                    'lokasi_penempatan' => $item->lokasi,
                    'tanggal_masuk' => now()->toDateString(),
                ]);
            }
        }

        $successMsg = "Barang '{$item->nama_barang}' (Kode: {$item->kode_barang}) berhasil ditambahkan";
        if ($isSarUnit) {
            $successMsg .= ($penempatanSarpras === 'gudang')
                ? ' ke Stok di Gudang Sarpras.'
                : ' ke Inventaris Fasilitas Umum Sekolah.';
        } else {
            $successMsg .= ' ke inventaris.';
        }

        return redirect()->route('items.show', $item)
            ->with('success', $successMsg);
    }

    /**
     * Tampilkan detail barang.
     */
    public function show(Item $item, Request $request): View
    {
        $user = $request->user();
        $sarJurusan = Jurusan::where('kode', 'SAR')->first();
        $sarJurusanId = $sarJurusan?->id;

        if ($user->isJurusan() && $item->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki akses ke barang dari jurusan lain.');
        }

        $item->load([
            'jurusan',
            'category',
            'units.maintenanceLogs.user',
            'usages.user',
            'restocks.user',
            'borrowings.itemUnit',
        ]);

        return view('items.show', compact('item'));
    }

    /**
     * Tampilkan formulir edit barang.
     */
    public function edit(Item $item, Request $request): View
    {
        $user = $request->user();
        if ($user->isJurusan() && $item->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak dapat mengedit barang dari jurusan lain.');
        }

        $categories = Category::all();
        $jurusans = Jurusan::all();
        $sarJurusan = Jurusan::where('kode', 'SAR')->first();
        $penempatanSarpras = (str_contains(strtolower($item->lokasi ?? ''), 'gudang') || $item->jenis === 'bahan') ? 'gudang' : 'umum';

        return view('items.edit', compact('item', 'categories', 'jurusans', 'user', 'sarJurusan', 'penempatanSarpras'));
    }

    /**
     * Simpan pembaruan data barang.
     */
    public function update(Request $request, Item $item): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $item->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak dapat memperbarui barang dari jurusan lain.');
        }

        if ($request->has('jumlah')) {
            $request->merge([
                'jumlah' => str_replace(',', '.', (string) $request->input('jumlah')),
            ]);
        }
        if ($request->has('min_stok')) {
            $request->merge([
                'min_stok' => str_replace(',', '.', (string) $request->input('min_stok')),
            ]);
        }

        $rules = [
            'kode_barang' => ['required', 'string', 'max:50', 'unique:items,kode_barang,'.$item->id],
            'nama_barang' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'jumlah' => ['required', 'numeric', 'min:0'],
            'satuan' => ['required', 'string', 'max:30'],
            'kondisi' => ['required', 'in:baik,rusak_ringan,rusak_berat'],
            'jenis' => ['required', 'in:alat,bahan'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'sumber_dana' => ['nullable', 'string', 'max:100'],
            'tahun_pengadaan' => ['nullable', 'integer', 'min:1990', 'max:'.(date('Y') + 1)],
            'spesifikasi' => ['nullable', 'string'],
            'min_stok' => ['nullable', 'numeric', 'min:0'],
            'penempatan_sarpras' => ['nullable', 'in:gudang,umum'],

            // Spesifikasi Komputer
            'is_computer' => ['nullable', 'boolean'],
            'processor' => ['nullable', 'string', 'max:255'],
            'ram' => ['nullable', 'string', 'max:100'],
            'storage' => ['nullable', 'string', 'max:100'],
            'gpu_vga' => ['nullable', 'string', 'max:100'],
            'monitor' => ['nullable', 'string', 'max:100'],
            'sistem_operasi' => ['nullable', 'string', 'max:100'],
            // Foto Fisik Barang
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'hapus_foto' => ['nullable', 'boolean'],
        ];

        if ($user->isSarprasOrKepalaSekolah() || ! $user->jurusan_id) {
            $rules['jurusan_id'] = ['required', 'exists:jurusans,id'];
        }

        $validated = $request->validate($rules, [
            'foto.image' => 'File foto harus berupa gambar (JPG, PNG, atau WEBP).',
            'foto.max' => 'Ukuran file foto maksimal 3 MB.',
        ]);

        $komCategory = Category::where('kode', 'KOM')->first();
        $isKomCategory = $komCategory && ! empty($validated['category_id']) && (int) $validated['category_id'] === $komCategory->id;

        // User dapat memilih apakah barang kategori KOM adalah Komputer (true) atau Perangkat IT Non-Komputer (false)
        $isComputerChoice = $request->has('is_computer') ? $request->boolean('is_computer') : ($item->is_computer ?? true);
        $validated['is_computer'] = $isKomCategory && $isComputerChoice;

        // Jika bukan komputer, kosongkan spesifikasi hardware komputer
        if (! $validated['is_computer']) {
            $validated['processor'] = null;
            $validated['ram'] = null;
            $validated['storage'] = null;
            $validated['gpu_vga'] = null;
            $validated['monitor'] = null;
            $validated['sistem_operasi'] = null;
        }
        $validated['min_stok'] = $validated['min_stok'] ?? 0;

        // Kelola file foto
        if ($request->boolean('hapus_foto')) {
            if ($item->foto && Storage::disk('public')->exists($item->foto)) {
                Storage::disk('public')->delete($item->foto);
            }
            $validated['foto'] = null;
        } elseif ($request->hasFile('foto')) {
            if ($item->foto && Storage::disk('public')->exists($item->foto)) {
                Storage::disk('public')->delete($item->foto);
            }
            $validated['foto'] = ImageOptimizer::optimizeAndStore($request->file('foto'), 'items');
        }
        unset($validated['hapus_foto']);

        $sarJurusan = Jurusan::where('kode', 'SAR')->first();
        $isSarUnit = $sarJurusan && ($validated['jurusan_id'] ?? $item->jurusan_id) == $sarJurusan->id;
        $penempatanSarpras = $request->input('penempatan_sarpras');

        if ($isSarUnit && $penempatanSarpras === 'gudang') {
            if (empty($validated['lokasi'])) {
                $validated['lokasi'] = 'Gudang Sarpras';
            } elseif (! str_contains(strtolower($validated['lokasi']), 'gudang')) {
                $validated['lokasi'] = 'Gudang Sarpras - '.$validated['lokasi'];
            }
        } elseif ($isSarUnit && $penempatanSarpras === 'umum') {
            if (empty($validated['lokasi'])) {
                $validated['lokasi'] = 'Fasilitas Umum Sekolah';
            }
        }
        unset($validated['penempatan_sarpras']);

        $item->update($validated);

        return redirect()->route('items.show', $item)
            ->with('success', "Data barang '{$item->nama_barang}' berhasil diperbarui.");
    }

    /**
     * Hapus barang dari inventaris.
     */
    public function destroy(Item $item, Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $item->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak dapat menghapus barang dari jurusan lain.');
        }

        // Hapus file foto dari storage jika ada
        if ($item->foto && Storage::disk('public')->exists($item->foto)) {
            Storage::disk('public')->delete($item->foto);
        }

        $namaBarang = $item->nama_barang;
        $item->delete();

        return redirect()->route('items.index')
            ->with('success', "Barang '{$namaBarang}' berhasil dihapus dari inventaris.");
    }
}
