<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Item;
use App\Models\Jurusan;
use App\Services\ImageOptimizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicComplaintController extends Controller
{
    /**
     * Tampilkan halaman awal portal publik & form pengaduan kendala fasilitas.
     */
    public function index(): View
    {
        $jurusans = Jurusan::orderBy('kode')->get();
        $items = Item::select('id', 'kode_barang', 'nama_barang', 'jurusan_id', 'lokasi')
            ->orderBy('nama_barang')
            ->get();

        // Beberapa pengaduan terbaru yang berstatus selesai sebagai transparansi layanan
        $recentResolved = Complaint::where('status', 'selesai')
            ->latest('tanggal_selesai')
            ->take(4)
            ->get();

        return view('welcome', compact('jurusans', 'items', 'recentResolved'));
    }

    /**
     * Simpan pengaduan baru dari guru/tendik tanpa perlu login.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_pelapor' => ['required', 'string', 'max:150'],
            'kontak' => ['required', 'string', 'max:50'],
            'jurusan_id' => ['nullable', 'exists:jurusans,id'],
            'lokasi_ruang' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'in:komputer_it,kelistrikan,mesin_peralatan,sarana_gedung,lainnya'],
            'item_id' => ['nullable', 'exists:items,id'],
            'judul_kendala' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'tingkat_urgensi' => ['required', 'in:rendah,sedang,tinggi_darurat'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
        ], [
            'nama_pelapor.required' => 'Nama pelapor / guru wajib diisi.',
            'kontak.required' => 'Nomor WhatsApp / HP wajib diisi agar mudah dikonfirmasi.',
            'lokasi_ruang.required' => 'Lokasi ruangan / laboratorium / bengkel wajib diisi.',
            'judul_kendala.required' => 'Judul atau ringkasan kendala wajib diisi.',
            'deskripsi.required' => 'Uraikan rincian kendala fasilitas yang Anda alami.',
            'foto.image' => 'File bukti harus berupa gambar (JPG, PNG, atau WEBP).',
            'foto.max' => 'Ukuran file foto maksimal 3 MB.',
        ]);

        // Generate kode tiket unik: misal ADU-YYMM-RAND
        $yearMonth = date('ym');
        $random = strtoupper(bin2hex(random_bytes(2)));
        $ticketCode = "ADU-{$yearMonth}-{$random}";

        // Upload dan kompres foto jika disertakan
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = ImageOptimizer::optimizeAndStore($request->file('foto'), 'complaints');
        }

        $complaint = Complaint::create([
            'ticket_code' => $ticketCode,
            'nama_pelapor' => $validated['nama_pelapor'],
            'kontak' => $validated['kontak'],
            'jurusan_id' => $validated['jurusan_id'] ?? null,
            'lokasi_ruang' => $validated['lokasi_ruang'],
            'kategori' => $validated['kategori'],
            'item_id' => $validated['item_id'] ?? null,
            'judul_kendala' => $validated['judul_kendala'],
            'deskripsi' => $validated['deskripsi'],
            'tingkat_urgensi' => $validated['tingkat_urgensi'],
            'foto' => $fotoPath,
            'status' => 'menunggu',
        ]);

        return redirect()->route('public.track', ['ticket' => $complaint->ticket_code])
            ->with('complaint_success', [
                'ticket_code' => $complaint->ticket_code,
                'nama' => $complaint->nama_pelapor,
                'judul' => $complaint->judul_kendala,
            ]);
    }

    /**
     * Halaman pelacakan status pengaduan berdasarkan kode tiket atau nomor kontak.
     */
    public function track(Request $request): View
    {
        $ticketCode = trim((string) $request->input('ticket'));
        $complaint = null;

        if (! empty($ticketCode)) {
            $complaint = Complaint::with(['jurusan', 'item'])
                ->where('ticket_code', $ticketCode)
                ->orWhere('kontak', $ticketCode)
                ->first();
        }

        return view('public.track', compact('complaint', 'ticketCode'));
    }
}
