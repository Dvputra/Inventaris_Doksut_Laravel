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

use App\Models\User;

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
                'pihak_kedua_peran' => $validated['pihak_kedua_peran'] ?? ($request->input('pihak_kedua_peran') ?? ($validated['jenis'] === 'penjualan' ? 'pembeli' : null)),
                'pihak_kedua_nip' => $validated['pihak_kedua_nip'] ?? null,
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
        $officialReport->load(['user', 'jurusan', 'items', 'approver']);
        $kepsekUser = User::where('role', 'kepala_sekolah')->first();
        $sarprasUser = User::where('role', 'sarpras')->first();
        $sarprasUnit = Jurusan::where('kode', 'SAR')->orWhere('nama', 'like', '%Sarpras%')->first();
        $jurusanUser = $officialReport->jurusan_id ? User::where('role', 'jurusan')->where('jurusan_id', $officialReport->jurusan_id)->first() : null;

        return view('official_reports.show', compact('officialReport', 'kepsekUser', 'sarprasUser', 'sarprasUnit', 'jurusanUser'));
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
     * Cetak Berita Acara resmi (format Surat Dinas A4 dengan Kop Resmi).
     */
    public function print(OfficialReport $officialReport): View
    {
        $officialReport->load(['user', 'jurusan', 'items', 'approver']);

        return view('official_reports.print', compact('officialReport'));
    }
}
