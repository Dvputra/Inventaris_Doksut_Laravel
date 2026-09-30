<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ItemUnitController extends Controller
{
    /**
     * Tambah unit fisik baru untuk barang tertentu.
     */
    public function store(Request $request, Item $item): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $item->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menambah unit pada barang ini.');
        }

        $validated = $request->validate([
            'unit_code' => ['nullable', 'string', 'max:60', 'unique:item_units,unit_code'],
            'nomor_seri' => ['nullable', 'string', 'max:100'],
            'nomor_meja' => ['nullable', 'string', 'max:50'],
            'processor' => ['nullable', 'string', 'max:255'],
            'ram' => ['nullable', 'string', 'max:100'],
            'storage' => ['nullable', 'string', 'max:100'],
            'gpu_vga' => ['nullable', 'string', 'max:100'],
            'monitor' => ['nullable', 'string', 'max:100'],
            'sistem_operasi' => ['nullable', 'string', 'max:100'],
            'kondisi' => ['required', 'in:baik,rusak_ringan,rusak_berat'],
            'status' => ['required', 'in:tersedia,dipinjam,dalam_perbaikan,afkir'],
            'lokasi_penempatan' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string'],
        ]);

        if (empty($validated['unit_code'])) {
            $nextNumber = $item->units()->count() + 1;
            $validated['unit_code'] = sprintf('%s-%02d', $item->kode_barang, $nextNumber);
        }

        // Jika item adalah komputer dan field spec unit kosong, warisi spec default dari parent item
        if ($item->is_computer) {
            $validated['processor'] = $validated['processor'] ?? $item->processor;
            $validated['ram'] = $validated['ram'] ?? $item->ram;
            $validated['storage'] = $validated['storage'] ?? $item->storage;
            $validated['gpu_vga'] = $validated['gpu_vga'] ?? $item->gpu_vga;
            $validated['monitor'] = $validated['monitor'] ?? $item->monitor;
            $validated['sistem_operasi'] = $validated['sistem_operasi'] ?? $item->sistem_operasi;
        }

        $validated['item_id'] = $item->id;
        $validated['jurusan_id'] = $item->jurusan_id;
        $validated['tanggal_masuk'] = $validated['tanggal_masuk'] ?? now()->toDateString();

        ItemUnit::create($validated);

        // Sinkronisasi jumlah item jika unit bertambah melebihi item->jumlah
        if ($item->units()->count() > $item->jumlah) {
            $item->update(['jumlah' => $item->units()->count()]);
        }

        return redirect()->route('items.show', $item)
            ->with('success', "Unit fisik '{$validated['unit_code']}' berhasil ditambahkan.");
    }

    /**
     * Tambah unit fisik secara batch (re-stok sekaligus).
     */
    public function storeBatch(Request $request, Item $item): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $item->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menambah unit pada barang ini.');
        }

        $validated = $request->validate([
            'jumlah_unit' => ['required', 'integer', 'min:1', 'max:100'],
            'tanggal_masuk' => ['required', 'date'],
            'kondisi' => ['required', 'in:baik,rusak_ringan,rusak_berat'],
            'lokasi_penempatan' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string'],
        ]);

        // Cari nomor unit terakhir dari kode barang yang sudah ada
        $lastUnit = $item->units()
            ->where('unit_code', 'like', $item->kode_barang.'-%')
            ->orderByRaw("CAST(REPLACE(unit_code, ?, '') AS INTEGER) DESC", [$item->kode_barang.'-'])
            ->first();

        $lastNumber = 0;
        if ($lastUnit) {
            $parts = explode('-', $lastUnit->unit_code);
            $lastNumber = (int) end($parts);
        }

        $createdCodes = [];
        for ($i = 1; $i <= $validated['jumlah_unit']; $i++) {
            $nextNumber = $lastNumber + $i;
            $unitCode = sprintf('%s-%02d', $item->kode_barang, $nextNumber);

            ItemUnit::create([
                'item_id' => $item->id,
                'jurusan_id' => $item->jurusan_id,
                'unit_code' => $unitCode,
                'kondisi' => $validated['kondisi'],
                'status' => 'tersedia',
                'lokasi_penempatan' => $validated['lokasi_penempatan'],
                'catatan' => $validated['catatan'],
                'tanggal_masuk' => $validated['tanggal_masuk'],
            ]);

            $createdCodes[] = $unitCode;
        }

        // Sinkronisasi jumlah item dengan total unit aktual
        $item->update(['jumlah' => $item->units()->count()]);

        $firstCode = reset($createdCodes);
        $lastCode = end($createdCodes);
        $count = count($createdCodes);

        return redirect()->route('items.show', $item)
            ->with('success', "Re-stok berhasil! {$count} unit baru ditambahkan ({$firstCode} s/d {$lastCode}).");
    }

    /**
     * Perbarui data unit fisik tertentu.
     */
    public function update(Request $request, ItemUnit $unit): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $unit->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah unit ini.');
        }

        $validated = $request->validate([
            'nomor_seri' => ['nullable', 'string', 'max:100'],
            'nomor_meja' => ['nullable', 'string', 'max:50'],
            'processor' => ['nullable', 'string', 'max:255'],
            'ram' => ['nullable', 'string', 'max:100'],
            'storage' => ['nullable', 'string', 'max:100'],
            'gpu_vga' => ['nullable', 'string', 'max:100'],
            'monitor' => ['nullable', 'string', 'max:100'],
            'sistem_operasi' => ['nullable', 'string', 'max:100'],
            'kondisi' => ['required', 'in:baik,rusak_ringan,rusak_berat'],
            'status' => ['required', 'in:tersedia,dipinjam,dalam_perbaikan,afkir'],
            'lokasi_penempatan' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string'],
        ]);

        $previousStatus = $unit->status;
        $unit->update($validated);

        if ($previousStatus === 'dalam_perbaikan' && $validated['status'] === 'tersedia') {
            $unit->maintenanceLogs()->where('status', 'proses')->update([
                'status' => 'selesai',
                'tindakan_perbaikan' => 'Selesai melalui pembaruan data unit.',
            ]);
        } elseif ($previousStatus === 'dalam_perbaikan' && $validated['status'] === 'afkir') {
            $unit->maintenanceLogs()->where('status', 'proses')->update([
                'status' => 'tidak_dapat_diperbaiki',
            ]);
        }

        return redirect()->route('items.show', $unit->item_id)
            ->with('success', "Data unit fisik '{$unit->unit_code}' berhasil diperbarui.");
    }

    /**
     * Hapus data unit fisik.
     */
    public function destroy(Request $request, ItemUnit $unit): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $unit->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus unit ini.');
        }

        $itemId = $unit->item_id;
        $unitCode = $unit->unit_code;
        $unit->delete();

        // Update jumlah item jika perlu
        $item = Item::find($itemId);
        if ($item && $item->jumlah > $item->units()->count()) {
            $item->update(['jumlah' => max(0, $item->units()->count())]);
        }

        return redirect()->route('items.show', $itemId)
            ->with('success', "Unit fisik '{$unitCode}' berhasil dihapus.");
    }
}
