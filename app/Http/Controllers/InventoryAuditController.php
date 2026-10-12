<?php

namespace App\Http\Controllers;

use App\Models\InventoryAudit;
use App\Models\InventoryAuditItem;
use App\Models\Item;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InventoryAuditController extends Controller
{
    /**
     * Daftar Riwayat Sesi Audit (Sarpras melihat semua, Jurusan melihat jurusannya saja).
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = InventoryAudit::with(['jurusan', 'auditor'])->latest('tanggal_audit');

        if ($user->isJurusan()) {
            $query->where('jurusan_id', $user->jurusan_id);
        } elseif ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('audit_code', 'like', "%{$search}%")
                    ->orWhere('judul', 'like', "%{$search}%")
                    ->orWhere('auditor_nama', 'like', "%{$search}%")
                    ->orWhereHas('jurusan', function ($jq) use ($search) {
                        $jq->where('nama', 'like', "%{$search}%")
                            ->orWhere('kode', 'like', "%{$search}%");
                    });
            });
        }

        $audits = $query->paginate(10)->withQueryString();
        $jurusans = Jurusan::all();

        // Statistik ringkas
        $statQuery = InventoryAudit::query();
        if ($user->isJurusan()) {
            $statQuery->where('jurusan_id', $user->jurusan_id);
        }
        $totalAudits = (clone $statQuery)->count();
        $totalSelesai = (clone $statQuery)->whereIn('status', ['selesai', 'final'])->count();
        $totalDraft = (clone $statQuery)->where('status', 'draft')->count();

        return view('inventory_audits.index', compact('audits', 'jurusans', 'totalAudits', 'totalSelesai', 'totalDraft'));
    }

    /**
     * Form Mulai Audit Baru (Khusus Sarpras).
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        if (! $user->isSarprasOrKepalaSekolah()) {
            abort(403, 'Hanya tim Sarpras yang berwenang membuka sesi audit baru.');
        }

        $jurusans = Jurusan::all();
        $yearMonth = date('Ym');
        $count = InventoryAudit::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count() + 1;
        $suggestedCode = sprintf('AUD-%s-%03d', $yearMonth, $count);

        return view('inventory_audits.create', compact('jurusans', 'suggestedCode', 'user'));
    }

    /**
     * Simpan Sesi Audit Baru & Otomatis Tarik Data Barang Unit Kerja.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isSarprasOrKepalaSekolah()) {
            abort(403, 'Hanya tim Sarpras yang berwenang membuka sesi audit.');
        }

        $validated = $request->validate([
            'audit_code' => ['required', 'string', 'max:50', 'unique:inventory_audits,audit_code'],
            'judul' => ['required', 'string', 'max:255'],
            'tanggal_audit' => ['required', 'date'],
            'jurusan_id' => ['required', 'exists:jurusans,id'],
            'auditor_nama' => ['required', 'string', 'max:150'],
            'auditor_nip' => ['nullable', 'string', 'max:50'],
            'auditor_jabatan' => ['required', 'string', 'max:100'],
            'catatan_umum' => ['nullable', 'string'],
        ]);

        $jurusan = Jurusan::findOrFail($validated['jurusan_id']);

        DB::transaction(function () use ($validated, $user, $jurusan, &$audit) {
            $audit = InventoryAudit::create([
                'audit_code' => $validated['audit_code'],
                'judul' => $validated['judul'],
                'tanggal_audit' => $validated['tanggal_audit'],
                'jurusan_id' => $validated['jurusan_id'],
                'auditor_user_id' => $user->id,
                'status' => 'draft',
                'auditor_nama' => $validated['auditor_nama'],
                'auditor_nip' => $validated['auditor_nip'],
                'auditor_jabatan' => $validated['auditor_jabatan'],
                'auditee_nama' => $jurusan->kepala_bengkel ?? 'Penanggung Jawab Unit Kerja',
                'auditee_jabatan' => 'Kepala ' . $jurusan->nama,
                'catatan_umum' => $validated['catatan_umum'],
            ]);

            // Tarik seluruh data barang milik unit kerja ini
            $this->populateAuditItems($audit);
        });

        return redirect()->route('inventory-audits.show', $audit)
            ->with('success', "Sesi audit [{$audit->audit_code}] berhasil dibuat dan data barang unit {$jurusan->kode} berhasil ditarik.");
    }

    /**
     * Tampilkan Detail Lembar Verifikasi & Riwayat Audit.
     */
    public function show(InventoryAudit $inventoryAudit, Request $request): View
    {
        $user = $request->user();
        if ($user->isJurusan() && $user->jurusan_id !== $inventoryAudit->jurusan_id) {
            abort(403, 'Anda tidak berwenang mengakses data audit unit kerja lain.');
        }

        $inventoryAudit->load(['jurusan', 'auditor', 'items']);
        $userSavedSignature = $user->signature;

        return view('inventory_audits.show', compact('inventoryAudit', 'userSavedSignature'));
    }

    /**
     * Tarik Ulang Data Barang jika ada pembaruan di inventaris saat audit masih draft.
     */
    public function pullItems(InventoryAudit $inventoryAudit, Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isSarprasOrKepalaSekolah()) {
            abort(403, 'Hanya tim Sarpras yang dapat menarik ulang data barang.');
        }

        if ($inventoryAudit->status !== 'draft') {
            return back()->with('error', 'Data barang tidak dapat ditarik ulang karena audit sudah diselesaikan/dikunci.');
        }

        DB::transaction(function () use ($inventoryAudit) {
            $inventoryAudit->items()->delete();
            $this->populateAuditItems($inventoryAudit);
        });

        return back()->with('success', 'Data barang berhasil ditarik ulang dari master inventaris unit kerja.');
    }

    /**
     * Update hasil verifikasi checklist audit fisik (bisa per baris atau batch).
     */
    public function updateItems(Request $request, InventoryAudit $inventoryAudit): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isSarprasOrKepalaSekolah()) {
            abort(403, 'Hanya tim Sarpras yang berwenang melakukan verifikasi fisik barang.');
        }

        if ($inventoryAudit->status === 'final') {
            return back()->with('error', 'Audit telah berstatus final dan kedua belah pihak telah menandatangani dokumen.');
        }

        $itemsData = $request->input('items', []);

        DB::transaction(function () use ($inventoryAudit, $itemsData, $request) {
            foreach ($itemsData as $itemId => $data) {
                $auditItem = InventoryAuditItem::where('inventory_audit_id', $inventoryAudit->id)
                    ->where('id', $itemId)
                    ->first();

                if ($auditItem) {
                    $qtyFisik = (float) str_replace(',', '.', (string) ($data['jumlah_fisik'] ?? $auditItem->jumlah_sistem));
                    $kondisiFisik = $data['kondisi_fisik'] ?? $auditItem->kondisi_sistem;
                    $catatan = $data['catatan'] ?? null;

                    // Evaluasi otomatis status verifikasi
                    $statusVerifikasi = $data['status_verifikasi'] ?? 'sesuai';
                    if (! isset($data['status_verifikasi']) || $data['status_verifikasi'] === 'auto') {
                        if ($qtyFisik <= 0) {
                            $statusVerifikasi = 'hilang';
                        } elseif ($qtyFisik != $auditItem->jumlah_sistem) {
                            $statusVerifikasi = 'selisih';
                        } elseif ($kondisiFisik !== 'baik' && $auditItem->kondisi_sistem === 'baik') {
                            $statusVerifikasi = 'rusak';
                        } else {
                            $statusVerifikasi = 'sesuai';
                        }
                    }

                    $auditItem->update([
                        'jumlah_fisik' => $qtyFisik,
                        'kondisi_fisik' => $kondisiFisik,
                        'status_verifikasi' => $statusVerifikasi,
                        'catatan' => $catatan,
                    ]);
                }
            }

            if ($request->has('catatan_umum')) {
                $inventoryAudit->catatan_umum = $request->input('catatan_umum');
            }

            // Hitung ulang summary temuan
            $inventoryAudit->recalculateSummary();

            // Jika tombol 'selesaikan' ditekan, ubah status ke selesai
            if ($request->input('action') === 'complete') {
                $inventoryAudit->status = 'selesai';
                $inventoryAudit->save();
            }
        });

        $message = $request->input('action') === 'complete'
            ? 'Audit berhasil diselesaikan dan dikunci. Silakan lakukan proses penandatanganan pengesahan.'
            : 'Perubahan verifikasi fisik berhasil disimpan.';

        return back()->with('success', $message);
    }

    /**
     * Buka kembali sesi audit yang terkunci (khusus Sarpras jika ada revisi).
     */
    public function unlock(InventoryAudit $inventoryAudit, Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isSarprasOrKepalaSekolah()) {
            abort(403, 'Hanya Sarpras yang berwenang membuka kunci audit.');
        }

        $hadSignatures = $inventoryAudit->ttd_auditor || $inventoryAudit->ttd_auditee;

        // Jika terdapat tanda tangan, hapus file gambar tanda tangan audit dari storage jika tersimpan lokal
        if ($inventoryAudit->ttd_auditor && Storage::disk('public')->exists($inventoryAudit->ttd_auditor)) {
            Storage::disk('public')->delete($inventoryAudit->ttd_auditor);
        }
        if ($inventoryAudit->ttd_auditee && Storage::disk('public')->exists($inventoryAudit->ttd_auditee)) {
            Storage::disk('public')->delete($inventoryAudit->ttd_auditee);
        }

        $inventoryAudit->update([
            'status' => 'draft',
            'ttd_auditor' => null,
            'ttd_auditor_at' => null,
            'ttd_auditee' => null,
            'ttd_auditee_at' => null,
        ]);

        $msg = $hadSignatures
            ? 'Kunci audit berhasil dibuka kembali ke status Draft. Tanda tangan sebelumnya telah direset untuk ditandatangani ulang setelah revisi selesai.'
            : 'Kunci audit berhasil dibuka kembali ke status Draft.';

        return back()->with('success', $msg);
    }

    /**
     * Tanda Tangan Digital oleh Auditor (Tim Sarpras).
     */
    public function signAuditor(Request $request, InventoryAudit $inventoryAudit): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isSarprasOrKepalaSekolah()) {
            abort(403, 'Hanya tim Sarpras yang berwenang menandatangani sebagai Auditor.');
        }

        $request->validate([
            'signature_data' => ['nullable', 'string'],
            'use_saved_signature' => ['nullable', 'boolean'],
            'save_signature_profile' => ['nullable', 'boolean'],
        ]);

        $signaturePath = null;
        if ($request->boolean('use_saved_signature')) {
            $signaturePath = $user->signature;
        } elseif ($request->filled('signature_data')) {
            $signaturePath = $this->saveBase64Signature($request->signature_data, 'signatures/auditor');
            if ($request->boolean('save_signature_profile')) {
                $user->update(['signature' => $signaturePath]);
            }
        }

        if (! $signaturePath) {
            return back()->with('error', 'Silakan goreskan tanda tangan atau gunakan tanda tangan profil tersimpan.');
        }

        $inventoryAudit->update([
            'ttd_auditor' => $signaturePath,
            'ttd_auditor_at' => now(),
            'auditor_nama' => $user->name,
            'status' => $inventoryAudit->ttd_auditee ? 'final' : $inventoryAudit->status,
        ]);

        return back()->with('success', 'Tanda tangan Auditor (Sarpras) berhasil disimpan.');
    }

    /**
     * Tanda Tangan Digital oleh Auditee (Kepala Bengkel / Unit Kerja).
     */
    public function signAuditee(Request $request, InventoryAudit $inventoryAudit): RedirectResponse
    {
        $user = $request->user();

        if ($user->isJurusan() && $user->jurusan_id !== $inventoryAudit->jurusan_id) {
            abort(403, 'Anda tidak berwenang menandatangani audit unit kerja lain.');
        }

        if (! $user->isJurusan() && ! $user->isStaffSarpras()) {
            abort(403, 'Hanya perwakilan Unit Kerja terkait atau Sarpras yang berwenang menandatangani sebagai Auditee.');
        }

        $request->validate([
            'signature_data' => ['nullable', 'string'],
            'use_saved_signature' => ['nullable', 'boolean'],
            'save_signature_profile' => ['nullable', 'boolean'],
            'auditee_nama' => ['nullable', 'string', 'max:150'],
            'auditee_nip' => ['nullable', 'string', 'max:50'],
        ]);

        $jurusanUser = User::where('role', 'jurusan')->where('jurusan_id', $inventoryAudit->jurusan_id)->first();

        $signaturePath = null;
        if ($request->boolean('use_saved_signature')) {
            $signaturePath = $jurusanUser?->signature ?: $user->signature;
        } elseif ($request->filled('signature_data')) {
            $signaturePath = $this->saveBase64Signature($request->signature_data, 'signatures/auditee');
            if ($request->boolean('save_signature_profile')) {
                if ($jurusanUser) {
                    $jurusanUser->update(['signature' => $signaturePath]);
                }
                if ($user->isJurusan()) {
                    $user->update(['signature' => $signaturePath]);
                }
            }
        }

        if (! $signaturePath) {
            return back()->with('error', 'Silakan goreskan tanda tangan atau pilih tanda tangan tersimpan.');
        }

        $updateData = [
            'ttd_auditee' => $signaturePath,
            'ttd_auditee_at' => now(),
        ];
        if ($request->filled('auditee_nama')) {
            $updateData['auditee_nama'] = $request->auditee_nama;
        }
        if ($request->filled('auditee_nip')) {
            $updateData['auditee_nip'] = $request->auditee_nip;
        }
        if ($inventoryAudit->ttd_auditor) {
            $updateData['status'] = 'final';
        }

        $inventoryAudit->update($updateData);

        return back()->with('success', 'Tanda tangan Penanggung Jawab Unit Kerja (Auditee) berhasil disimpan.');
    }

    /**
     * Batalkan tanda tangan audit.
     */
    public function cancelSignature(InventoryAudit $inventoryAudit, Request $request): RedirectResponse
    {
        $user = $request->user();
        $type = $request->query('type', 'auditor');

        if ($type === 'auditor') {
            if (! $user->isStaffSarpras()) {
                abort(403, 'Hanya tim Sarpras yang berwenang membatalkan tanda tangan Auditor.');
            }

            $inventoryAudit->update([
                'ttd_auditor' => null,
                'ttd_auditor_at' => null,
                'status' => 'selesai',
            ]);

            return back()->with('success', 'Tanda tangan Auditor (Sarpras) berhasil dibatalkan.');
        }

        if ($type === 'auditee') {
            if ($user->isJurusan() && $user->jurusan_id !== $inventoryAudit->jurusan_id) {
                abort(403, 'Anda tidak berwenang membatalkan tanda tangan unit kerja lain.');
            }

            $inventoryAudit->update([
                'ttd_auditee' => null,
                'ttd_auditee_at' => null,
                'status' => 'selesai',
            ]);

            return back()->with('success', 'Tanda tangan Penanggung Jawab Unit Kerja (Auditee) berhasil dibatalkan.');
        }

        return back()->with('error', 'Tipe pembatalan tanda tangan tidak valid.');
    }

    /**
     * Hapus arsip audit (Khusus Sarpras).
     */
    public function destroy(InventoryAudit $inventoryAudit, Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->isSarprasOrKepalaSekolah()) {
            abort(403, 'Hanya Sarpras yang berwenang menghapus dokumen audit.');
        }

        if ($inventoryAudit->ttd_auditor && Storage::disk('public')->exists($inventoryAudit->ttd_auditor)) {
            Storage::disk('public')->delete($inventoryAudit->ttd_auditor);
        }
        if ($inventoryAudit->ttd_auditee && Storage::disk('public')->exists($inventoryAudit->ttd_auditee)) {
            Storage::disk('public')->delete($inventoryAudit->ttd_auditee);
        }

        $code = $inventoryAudit->audit_code;
        $inventoryAudit->delete();

        return redirect()->route('inventory-audits.index')
            ->with('success', "Arsip sesi audit [{$code}] berhasil dihapus.");
    }

    /**
     * Cetak Berita Acara Hasil Audit Fisik A4 dengan Kop Surat & TTD.
     */
    public function print(InventoryAudit $inventoryAudit, Request $request): View
    {
        $user = $request->user();
        if ($user->isJurusan() && $user->jurusan_id !== $inventoryAudit->jurusan_id) {
            abort(403, 'Anda tidak berwenang mencetak dokumen audit unit kerja lain.');
        }

        $inventoryAudit->load(['jurusan', 'items']);
        $sarprasUnit = Jurusan::where('kode', 'SAR')->orWhere('nama', 'like', '%Sarpras%')->first();

        return view('inventory_audits.print', compact('inventoryAudit', 'user', 'sarprasUnit'));
    }

    /**
     * Helper populasikan barang dari unit kerja ke lembar audit.
     */
    private function populateAuditItems(InventoryAudit $audit): void
    {
        $items = Item::with('category')
            ->where('jurusan_id', $audit->jurusan_id)
            ->orderBy('kode_barang')
            ->get();

        foreach ($items as $item) {
            InventoryAuditItem::create([
                'inventory_audit_id' => $audit->id,
                'item_id' => $item->id,
                'kode_barang' => $item->kode_barang,
                'nama_barang' => $item->nama_barang,
                'kategori_nama' => $item->category?->nama ?? '-',
                'satuan' => $item->satuan,
                'lokasi_sistem' => $item->lokasi ?? '-',
                'jumlah_sistem' => (float) $item->jumlah,
                'kondisi_sistem' => $item->kondisi,
                'jumlah_fisik' => (float) $item->jumlah,
                'kondisi_fisik' => $item->kondisi,
                'status_verifikasi' => 'belum_dicek',
                'catatan' => null,
            ]);
        }

        $audit->recalculateSummary();
    }

    /**
     * Helper simpan base64 canvas signature ke storage PNG.
     */
    private function saveBase64Signature(string $base64Data, string $folder): string
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $data = substr($base64Data, strpos($base64Data, ',') + 1);
            $type = strtolower($type[1]);

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
}
