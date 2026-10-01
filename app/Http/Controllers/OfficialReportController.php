<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\Jurusan;
use App\Models\OfficialReport;
use App\Models\OfficialReportItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $query = OfficialReport::with(['user', 'jurusan', 'items'])->latest('tanggal');

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
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

        $stats = [
            'total' => OfficialReport::count(),
            'barang_rusak' => OfficialReport::where('jenis', 'barang_rusak')->count(),
            'penjualan' => OfficialReport::where('jenis', 'penjualan')->count(),
            'total_penjualan' => OfficialReport::where('jenis', 'penjualan')->sum('total_nominal'),
        ];

        return view('official_reports.index', compact('reports', 'jurusans', 'stats'));
    }

    /**
     * Tampilkan form pembuatan Berita Acara baru.
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $jurusans = Jurusan::orderBy('nama')->get();
        $items = Item::with('category')->orderBy('nama_barang')->get();
        $units = ItemUnit::with('item')->where('kondisi', '!=', 'baik')->orWhere('status', '!=', 'tersedia')->get();

        // Rekomendasi nomor surat otomatis
        $tahun = date('Y');
        $bulanRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ][(int) date('n')];

        $nextNumber = str_pad(OfficialReport::whereYear('tanggal', $tahun)->count() + 1, 3, '0', STR_PAD_LEFT);
        $suggestedNumberRusak = "{$nextNumber}/BA-RUSAK/SMK-DS/{$bulanRomawi}/{$tahun}";
        $suggestedNumberJual = "{$nextNumber}/BA-LELANG/SMK-DS/{$bulanRomawi}/{$tahun}";

        return view('official_reports.create', compact('user', 'jurusans', 'items', 'suggestedNumberRusak', 'suggestedNumberJual'));
    }

    /**
     * Simpan Berita Acara baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:100|unique:official_reports,nomor_surat',
            'jenis' => 'required|in:barang_rusak,penjualan',
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'pihak_pertama_nama' => 'required|string|max:150',
            'pihak_pertama_jabatan' => 'required|string|max:150',
            'pihak_pertama_nip' => 'nullable|string|max:50',
            'pihak_kedua_nama' => 'required|string|max:150',
            'pihak_kedua_jabatan' => 'required|string|max:150',
            'pihak_kedua_instansi' => 'nullable|string|max:150',
            'pihak_kedua_kontak' => 'nullable|string|max:50',
            'mengetahui_nama' => 'required|string|max:150',
            'mengetahui_jabatan' => 'required|string|max:150',
            'mengetahui_nip' => 'nullable|string|max:50',
            'latar_belakang' => 'nullable|string',
            'catatan' => 'nullable|string',
            'file_lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

            // Rincian barang
            'items' => 'required|array|min:1',
            'items.*.nama_barang' => 'required|string|max:200',
            'items.*.kode_barang' => 'nullable|string|max:100',
            'items.*.unit_code' => 'nullable|string|max:100',
            'items.*.nomor_seri' => 'nullable|string|max:100',
            'items.*.jumlah' => 'required|numeric|min:1',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.kondisi_saat_lapor' => 'nullable|string|max:100',
            'items.*.harga_satuan' => 'nullable|numeric|min:0',
            'items.*.keterangan' => 'nullable|string|max:255',
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
                'pihak_kedua_instansi' => $validated['pihak_kedua_instansi'] ?? null,
                'pihak_kedua_kontak' => $validated['pihak_kedua_kontak'] ?? null,
                'mengetahui_nama' => $validated['mengetahui_nama'],
                'mengetahui_jabatan' => $validated['mengetahui_jabatan'],
                'mengetahui_nip' => $validated['mengetahui_nip'] ?? null,
                'latar_belakang' => $validated['latar_belakang'] ?? null,
                'total_nominal' => $totalNominal,
                'status_dokumen' => 'selesai',
                'file_lampiran' => $filePath,
                'catatan' => $validated['catatan'] ?? null,
            ]);

            foreach ($request->items as $itemData) {
                $qty = (int) ($itemData['jumlah'] ?? 1);
                $price = (float) ($itemData['harga_satuan'] ?? 0);
                $subtotal = $qty * $price;

                OfficialReportItem::create([
                    'official_report_id' => $report->id,
                    'item_id' => $itemData['item_id'] ?? null,
                    'item_unit_id' => $itemData['item_unit_id'] ?? null,
                    'kode_barang' => $itemData['kode_barang'] ?? null,
                    'nama_barang' => $itemData['nama_barang'],
                    'unit_code' => $itemData['unit_code'] ?? null,
                    'nomor_seri' => $itemData['nomor_seri'] ?? null,
                    'jumlah' => $qty,
                    'satuan' => $itemData['satuan'] ?? 'unit',
                    'kondisi_saat_lapor' => $itemData['kondisi_saat_lapor'] ?? 'rusak_berat',
                    'harga_satuan' => $price,
                    'subtotal' => $subtotal,
                    'keterangan' => $itemData['keterangan'] ?? null,
                ]);
            }
        });

        return redirect()->route('official-reports.index')->with('success', 'Berita Acara berhasil dibuat dan tercatat resmi.');
    }

    /**
     * Tampilkan detail Berita Acara.
     */
    public function show(OfficialReport $officialReport): View
    {
        $officialReport->load(['user', 'jurusan', 'items']);

        return view('official_reports.show', compact('officialReport'));
    }

    /**
     * Hapus arsip Berita Acara.
     */
    public function destroy(OfficialReport $officialReport): RedirectResponse
    {
        if ($officialReport->file_lampiran && Storage::disk('public')->exists($officialReport->file_lampiran)) {
            Storage::disk('public')->delete($officialReport->file_lampiran);
        }

        $officialReport->delete();

        return redirect()->route('official-reports.index')->with('success', 'Arsip Berita Acara berhasil dihapus.');
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
            'catatan_approval' => 'nullable|string|max:500',
            'signature_data' => 'nullable|string', // Base64 data URL from signature pad
        ]);

        $signaturePath = $officialReport->ttd_mengetahui;
        if ($request->filled('signature_data')) {
            $signaturePath = $this->saveBase64Signature($request->signature_data, 'signatures/kepsek');
        }

        $officialReport->update([
            'status_approval' => 'disetujui',
            'approved_by' => $user->id,
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
            'catatan_approval' => 'required|string|max:500',
        ]);

        $officialReport->update([
            'status_approval' => 'ditolak',
            'approved_by' => $user->id,
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

        if (! $user->isSarpras()) {
            abort(403, 'Hanya akun Sarpras yang dapat menandatangani sebagai Pihak Pertama.');
        }

        $request->validate([
            'signature_data' => 'required|string',
        ]);

        $signaturePath = $this->saveBase64Signature($request->signature_data, 'signatures/sarpras');

        $officialReport->update([
            'ttd_pihak_pertama' => $signaturePath,
            'ttd_pihak_pertama_at' => now(),
        ]);

        return back()->with('success', 'Tanda tangan Pihak Pertama (Sarpras) berhasil disimpan.');
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
     * Cetak Berita Acara resmi (format Surat Dinas A4 dengan Kop Resmi).
     */
    public function print(OfficialReport $officialReport): View
    {
        $officialReport->load(['user', 'jurusan', 'items', 'approver']);

        return view('official_reports.print', compact('officialReport'));
    }
}
