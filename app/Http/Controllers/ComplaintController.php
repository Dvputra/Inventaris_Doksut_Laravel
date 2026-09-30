<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Jurusan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    /**
     * Tampilkan daftar pengaduan kendala fasilitas dari guru/tendik.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Complaint::with(['jurusan', 'item'])->latest();

        if ($user->isJurusan()) {
            $query->where('jurusan_id', $user->jurusan_id);
        } elseif ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_code', 'like', "%{$search}%")
                    ->orWhere('nama_pelapor', 'like', "%{$search}%")
                    ->orWhere('judul_kendala', 'like', "%{$search}%")
                    ->orWhere('lokasi_ruang', 'like', "%{$search}%");
            });
        }

        $complaints = $query->paginate(12)->withQueryString();
        $jurusans = Jurusan::all();

        // Statistik ringkas status
        $statsQuery = Complaint::query();
        if ($user->isJurusan()) {
            $statsQuery->where('jurusan_id', $user->jurusan_id);
        }
        $menungguCount = (clone $statsQuery)->where('status', 'menunggu')->count();
        $diprosesCount = (clone $statsQuery)->where('status', 'diproses')->count();
        $selesaiCount = (clone $statsQuery)->where('status', 'selesai')->count();

        return view('complaints.index', compact('complaints', 'jurusans', 'menungguCount', 'diprosesCount', 'selesaiCount'));
    }

    /**
     * Tampilkan detail pengaduan kendala fasilitas.
     */
    public function show(Complaint $complaint, Request $request): View
    {
        $user = $request->user();
        if ($user->isJurusan() && $complaint->jurusan_id && $complaint->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak akses pada pengaduan dari jurusan lain.');
        }

        $complaint->load(['jurusan', 'item']);

        return view('complaints.show', compact('complaint'));
    }

    /**
     * Perbarui status penanganan & tindak lanjut perbaikan pengaduan.
     */
    public function update(Request $request, Complaint $complaint): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $complaint->jurusan_id && $complaint->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak akses mengubah pengaduan ini.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:menunggu,diproses,selesai,ditolak'],
            'teknisi_penanganan' => ['nullable', 'string', 'max:150'],
            'tindak_lanjut' => ['nullable', 'string'],
        ]);

        if ($validated['status'] === 'selesai' && ! $complaint->tanggal_selesai) {
            $validated['tanggal_selesai'] = now();
        } elseif ($validated['status'] !== 'selesai') {
            $validated['tanggal_selesai'] = null;
        }

        $complaint->update($validated);

        return redirect()->route('complaints.show', $complaint)
            ->with('success', "Status pengaduan [{$complaint->ticket_code}] berhasil diperbarui menjadi ".strtoupper($validated['status']).'.');
    }

    /**
     * Hapus pengaduan kendala.
     */
    public function destroy(Complaint $complaint, Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $complaint->jurusan_id && $complaint->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $ticket = $complaint->ticket_code;
        $complaint->delete();

        return redirect()->route('complaints.index')
            ->with('success', "Pengaduan [{$ticket}] berhasil dihapus.");
    }
}
