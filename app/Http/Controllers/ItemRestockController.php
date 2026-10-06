<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemRestock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ItemRestockController extends Controller
{
    /**
     * Simpan transaksi penambahan stok bahan (Re-stok Masuk).
     */
    public function store(Request $request, Item $item): RedirectResponse
    {
        $user = $request->user();

        if ($user->isJurusan() && $item->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menambah stok barang ini.');
        }

        if ($item->jenis !== 'bahan') {
            return back()->with('error', 'Re-stok via formulir ini hanya diperuntukkan bagi kategori bahan habis pakai.');
        }

        if ($request->has('jumlah')) {
            $request->merge([
                'jumlah' => str_replace(',', '.', (string) $request->input('jumlah')),
            ]);
        }

        $validated = $request->validate([
            'jumlah' => ['required', 'numeric', 'min:0.01'],
            'tanggal_masuk' => ['required', 'date'],
            'sumber_dana' => ['nullable', 'string', 'max:100'],
            'pemasok' => ['nullable', 'string', 'max:150'],
            'catatan' => ['nullable', 'string'],
        ]);

        $stokSebelum = $item->jumlah;
        $stokSesudah = $stokSebelum + $validated['jumlah'];

        // Tambah stok item
        $item->update(['jumlah' => $stokSesudah]);

        // Buat log re-stok masuk
        ItemRestock::create([
            'item_id' => $item->id,
            'jurusan_id' => $item->jurusan_id,
            'user_id' => $user->id,
            'jumlah' => $validated['jumlah'],
            'satuan' => $item->satuan,
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'sumber_dana' => $validated['sumber_dana'] ?? null,
            'pemasok' => $validated['pemasok'] ?? null,
            'stok_sebelum' => $stokSebelum,
            'stok_sesudah' => $stokSesudah,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()->route('items.show', $item)
            ->with('success', "Re-stok berhasil! Stok {$item->nama_barang} bertambah {$validated['jumlah']} {$item->satuan} (Total sekarang: {$stokSesudah} {$item->satuan}).");
    }

    /**
     * Hapus / batalkan pencatatan re-stok bahan dan kurangi kembali stoknya.
     */
    public function destroy(Request $request, ItemRestock $restock): RedirectResponse
    {
        $user = $request->user();

        if ($user->isJurusan() && $restock->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak berhak membatalkan riwayat re-stok ini.');
        }

        $item = $restock->item;

        if ($item && $item->jumlah < $restock->jumlah) {
            return back()->with('error', "Tidak dapat membatalkan re-stok karena sisa stok saat ini ({$item->jumlah} {$item->satuan}) lebih sedikit dari jumlah re-stok ({$restock->jumlah} {$restock->satuan}). Sebagian bahan mungkin telah digunakan.");
        }

        if ($item) {
            $item->decrement('jumlah', $restock->jumlah);
        }

        $restock->delete();

        return redirect()->route('items.show', $item ?? 1)
            ->with('success', "Riwayat re-stok berhasil dibatalkan. Stok {$item->nama_barang} disesuaikan kembali (-{$restock->jumlah} {$restock->satuan}).");
    }
}
