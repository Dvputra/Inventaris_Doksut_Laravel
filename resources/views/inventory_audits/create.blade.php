@extends('layouts.app')

@section('title', 'Mulai Audit Baru - SIM Sarpras')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between pb-3 border-b border-slate-200/80">
        <div class="flex items-center gap-3">
            <a href="{{ route('inventory-audits.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Mulai Sesi Audit Baru</h1>
                <p class="text-xs sm:text-sm text-slate-500">Tentukan unit kerja yang akan diaudit untuk menarik data barang secara otomatis.</p>
            </div>
        </div>
    </div>

    <!-- Form Buat Audit -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
        <form action="{{ route('inventory-audits.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Identitas Dokumen Audit -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nomor / Kode Audit <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="audit_code" value="{{ old('audit_code', $suggestedCode) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 font-mono text-sm font-semibold focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <p class="text-[11px] text-slate-400 mt-1">Dibuat otomatis oleh sistem, dapat disesuaikan jika memiliki nomor dinas khusus.</p>
                    @error('audit_code') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Tanggal Pelaksanaan Audit <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal_audit" value="{{ old('tanggal_audit', date('Y-m-d')) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('tanggal_audit') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Judul / Nama Sesi Audit <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="judul" value="{{ old('judul', 'Audit Fisik Inventaris Berkala Semester Ganjil ' . date('Y')) }}" required
                           placeholder="Contoh: Audit Fisik Aset Laboratorium Komputer Semester Ganjil 2026/2027"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('judul') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Pilih Unit Kerja / Jurusan yang Diaudit <span class="text-rose-500">*</span>
                    </label>
                    <select name="jurusan_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">-- Pilih Unit Kerja / Laboratorium / Bengkel --</option>
                        @foreach($jurusans as $j)
                            <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>
                                [{{ $j->kode }}] {{ $j->nama }} (PJ: {{ $j->kepala_bengkel ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-amber-600 mt-1.5 flex items-center gap-1 font-medium">
                        <i class="bi bi-info-circle-fill"></i>
                        Seluruh barang aktif milik unit kerja yang dipilih akan otomatis disalin ke dalam lembar verifikasi audit ini.
                    </p>
                    @error('jurusan_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <hr class="border-slate-100">

            <!-- Identitas Petugas Auditor (Sarpras) -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Identitas Petugas Pemeriksa (Auditor Sarpras)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Petugas Auditor <span class="text-rose-500">*</span></label>
                        <input type="text" name="auditor_nama" value="{{ old('auditor_nama', $user->name) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-blue-500">
                        @error('auditor_nama') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">NIP / NPY Auditor</label>
                        <input type="text" name="auditor_nip" value="{{ old('auditor_nip') }}" placeholder="Contoh: 19800101 200501 1 001"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-blue-500">
                        @error('auditor_nip') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jabatan Auditor <span class="text-rose-500">*</span></label>
                        <input type="text" name="auditor_jabatan" value="{{ old('auditor_jabatan', 'Staff Sarana dan Prasarana') }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-blue-500">
                        @error('auditor_jabatan') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Catatan / Tujuan Khusus Audit -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Catatan / Arahan Pelaksanaan Audit (Opsional)</label>
                <textarea name="catatan_umum" rows="3" placeholder="Tuliskan catatan arahan audit, misalnya fokus pemeriksaan kelayakan mesin praktikum..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-blue-500">{{ old('catatan_umum') }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('inventory-audits.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all flex items-center gap-2 active:scale-95">
                    <i class="bi bi-box-arrow-in-down"></i>
                    <span>Buat Sesi &amp; Tarik Data Barang</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
