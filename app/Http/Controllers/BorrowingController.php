<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\Jurusan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BorrowingController extends Controller
{
    /**
     * Tampilkan riwayat dan daftar peminjaman alat.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Borrowing::with(['item', 'itemUnit', 'jurusan'])->latest();

        if ($user->isJurusan()) {
            $query->where('jurusan_id', $user->jurusan_id);
        } elseif ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_peminjam', 'like', "%{$search}%")
                    ->orWhere('kelas_atau_jabatan', 'like', "%{$search}%")
                    ->orWhereHas('item', fn ($i) => $i->where('nama_barang', 'like', "%{$search}%"))
                    ->orWhereHas('itemUnit', fn ($u) => $u->where('unit_code', 'like', "%{$search}%"));
            });
        }

        $borrowings = $query->paginate(10)->withQueryString();
        $jurusans = Jurusan::all();

        return view('borrowings.index', compact('borrowings', 'jurusans'));
    }

    /**
     * Tampilkan formulir pencatatan peminjaman alat.
     */
    public function create(Request $request): View
    {
        $user = $request->user();

        // Hanya alat bengkel yang bisa dipinjam
        $itemsQuery = Item::where('jenis', 'alat')
            ->with(['jurusan', 'units' => fn ($q) => $q->where('status', 'tersedia')]);

        if ($user->isJurusan()) {
            $itemsQuery->where('jurusan_id', $user->jurusan_id);
        }

        $items = $itemsQuery->orderBy('nama_barang')->get();
        $selectedItemId = $request->query('item_id');
        $selectedUnitId = $request->query('unit_id');

        $selectedItem = null;
        if ($selectedItemId) {
            $selectedItem = $items->firstWhere('id', (int) $selectedItemId);
        }

        return view('borrowings.create', compact('items', 'user', 'selectedItemId', 'selectedUnitId', 'selectedItem'));
    }

    /**
     * Simpan data peminjaman alat.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'item_unit_id' => ['nullable', 'exists:item_units,id'],
            'nama_peminjam' => ['required', 'string', 'max:255'],
            'kelas_atau_jabatan' => ['nullable', 'string', 'max:100'],
            'kontak' => ['nullable', 'string', 'max:50'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal_pinjam' => ['required', 'date'],
            'catatan' => ['nullable', 'string'],
        ], [
            'item_id.required' => 'Pilih alat yang dipinjam.',
            'nama_peminjam.required' => 'Nama peminjam wajib diisi.',
            'jumlah.required' => 'Jumlah alat wajib diisi minimal 1.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
        ]);

        $item = Item::findOrFail($validated['item_id']);

        // Pastikan akun jurusan hanya bisa meminjamkan alat jurusannya
        if ($user->isJurusan() && $item->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak dapat meminjamkan alat dari jurusan lain.');
        }

        // Jika unit fisik tertentu dipinjam, pastikan unit milik item ini dan berstatus tersedia
        if (! empty($validated['item_unit_id'])) {
            $unit = ItemUnit::where('item_id', $item->id)->find($validated['item_unit_id']);
            if (! $unit) {
                return back()->withInput()->withErrors(['item_unit_id' => 'Unit fisik yang dipilih tidak sesuai dengan alat ini.']);
            }
            if ($unit->status !== 'tersedia') {
                return back()->withInput()->withErrors(['item_unit_id' => "Unit fisik '{$unit->unit_code}' sedang tidak tersedia (status: {$unit->status})."]);
            }
        }

        $validated['jurusan_id'] = $item->jurusan_id;
        $validated['status'] = 'dipinjam';

        $borrowing = Borrowing::create($validated);

        // Jika unit fisik tertentu dipinjam, ubah status unit menjadi 'dipinjam'
        if (! empty($validated['item_unit_id'])) {
            $unit = ItemUnit::find($validated['item_unit_id']);
            if ($unit) {
                $unit->update(['status' => 'dipinjam']);
            }
        }

        return redirect()->route('borrowings.index')
            ->with('success', "Peminjaman alat '{$item->nama_barang}' oleh {$validated['nama_peminjam']} berhasil dicatat.");
    }

    /**
     * Kembalikan alat (tandai status kembali).
     */
    public function markAsReturned(Borrowing $borrowing, Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $borrowing->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak untuk memproses pengembalian ini.');
        }

        $borrowing->update([
            'status' => 'kembali',
            'tanggal_kembali' => now()->toDateString(),
        ]);

        // Jika ada item unit, kembalikan status unit ke 'tersedia'
        if ($borrowing->item_unit_id && $borrowing->itemUnit) {
            $borrowing->itemUnit->update(['status' => 'tersedia']);
        }

        return redirect()->route('borrowings.index')
            ->with('success', "Alat '{$borrowing->item->nama_barang}' yang dipinjam oleh {$borrowing->nama_peminjam} telah ditandai kembali.");
    }

    /**
     * Tampilkan form edit peminjaman alat.
     */
    public function edit(Borrowing $borrowing, Request $request): View
    {
        $user = $request->user();
        if ($user->isJurusan() && $borrowing->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak untuk mengedit data peminjaman jurusan lain.');
        }

        $itemsQuery = Item::where('jenis', 'alat')->with(['units' => function ($q) use ($borrowing) {
            $q->where('status', 'tersedia')
                ->orWhere('id', $borrowing->item_unit_id);
        }]);

        if ($user->isJurusan()) {
            $itemsQuery->where('jurusan_id', $user->jurusan_id);
        }

        $items = $itemsQuery->orderBy('nama_barang')->get();

        return view('borrowings.edit', compact('borrowing', 'items', 'user'));
    }

    /**
     * Perbarui data peminjaman alat.
     */
    public function update(Request $request, Borrowing $borrowing): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $borrowing->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak untuk memperbarui data peminjaman jurusan lain.');
        }

        $validated = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'item_unit_id' => ['nullable', 'exists:item_units,id'],
            'nama_peminjam' => ['required', 'string', 'max:255'],
            'kelas_atau_jabatan' => ['nullable', 'string', 'max:100'],
            'kontak' => ['nullable', 'string', 'max:50'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali' => ['nullable', 'date'],
            'status' => ['required', 'in:dipinjam,kembali'],
            'catatan' => ['nullable', 'string'],
        ], [
            'item_id.required' => 'Pilih alat yang dipinjam.',
            'nama_peminjam.required' => 'Nama peminjam wajib diisi.',
            'jumlah.required' => 'Jumlah alat wajib diisi minimal 1.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'status.required' => 'Status peminjaman wajib dipilih.',
        ]);

        $item = Item::findOrFail($validated['item_id']);
        if ($user->isJurusan() && $item->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak dapat meminjamkan alat dari jurusan lain.');
        }

        $validated['jurusan_id'] = $item->jurusan_id;

        // Auto date adjustment based on status
        if ($validated['status'] === 'kembali' && empty($validated['tanggal_kembali'])) {
            $validated['tanggal_kembali'] = now()->toDateString();
        } elseif ($validated['status'] === 'dipinjam') {
            $validated['tanggal_kembali'] = null;
        }

        $oldUnitId = $borrowing->item_unit_id;
        $newUnitId = ! empty($validated['item_unit_id']) ? (int) $validated['item_unit_id'] : null;
        $newStatus = $validated['status'];

        // Sinkronisasi status unit fisik
        if ($oldUnitId && $oldUnitId !== $newUnitId) {
            ItemUnit::where('id', $oldUnitId)->update(['status' => 'tersedia']);
        }

        if ($newUnitId) {
            $unitStatus = $newStatus === 'dipinjam' ? 'dipinjam' : 'tersedia';
            ItemUnit::where('id', $newUnitId)->update(['status' => $unitStatus]);
        } elseif ($oldUnitId && ! $newUnitId) {
            ItemUnit::where('id', $oldUnitId)->update(['status' => 'tersedia']);
        }

        $borrowing->update($validated);

        return redirect()->route('borrowings.index')
            ->with('success', "Data peminjaman alat '{$item->nama_barang}' oleh {$borrowing->nama_peminjam} berhasil diperbarui.");
    }

    /**
     * Hapus data peminjaman alat.
     */
    public function destroy(Borrowing $borrowing, Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $borrowing->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus data peminjaman jurusan lain.');
        }

        // Jika status saat ini masih dipinjam dan memiliki unit fisik, kembalikan ke tersedia
        if ($borrowing->status === 'dipinjam' && $borrowing->item_unit_id) {
            ItemUnit::where('id', $borrowing->item_unit_id)->update(['status' => 'tersedia']);
        }

        $namaPeminjam = $borrowing->nama_peminjam;
        $namaBarang = $borrowing->item->nama_barang ?? 'Alat';

        $borrowing->delete();

        return redirect()->route('borrowings.index')
            ->with('success', "Data peminjaman '{$namaBarang}' oleh {$namaPeminjam} berhasil dihapus.");
    }
}
