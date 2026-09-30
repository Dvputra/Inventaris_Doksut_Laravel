@extends('layouts.app')

@section('title', 'Edit Pemakaian Bahan')

@section('content')
<div class="mb-6">
    <a href="{{ route('usages.index') }}" 
       class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-xl shadow-xs transition-all mb-3">
        <i class="bi bi-arrow-left"></i>
        <span>Kembali ke Log Pemakaian</span>
    </a>
    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Edit Pemakaian Bahan</h2>
    <p class="text-xs sm:text-sm text-slate-500 mt-1">Perbarui data transaksi pemakaian bahan. Stok barang akan otomatis disesuaikan secara proporsional.</p>
</div>

<div class="max-w-3xl mx-auto bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 sm:p-8">
    <form action="{{ route('usages.update', $usage) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
            <!-- Informasi Bahan (Read-Only) -->
            <div class="md:col-span-12">
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Bahan / ATK
                </label>
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm border border-blue-100">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">{{ $usage->item->nama_barang ?? 'Barang Terhapus' }}</h4>
                            <span class="font-mono text-[10px] text-slate-500">Kode: {{ $usage->item->kode_barang ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 block">Sisa Stok Saat Ini</span>
                        <strong class="text-xs font-bold text-blue-700">{{ $usage->item->jumlah }} {{ $usage->satuan }}</strong>
                    </div>
                </div>
            </div>

            <!-- Jumlah Pemakaian & Satuan -->
            <div class="md:col-span-6">
                <label for="jumlah" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Jumlah yang Dipakai <span class="text-rose-500">*</span>
                </label>
                <div class="flex">
                    <input type="number" name="jumlah" id="jumlah" min="1" 
                           class="flex-1 px-3.5 py-2.5 text-xs rounded-l-xl border @error('jumlah') border-rose-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                           value="{{ old('jumlah', $usage->jumlah) }}" required>
                    <span class="px-3.5 py-2.5 text-xs font-semibold bg-slate-100 text-slate-600 border border-l-0 border-slate-200 rounded-r-xl">
                        {{ $usage->satuan }}
                    </span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">
                    Sebelumnya: {{ $usage->jumlah }} {{ $usage->satuan }}. Maksimal tambahan: {{ $usage->item->jumlah }} {{ $usage->satuan }}.
                </p>
                @error('jumlah') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Tanggal Pemakaian -->
            <div class="md:col-span-6">
                <label for="tanggal_pemakaian" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Tanggal Pemakaian <span class="text-rose-500">*</span>
                </label>
                <input type="date" name="tanggal_pemakaian" id="tanggal_pemakaian" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border @error('tanggal_pemakaian') border-rose-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       value="{{ old('tanggal_pemakaian', $usage->tanggal_pemakaian->format('Y-m-d')) }}" required>
                @error('tanggal_pemakaian') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Guru Pengampu / Penanggung Jawab & Kelas -->
            <div class="md:col-span-6">
                <label for="nama_guru" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Nama Guru Pengampu / Penanggung Jawab <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_guru" id="nama_guru" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border @error('nama_guru') border-rose-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       placeholder="Contoh: Bpk. Budi Santoso, S.Pd / Ibu Ratna (Staf TU)" 
                       value="{{ old('nama_guru', $usage->nama_guru) }}" required>
                @error('nama_guru') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-6">
                <label for="kelas" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Kelas / Kelompok Siswa / Nama <span class="text-[10px] font-normal text-slate-400">(Opsional)</span>
                </label>
                <input type="text" name="kelas" id="kelas" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border @error('kelas') border-rose-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       placeholder="Contoh: XI TKR 1 / Kelompok A / Budi Pratama" 
                       value="{{ old('kelas', $usage->kelas) }}">
                @error('kelas') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Keperluan / Jobsheet / Unit Kerja -->
            <div class="md:col-span-12">
                <label for="keperluan_jobsheet" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Materi / Jobsheet Praktikum / Unit Kerja <span class="text-[10px] font-normal text-slate-400">(Opsional)</span>
                </label>
                <input type="text" name="keperluan_jobsheet" id="keperluan_jobsheet" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border @error('keperluan_jobsheet') border-rose-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       placeholder="Contoh: Praktik Tune Up EFI Mesin Avanza / Pemeliharaan Unit Kerja Tata Usaha" 
                       value="{{ old('keperluan_jobsheet', $usage->keperluan_jobsheet) }}">
                @error('keperluan_jobsheet') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Catatan Tambahan -->
            <div class="md:col-span-12">
                <label for="catatan" class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Tambahan (Opsional)</label>
                <textarea name="catatan" id="catatan" rows="3" 
                          class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                          placeholder="Keterangan kondisi sisa, kelompok praktikan, atau informasi pendukung lainnya...">{{ old('catatan', $usage->catatan) }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-100">
            <!-- Batal (Urgency: Neutral) -->
            <a href="{{ route('usages.index') }}" 
               class="px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-xs transition-all active:scale-95">
                Batal
            </a>
            <!-- Simpan Perubahan (Urgency: Primary Action / Blue) -->
            <button type="submit" 
                    class="px-5 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-200 rounded-xl transition-all active:scale-95 flex items-center gap-2">
                <i class="bi bi-check2-circle"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>
@endsection
