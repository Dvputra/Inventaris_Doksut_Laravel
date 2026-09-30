@extends('layouts.app')

@section('title', 'Ubah Unit Kerja: ' . $jurusan->nama)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('jurusans.index') }}" class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors shadow-xs" title="Kembali ke Daftar">
            <i class="bi bi-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Ubah Data Unit Kerja / Jurusan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Perbarui nama unit, kode singkatan, atau nama kepala bengkel / kepala unit kerja.</p>
        </div>
    </div>

    <!-- Info Box -->
    <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                {{ $jurusan->kode }}
            </div>
            <div>
                <h4 class="font-bold text-sm text-slate-900">{{ $jurusan->nama }}</h4>
                <p class="text-xs text-slate-500">Terdaftar dengan {{ $jurusan->items()->count() }} barang inventaris dan {{ $jurusan->users()->count() }} akun pengguna.</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white text-blue-700 border border-blue-200 shadow-xs shrink-0">
            <i class="bi bi-check2-circle text-emerald-500"></i> Aktif
        </span>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form action="{{ route('jurusans.update', $jurusan) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <!-- Kode & Nama Unit -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                    <div class="sm:col-span-4">
                        <label for="kode" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Kode / Singkatan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="kode" id="kode" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border @error('kode') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm font-mono uppercase text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                               value="{{ old('kode', $jurusan->kode) }}" required maxlength="20"
                               {{ $jurusan->kode === 'SAR' ? 'readonly' : '' }}>
                        @if($jurusan->kode === 'SAR')
                            <p class="text-[11px] text-amber-600 mt-1">Kode unit SAR adalah kode sistem utama dan terkunci.</p>
                        @endif
                        @error('kode')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-8">
                        <label for="nama" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nama Lengkap Unit Kerja / Jurusan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama" id="nama" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border @error('nama') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                               value="{{ old('nama', $jurusan->nama) }}" required maxlength="255">
                        @error('nama')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Kepala Bengkel / Kepala Unit -->
                <div>
                    <label for="kepala_bengkel" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nama Kepala Bengkel / Kepala Unit Kerja
                    </label>
                    <div class="relative rounded-xl">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-person-badge text-xs"></i>
                        </div>
                        <input type="text" name="kepala_bengkel" id="kepala_bengkel" 
                               class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border @error('kepala_bengkel') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                               placeholder="Contoh: Bpk. Budi Santoso, S.Pd / Waka Bidang..." 
                               value="{{ old('kepala_bengkel', $jurusan->kepala_bengkel) }}">
                    </div>
                    <span class="block text-[11px] text-slate-400 mt-1">Nama ini akan dicantumkan pada header dashboard unit dan lembar pengesahan tanda tangan dokumen cetak.</span>
                    @error('kepala_bengkel')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi / Ruang Lingkup -->
                <div>
                    <label for="deskripsi" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Deskripsi / Ruang Lingkup Fasilitas (Opsional)
                    </label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" 
                              class="w-full px-3.5 py-2.5 bg-slate-50 border @error('deskripsi') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">{{ old('deskripsi', $jurusan->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3 mt-8 pt-5 border-t border-slate-100">
                <a href="{{ route('jurusans.index') }}" class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm transition-colors">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xs transition-colors">
                    <i class="bi bi-save text-sm"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
