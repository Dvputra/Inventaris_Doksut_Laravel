<?php

namespace App\Http\Controllers;

use App\Models\ItemUnit;
use App\Models\MaintenanceLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MaintenanceLogController extends Controller
{
    /**
     * Catat log pemeliharaan / perbaikan unit fisik.
     */
    public function store(Request $request, ItemUnit $unit): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $unit->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak akses mencatat perbaikan unit ini.');
        }

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'gejala_kerusakan' => ['required', 'string'],
            'tindakan_perbaikan' => ['required', 'string'],
            'biaya' => ['nullable', 'numeric', 'min:0'],
            'teknisi_pelaksana' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'in:proses,selesai,tidak_dapat_diperbaiki'],
        ]);

        $validated['item_unit_id'] = $unit->id;
        $validated['jurusan_id'] = $unit->jurusan_id;
        $validated['user_id'] = $user->id;

        MaintenanceLog::create($validated);

        // Otomatis sinkronkan status & kondisi unit
        if ($validated['status'] === 'proses') {
            $unit->update([
                'status' => 'dalam_perbaikan',
                'kondisi' => 'rusak_ringan',
            ]);
        } elseif ($validated['status'] === 'selesai') {
            $unit->update([
                'status' => 'tersedia',
                'kondisi' => 'baik',
            ]);
        } elseif ($validated['status'] === 'tidak_dapat_diperbaiki') {
            $unit->update([
                'status' => 'afkir',
                'kondisi' => 'rusak_berat',
            ]);
        }

        return redirect()->route('items.show', $unit->item_id)
            ->with('success', "Riwayat perbaikan untuk unit '{$unit->unit_code}' berhasil dicatat.");
    }

    /**
     * Selesaikan proses servis unit fisik dan kembalikan ke status tersedia/afkir.
     */
    public function completeUnit(Request $request, ItemUnit $unit): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $unit->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $validated = $request->validate([
            'tanggal' => ['nullable', 'date'],
            'tindakan_perbaikan' => ['required', 'string'],
            'biaya' => ['nullable', 'numeric', 'min:0'],
            'teknisi_pelaksana' => ['nullable', 'string', 'max:150'],
            'kondisi' => ['required', 'in:baik,rusak_ringan,rusak_berat'],
        ]);

        $log = $unit->maintenanceLogs()->where('status', 'proses')->latest()->first();

        $isAfkir = $validated['kondisi'] === 'rusak_berat';
        $logStatus = $isAfkir ? 'tidak_dapat_diperbaiki' : 'selesai';

        if ($log) {
            $log->update([
                'status' => $logStatus,
                'tindakan_perbaikan' => $validated['tindakan_perbaikan'],
                'biaya' => $validated['biaya'] ?? $log->biaya,
                'teknisi_pelaksana' => $validated['teknisi_pelaksana'] ?? $log->teknisi_pelaksana,
                'tanggal' => $validated['tanggal'] ?? $log->tanggal,
            ]);
        } else {
            MaintenanceLog::create([
                'item_unit_id' => $unit->id,
                'jurusan_id' => $unit->jurusan_id,
                'user_id' => $user->id,
                'tanggal' => $validated['tanggal'] ?? now()->toDateString(),
                'gejala_kerusakan' => 'Perbaikan / Pemeliharaan Unit',
                'tindakan_perbaikan' => $validated['tindakan_perbaikan'],
                'biaya' => $validated['biaya'] ?? null,
                'teknisi_pelaksana' => $validated['teknisi_pelaksana'] ?? null,
                'status' => $logStatus,
            ]);
        }

        if ($isAfkir) {
            $unit->update([
                'status' => 'afkir',
                'kondisi' => 'rusak_berat',
            ]);
            $message = "Servis unit '{$unit->unit_code}' selesai dengan status tidak dapat diperbaiki (Afkir).";
        } else {
            $unit->update([
                'status' => 'tersedia',
                'kondisi' => $validated['kondisi'],
            ]);
            $message = "Servis unit '{$unit->unit_code}' berhasil diselesaikan dan status unit kembali Tersedia.";
        }

        return redirect()->route('items.show', $unit->item_id)
            ->with('success', $message);
    }

    /**
     * Batalkan status servis unit fisik dan kembalikan ke status tersedia.
     */
    public function cancelUnit(Request $request, ItemUnit $unit): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $unit->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        // Hapus log yang berstatus 'proses' untuk unit ini
        $activeLogs = $unit->maintenanceLogs()->where('status', 'proses')->get();
        foreach ($activeLogs as $log) {
            $log->delete();
        }

        // Kembalikan status dan kondisi unit ke tersedia
        $unit->update([
            'status' => 'tersedia',
            'kondisi' => 'baik',
        ]);

        return redirect()->route('items.show', $unit->item_id)
            ->with('success', "Status servis unit '{$unit->unit_code}' berhasil dibatalkan dan status unit dikembalikan ke Tersedia.");
    }

    /**
     * Perbarui status tindak lanjut perbaikan.
     */
    public function updateStatus(Request $request, MaintenanceLog $log): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $log->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:proses,selesai,tidak_dapat_diperbaiki'],
            'tindakan_perbaikan' => ['nullable', 'string'],
            'biaya' => ['nullable', 'numeric', 'min:0'],
            'teknisi_pelaksana' => ['nullable', 'string', 'max:150'],
            'kondisi' => ['nullable', 'in:baik,rusak_ringan,rusak_berat'],
        ]);

        $logData = ['status' => $validated['status']];
        if (isset($validated['tindakan_perbaikan'])) {
            $logData['tindakan_perbaikan'] = $validated['tindakan_perbaikan'];
        }
        if (isset($validated['biaya'])) {
            $logData['biaya'] = $validated['biaya'];
        }
        if (isset($validated['teknisi_pelaksana'])) {
            $logData['teknisi_pelaksana'] = $validated['teknisi_pelaksana'];
        }

        $log->update($logData);

        // Sinkronisasi status unit
        $unit = $log->itemUnit;
        if ($unit) {
            if ($validated['status'] === 'selesai') {
                $kondisi = $validated['kondisi'] ?? 'baik';
                $unit->update(['status' => 'tersedia', 'kondisi' => $kondisi]);
            } elseif ($validated['status'] === 'tidak_dapat_diperbaiki') {
                $unit->update(['status' => 'afkir', 'kondisi' => 'rusak_berat']);
            } elseif ($validated['status'] === 'proses') {
                $unit->update(['status' => 'dalam_perbaikan', 'kondisi' => 'rusak_ringan']);
            }
        }

        return back()->with('success', 'Status perbaikan berhasil diperbarui.');
    }

    /**
     * Hapus riwayat catatan perbaikan.
     */
    public function destroy(Request $request, MaintenanceLog $log): RedirectResponse
    {
        $user = $request->user();
        if ($user->isJurusan() && $log->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $unit = $log->itemUnit;
        $wasInProcess = ($log->status === 'proses');

        $log->delete();

        // Jika log yang dihapus adalah proses dan tidak ada log proses lainnya, kembalikan status unit
        if ($unit && $wasInProcess) {
            $hasOtherProses = $unit->maintenanceLogs()->where('status', 'proses')->exists();
            if (! $hasOtherProses && $unit->status === 'dalam_perbaikan') {
                $unit->update(['status' => 'tersedia', 'kondisi' => 'baik']);
            }
        }

        return back()->with('success', 'Catatan riwayat perbaikan berhasil dihapus.');
    }
}
