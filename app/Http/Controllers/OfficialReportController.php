<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemRestock;
use App\Models\ItemUnit;
use App\Models\Jurusan;
use App\Models\OfficialReport;
use App\Models\OfficialReportItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OfficialReportController extends Controller
{
    /**
     * Tampilkan daftar seluruh Berita Acara.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = OfficialReport::with(['user', 'jurusan', 'items'])->latest('tanggal');

        // Jika user adalah jurusan, filter dokumen yang terkait dengan jurusannya
        if ($user->isJurusan()) {
            $query->where('jurusan_id', $user->jurusan_id);
        } elseif ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('judul', 'like', "%{$search}%")
                    ->orWhere('pihak_pertama_nama', 'like', "%{$search}%")
                    ->orWhere('pihak_kedua_nama', 'like', "%{$search}%");
            });
        }

        $reports = $query->paginate(10)->withQueryString();
        $jurusans = Jurusan::all();

        // Scope statistik
        $baseStatQuery = OfficialReport::query();
        if ($user->isJurusan()) {
            $baseStatQuery->where('jurusan_id', $user->jurusan_id);
        }

        $stats = [
            'total' => (clone $baseStatQuery)->count(),
            'serah_terima' => (clone $baseStatQuery)->where('jenis', 'serah_terima')->count(),
            'barang_rusak' => (clone $baseStatQuery)->where('jenis', 'barang_rusak')->count(),
            'penjualan' => (clone $baseStatQuery)->where('jenis', 'penjualan')->count(),
            'total_penjualan' => (clone $baseStatQuery)->where('jenis', 'penjualan')->sum('total_nominal'),
        ];

        return view('official_reports.index', compact('reports', 'jurusans', 'stats'));
    }

    /**
     * Tampilkan form pembuatan Berita Acara baru.
     */
    public function create(Request $request): View
    {
        $user = $request->user() ?? auth()->user();
        $jurusans = Jurusan::orderBy('nama')->get();
        $categories = Category::orderBy('nama')->get();
        $items = Item::with('category')->orderBy('nama_barang')->get();
        $units = ItemUnit::with('item')->where('kondisi', '!=', 'baik')->orWhere('status', '!=', 'tersedia')->get();

        // Data default Pihak Pertama (Sarpras) & Mengetahui (Kepala Sekolah)
        $sarprasUser = User::where('role', 'sarpras')->first();
        $sarprasUnit = Jurusan::where('kode', 'SAR')->orWhere('nama', 'like', '%Sarpras%')->first();
        $kepsekUser = User::where('role', 'kepala_sekolah')->first();

        $defaultPihakPertamaNama = $sarprasUnit->kepala_bengkel ?? ($sarprasUser->name ?? ($user->name ?? 'Waka Bidang Sarana & Prasarana'));
        $defaultPihakPertamaJabatan = 'Waka Bidang Sarana & Prasarana';
        $defaultPihakPertamaNip = $sarprasUnit->nip ?? ($sarprasUser->nip ?? null);

        $defaultMengetahuiNama = $kepsekUser->name ?? 'Bpk. Kepala Sekolah, M.Pd';
        $defaultMengetahuiJabatan = 'Kepala SMK Dr. Sutomo Temanggung';
        $defaultMengetahuiNip = $kepsekUser->nip ?? null;

        // Jika user adalah jurusan, sesuaikan Pihak Pertama atau Pihak Kedua
        $defaultPihakKeduaNama = '';
        $defaultPihakKeduaJabatan = 'Kepala Bengkel / Laboratorium';
        $defaultPihakKeduaNip = '';

        if ($user->isJurusan()) {
            $userJurusan = $user->jurusan;
            if ($userJurusan) {
                $jurusans = Jurusan::where('id', $user->jurusan_id)->get();
                $defaultPihakKeduaNama = $userJurusan->kepala_bengkel ?? ($user->name ?? '');
                $defaultPihakKeduaJabatan = 'Kepala ' . $userJurusan->nama;
                $defaultPihakKeduaNip = $userJurusan->nip ?? ($user->nip ?? '');
            }
        }

        // Rekomendasi nomor surat otomatis
        $tahun = date('Y');
        $bulanRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ][(int) date('n')];

        $nextNumber = str_pad(OfficialReport::whereYear('tanggal', $tahun)->count() + 1, 3, '0', STR_PAD_LEFT);
        $suggestedNumberSerahTerima = "{$nextNumber}/BAST/SMK-DS/{$bulanRomawi}/{$tahun}";
        $suggestedNumberRusak = "{$nextNumber}/BA-RUSAK/SMK-DS/{$bulanRomawi}/{$tahun}";
        $suggestedNumberJual = "{$nextNumber}/BA-LELANG/SMK-DS/{$bulanRomawi}/{$tahun}";

        return view('official_reports.create', compact(
            'user',
            'jurusans',
            'categories',
            'items',
            'suggestedNumberSerahTerima',
            'suggestedNumberRusak',
            'suggestedNumberJual',
            'defaultPihakPertamaNama',
            'defaultPihakPertamaJabatan',
            'defaultPihakPertamaNip',
            'defaultPihakKeduaNama',
            'defaultPihakKeduaJabatan',
            'defaultPihakKeduaNip',
            'defaultMengetahuiNama',
            'defaultMengetahuiJabatan',
            'defaultMengetahuiNip'
        ));
    }

    /**
     * Simpan Berita Acara baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        // Jika user adalah jurusan, batasi hanya boleh membuat berita acara barang rusak
        $allowedJenis = 'in:serah_terima,barang_rusak,penjualan';
        if ($request->user()->isJurusan()) {
            $allowedJenis = 'in:barang_rusak';
            if ($request->jenis !== 'barang_rusak') {
                return back()->withInput()->with('error', 'Akun Unit Kerja / Jurusan hanya berwenang membuat Berita Acara Barang Rusak.');
            }
        }

        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:100|unique:official_reports,nomor_surat',
            'jenis' => ['required', $allowedJenis],
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'pihak_pertama_nama' => 'required|string|max:150',
            'pihak_pertama_jabatan' => 'required|string|max:150',
            'pihak_pertama_nip' => 'nullable|string|max:50',
            'pihak_kedua_nama' => 'required|string|max:150',
            'pihak_kedua_jabatan' => 'required|string|max:150',
            'pihak_kedua_peran' => 'nullable|string|max:50',
            'pihak_kedua_nip' => 'nullable|string|max:50',
            'pihak_kedua_instansi' => 'nullable|string|max:150',
            'pihak_kedua_kontak' => 'nullable|string|max:50',
            'mengetahui_nama' => 'nullable|string|max:150',
            'mengetahui_jabatan' => 'nullable|string|max:150',
            'mengetahui_nip' => 'nullable|string|max:50',
            'latar_belakang' => 'nullable|string',
            'catatan' => 'nullable|string',
            'file_lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

            // Rincian barang
            'items' => 'required|array|min:1',
            'items.*.tipe_barang' => 'nullable|string|in:existing,new',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.category_id' => 'nullable|exists:categories,id',
            'items.*.is_computer' => 'nullable|boolean',
            'items.*.jenis' => 'nullable|in:alat,bahan',
            'items.*.lokasi' => 'nullable|string|max:255',
            'items.*.spesifikasi' => 'nullable|string',
            'items.*.nama_barang' => 'required|string|max:200',
            'items.*.kode_barang' => 'nullable|string|max:100',
            'items.*.unit_code' => 'nullable|string|max:100',
            'items.*.nomor_seri' => 'nullable|string|max:100',
            'items.*.jumlah' => 'required|numeric|min:0',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.kondisi_saat_lapor' => 'nullable|string|max:100',
            'items.*.harga_satuan' => 'nullable|numeric|min:0',
            'items.*.keterangan' => 'nullable|string|max:255',
            'status_dokumen' => 'nullable|in:draft,selesai',
        ]);

        $filePath = null;
        if ($request->hasFile('file_lampiran')) {
            $filePath = $request->file('file_lampiran')->store('official_reports', 'public');
        }

        DB::transaction(function () use ($request, $validated, $filePath) {
            $totalNominal = 0;

            if ($validated['jenis'] === 'penjualan') {
                foreach ($request->items as $row) {
                    $qty = (int) ($row['jumlah'] ?? 1);
                    $price = (float) ($row['harga_satuan'] ?? 0);
                    $totalNominal += ($qty * $price);
                }
            }

            $report = OfficialReport::create([
                'nomor_surat' => $validated['nomor_surat'],
                'jenis' => $validated['jenis'],
                'judul' => $validated['judul'],
                'tanggal' => $validated['tanggal'],
                'user_id' => $request->user()->id,
                'jurusan_id' => $validated['jurusan_id'] ?? null,
                'pihak_pertama_nama' => $validated['pihak_pertama_nama'],
                'pihak_pertama_jabatan' => $validated['pihak_pertama_jabatan'],
                'pihak_pertama_nip' => $validated['pihak_pertama_nip'] ?? null,
                'pihak_kedua_nama' => $validated['pihak_kedua_nama'],
                'pihak_kedua_jabatan' => $validated['pihak_kedua_jabatan'],
                'pihak_kedua_peran' => $validated['pihak_kedua_peran'] ?? ($request->input('pihak_kedua_peran') ?? ($validated['jenis'] === 'penjualan' ? 'pembeli' : null)),
                'pihak_kedua_nip' => $validated['pihak_kedua_nip'] ?? null,
                'pihak_kedua_instansi' => $validated['pihak_kedua_instansi'] ?? null,
                'pihak_kedua_kontak' => $validated['pihak_kedua_kontak'] ?? null,
                'mengetahui_nama' => $validated['mengetahui_nama'] ?? null,
                'mengetahui_jabatan' => $validated['mengetahui_jabatan'] ?? null,
                'mengetahui_nip' => $validated['mengetahui_nip'] ?? null,
                'latar_belakang' => $validated['latar_belakang'] ?? null,
                'total_nominal' => $totalNominal,
                'status_dokumen' => $request->input('status_dokumen', 'selesai'),
                'file_lampiran' => $filePath,
                'catatan' => $validated['catatan'] ?? null,
            ]);

            foreach ($request->items as $itemData) {
                $qty = (float) str_replace(',', '.', (string) ($itemData['jumlah'] ?? 1));
                $price = (float) ($itemData['harga_satuan'] ?? 0);
                $subtotal = $qty * $price;

                OfficialReportItem::create([
                    'official_report_id' => $report->id,
                    'item_id' => $itemData['item_id'] ?? null,
                    'item_unit_id' => $itemData['item_unit_id'] ?? null,
                    'tipe_barang' => $itemData['tipe_barang'] ?? 'new',
                    'category_id' => $itemData['category_id'] ?? null,
                    'is_computer' => !empty($itemData['is_computer']),
                    'jenis' => $itemData['jenis'] ?? 'alat',
                    'lokasi' => $itemData['lokasi'] ?? null,
                    'spesifikasi' => $itemData['spesifikasi'] ?? null,
                    'kode_barang' => $itemData['kode_barang'] ?? null,
                    'nama_barang' => $itemData['nama_barang'],
                    'unit_code' => $itemData['unit_code'] ?? null,
                    'nomor_seri' => $itemData['nomor_seri'] ?? null,
                    'jumlah' => $qty,
                    'satuan' => $itemData['satuan'] ?? 'unit',
                    'kondisi_saat_lapor' => $itemData['kondisi_saat_lapor'] ?? 'baik',
                    'harga_satuan' => $price,
                    'subtotal' => $subtotal,
                    'keterangan' => $itemData['keterangan'] ?? null,
                ]);
            }
        });

        $message = $request->input('status_dokumen') === 'draft'
            ? 'Draft Berita Acara berhasil disimpan. Unit kerja dapat mulai melakukan checklist fisik barang.'
            : 'Berita Acara berhasil dibuat dan tercatat resmi.';

        return redirect()->route('official-reports.index')->with('success', $message);
    }

    /**
     * Tampilkan detail Berita Acara.
     */
    public function show(OfficialReport $officialReport): View
    {
        $officialReport->load(['user', 'jurusan', 'items.category', 'approver', 'lockedBy', 'syncedBy']);
        $categories = Category::orderBy('nama')->get();
        $kepsekUser = User::where('role', 'kepala_sekolah')->first();
        $sarprasUser = User::where('role', 'sarpras')->first();
        $sarprasUnit = Jurusan::where('kode', 'SAR')->orWhere('nama', 'like', '%Sarpras%')->first();
        $jurusanUser = $officialReport->jurusan_id ? User::where('role', 'jurusan')->where('jurusan_id', $officialReport->jurusan_id)->first() : null;

        return view('official_reports.show', compact('officialReport', 'kepsekUser', 'sarprasUser', 'sarprasUnit', 'jurusanUser'));
    }

    /**
     * Tampilkan form edit Berita Acara (Hanya Akun Sarpras).
     */
    public function edit(OfficialReport $officialReport, Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->isStaffSarpras()) {
            abort(403, 'Hanya tim Sarpras yang berwenang mengedit Berita Acara.');
        }

        if ($officialReport->is_locked) {
            return redirect()->route('official-reports.show', $officialReport)
                ->with('error', 'Berita Acara ini telah dikunci. Buka kunci dokumen terlebih dahulu jika ingin mengedit data.');
        }

        if ($officialReport->is_synced) {
            return redirect()->route('official-reports.show', $officialReport)
                ->with('error', 'Berita Acara telah disinkronisasikan ke data barang master dan tidak dapat diedit lagi.');
        }

        $officialReport->load(['items.category', 'jurusan']);
        $jurusans = Jurusan::orderBy('nama')->get();
        $categories = Category::orderBy('nama')->get();
        $items = Item::with('category')->orderBy('nama_barang')->get();

        return view('official_reports.edit', compact('officialReport', 'jurusans', 'categories', 'items', 'user'));
    }

    /**
     * Perbarui data Berita Acara (Hanya Akun Sarpras).
     */
    public function update(Request $request, OfficialReport $officialReport): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isStaffSarpras()) {
            abort(403, 'Hanya tim Sarpras yang berwenang memperbarui Berita Acara.');
        }

        if ($officialReport->is_locked) {
            return back()->with('error', 'Berita Acara telah dikunci dan tidak dapat diedit kembali.');
        }

        if ($officialReport->is_synced) {
            return back()->with('error', 'Berita Acara telah disinkronisasikan ke data barang.');
        }

        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:100|unique:official_reports,nomor_surat,' . $officialReport->id,
            'jenis' => 'required|in:serah_terima,barang_rusak,penjualan',
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'pihak_pertama_nama' => 'required|string|max:150',
            'pihak_pertama_jabatan' => 'required|string|max:150',
            'pihak_pertama_nip' => 'nullable|string|max:50',
            'pihak_kedua_nama' => 'required|string|max:150',
            'pihak_kedua_jabatan' => 'required|string|max:150',
            'pihak_kedua_peran' => 'nullable|string|max:50',
            'pihak_kedua_nip' => 'nullable|string|max:50',
            'pihak_kedua_instansi' => 'nullable|string|max:150',
            'pihak_kedua_kontak' => 'nullable|string|max:50',
            'mengetahui_nama' => 'nullable|string|max:150',
            'mengetahui_jabatan' => 'nullable|string|max:150',
            'mengetahui_nip' => 'nullable|string|max:50',
            'latar_belakang' => 'nullable|string',
            'catatan' => 'nullable|string',
            'file_lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

            // Rincian barang
            'items' => 'required|array|min:1',
            'items.*.tipe_barang' => 'nullable|string|in:existing,new',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.category_id' => 'nullable|exists:categories,id',
            'items.*.is_computer' => 'nullable|boolean',
            'items.*.jenis' => 'nullable|in:alat,bahan',
            'items.*.lokasi' => 'nullable|string|max:255',
            'items.*.spesifikasi' => 'nullable|string',
            'items.*.nama_barang' => 'required|string|max:200',
            'items.*.kode_barang' => 'nullable|string|max:100',
            'items.*.unit_code' => 'nullable|string|max:100',
            'items.*.nomor_seri' => 'nullable|string|max:100',
            'items.*.jumlah' => 'required|numeric|min:0',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.kondisi_saat_lapor' => 'nullable|string|max:100',
            'items.*.harga_satuan' => 'nullable|numeric|min:0',
            'items.*.keterangan' => 'nullable|string|max:255',
            'status_dokumen' => 'nullable|in:draft,selesai',
        ]);

        $filePath = $officialReport->file_lampiran;
        if ($request->hasFile('file_lampiran')) {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file_lampiran')->store('official_reports', 'public');
        }

        DB::transaction(function () use ($request, $validated, $filePath, $officialReport) {
            $totalNominal = 0;

            if ($validated['jenis'] === 'penjualan') {
                foreach ($request->items as $row) {
                    $qty = (int) ($row['jumlah'] ?? 1);
                    $price = (float) ($row['harga_satuan'] ?? 0);
                    $totalNominal += ($qty * $price);
                }
            }

            $officialReport->update([
                'nomor_surat' => $validated['nomor_surat'],
                'jenis' => $validated['jenis'],
                'judul' => $validated['judul'],
                'tanggal' => $validated['tanggal'],
                'jurusan_id' => $validated['jurusan_id'] ?? null,
                'pihak_pertama_nama' => $validated['pihak_pertama_nama'],
                'pihak_pertama_jabatan' => $validated['pihak_pertama_jabatan'],
                'pihak_pertama_nip' => $validated['pihak_pertama_nip'] ?? null,
                'pihak_kedua_nama' => $validated['pihak_kedua_nama'],
                'pihak_kedua_jabatan' => $validated['pihak_kedua_jabatan'],
                'pihak_kedua_peran' => $validated['pihak_kedua_peran'] ?? ($request->input('pihak_kedua_peran') ?? ($validated['jenis'] === 'penjualan' ? 'pembeli' : null)),
                'pihak_kedua_nip' => $validated['pihak_kedua_nip'] ?? null,
                'pihak_kedua_instansi' => $validated['pihak_kedua_instansi'] ?? null,
                'pihak_kedua_kontak' => $validated['pihak_kedua_kontak'] ?? null,
                'mengetahui_nama' => $validated['mengetahui_nama'] ?? null,
                'mengetahui_jabatan' => $validated['mengetahui_jabatan'] ?? null,
                'mengetahui_nip' => $validated['mengetahui_nip'] ?? null,
                'latar_belakang' => $validated['latar_belakang'] ?? null,
                'total_nominal' => $totalNominal,
                'status_dokumen' => $request->input('status_dokumen', $officialReport->status_dokumen ?? 'selesai'),
                'file_lampiran' => $filePath,
                'catatan' => $validated['catatan'] ?? null,
            ]);

            // Hapus items lama dan ganti dengan items baru
            $officialReport->items()->delete();

            foreach ($request->items as $itemData) {
                $qty = (float) str_replace(',', '.', (string) ($itemData['jumlah'] ?? 1));
                $price = (float) ($itemData['harga_satuan'] ?? 0);
                $subtotal = $qty * $price;

                OfficialReportItem::create([
                    'official_report_id' => $officialReport->id,
                    'item_id' => $itemData['item_id'] ?? null,
                    'item_unit_id' => $itemData['item_unit_id'] ?? null,
                    'tipe_barang' => $itemData['tipe_barang'] ?? 'new',
                    'category_id' => $itemData['category_id'] ?? null,
                    'is_computer' => !empty($itemData['is_computer']),
                    'jenis' => $itemData['jenis'] ?? 'alat',
                    'lokasi' => $itemData['lokasi'] ?? null,
                    'spesifikasi' => $itemData['spesifikasi'] ?? null,
                    'kode_barang' => $itemData['kode_barang'] ?? null,
                    'nama_barang' => $itemData['nama_barang'],
                    'unit_code' => $itemData['unit_code'] ?? null,
                    'nomor_seri' => $itemData['nomor_seri'] ?? null,
                    'jumlah' => $qty,
                    'satuan' => $itemData['satuan'] ?? 'unit',
                    'kondisi_saat_lapor' => $itemData['kondisi_saat_lapor'] ?? 'baik',
                    'harga_satuan' => $price,
                    'subtotal' => $subtotal,
                    'keterangan' => $itemData['keterangan'] ?? null,
                ]);
            }
        });

        return redirect()->route('official-reports.show', $officialReport)->with('success', 'Berita Acara berhasil diperbarui.');
    }

    /**
     * Hapus arsip Berita Acara dengan rollback otomatis jika sudah sinkron ke inventaris.
     */
    public function destroy(OfficialReport $officialReport, Request $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Cek otorisasi dasar
        if ($user->isJurusan() && $officialReport->jurusan_id && $user->jurusan_id !== $officialReport->jurusan_id) {
            abort(403, 'Anda tidak berwenang menghapus Berita Acara unit kerja lain.');
        }

        $isAccSarpras = ($officialReport->ttd_pihak_pertama !== null);

        // Jika user adalah jurusan, dilarang hapus jika sudah di-ACC sarpras, sudah dikunci, atau sudah sinkron
        if (! $user->isStaffSarpras()) {
            if ($officialReport->is_locked) {
                return back()->with('error', 'Berita Acara ini telah dikunci dan tidak dapat dihapus oleh akun Unit Kerja.');
            }
            if ($officialReport->is_synced) {
                return back()->with('error', 'Berita Acara ini telah disinkronisasikan ke inventaris dan hanya dapat dihapus/di-rollback oleh Tim Sarpras.');
            }
            if ($isAccSarpras) {
                abort(403, 'Berita Acara yang telah ditandatangani/di-ACC oleh Sarpras tidak dapat dihapus oleh akun Jurusan / Unit Kerja.');
            }
        }

        // 2. Jika Berita Acara SUDAH disinkronkan ke Master Data Barang (is_synced = true),
        // jalankan validasi dependensi transaksi & rollback otomatis stok/unit fisik.
        if ($officialReport->is_synced) {
            $officialReport->load(['items.item']);
            $unitKeteranganMatch = 'dari BAST ' . $officialReport->nomor_surat;
            $restockCatatanMatch = 'Penerimaan barang dari BAST ' . $officialReport->nomor_surat;

            // Tahap A: Pengecekan Ketergantungan (Dependency Check)
            foreach ($officialReport->items as $reportItem) {
                $item = $reportItem->item;
                if (! $item) {
                    continue;
                }

                // Cari unit-unit fisik yang pernah digenerate oleh BAST ini
                $createdUnits = ItemUnit::where('item_id', $item->id)
                    ->where(function ($q) use ($unitKeteranganMatch) {
                        $q->where('keterangan', 'like', "%{$unitKeteranganMatch}%")
                          ->orWhere('catatan', 'like', "%{$unitKeteranganMatch}%");
                    })
                    ->get();

                foreach ($createdUnits as $unit) {
                    if ($unit->borrowings()->count() > 0) {
                        return back()->with('error', "Gagal menghapus: Unit fisik [{$unit->unit_code}] dari barang [{$item->nama_barang}] memiliki riwayat peminjaman di sistem. Hapus atau selesaikan data peminjaman terlebih dahulu.");
                    }
                    if ($unit->maintenanceLogs()->count() > 0) {
                        return back()->with('error', "Gagal menghapus: Unit fisik [{$unit->unit_code}] dari barang [{$item->nama_barang}] memiliki riwayat pemeliharaan/perbaikan. Hapus log pemeliharaan terlebih dahulu.");
                    }
                }

                // Jika barang ini dibuat baru sepenuhnya oleh BAST ini
                if ($reportItem->tipe_barang !== 'existing') {
                    if ($item->borrowings()->count() > 0) {
                        return back()->with('error', "Gagal menghapus: Barang [{$item->nama_barang}] sudah memiliki riwayat peminjaman.");
                    }
                    if ($item->usages()->count() > 0) {
                        return back()->with('error', "Gagal menghapus: Bahan habis pakai [{$item->nama_barang}] sudah memiliki riwayat pemakaian barang.");
                    }
                }
            }

            // Tahap B: Eksekusi Rollback dalam Database Transaction
            try {
                DB::transaction(function () use ($officialReport, $unitKeteranganMatch, $restockCatatanMatch) {
                    foreach ($officialReport->items as $reportItem) {
                        $item = $reportItem->item;
                        if (! $item) {
                            continue;
                        }

                        $qty = (float) ($reportItem->jumlah_cek ?? $reportItem->jumlah);

                        if ($reportItem->tipe_barang === 'existing') {
                            // 1. Kurangi kembali stok item yang bertambah
                            $newJumlah = max(0, $item->jumlah - $qty);
                            $item->update(['jumlah' => $newJumlah]);

                            // 2. Hapus log restock yang sesuai dengan BAST ini
                            ItemRestock::where('item_id', $item->id)
                                ->where('catatan', 'like', "%{$restockCatatanMatch}%")
                                ->delete();

                            // 3. Hapus unit fisik yang dibuat dari BAST ini
                            ItemUnit::where('item_id', $item->id)
                                ->where(function ($q) use ($unitKeteranganMatch) {
                                    $q->where('keterangan', 'like', "%{$unitKeteranganMatch}%")
                                      ->orWhere('catatan', 'like', "%{$unitKeteranganMatch}%");
                                })
                                ->delete();
                        } else {
                            // Barang Baru: Hapus seluruh unit fisik, riwayat restock, dan master item-nya
                            ItemUnit::where('item_id', $item->id)->delete();
                            ItemRestock::where('item_id', $item->id)->delete();
                            $item->delete();
                        }
                    }
                });
            } catch (\Exception $e) {
                return back()->with('error', 'Terjadi kesalahan saat membatalkan (rollback) data inventaris: ' . $e->getMessage());
            }
        }

        // 3. Hapus file lampiran fisik jika ada
        if ($officialReport->file_lampiran && Storage::disk('public')->exists($officialReport->file_lampiran)) {
            Storage::disk('public')->delete($officialReport->file_lampiran);
        }

        // Hapus file tanda tangan jika ada
        foreach (['ttd_pihak_pertama', 'ttd_pihak_kedua', 'ttd_mengetahui'] as $sigField) {
            if ($officialReport->$sigField && Storage::disk('public')->exists($officialReport->$sigField)) {
                Storage::disk('public')->delete($officialReport->$sigField);
            }
        }

        $nomorSurat = $officialReport->nomor_surat;
        $isSyncedPrior = $officialReport->is_synced;
        $officialReport->delete();

        $pesanSukses = $isSyncedPrior
            ? "Arsip Berita Acara [{$nomorSurat}] berhasil dihapus dan seluruh data barang/stok di inventaris telah di-rollback secara otomatis."
            : "Arsip Berita Acara [{$nomorSurat}] berhasil dihapus.";

        return redirect()->route('official-reports.index')->with('success', $pesanSukses);
    }

    /**
     * Batalkan tanda tangan Berita Acara (Pihak Pertama / Sarpras atau Kepala Sekolah).
     */
    public function cancelSignature(OfficialReport $officialReport, Request $request): RedirectResponse
    {
        $user = $request->user();
        $type = $request->query('type', 'pihak_pertama');

        if ($type === 'pihak_pertama') {
            if (! $user->isStaffSarpras()) {
                abort(403, 'Hanya Sarpras yang berwenang membatalkan tanda tangan Pihak Pertama.');
            }

            $officialReport->update([
                'ttd_pihak_pertama' => null,
                'ttd_pihak_pertama_at' => null,
            ]);

            return back()->with('success', 'Tanda tangan Pihak Pertama (Sarpras) berhasil dibatalkan.');
        }

        if ($type === 'pihak_kedua') {
            if ($user->isJurusan() && $officialReport->jurusan_id && $user->jurusan_id !== $officialReport->jurusan_id) {
                abort(403, 'Anda tidak berwenang membatalkan tanda tangan Berita Acara jurusan lain.');
            }

            if (! $user->isJurusan() && ! $user->isStaffSarpras()) {
                abort(403, 'Hanya Jurusan atau Sarpras yang berwenang membatalkan tanda tangan Pihak Kedua.');
            }

            $officialReport->update([
                'ttd_pihak_kedua' => null,
                'ttd_pihak_kedua_at' => null,
            ]);

            return back()->with('success', 'Tanda tangan Pihak Kedua (Jurusan / Penerima) berhasil dibatalkan.');
        }

        if ($type === 'kepsek') {
            if (! $user->isKepalaSekolah() && ! $user->isSarpras()) {
                abort(403, 'Hanya Kepala Sekolah atau Sarpras yang berwenang membatalkan pengesahan Kepala Sekolah.');
            }

            $officialReport->update([
                'status_approval' => 'menunggu',
                'approved_by' => null,
                'approved_at' => null,
                'catatan_approval' => null,
                'ttd_mengetahui' => null,
                'ttd_mengetahui_at' => null,
            ]);

            return back()->with('success', 'Pengesahan dan tanda tangan Kepala Sekolah berhasil dibatalkan. Status Berita Acara kembali Menunggu ACC.');
        }

        return back()->with('error', 'Tipe pembatalan tanda tangan tidak valid.');
    }

    /**
     * Setujui (ACC) Berita Acara oleh Kepala Sekolah beserta Tanda Tangan Digital.
     */
    public function approve(Request $request, OfficialReport $officialReport): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isKepalaSekolah() && ! $user->isSarpras()) {
            abort(403, 'Hanya Kepala Sekolah atau Sarpras yang dapat melakukan persetujuan/ACC.');
        }

        $request->validate([
            'catatan_approval' => ['nullable', 'string', 'max:500'],
            'signature_data' => ['nullable', 'string'], // Base64 data URL from signature pad
            'use_saved_signature' => ['nullable', 'boolean'],
            'save_signature_profile' => ['nullable', 'boolean'],
        ]);

        $kepsekUser = User::where('role', 'kepala_sekolah')->first();

        $signaturePath = $officialReport->ttd_mengetahui;
        if ($request->boolean('use_saved_signature')) {
            $signaturePath = ($kepsekUser?->signature) ?: ($user->signature ?: $signaturePath);
        } elseif ($request->filled('signature_data')) {
            $signaturePath = $this->saveBase64Signature($request->signature_data, 'signatures/kepsek');
            if ($request->boolean('save_signature_profile')) {
                if ($kepsekUser) {
                    $kepsekUser->update(['signature' => $signaturePath]);
                }
                if ($user->isKepalaSekolah()) {
                    $user->update(['signature' => $signaturePath]);
                }
            }
        }

        $officialReport->update([
            'status_approval' => 'disetujui',
            'approved_by' => $kepsekUser->id,
            'approved_at' => now(),
            'catatan_approval' => $request->catatan_approval,
            'ttd_mengetahui' => $signaturePath,
            'ttd_mengetahui_at' => $signaturePath ? now() : $officialReport->ttd_mengetahui_at,
        ]);

        return back()->with('success', 'Berita Acara berhasil disetujui (ACC) dan ditandatangani.');
    }

    /**
     * Tolak / minta revisi Berita Acara oleh Kepala Sekolah.
     */
    public function reject(Request $request, OfficialReport $officialReport): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isKepalaSekolah() && ! $user->isSarpras()) {
            abort(403, 'Hanya Kepala Sekolah atau Sarpras yang dapat memproses penolakan.');
        }

        $request->validate([
            'catatan_approval' => ['required', 'string', 'max:500'],
        ]);

        $kepsekUser = User::where('role', 'kepala_sekolah')->first() ?: $user;

        $officialReport->update([
            'status_approval' => 'ditolak',
            'approved_by' => $kepsekUser->id,
            'approved_at' => now(),
            'catatan_approval' => $request->catatan_approval,
        ]);

        return back()->with('success', 'Berita Acara ditolak dengan catatan evaluasi.');
    }

    /**
     * Tanda Tangan Digital oleh Pihak Pertama (Sarpras).
     */
    public function signPihakPertama(Request $request, OfficialReport $officialReport): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isStaffSarpras()) {
            abort(403, 'Hanya tim Sarpras yang dapat menandatangani sebagai Pihak Pertama.');
        }

        $request->validate([
            'signature_data' => ['nullable', 'string'],
            'use_saved_signature' => ['nullable', 'boolean'],
            'save_signature_profile' => ['nullable', 'boolean'],
        ]);

        $signaturePath = null;
        if ($request->boolean('use_saved_signature') && $user->signature) {
            $signaturePath = $user->signature;
        } elseif ($request->filled('signature_data')) {
            $signaturePath = $this->saveBase64Signature($request->signature_data, 'signatures/sarpras');
            if ($request->boolean('save_signature_profile')) {
                $user->update(['signature' => $signaturePath]);
            }
        }

        if (! $signaturePath) {
            return back()->with('error', 'Silakan goreskan tanda tangan atau pilih tanda tangan tersimpan.');
        }

        $officialReport->update([
            'ttd_pihak_pertama' => $signaturePath,
            'ttd_pihak_pertama_at' => now(),
        ]);

        return back()->with('success', 'Tanda tangan Pihak Pertama (Sarpras) berhasil disimpan.');
    }

    /**
     * Tanda Tangan Digital oleh Pihak Kedua (Jurusan / Penerima Barang).
     */
    public function signPihakKedua(Request $request, OfficialReport $officialReport): RedirectResponse
    {
        $user = $request->user();

        if ($user->isJurusan() && $officialReport->jurusan_id && $user->jurusan_id !== $officialReport->jurusan_id) {
            abort(403, 'Anda tidak berwenang menandatangani Berita Acara jurusan lain.');
        }

        if (! $user->isJurusan() && ! $user->isStaffSarpras()) {
            abort(403, 'Hanya Jurusan terkait atau Sarpras yang dapat menandatangani sebagai Pihak Kedua.');
        }

        $request->validate([
            'signature_data' => ['nullable', 'string'],
            'use_saved_signature' => ['nullable', 'boolean'],
            'save_signature_profile' => ['nullable', 'boolean'],
        ]);

        $jurusanUser = $officialReport->jurusan_id ? User::where('role', 'jurusan')->where('jurusan_id', $officialReport->jurusan_id)->first() : null;

        $signaturePath = null;
        if ($request->boolean('use_saved_signature')) {
            $signaturePath = ($jurusanUser?->signature) ?: ($user->signature ?: null);
        } elseif ($request->filled('signature_data')) {
            $signaturePath = $this->saveBase64Signature($request->signature_data, 'signatures/jurusan');
            if ($request->boolean('save_signature_profile')) {
                if ($jurusanUser) {
                    $jurusanUser->update(['signature' => $signaturePath]);
                }
                if ($user->isJurusan() || $user->isStaffSarpras()) {
                    $user->update(['signature' => $signaturePath]);
                }
            }
        }

        if (! $signaturePath) {
            return back()->with('error', 'Silakan goreskan tanda tangan atau pilih tanda tangan tersimpan.');
        }

        $officialReport->update([
            'ttd_pihak_kedua' => $signaturePath,
            'ttd_pihak_kedua_at' => now(),
        ]);

        return back()->with('success', 'Tanda tangan Pihak Kedua berhasil disimpan.');
    }

    /**
     * Helper simpan base64 canvas signature ke storage PNG.
     */
    private function saveBase64Signature(string $base64Data, string $folder): string
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $data = substr($base64Data, strpos($base64Data, ',') + 1);
            $type = strtolower($type[1]); // png, jpg, etc.

            $data = base64_decode($data);
            if ($data === false) {
                throw new \Exception('Gagal memproses data tanda tangan digital.');
            }

            $fileName = $folder . '/' . uniqid('ttd_') . '.' . $type;
            Storage::disk('public')->put($fileName, $data);

            return $fileName;
        }

        throw new \Exception('Format tanda tangan tidak valid.');
    }

    /**
     * Kunci Berita Acara (Hanya Akun Sarpras).
     */
    public function lock(OfficialReport $officialReport, Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isStaffSarpras()) {
            abort(403, 'Hanya tim Sarpras yang berwenang mengunci Berita Acara.');
        }

        $officialReport->update([
            'is_locked' => true,
            'locked_at' => now(),
            'locked_by' => $user->id,
            'status_dokumen' => 'selesai',
        ]);

        return back()->with('success', 'Berita Acara berhasil dikunci. Data dokumen dan rincian barang kini dilindungi dari perubahan.');
    }

    /**
     * Buka Kunci Berita Acara (Hanya Akun Sarpras).
     */
    public function unlock(OfficialReport $officialReport, Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isStaffSarpras()) {
            abort(403, 'Hanya tim Sarpras yang berwenang membuka kunci Berita Acara.');
        }

        if ($officialReport->is_synced) {
            return back()->with('error', 'Berita Acara ini telah dimasukkan/disinkronisasikan ke data barang master. Kunci tidak dapat dibuka.');
        }

        $officialReport->update([
            'is_locked' => false,
            'locked_at' => null,
            'locked_by' => null,
            'status_dokumen' => 'draft',
        ]);

        return back()->with('success', 'Kunci Berita Acara berhasil dibuka. Dokumen kembali dalam mode Draft/Revisi.');
    }

    /**
     * Update checklist verifikasi fisik per barang oleh Unit Kerja (atau Sarpras).
     */
    public function updateChecklist(Request $request, OfficialReport $officialReport): RedirectResponse
    {
        $user = $request->user();

        if ($user->isJurusan() && $officialReport->jurusan_id && $user->jurusan_id !== $officialReport->jurusan_id) {
            abort(403, 'Anda tidak berwenang melakukan checklist pada Berita Acara unit kerja lain.');
        }

        if ($officialReport->is_locked) {
            return back()->with('error', 'Berita Acara telah dikunci dan tidak dapat diubah hasil checklistnya.');
        }

        // Support both formats: $request->items (list with 'id') or $request->checks (keyed by id)
        $itemsData = $request->input('items', []);
        $checksMap = [];

        if (! empty($itemsData) && is_array($itemsData)) {
            foreach ($itemsData as $entry) {
                if (isset($entry['id'])) {
                    $checksMap[$entry['id']] = $entry;
                }
            }
        } else {
            $checksMap = $request->input('checks', []);
        }

        DB::transaction(function () use ($officialReport, $checksMap, $user) {
            foreach ($officialReport->items as $item) {
                $row = $checksMap[$item->id] ?? null;
                $isChecked = false;
                if ($row) {
                    $isChecked = isset($row['is_checked']) && in_array((string)$row['is_checked'], ['1', 'true', 'on'], true);
                }

                $kondisi = $row['kondisi_cek'] ?? ($item->kondisi_saat_lapor ?? 'baik');
                $jumlahCek = isset($row['jumlah_cek']) && $row['jumlah_cek'] !== ''
                    ? (float) str_replace(',', '.', (string) $row['jumlah_cek'])
                    : $item->jumlah;
                $catatanCek = $row['catatan_cek'] ?? null;

                $item->update([
                    'is_checked' => $isChecked,
                    'checked_at' => $isChecked ? ($item->checked_at ?? now()) : null,
                    'checked_by' => $isChecked ? ($item->checked_by ?? $user->id) : null,
                    'kondisi_cek' => $kondisi,
                    'jumlah_cek' => $jumlahCek,
                    'catatan_cek' => $catatanCek,
                ]);
            }
        });

        return back()->with('success', 'Hasil verifikasi checklist fisik barang berhasil disimpan.');
    }

    /**
     * Masukkan / Sinkronisasikan barang BAST ke Data Barang (Master Inventaris).
     */
    public function syncToInventory(Request $request, OfficialReport $officialReport): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isStaffSarpras()) {
            abort(403, 'Hanya tim Sarpras yang berwenang memasukkan data Berita Acara ke Master Inventaris.');
        }

        if ($officialReport->is_synced) {
            return back()->with('error', 'Data Berita Acara ini sudah pernah dimasukkan ke Master Barang sebelumnya.');
        }

        if ($officialReport->jenis !== 'serah_terima') {
            return back()->with('error', 'Hanya Berita Acara Serah Terima yang dapat disinkronisasikan ke data barang masuk.');
        }

        if (! $officialReport->jurusan_id) {
            return back()->with('error', 'Unit Kerja penerima belum ditentukan pada Berita Acara ini.');
        }

        $officialReport->load(['items.item', 'jurusan']);

        DB::transaction(function () use ($officialReport, $user) {
            foreach ($officialReport->items as $reportItem) {
                $qty = (float) ($reportItem->jumlah_cek ?? $reportItem->jumlah);
                if ($qty <= 0) {
                    continue;
                }

                // OPSI 1: Barang yang sudah ada di unit tersebut (Restock Stok Lama)
                if ($reportItem->tipe_barang === 'existing' && $reportItem->item_id) {
                    $existingItem = Item::find($reportItem->item_id);
                    if ($existingItem) {
                        $stokSebelum = $existingItem->jumlah;
                        $stokSesudah = $stokSebelum + $qty;

                        $existingItem->update([
                            'jumlah' => $stokSesudah,
                            'kondisi' => $reportItem->kondisi_cek ?? $existingItem->kondisi,
                        ]);

                        // Buat riwayat restock
                        ItemRestock::create([
                            'item_id' => $existingItem->id,
                            'jurusan_id' => $existingItem->jurusan_id,
                            'user_id' => Auth::id(),
                            'jumlah' => $qty,
                            'satuan' => $reportItem->satuan ?: ($existingItem->satuan ?: 'unit'),
                            'stok_sebelum' => $stokSebelum,
                            'stok_sesudah' => $stokSesudah,
                            'tanggal_masuk' => $officialReport->tanggal,
                            'sumber_dana' => 'BAST Serah Terima: ' . $officialReport->nomor_surat,
                            'pemasok' => $officialReport->pihak_pertama_nama,
                            'catatan' => 'Penerimaan barang dari BAST ' . $officialReport->nomor_surat,
                        ]);

                        // Jika jenisnya alat, tambahkan unit fisik sesuai jumlah jika ada
                        if ($existingItem->jenis === 'alat') {
                            $existingUnitsCount = $existingItem->units()->count();
                            for ($i = 1; $i <= (int) $qty; $i++) {
                                $unitNum = $existingUnitsCount + $i;
                                $unitCode = sprintf('%s-%02d', $existingItem->kode_barang, $unitNum);
                                ItemUnit::create([
                                    'item_id' => $existingItem->id,
                                    'jurusan_id' => $existingItem->jurusan_id,
                                    'unit_code' => $unitCode,
                                    'nomor_seri' => null,
                                    'nomor_meja' => $existingItem->is_computer ? sprintf('Meja PC-%02d', $unitNum) : null,
                                    'kondisi' => $reportItem->kondisi_cek ?? 'baik',
                                    'status' => 'tersedia',
                                    'tanggal_masuk' => $officialReport->tanggal,
                                    'lokasi_penempatan' => $reportItem->lokasi ?? $existingItem->lokasi,
                                    'keterangan' => 'Unit fisik dari BAST ' . $officialReport->nomor_surat,
                                ]);
                            }
                        }
                    }
                } else {
                    // OPSI 2: Barang Baru (Buat Master Item Baru di Unit Penerima)
                    $jurusanId = $officialReport->jurusan_id;
                    $categoryId = $reportItem->category_id;

                    // Generate kode barang otomatis
                    $generatedCode = $reportItem->kode_barang;
                    if (empty($generatedCode)) {
                        $generatedCode = $this->createItemCode($jurusanId, $categoryId);
                    }

                    $newItem = Item::create([
                        'kode_barang' => $generatedCode,
                        'nama_barang' => $reportItem->nama_barang,
                        'jurusan_id' => $jurusanId,
                        'category_id' => $categoryId,
                        'is_computer' => (bool) $reportItem->is_computer,
                        'jumlah' => $qty,
                        'satuan' => $reportItem->satuan ?: 'unit',
                        'kondisi' => $reportItem->kondisi_cek ?? 'baik',
                        'lokasi' => $reportItem->lokasi ?? 'Ruang Praktik / Bengkel',
                        'jenis' => $reportItem->jenis ?? 'alat',
                        'sumber_dana' => 'BAST Serah Terima: ' . $officialReport->nomor_surat,
                        'tahun_pengadaan' => (int) date('Y', strtotime($officialReport->tanggal)),
                        'spesifikasi' => $reportItem->spesifikasi ?? $reportItem->keterangan,
                        'min_stok' => 0,
                    ]);

                    // Update referensi item_id di report_items
                    $reportItem->update([
                        'item_id' => $newItem->id,
                        'kode_barang' => $generatedCode,
                    ]);

                    // Jika barang baru berjenis alat, buatkan item_units otomatis
                    if ($newItem->jenis === 'alat') {
                        for ($i = 1; $i <= (int) $qty; $i++) {
                            $unitCode = sprintf('%s-%02d', $newItem->kode_barang, $i);
                            ItemUnit::create([
                                'item_id' => $newItem->id,
                                'jurusan_id' => $newItem->jurusan_id,
                                'unit_code' => $unitCode,
                                'nomor_meja' => $newItem->is_computer ? sprintf('Meja PC-%02d', $i) : null,
                                'nomor_seri' => ($i === 1) ? $reportItem->nomor_seri : null,
                                'kondisi' => $reportItem->kondisi_cek ?? 'baik',
                                'status' => 'tersedia',
                                'tanggal_masuk' => $officialReport->tanggal,
                                'lokasi_penempatan' => $newItem->lokasi,
                                'keterangan' => 'Unit baru dari BAST ' . $officialReport->nomor_surat,
                            ]);
                        }
                    }
                }
            }

            // Tandai Berita Acara sebagai tersinkronisasi
            $officialReport->update([
                'is_synced' => true,
                'synced_at' => now(),
                'synced_by' => $user->id,
                'is_locked' => true,
                'locked_at' => $officialReport->locked_at ?? now(),
                'locked_by' => $officialReport->locked_by ?? $user->id,
            ]);
        });

        return back()->with('success', 'Seluruh barang serah terima berhasil dimasukkan ke Master Data Barang & Inventaris unit terkait.');
    }

    /**
     * API ambil daftar barang berdasarkan unit kerja terpilih.
     */
    public function getItemsByJurusan(Jurusan $jurusan): JsonResponse
    {
        $items = Item::with('category')
            ->where('jurusan_id', $jurusan->id)
            ->orderBy('nama_barang')
            ->get(['id', 'kode_barang', 'nama_barang', 'satuan', 'jumlah', 'kondisi', 'jenis', 'category_id', 'lokasi', 'spesifikasi']);

        return response()->json($items);
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
     * Cetak Berita Acara resmi (format Surat Dinas A4 dengan Kop Resmi).
     */
    public function print(OfficialReport $officialReport): View
    {
        $officialReport->load(['user', 'jurusan', 'items.category', 'approver', 'lockedBy', 'syncedBy']);
        $categories = Category::orderBy('nama')->get();

        return view('official_reports.print', compact('officialReport'));
    }
}
