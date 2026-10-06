<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemUsage;
use App\Models\Jurusan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemUsageController extends Controller
{
    /**
     * Tampilkan riwayat pemakaian bahan praktik.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = ItemUsage::with(['item', 'jurusan', 'user'])->latest('tanggal_pemakaian');

        if ($user->isJurusan()) {
            $query->where('jurusan_id', $user->jurusan_id);
        } elseif ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_guru', 'like', "%{$search}%")
                    ->orWhere('kelas', 'like', "%{$search}%")
                    ->orWhere('keperluan_jobsheet', 'like', "%{$search}%")
                    ->orWhereHas('item', fn ($i) => $i->where('nama_barang', 'like', "%{$search}%"));
            });
        }

        $usages = $query->paginate(15)->withQueryString();
        $jurusans = Jurusan::all();

        // Cari bahan yang stoknya mendekati atau di bawah batas minimum (min_stok)
        $lowStockQuery = Item::where('jenis', 'bahan')->whereColumn('jumlah', '<=', 'min_stok')->where('min_stok', '>', 0);
        if ($user->isJurusan()) {
            $lowStockQuery->where('jurusan_id', $user->jurusan_id);
        }
        $lowStockItems = $lowStockQuery->get();

        return view('usages.index', compact('usages', 'jurusans', 'lowStockItems'));
    }

    /**
     * Tampilkan form pencatatan pemakaian bahan praktik baru.
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $itemsQuery = Item::where('jenis', 'bahan')->orderBy('nama_barang');

        if ($user->isJurusan()) {
            $itemsQuery->where('jurusan_id', $user->jurusan_id);
        }

        $items = $itemsQuery->get();
        $selectedItemId = $request->query('item_id');

        return view('usages.create', compact('items', 'selectedItemId', 'user'));
    }

    /**
     * Simpan transaksi pemakaian bahan dan kurangi stok item secara otomatis.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($request->has('jumlah')) {
            $request->merge([
                'jumlah' => str_replace(',', '.', (string) $request->input('jumlah')),
            ]);
        }

        $validated = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'jumlah' => ['required', 'numeric', 'min:0.01'],
            'tanggal_pemakaian' => ['required', 'date'],
            'nama_guru' => ['required', 'string', 'max:150'],
            'kelas' => ['nullable', 'string', 'max:100'],
            'keperluan_jobsheet' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string'],
        ]);

        $item = Item::findOrFail($validated['item_id']);

        if ($user->isJurusan() && $item->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak berhak mencatat pemakaian bahan dari jurusan lain.');
        }

        if ($item->jumlah < $validated['jumlah']) {
            return back()->withInput()->withErrors([
                'jumlah' => "Stok {$item->nama_barang} tidak mencukupi! Stok saat ini: {$item->jumlah} {$item->satuan}.",
            ]);
        }

        $stokSebelum = $item->jumlah;
        $stokSesudah = $stokSebelum - $validated['jumlah'];

        // Kurangi stok barang
        $item->update(['jumlah' => $stokSesudah]);

        // Buat log pemakaian
        ItemUsage::create([
            'item_id' => $item->id,
            'jurusan_id' => $item->jurusan_id,
            'user_id' => $user->id,
            'jumlah' => $validated['jumlah'],
            'satuan' => $item->satuan,
            'tanggal_pemakaian' => $validated['tanggal_pemakaian'],
            'nama_guru' => $validated['nama_guru'],
            'kelas' => $validated['kelas'],
            'keperluan_jobsheet' => $validated['keperluan_jobsheet'],
            'stok_sebelum' => $stokSebelum,
            'stok_sesudah' => $stokSesudah,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        $pesan = "Pemakaian {$validated['jumlah']} {$item->satuan} {$item->nama_barang} berhasil dicatat. Sisa stok: {$stokSesudah} {$item->satuan}.";
        if ($stokSesudah <= $item->min_stok && $item->min_stok > 0) {
            $pesan .= " PERINGATAN: Sisa stok berada pada atau di bawah batas minimum ({$item->min_stok} {$item->satuan})!";
        }

        return redirect()->route('usages.index')->with('success', $pesan);
    }

    /**
     * Tampilkan formulir edit pemakaian bahan praktik.
     */
    public function edit(Request $request, ItemUsage $usage): View
    {
        $user = $request->user();

        if ($user->isJurusan() && $usage->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak berhak mengedit pemakaian ini.');
        }

        $itemsQuery = Item::where('jenis', 'bahan')->orderBy('nama_barang');
        if ($user->isJurusan()) {
            $itemsQuery->where('jurusan_id', $user->jurusan_id);
        }
        $items = $itemsQuery->get();

        return view('usages.edit', compact('usage', 'items', 'user'));
    }

    /**
     * Perbarui data pemakaian bahan dan sesuaikan stok barang secara proporsional.
     */
    public function update(Request $request, ItemUsage $usage): RedirectResponse
    {
        $user = $request->user();

        if ($user->isJurusan() && $usage->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak berhak mengedit pemakaian ini.');
        }

        if ($request->has('jumlah')) {
            $request->merge([
                'jumlah' => str_replace(',', '.', (string) $request->input('jumlah')),
            ]);
        }

        $validated = $request->validate([
            'jumlah' => ['required', 'numeric', 'min:0.01'],
            'tanggal_pemakaian' => ['required', 'date'],
            'nama_guru' => ['required', 'string', 'max:150'],
            'kelas' => ['nullable', 'string', 'max:100'],
            'keperluan_jobsheet' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string'],
        ]);

        $item = $usage->item;
        $jumlahLama = $usage->jumlah;
        $jumlahBaru = $validated['jumlah'];
        $selisih = $jumlahBaru - $jumlahLama; // positif: pemakaian bertambah (stok berkurang), negatif: pemakaian berkurang (stok bertambah)

        // Cek apakah stok cukup jika pemakaian bertambah
        if ($selisih > 0 && $item->jumlah < $selisih) {
            return back()->withInput()->withErrors([
                'jumlah' => "Tambahan pemakaian ({$selisih} {$item->satuan}) melebihi sisa stok yang tersedia ({$item->jumlah} {$item->satuan})!",
            ]);
        }

        $stokSesudahBaru = $item->jumlah - $selisih;
        $item->update(['jumlah' => $stokSesudahBaru]);

        $usage->update([
            'jumlah' => $jumlahBaru,
            'tanggal_pemakaian' => $validated['tanggal_pemakaian'],
            'nama_guru' => $validated['nama_guru'],
            'kelas' => $validated['kelas'],
            'keperluan_jobsheet' => $validated['keperluan_jobsheet'],
            'stok_sesudah' => $stokSesudahBaru,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()->route('usages.index')
            ->with('success', "Data pemakaian {$item->nama_barang} berhasil diperbarui. Sisa stok: {$stokSesudahBaru} {$item->satuan}.");
    }

    /**
     * Hapus / batalkan pencatatan pemakaian bahan dan kembalikan stok.
     */
    public function destroy(Request $request, ItemUsage $usage): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $usage->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak berhak membatalkan pemakaian ini.');
        }

        $item = $usage->item;
        if ($item) {
            $item->increment('jumlah', $usage->jumlah);
        }

        $usage->delete();

        return redirect()->route('usages.index')
            ->with('success', "Pencatatan pemakaian bahan dibatalkan. Stok {$item->nama_barang} telah dikembalikan sebanyak {$usage->jumlah} {$usage->satuan}.");
    }
}
