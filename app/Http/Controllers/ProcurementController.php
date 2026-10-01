<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Procurement;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProcurementController extends Controller
{
    /**
     * Tampilkan daftar usulan pengadaan barang/bahan.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Procurement::with(['jurusan', 'user', 'items'])->latest();

        if ($user->isJurusan()) {
            $query->where('jurusan_id', $user->jurusan_id);
        } elseif ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $procurements = $query->paginate(10)->withQueryString();
        $jurusans = Jurusan::all();

        return view('procurements.index', compact('procurements', 'jurusans'));
    }

    /**
     * Tampilkan form pengajuan baru.
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $jurusans = Jurusan::all();

        return view('procurements.create', compact('user', 'jurusans'));
    }

    /**
     * Simpan pengajuan pengadaan dari jurusan (mendukung multi-barang).
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Dukungan backward-compatibility jika input lama (single item) dikirim
        if (! $request->has('items') && $request->filled('nama_barang')) {
            $request->merge([
                'items' => [
                    [
                        'nama_barang' => $request->nama_barang,
                        'spesifikasi' => $request->spesifikasi,
                        'jumlah' => $request->jumlah,
                        'satuan' => $request->satuan,
                        'harga_satuan' => $request->perkiraan_biaya && $request->jumlah ? ((float) $request->perkiraan_biaya / (int) $request->jumlah) : null,
                        'perkiraan_biaya' => $request->perkiraan_biaya,
                    ],
                ],
            ]);
        }

        $rules = [
            'judul_pengadaan' => ['nullable', 'string', 'max:255'],
            'alasan' => ['required', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.nama_barang' => ['required', 'string', 'max:255'],
            'items.*.spesifikasi' => ['nullable', 'string'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'items.*.satuan' => ['required', 'string', 'max:30'],
            'items.*.harga_satuan' => ['nullable', 'numeric', 'min:0'],
            'items.*.perkiraan_biaya' => ['nullable', 'numeric', 'min:0'],
            'items.*.keterangan' => ['nullable', 'string'],
        ];

        if ($user->isSarprasOrKepalaSekolah() || ! $user->jurusan_id) {
            $rules['jurusan_id'] = ['required', 'exists:jurusans,id'];
        }

        $validated = $request->validate($rules, [
            'alasan.required' => 'Alasan atau justifikasi kebutuhan wajib diisi.',
            'items.required' => 'Minimal satu item barang harus diusulkan.',
            'items.*.nama_barang.required' => 'Nama barang pada daftar wajib diisi.',
            'items.*.jumlah.required' => 'Jumlah barang wajib diisi minimal 1.',
            'items.*.satuan.required' => 'Satuan barang wajib diisi.',
        ]);

        $jurusanId = ($user->isJurusan() && $user->jurusan_id) ? $user->jurusan_id : $validated['jurusan_id'];

        $procurement = DB::transaction(function () use ($validated, $user, $jurusanId) {
            // Generate nomor usulan otomatis: UP-YYYYMM-XXXX
            $prefix = 'UP-'.date('Ym').'-';
            $latest = Procurement::where('nomor_usulan', 'like', $prefix.'%')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if ($latest && preg_match('/-(\d+)$/', $latest->nomor_usulan, $matches)) {
                $nextSeq = (int) $matches[1] + 1;
            } else {
                $nextSeq = Procurement::count() + 1;
            }
            $nomorUsulan = $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);

            $itemsData = $validated['items'];
            $firstItem = $itemsData[0];

            $totalBiaya = 0;
            $processedItems = [];

            foreach ($itemsData as $item) {
                $jumlah = (int) ($item['jumlah'] ?? 1);
                $hargaSatuan = isset($item['harga_satuan']) && $item['harga_satuan'] !== '' ? (float) $item['harga_satuan'] : null;
                $biaya = isset($item['perkiraan_biaya']) && $item['perkiraan_biaya'] !== '' ? (float) $item['perkiraan_biaya'] : ($hargaSatuan ? $hargaSatuan * $jumlah : 0);

                $totalBiaya += $biaya;
                $processedItems[] = [
                    'nama_barang' => $item['nama_barang'],
                    'spesifikasi' => $item['spesifikasi'] ?? null,
                    'jumlah' => $jumlah,
                    'satuan' => $item['satuan'] ?? 'unit',
                    'harga_satuan' => $hargaSatuan,
                    'perkiraan_biaya' => $biaya > 0 ? $biaya : null,
                    'keterangan' => $item['keterangan'] ?? null,
                ];
            }

            $countItems = count($processedItems);
            $judul = ! empty($validated['judul_pengadaan'])
                ? $validated['judul_pengadaan']
                : ('Pengadaan '.$firstItem['nama_barang'].($countItems > 1 ? ' (+'.($countItems - 1).' barang lainnya)' : ''));

            $procurement = Procurement::create([
                'nomor_usulan' => $nomorUsulan,
                'user_id' => $user->id,
                'jurusan_id' => $jurusanId,
                'judul_pengadaan' => $judul,
                'nama_barang' => $firstItem['nama_barang'],
                'spesifikasi' => $firstItem['spesifikasi'] ?? null,
                'jumlah' => $firstItem['jumlah'],
                'satuan' => $firstItem['satuan'],
                'perkiraan_biaya' => $totalBiaya > 0 ? $totalBiaya : null,
                'alasan' => $validated['alasan'],
                'status' => 'menunggu',
            ]);

            foreach ($processedItems as $item) {
                $procurement->items()->create($item);
            }

            return $procurement;
        });

        return redirect()->route('procurements.index')
            ->with('success', "Usulan pengadaan [{$procurement->nomor_usulan}] '{$procurement->summary_barang}' berhasil dikirim ke Sarpras.");
    }

    /**
     * Tampilkan form edit usulan pengadaan.
     */
    public function edit(Procurement $procurement, Request $request): View
    {
        $user = $request->user();
        if ($user->isJurusan() && $procurement->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit usulan pengadaan jurusan lain.');
        }

        $procurement->load(['items', 'jurusan']);
        $jurusans = Jurusan::all();

        return view('procurements.edit', compact('procurement', 'jurusans'));
    }

    /**
     * Perbarui usulan pengadaan barang.
     */
    public function update(Request $request, Procurement $procurement): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $procurement->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk memperbarui usulan pengadaan jurusan lain.');
        }

        // Dukungan backward-compatibility jika input single item dikirim
        if (! $request->has('items') && $request->filled('nama_barang')) {
            $request->merge([
                'items' => [
                    [
                        'nama_barang' => $request->nama_barang,
                        'spesifikasi' => $request->spesifikasi,
                        'jumlah' => $request->jumlah,
                        'satuan' => $request->satuan,
                        'harga_satuan' => $request->perkiraan_biaya && $request->jumlah ? ((float) $request->perkiraan_biaya / (int) $request->jumlah) : null,
                        'perkiraan_biaya' => $request->perkiraan_biaya,
                    ],
                ],
            ]);
        }

        $rules = [
            'judul_pengadaan' => ['nullable', 'string', 'max:255'],
            'alasan' => ['required', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.nama_barang' => ['required', 'string', 'max:255'],
            'items.*.spesifikasi' => ['nullable', 'string'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'items.*.satuan' => ['required', 'string', 'max:30'],
            'items.*.harga_satuan' => ['nullable', 'numeric', 'min:0'],
            'items.*.perkiraan_biaya' => ['nullable', 'numeric', 'min:0'],
            'items.*.keterangan' => ['nullable', 'string'],
        ];

        if ($user->isSarprasOrKepalaSekolah() || ! $user->jurusan_id) {
            $rules['jurusan_id'] = ['required', 'exists:jurusans,id'];
        }

        $validated = $request->validate($rules, [
            'alasan.required' => 'Alasan atau justifikasi kebutuhan wajib diisi.',
            'items.required' => 'Minimal satu item barang harus diusulkan.',
            'items.*.nama_barang.required' => 'Nama barang pada daftar wajib diisi.',
            'items.*.jumlah.required' => 'Jumlah barang wajib diisi minimal 1.',
            'items.*.satuan.required' => 'Satuan barang wajib diisi.',
        ]);

        DB::transaction(function () use ($validated, $procurement, $user) {
            $itemsData = $validated['items'];
            $firstItem = $itemsData[0];

            $totalBiaya = 0;
            $processedItems = [];

            foreach ($itemsData as $item) {
                $jumlah = (int) ($item['jumlah'] ?? 1);
                $hargaSatuan = isset($item['harga_satuan']) && $item['harga_satuan'] !== '' ? (float) $item['harga_satuan'] : null;
                $biaya = isset($item['perkiraan_biaya']) && $item['perkiraan_biaya'] !== '' ? (float) $item['perkiraan_biaya'] : ($hargaSatuan ? $hargaSatuan * $jumlah : 0);

                $totalBiaya += $biaya;
                $processedItems[] = [
                    'nama_barang' => $item['nama_barang'],
                    'spesifikasi' => $item['spesifikasi'] ?? null,
                    'jumlah' => $jumlah,
                    'satuan' => $item['satuan'] ?? 'unit',
                    'harga_satuan' => $hargaSatuan,
                    'perkiraan_biaya' => $biaya > 0 ? $biaya : null,
                    'keterangan' => $item['keterangan'] ?? null,
                ];
            }

            $countItems = count($processedItems);
            $judul = ! empty($validated['judul_pengadaan'])
                ? $validated['judul_pengadaan']
                : ($procurement->judul_pengadaan ?: ('Pengadaan '.$firstItem['nama_barang'].($countItems > 1 ? ' (+'.($countItems - 1).' barang lainnya)' : '')));

            $updateData = [
                'judul_pengadaan' => $judul,
                'nama_barang' => $firstItem['nama_barang'],
                'spesifikasi' => $firstItem['spesifikasi'] ?? null,
                'jumlah' => $firstItem['jumlah'],
                'satuan' => $firstItem['satuan'],
                'perkiraan_biaya' => $totalBiaya > 0 ? $totalBiaya : null,
                'alasan' => $validated['alasan'],
            ];

            if ($user->isSarpras() && isset($validated['jurusan_id'])) {
                $updateData['jurusan_id'] = $validated['jurusan_id'];
            }

            $procurement->update($updateData);

            // Re-sync items
            $procurement->items()->delete();
            foreach ($processedItems as $item) {
                $procurement->items()->create($item);
            }
        });

        return redirect()->route('procurements.index')
            ->with('success', "Usulan pengadaan [{$procurement->nomor_usulan}] berhasil diperbarui.");
    }

    /**
     * Hapus usulan pengadaan barang.
     */
    public function destroy(Procurement $procurement, Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $procurement->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus usulan pengadaan jurusan lain.');
        }

        if (! $user->isSarpras() && $procurement->status === 'disetujui') {
            abort(403, 'Usulan pengadaan yang telah disetujui tidak dapat dihapus oleh akun selain Sarpras.');
        }

        $nomor = $procurement->nomor_usulan ?? 'UP-'.$procurement->id;
        $nama = $procurement->summary_barang;

        $procurement->delete();

        return redirect()->route('procurements.index')
            ->with('success', "Usulan pengadaan [{$nomor}] '{$nama}' berhasil dihapus.");
    }

    /**
     * Cetak dokumen resmi usulan pengadaan barang (A4).
     */
    public function print(Procurement $procurement, Request $request): View
    {
        $user = $request->user();
        if ($user->isJurusan() && $procurement->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mencetak usulan pengadaan jurusan lain.');
        }

        $procurement->load(['jurusan', 'user', 'items']);
        $kepsekUser = User::where('role', 'kepala_sekolah')->first();
        $sarprasUser = User::where('role', 'sarpras')->first();

        return view('procurements.print', compact('procurement', 'kepsekUser', 'sarprasUser'));
    }

    /**
     * Sarpras menyetujui pengajuan pengadaan.
     */
    public function approve(Procurement $procurement, Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isSarpras()) {
            abort(403, 'Hanya Sarpras yang berwenang menyetujui pengajuan.');
        }

        $request->validate([
            'catatan_sarpras' => ['nullable', 'string'],
        ]);

        $procurement->update([
            'status' => 'disetujui',
            'catatan_sarpras' => $request->catatan_sarpras ?: 'Disetujui oleh Sarpras.',
            'tanggal_persetujuan' => now()->toDateString(),
        ]);

        return redirect()->route('procurements.index')
            ->with('success', "Pengajuan '{$procurement->summary_barang}' untuk {$procurement->jurusan->nama} berhasil disetujui.");
    }

    /**
     * Sarpras menolak pengajuan pengadaan.
     */
    public function reject(Procurement $procurement, Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isSarpras()) {
            abort(403, 'Hanya Sarpras yang berwenang menolak pengajuan.');
        }

        $request->validate([
            'catatan_sarpras' => ['required', 'string'],
        ], [
            'catatan_sarpras.required' => 'Harap berikan alasan penolakan pada catatan Sarpras.',
        ]);

        $procurement->update([
            'status' => 'ditolak',
            'catatan_sarpras' => $request->catatan_sarpras,
            'tanggal_persetujuan' => now()->toDateString(),
        ]);

        return redirect()->route('procurements.index')
            ->with('success', "Pengajuan '{$procurement->summary_barang}' telah ditolak dengan catatan yang diberikan.");
    }
}
