@extends('layouts.app')

@section('title', 'Detail Barang - ' . $item->nama_barang)

@section('content')
<div class="mb-6">
    <div class="flex flex-wrap items-center gap-2 mb-3">
        <!-- Kembali ke Daftar Barang (Urgency: Neutral) -->
        <a href="{{ route('items.index') }}" 
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-xl shadow-xs transition-all">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Daftar Barang</span>
        </a>
        @if(Auth::user()->isSarpras() && $item->jurusan->kode === 'SAR')
            @if(str_contains(strtolower($item->lokasi ?? ''), 'gudang') || $item->jenis === 'bahan')
                <!-- Buka Stok di Gudang (Urgency: Amber Accent) -->
                <a href="{{ route('sarpras.gudang') }}" 
                   class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-950 bg-amber-400 hover:bg-amber-300 border border-amber-400 px-3 py-1.5 rounded-xl shadow-xs transition-all">
                    <i class="bi bi-archive-fill"></i>
                    <span>Buka Stok di Gudang</span>
                </a>
            @else
                <!-- Buka Inventaris Umum (Urgency: Primary Blue) -->
                <a href="{{ route('sarpras.umum') }}" 
                   class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-3 py-1.5 rounded-xl shadow-xs transition-all">
                    <i class="bi bi-shield-check"></i>
                    <span>Buka Inventaris Fasilitas Umum</span>
                </a>
            @endif
        @endif
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5 mb-1.5">
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">{{ $item->nama_barang }}</h2>
                @if($item->is_computer)
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 border border-blue-200 shadow-xs">
                        <i class="bi bi-display"></i> PC
                    </span>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-mono text-xs font-semibold px-2.5 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                    {{ $item->kode_barang }}
                </span>
                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                    {{ $item->jurusan->nama }} ({{ $item->jurusan->kode }})
                </span>
                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700">
                    {{ $item->category->nama ?? 'Tanpa Kategori' }}
                </span>
                @if($item->jurusan->kode === 'SAR')
                    @if(str_contains(strtolower($item->lokasi ?? ''), 'gudang') || $item->jenis === 'bahan')
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-amber-400 text-slate-950 shadow-xs">
                            <i class="bi bi-archive-fill me-1"></i>Stok di Gudang
                        </span>
                    @else
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-blue-600 text-white shadow-xs">
                            <i class="bi bi-building me-1"></i>Fasilitas Umum
                        </span>
                    @endif
                @endif
            </div>
        </div>

        <!-- Action Buttons by Urgency -->
        <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2 w-full sm:w-auto mt-2 sm:mt-0">
            @if($item->jenis === 'alat')
                <!-- Tambah Unit Fisik (Urgency: Success / Emerald) -->
                <button type="button" onclick="openModal('addUnitModal')" 
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs transition-all active:scale-95 min-h-[40px] sm:min-h-0">
                    <i class="bi bi-plus-circle"></i>
                    <span>Tambah Unit</span>
                </button>
                <!-- Pinjamkan Alat (Urgency: Neutral Blue) -->
                <a href="{{ route('borrowings.create', ['item_id' => $item->id]) }}" 
                   class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-xl transition-all shadow-xs active:scale-95 min-h-[40px] sm:min-h-0">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Pinjamkan</span>
                </a>
            @else
                <!-- Re-stok Bahan (Urgency: Primary Action / Emerald) -->
                <button type="button" onclick="openModal('restokBahanModal')" 
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs transition-all active:scale-95 min-h-[40px] sm:min-h-0">
                    <i class="bi bi-box-arrow-in-down"></i>
                    <span>Re-stok Bahan</span>
                </button>
                <!-- Catat Pemakaian Bahan (Urgency: Neutral / Sky) -->
                <a href="{{ route('usages.create', ['item_id' => $item->id]) }}" 
                   class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 rounded-xl shadow-xs transition-all active:scale-95 min-h-[40px] sm:min-h-0">
                    <i class="bi bi-droplet-half"></i>
                    <span>Catat Pakai</span>
                </a>
            @endif
            <!-- Edit Data (Urgency: Primary Action / Blue) -->
            <a href="{{ route('items.edit', $item) }}" 
               class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs transition-all active:scale-95 min-h-[40px] sm:min-h-0">
                <i class="bi bi-pencil"></i>
                <span>Edit Data</span>
            </a>
            <!-- Hapus Barang (Urgency: Danger / Rose) -->
            <button type="button" 
                    onclick="confirmDeleteItem('{{ route('items.destroy', $item) }}', '{{ addslashes($item->nama_barang) }}', '{{ $item->kode_barang }}', '{{ addslashes($item->category->nama ?? '-') }}', '{{ $item->jumlah }} {{ $item->satuan }}')"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition-all shadow-xs active:scale-95 min-h-[40px] sm:min-h-0" 
                    title="Hapus Barang">
                <i class="bi bi-trash"></i>
                <span>Hapus</span>
            </button>
        </div>
    </div>
</div>


<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
    <!-- INFORMASI UMUM BARANG -->
    <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Informasi Umum Barang</h3>
        </div>
        <div class="p-5">
            <dl class="divide-y divide-slate-100 text-xs">
                <div class="py-2.5 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-slate-500">Kode Barang</dt>
                    <dd class="mt-1 font-mono font-bold text-blue-700 sm:col-span-2 sm:mt-0">{{ $item->kode_barang }}</dd>
                </div>
                <div class="py-2.5 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-slate-500">Nama Barang</dt>
                    <dd class="mt-1 font-bold text-slate-900 sm:col-span-2 sm:mt-0">{{ $item->nama_barang }}</dd>
                </div>
                <div class="py-2.5 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-slate-500">Penempatan Jurusan</dt>
                    <dd class="mt-1 text-slate-800 font-semibold sm:col-span-2 sm:mt-0">{{ $item->jurusan->nama }} ({{ $item->jurusan->kode }})</dd>
                </div>
                <div class="py-2.5 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-slate-500">Kategori</dt>
                    <dd class="mt-1 text-slate-800 sm:col-span-2 sm:mt-0">{{ $item->category->nama ?? '-' }}</dd>
                </div>
                <div class="py-2.5 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-slate-500">Jenis Inventaris</dt>
                    <dd class="mt-1 sm:col-span-2 sm:mt-0">
                        @if($item->jenis === 'alat')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                Alat / Mesin Praktik
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                Bahan / ATK (Habis Pakai)
                            </span>
                        @endif
                    </dd>
                </div>
                <div class="py-2.5 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-slate-500">Jumlah Stok Saat Ini</dt>
                    <dd class="mt-1 sm:col-span-2 sm:mt-0">
                        <span class="text-base font-extrabold text-slate-900">@formatJumlah($item->jumlah)</span>
                        <span class="text-slate-500">{{ $item->satuan }}</span>
                        @if($item->jenis === 'bahan' && $item->min_stok > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold ml-2 {{ $item->jumlah <= $item->min_stok ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600' }}">
                                Min Stok: @formatJumlah($item->min_stok) {{ $item->satuan }}
                            </span>
                        @endif
                    </dd>
                </div>
                <div class="py-2.5 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-slate-500">Kondisi Fisik</dt>
                    <dd class="mt-1 sm:col-span-2 sm:mt-0">
                        @if($item->kondisi === 'baik')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Baik</span>
                        @elseif($item->kondisi === 'rusak_ringan')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Rusak Ringan</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">Rusak Berat</span>
                        @endif
                    </dd>
                </div>
                <div class="py-2.5 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-slate-500">Lokasi Ruang / Lab</dt>
                    <dd class="mt-1 text-slate-800 sm:col-span-2 sm:mt-0">{{ $item->lokasi ?? '-' }}</dd>
                </div>
                <div class="py-2.5 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-slate-500">Sumber Dana & Tahun</dt>
                    <dd class="mt-1 text-slate-800 sm:col-span-2 sm:mt-0">{{ $item->sumber_dana ?? '-' }} (Tahun {{ $item->tahun_pengadaan ?? '-' }})</dd>
                </div>
                <div class="py-2.5 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="font-medium text-slate-500">Spesifikasi / Catatan</dt>
                    <dd class="mt-1 text-slate-700 sm:col-span-2 sm:mt-0 leading-relaxed">{{ $item->spesifikasi ?? 'Tidak ada catatan spesifikasi khusus.' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- KANAN: FOTO BARANG & RINGKASAN STATUS -->
    <div class="lg:col-span-5 flex flex-col gap-6">
        <!-- FOTO FISIK BARANG -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5 overflow-hidden">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="bi bi-camera text-blue-600"></i>
                    <span>Foto Fisik Barang</span>
                </h3>
                <a href="{{ route('items.edit', $item) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                    {{ $item->foto ? 'Ganti Foto' : 'Unggah Foto' }}
                </a>
            </div>
            @if($item->foto)
                <div class="relative rounded-xl overflow-hidden border border-slate-200 group bg-slate-100 max-h-56">
                    <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama_barang }}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-200">
                    <a href="{{ asset('storage/' . $item->foto) }}" target="_blank" class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-semibold transition-opacity">
                        <i class="bi bi-arrows-fullscreen mr-1.5"></i> Lihat Ukuran Penuh
                    </a>
                </div>
            @else
                <div class="py-8 px-4 rounded-xl border border-dashed border-slate-200 text-center bg-slate-50/70">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                        <i class="bi bi-image text-xl"></i>
                    </div>
                    <p class="text-xs font-medium text-slate-600">Belum ada foto fisik barang</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Unggah foto alat/mesin saat mengedit barang untuk memudahkan identifikasi visual.</p>
                </div>
            @endif
        </div>

        @if($item->jenis === 'alat')
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5 flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900">Ringkasan Unit Fisik</h3>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded bg-blue-50 text-blue-700">
                            {{ $item->units->count() }} Unit Terdata
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-center mb-4">
                        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-100">
                            <span class="text-xl font-black text-emerald-700 block">{{ $item->units->where('status', 'tersedia')->count() }}</span>
                            <span class="text-[11px] font-medium text-emerald-800">Tersedia Siap Pakai</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-100">
                            <span class="text-xl font-black text-amber-700 block">{{ $item->units->where('status', 'dipinjam')->count() }}</span>
                            <span class="text-[11px] font-medium text-amber-800">Sedang Dipinjam</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-100">
                            <span class="text-xl font-black text-rose-700 block">{{ $item->units->where('status', 'dalam_perbaikan')->count() }}</span>
                            <span class="text-[11px] font-medium text-rose-800">Dalam Perbaikan</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-slate-100 border border-slate-200">
                            <span class="text-xl font-black text-slate-700 block">{{ $item->units->where('status', 'afkir')->count() }}</span>
                            <span class="text-[11px] font-medium text-slate-600">Rusak Berat / Afkir</span>
                        </div>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-blue-50/50 border border-blue-200/60 text-xs text-slate-600 leading-relaxed">
                    <i class="bi bi-info-circle text-blue-600 mr-1"></i>
                    Setiap unit fisik memiliki kode unit tersendiri (ID Barang) untuk pemantauan serial number, meja laboratorium, dan catatan riwayat perbaikan/servis.
                </div>
            </div>
        @else
            <!-- RINGKASAN ARUS STOK BAHAN HABIS PAKAI -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5 flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900">Arus Stok Bahan Habis Pakai</h3>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600">
                            {{ $item->restocks->count() }} Masuk • {{ $item->usages->count() }} Keluar
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200/70 text-center">
                            <span class="text-[11px] font-semibold text-emerald-800 block mb-1">
                                <i class="bi bi-box-arrow-in-down mr-1"></i>Total Re-stok
                            </span>
                            <h4 class="text-xl font-black text-emerald-700">+@formatJumlah($item->restocks->sum('jumlah'))</h4>
                            <span class="text-[10px] text-emerald-600">{{ $item->satuan }}</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-rose-50/60 border border-rose-200/70 text-center">
                            <span class="text-[11px] font-semibold text-rose-800 block mb-1">
                                <i class="bi bi-box-arrow-up mr-1"></i>Total Keluar
                            </span>
                            <h4 class="text-xl font-black text-rose-700">-@formatJumlah($item->usages->sum('jumlah'))</h4>
                            <span class="text-[10px] text-rose-600">{{ $item->satuan }}</span>
                        </div>
                    </div>

                    @if($item->jumlah <= $item->min_stok && $item->min_stok > 0)
                        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 mb-4 flex items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-rose-600 text-base"></i>
                            <div>
                                <strong>Peringatan!</strong> Sisa stok ({{ $item->jumlah }} {{ $item->satuan }}) telah mencapai batas minimum.
                            </div>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-2 pt-2">
                    <button type="button" onclick="openModal('restokBahanModal')" 
                            class="py-2.5 px-3 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm rounded-xl transition-all text-center flex items-center justify-center gap-1.5 active:scale-95">
                        <i class="bi bi-box-arrow-in-down"></i>
                        <span>Re-stok Bahan</span>
                    </button>
                    <a href="{{ route('usages.create', ['item_id' => $item->id]) }}" 
                       class="py-2.5 px-3 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 shadow-sm rounded-xl transition-all text-center flex items-center justify-center gap-1.5 active:scale-95">
                        <i class="bi bi-droplet-half"></i>
                        <span>Catat Pemakaian</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- SECTION DAFTAR UNIT FISIK (JIKA ALAT) -->
@if($item->jenis === 'alat')
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs mb-6 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Daftar Unit Fisik & Nomor Meja / Seri (Item Units)</h3>
                <p class="text-[11px] text-slate-400">Pelacakan fisik setiap unit barang di bengkel / lab.</p>
            </div>
            <!-- Tambah Unit (Urgency: Primary Action / Blue) -->
            <div class="flex items-center gap-2">
                <button type="button" onclick="openModal('restokBatchModal')" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl shadow-xs transition-all active:scale-95">
                    <i class="bi bi-box-seam text-xs"></i>
                    <span>Re-stok Batch</span>
                </button>
                <button type="button" onclick="openModal('addUnitModal')" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs transition-all active:scale-95">
                    <i class="bi bi-plus-lg text-xs"></i>
                    <span>Tambah Unit</span>
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-4">ID Barang (Unit Code)</th>
                        <th class="py-3 px-4">Nomor Meja / Ruang</th>
                        <th class="py-3 px-4">Nomor Seri (SN)</th>
                        <th class="py-3 px-4">Kondisi Fisik</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Tanggal Masuk</th>
                        <th class="py-3 px-4">Riwayat Perbaikan</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($item->units as $unit)
                        <tr class="hover:bg-slate-50/75 transition-colors">
                            <td class="py-3 px-4">
                                <span class="font-mono font-bold text-blue-700 text-xs">{{ $unit->unit_code }}</span>
                                @if($item->is_computer)
                                    @php
                                        $uProc = $unit->processor ?: $item->processor;
                                        $uRam = $unit->ram ?: $item->ram;
                                        $uStorage = $unit->storage ?: $item->storage;
                                    @endphp
                                    @if($uProc || $uRam || $uStorage)
                                        <p class="text-[11px] text-slate-500 font-medium mt-0.5 flex items-center gap-1.5 flex-wrap">
                                            <span class="inline-flex items-center gap-1 text-slate-700 font-semibold">
                                                <i class="bi bi-cpu text-blue-600"></i>{{ $uProc ?: '-' }}
                                            </span>
                                            <span class="text-slate-300">•</span>
                                            <span class="inline-flex items-center gap-1 text-slate-600">
                                                <i class="bi bi-memory text-sky-600"></i>{{ $uRam ?: '-' }}
                                            </span>
                                            <span class="text-slate-300">•</span>
                                            <span class="inline-flex items-center gap-1 text-slate-600">
                                                <i class="bi bi-hdd text-amber-500"></i>{{ $uStorage ?: '-' }}
                                            </span>
                                        </p>
                                    @endif
                                @endif
                                @if($unit->catatan)
                                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $unit->catatan }}</p>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($unit->nomor_meja)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-mono font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                                        <i class="bi bi-laptop text-xs"></i> {{ $unit->nomor_meja }}
                                    </span>
                                @else
                                    <span class="text-slate-400">{{ $unit->lokasi_penempatan ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-500">{{ $unit->nomor_seri ?? '-' }}</td>
                            <td class="py-3 px-4">
                                @if($unit->kondisi === 'baik')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Baik</span>
                                @elseif($unit->kondisi === 'rusak_ringan')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Rusak Ringan</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">Rusak Berat</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($unit->status === 'tersedia')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Tersedia</span>
                                @elseif($unit->status === 'dipinjam')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Dipinjam</span>
                                @elseif($unit->status === 'dalam_perbaikan')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                        <span>Dalam Perbaikan</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">Afkir</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($unit->tanggal_masuk)
                                    <span class="text-slate-600 text-[11px] font-medium">{{ $unit->tanggal_masuk->format('d/m/Y') }}</span>
                                @else
                                    <span class="text-slate-400 text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($unit->maintenanceLogs->isNotEmpty())
                                    <button type="button" onclick="openModal('historyLogModal-{{ $unit->id }}')" 
                                            class="inline-flex items-center gap-1 font-mono text-[11px] font-bold text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 px-2 py-0.5 rounded-lg transition-colors">
                                        <i class="bi bi-wrench"></i>
                                        <span>{{ $unit->maintenanceLogs->count() }} Servis</span>
                                    </button>
                                @else
                                    <span class="text-slate-400 text-[11px]">Normal (Belum servis)</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1">
                                    @if($unit->status === 'tersedia')
                                        <!-- Pinjamkan Unit Ini (Urgency: Neutral Blue) -->
                                        <a href="{{ route('borrowings.create', ['item_id' => $item->id, 'unit_id' => $unit->id]) }}" 
                                           class="p-1.5 rounded-lg text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-colors shadow-xs" 
                                           title="Pinjamkan Unit Ini ({{ $unit->unit_code }})">
                                            <i class="bi bi-arrow-left-right text-xs"></i>
                                        </a>
                                    @endif

                                    @if($unit->status === 'dalam_perbaikan')
                                        <!-- Selesaikan Servis (Urgency: Emerald / Success) -->
                                        <button type="button" onclick="openModal('completeLogModal-{{ $unit->id }}')" 
                                                class="p-1.5 rounded-lg text-emerald-700 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors shadow-xs" 
                                                title="Selesaikan Servis & Kembalikan ke Tersedia">
                                            <i class="bi bi-check2-circle text-xs font-bold"></i>
                                        </button>
                                        <!-- Batalkan Servis (Urgency: Amber / Warning) -->
                                        <button type="button" 
                                                onclick="confirmCancelMaintenance('{{ route('units.maintenance.cancel', $unit) }}', '{{ $unit->unit_code }}')" 
                                                class="p-1.5 rounded-lg text-amber-700 hover:text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition-colors shadow-xs" 
                                                title="Batalkan Servis & Kembalikan ke Tersedia">
                                            <i class="bi bi-arrow-counterclockwise text-xs"></i>
                                        </button>
                                    @else
                                        <!-- Catat Servis (Urgency: Amber / Warning) -->
                                        <button type="button" onclick="openModal('addLogModal-{{ $unit->id }}')" 
                                                class="p-1.5 rounded-lg text-amber-700 hover:text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition-colors shadow-xs" 
                                                title="Catat Kerusakan / Servis">
                                            <i class="bi bi-tools text-xs"></i>
                                        </button>
                                    @endif

                                    <!-- Edit Unit (Neutral) -->
                                    <button type="button" onclick="openModal('editUnitModal-{{ $unit->id }}')" 
                                            class="p-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200 transition-colors shadow-xs" 
                                            title="Edit Data Unit">
                                        <i class="bi bi-pencil text-xs"></i>
                                    </button>
                                    <!-- Hapus Unit (Urgency: Danger / Rose) -->
                                    <button type="button" 
                                            onclick="confirmDeleteUnit('{{ route('units.destroy', $unit) }}', '{{ $unit->unit_code }}', '{{ addslashes($unit->nomor_meja ?? '-') }}')"
                                            class="p-1.5 rounded-lg text-rose-600 hover:text-rose-700 hover:bg-rose-50 border border-rose-200 transition-colors shadow-xs" 
                                            title="Hapus Unit">
                                        <i class="bi bi-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                Belum ada unit fisik yang didaftarkan. Klik tombol "+ Tambah Unit" untuk mendaftarkan unit fisik.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL-MODAL KELOLA UNIT FISIK -->
    @foreach($item->units as $unit)
        <!-- MODAL EDIT UNIT -->
        <div id="editUnitModal-{{ $unit->id }}" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" role="dialog" aria-modal="true">
            <div class="relative w-full {{ $item->is_computer ? 'max-w-xl' : 'max-w-md' }} bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200">
                <form action="{{ route('units.update', $unit) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-slate-900">Edit Unit: {{ $unit->unit_code }}</h4>
                            @if($item->is_computer)
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                    <i class="bi bi-display mr-0.5"></i> PC Workstation
                                </span>
                            @endif
                        </div>
                        <button type="button" onclick="closeModal('editUnitModal-{{ $unit->id }}')" class="text-slate-400 hover:text-slate-600 p-1">
                            <i class="bi bi-x-lg text-sm"></i>
                        </button>
                    </div>
                    <div class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                        @if($item->is_computer)
                            <!-- SPESIFIKASI KHUSUS UNIT KOMPUTER -->
                            <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-200/80 space-y-3">
                                <div class="pb-2 border-b border-blue-200/60">
                                    <span class="text-xs font-bold text-blue-900 flex items-center gap-1.5">
                                        <i class="bi bi-motherboard text-blue-600"></i> Spesifikasi Hardware Unit Ini
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Processor (CPU)</label>
                                        <input type="text" name="processor" class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="{{ $item->processor ?: 'Core i5 / Ryzen 5' }}" value="{{ $unit->processor ?? $item->processor }}">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Kapasitas RAM</label>
                                        <input type="text" name="ram" class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="{{ $item->ram ?: '8 GB / 16 GB' }}" value="{{ $unit->ram ?? $item->ram }}">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Penyimpanan (Storage)</label>
                                        <input type="text" name="storage" class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="{{ $item->storage ?: 'SSD 512 GB' }}" value="{{ $unit->storage ?? $item->storage }}">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Kartu Grafis (GPU / VGA)</label>
                                        <input type="text" name="gpu_vga" class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="{{ $item->gpu_vga ?: 'Integrated / GTX 1650' }}" value="{{ $unit->gpu_vga ?? $item->gpu_vga }}">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Monitor / Layar</label>
                                        <input type="text" name="monitor" class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="{{ $item->monitor ?: '24 Inch IPS' }}" value="{{ $unit->monitor ?? $item->monitor }}">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Sistem Operasi</label>
                                        <input type="text" name="sistem_operasi" class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="{{ $item->sistem_operasi ?: 'Windows 11 / Linux' }}" value="{{ $unit->sistem_operasi ?? $item->sistem_operasi }}">
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">{{ $item->is_computer ? 'Nomor Meja Lab PC' : 'Nomor Meja / Rak' }}</label>
                                <input type="text" name="nomor_meja" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="{{ $item->is_computer ? 'Contoh: Meja PC-01' : 'Nomor Meja / Rak' }}" value="{{ $unit->nomor_meja }}">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Seri Pabrik (SN)</label>
                                <input type="text" name="nomor_seri" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" value="{{ $unit->nomor_seri }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Kondisi Fisik</label>
                                <select name="kondisi" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" required>
                                    <option value="baik" {{ $unit->kondisi === 'baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="rusak_ringan" {{ $unit->kondisi === 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                    <option value="rusak_berat" {{ $unit->kondisi === 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Operasional</label>
                                <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" required>
                                    <option value="tersedia" {{ $unit->status === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                    <option value="dipinjam" {{ $unit->status === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                    <option value="dalam_perbaikan" {{ $unit->status === 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                                    <option value="afkir" {{ $unit->status === 'afkir' ? 'selected' : '' }}>Afkir</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Lokasi Penempatan</label>
                            <input type="text" name="lokasi_penempatan" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" value="{{ $unit->lokasi_penempatan }}">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Khusus</label>
                            <textarea name="catatan" rows="2" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">{{ $unit->catatan }}</textarea>
                        </div>
                    </div>
                    <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" onclick="closeModal('editUnitModal-{{ $unit->id }}')" class="px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL CATAT PEMELIHARAAN / SERVIS -->
        <div id="addLogModal-{{ $unit->id }}" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" role="dialog" aria-modal="true">
            <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200">
                <form action="{{ route('units.maintenance.store', $unit) }}" method="POST">
                    @csrf
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h4 class="text-sm font-bold text-slate-900">Catat Perbaikan Unit: {{ $unit->unit_code }}</h4>
                        <button type="button" onclick="closeModal('addLogModal-{{ $unit->id }}')" class="text-slate-400 hover:text-slate-600 p-1">
                            <i class="bi bi-x-lg text-sm"></i>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Servis</label>
                                <input type="date" name="tanggal" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Servis</label>
                                <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200" required>
                                    <option value="proses">Sedang Dikerjakan</option>
                                    <option value="selesai" selected>Selesai Diperbaiki</option>
                                    <option value="tidak_dapat_diperbaiki">Tidak Dapat Diperbaiki</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Gejala Kerusakan / Keluhan</label>
                            <textarea name="gejala_kerusakan" rows="2" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200" placeholder="Contoh: Kipas bising, tidak mau menyala, LCD bergaris..." required></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tindakan Perbaikan / Solusi</label>
                            <textarea name="tindakan_perbaikan" rows="2" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200" placeholder="Contoh: Penggantian pasta processor & power supply..." required></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Teknisi / Toolman</label>
                                <input type="text" name="teknisi_pelaksana" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200" placeholder="Nama teknisi">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Biaya Perbaikan (Rp)</label>
                                <input type="number" name="biaya" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200" placeholder="0" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" onclick="closeModal('addLogModal-{{ $unit->id }}')" class="px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs">Simpan Catatan Servis</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL SELESAIKAN SERVIS UNIT -->
        @php
            $activeLog = $unit->maintenanceLogs->where('status', 'proses')->sortByDesc('id')->first();
        @endphp
        <div id="completeLogModal-{{ $unit->id }}" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" role="dialog" aria-modal="true">
            <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200">
                <form action="{{ route('units.maintenance.complete', $unit) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-emerald-50/50">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg font-bold">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Selesaikan Servis Unit: {{ $unit->unit_code }}</h4>
                                <p class="text-[11px] text-slate-500">Perbarui hasil perbaikan dan kembalikan unit ke status operasional</p>
                            </div>
                        </div>
                        <button type="button" onclick="closeModal('completeLogModal-{{ $unit->id }}')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                            <i class="bi bi-x-lg text-sm"></i>
                        </button>
                    </div>
                    <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                        @if($activeLog && $activeLog->gejala_kerusakan)
                            <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200/80 text-xs">
                                <span class="font-bold text-amber-800 block mb-0.5"><i class="bi bi-info-circle mr-1"></i> Gejala Kerusakan Awal:</span>
                                <p class="text-amber-900">{{ $activeLog->gejala_kerusakan }}</p>
                            </div>
                        @endif
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Selesai</label>
                                <input type="date" name="tanggal" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Kondisi Fisik Akhir</label>
                                <select name="kondisi" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500" required>
                                    <option value="baik" selected>Baik (Siap Digunakan)</option>
                                    <option value="rusak_ringan">Rusak Ringan (Bisa Dipakai Terbatas)</option>
                                    <option value="rusak_berat">Tidak Dapat Diperbaiki (Afkir)</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tindakan Perbaikan / Solusi <span class="text-rose-500">*</span></label>
                            <textarea name="tindakan_perbaikan" rows="3" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500" placeholder="Jelaskan komponen yang diganti atau perbaikan yang telah selesai..." required>{{ $activeLog?->tindakan_perbaikan }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Teknisi / Toolman</label>
                                <input type="text" name="teknisi_pelaksana" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500" placeholder="Nama teknisi" value="{{ $activeLog?->teknisi_pelaksana }}">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Biaya Perbaikan (Rp)</label>
                                <input type="number" name="biaya" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500" placeholder="0" min="0" value="{{ $activeLog?->biaya ? (int)$activeLog->biaya : '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" onclick="closeModal('completeLogModal-{{ $unit->id }}')" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs transition-colors active:scale-95">
                            <i class="bi bi-check2-circle text-xs"></i>
                            <span>Selesaikan & Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL RIWAYAT SERVIS -->
        @if($unit->maintenanceLogs->isNotEmpty())
            <div id="historyLogModal-{{ $unit->id }}" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" role="dialog" aria-modal="true">
                <div class="relative w-full max-w-3xl bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center">
                                <i class="bi bi-wrench"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Riwayat Servis: {{ $unit->unit_code }}</h4>
                                <p class="text-[11px] text-slate-400">Total {{ $unit->maintenanceLogs->count() }} catatan pemeliharaan/perbaikan fisik</p>
                            </div>
                        </div>
                        <button type="button" onclick="closeModal('historyLogModal-{{ $unit->id }}')" class="text-slate-400 hover:text-slate-600 p-1">
                            <i class="bi bi-x-lg text-sm"></i>
                        </button>
                    </div>
                    <div class="overflow-x-auto max-h-96">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[10px]">
                                <tr>
                                    <th class="py-2.5 px-4">Tanggal</th>
                                    <th class="py-2.5 px-4">Gejala / Keluhan</th>
                                    <th class="py-2.5 px-4">Tindakan Perbaikan</th>
                                    <th class="py-2.5 px-4">Teknisi & Biaya</th>
                                    <th class="py-2.5 px-4">Status</th>
                                    <th class="py-2.5 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($unit->maintenanceLogs as $log)
                                    <tr>
                                        <td class="py-2.5 px-4 whitespace-nowrap text-slate-500">{{ $log->tanggal->format('d/m/Y') }}</td>
                                        <td class="py-2.5 px-4 text-slate-800 font-medium">{{ $log->gejala_kerusakan }}</td>
                                        <td class="py-2.5 px-4 text-slate-700">{{ $log->tindakan_perbaikan }}</td>
                                        <td class="py-2.5 px-4">
                                            <p class="font-semibold text-slate-800">{{ $log->teknisi_pelaksana ?? '-' }}</p>
                                            @if($log->biaya)
                                                <span class="text-[10px] text-slate-400">Rp {{ number_format($log->biaya, 0, ',', '.') }}</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-4">
                                            @if($log->status === 'selesai')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Selesai</span>
                                            @elseif($log->status === 'proses')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                    <span>Proses</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Afkir</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-4 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1 justify-end">
                                                @if($log->status === 'proses')
                                                    <button type="button" 
                                                            onclick="closeModal('historyLogModal-{{ $unit->id }}'); openModal('completeLogModal-{{ $unit->id }}');" 
                                                            class="px-2 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition-colors flex items-center gap-1 shadow-xs"
                                                            title="Selesaikan Servis">
                                                        <i class="bi bi-check2"></i> Selesai
                                                    </button>
                                                    <button type="button" 
                                                            onclick="closeModal('historyLogModal-{{ $unit->id }}'); confirmCancelMaintenance('{{ route('units.maintenance.cancel', $unit) }}', '{{ $unit->unit_code }}');" 
                                                            class="px-2 py-1 text-[11px] font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-lg transition-colors flex items-center gap-1 shadow-xs"
                                                            title="Batalkan Servis">
                                                        <i class="bi bi-x-circle"></i> Batal
                                                    </button>
                                                @endif
                                                <button type="button" 
                                                        onclick="closeModal('historyLogModal-{{ $unit->id }}'); confirmDeleteMaintenanceLog('{{ route('maintenance.destroy', $log) }}', '{{ $unit->unit_code }}', '{{ $log->tanggal->format('d/m/Y') }}');" 
                                                        class="p-1 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors"
                                                        title="Hapus Catatan Servis Ini">
                                                    <i class="bi bi-trash text-xs"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 text-right">
                        <button type="button" onclick="closeModal('historyLogModal-{{ $unit->id }}')" class="px-4 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl">Tutup</button>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- MODAL TAMBAH UNIT BARU -->
    <div id="addUnitModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" role="dialog" aria-modal="true">
        <div class="relative w-full {{ $item->is_computer ? 'max-w-xl' : 'max-w-md' }} bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200">
            <form action="{{ route('items.units.store', $item) }}" method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <h4 class="text-sm font-bold text-slate-900">Tambah Unit Fisik Baru</h4>
                        @if($item->is_computer)
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                <i class="bi bi-display mr-0.5"></i> PC Workstation
                            </span>
                        @endif
                    </div>
                    <button type="button" onclick="closeModal('addUnitModal')" class="text-slate-400 hover:text-slate-600 p-1">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                    @if($item->is_computer)
                        <!-- SPESIFIKASI KHUSUS UNIT KOMPUTER -->
                        <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-200/80 space-y-3">
                            <div class="pb-2 border-b border-blue-200/60">
                                <span class="text-xs font-bold text-blue-900 flex items-center gap-1.5">
                                    <i class="bi bi-motherboard text-blue-600"></i> Spesifikasi Hardware Unit Ini
                                </span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Processor (CPU)</label>
                                    <input type="text" name="processor" class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="{{ $item->processor ?: 'Core i5 / Ryzen 5' }}" value="{{ $item->processor }}">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Kapasitas RAM</label>
                                    <input type="text" name="ram" class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="{{ $item->ram ?: '8 GB / 16 GB' }}" value="{{ $item->ram }}">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Penyimpanan (Storage)</label>
                                    <input type="text" name="storage" class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="{{ $item->storage ?: 'SSD 512 GB' }}" value="{{ $item->storage }}">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Kartu Grafis (GPU / VGA)</label>
                                    <input type="text" name="gpu_vga" class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="{{ $item->gpu_vga ?: 'Integrated / GTX 1650' }}" value="{{ $item->gpu_vga }}">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Monitor / Layar</label>
                                    <input type="text" name="monitor" class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="{{ $item->monitor ?: '24 Inch IPS' }}" value="{{ $item->monitor }}">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Sistem Operasi</label>
                                    <input type="text" name="sistem_operasi" class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="{{ $item->sistem_operasi ?: 'Windows 11 / Linux' }}" value="{{ $item->sistem_operasi }}">
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Unit (ID Fisik)</label>
                            <input type="text" name="unit_code" class="w-full font-mono px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Kosongkan untuk auto-generate ({{ $item->kode_barang }}-0{{ $item->units->count() + 1 }})">
                            <p class="text-[10px] text-slate-400 mt-0.5">Bisa dikosongkan untuk auto-generate.</p>
                        </div>
                        @if($item->is_computer)
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Meja Lab PC</label>
                                <input type="text" name="nomor_meja" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Contoh: Meja PC-0{{ $item->units->count() + 1 }}">
                            </div>
                        @else
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Seri Pabrik (SN)</label>
                                <input type="text" name="nomor_seri" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Contoh: SN-2024-XXXX">
                            </div>
                        @endif
                    </div>
                    @if($item->is_computer)
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Seri Pabrik (SN)</label>
                            <input type="text" name="nomor_seri" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Contoh: SN-2024-XXXX">
                        </div>
                    @endif
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kondisi</label>
                            <select name="kondisi" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" required>
                                <option value="baik" selected>Baik</option>
                                <option value="rusak_ringan">Rusak Ringan</option>
                                <option value="rusak_berat">Rusak Berat</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" required>
                                <option value="tersedia" selected>Tersedia</option>
                                <option value="dipinjam">Dipinjam</option>
                                <option value="dalam_perbaikan">Dalam Perbaikan</option>
                                <option value="afkir">Afkir</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Lokasi Penempatan</label>
                        <input type="text" name="lokasi_penempatan" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Ruang / Meja / Lemari" value="{{ $item->lokasi }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan</label>
                        <textarea name="catatan" rows="2" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Catatan tambahan..."></textarea>
                    </div>
                </div>
                <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeModal('addUnitModal')" class="px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs">Simpan Unit Baru</button>
                </div>
            </form>
        </div>
    </div>
@endif

@if($item->jenis === 'alat')
    <!-- MODAL RE-STOK BATCH (TAMBAH UNIT SEKALIGUS) -->
    <div id="restokBatchModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" role="dialog" aria-modal="true">
        <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200">
            <form action="{{ route('items.units.store-batch', $item) }}" method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm border border-emerald-200/60">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Re-stok Unit Batch</h4>
                            <p class="text-[10px] text-slate-400">Tambah beberapa unit fisik sekaligus</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModal('restokBatchModal')" class="text-slate-400 hover:text-slate-600 p-1">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-800 flex items-start gap-2">
                        <i class="bi bi-info-circle-fill text-blue-600 text-sm mt-0.5 shrink-0"></i>
                        <div>
                            Sistem akan otomatis membuat <strong>kode unit berurutan</strong> melanjutkan dari unit terakhir.
                            @if($item->units->count() > 0)
                                Unit terakhir: <code class="font-mono font-bold">{{ $item->units->sortBy('unit_code')->last()->unit_code }}</code>
                            @else
                                Belum ada unit terdaftar, akan dimulai dari <code class="font-mono font-bold">{{ $item->kode_barang }}-01</code>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Jumlah Unit Baru <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="jumlah_unit" min="1" max="100" value="1" required
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                                   placeholder="Contoh: 10">
                            <p class="text-[10px] text-slate-400 mt-0.5">Maks. 100 unit per batch</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Tanggal Masuk / Pengadaan <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_masuk" value="{{ now()->toDateString() }}" required
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kondisi <span class="text-rose-500">*</span></label>
                            <select name="kondisi" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                <option value="baik" selected>Baik</option>
                                <option value="rusak_ringan">Rusak Ringan</option>
                                <option value="rusak_berat">Rusak Berat</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Lokasi Penempatan</label>
                            <input type="text" name="lokasi_penempatan" value="{{ $item->lokasi }}"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                                   placeholder="Ruang / Meja / Lemari">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Batch</label>
                        <textarea name="catatan" rows="2"
                                  class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                                  placeholder="Contoh: Pengadaan tahun 2026 dari APBD..."></textarea>
                    </div>
                </div>
                <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeModal('restokBatchModal')" class="px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl">Batal</button>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs">
                        <i class="bi bi-box-seam text-xs"></i>
                        <span>Proses Re-stok</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<!-- SECTION RIWAYAT PEMAKAIAN BAHAN (JIKA BAHAN) -->
@if($item->jenis === 'bahan')
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs mb-6 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Riwayat Pemakaian Bahan Ini</h3>
                <p class="text-[11px] text-slate-400">Pencatatan konsumsi bahan habis pakai / ATK unit kerja.</p>
            </div>
            <a href="{{ route('usages.create', ['item_id' => $item->id]) }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs transition-all active:scale-95">
                <i class="bi bi-plus-lg text-xs"></i>
                <span>Catat Pemakaian Baru</span>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Guru / Penanggung Jawab</th>
                        <th class="py-3 px-4">Kelas / Nama</th>
                        <th class="py-3 px-4">Keperluan / Jobsheet / Unit Kerja</th>
                        <th class="py-3 px-4 text-center">Jumlah Keluar</th>
                        <th class="py-3 px-4 text-center">Perubahan Stok</th>
                        <th class="py-3 px-4">Catatan</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($item->usages as $usage)
                        <tr class="hover:bg-slate-50/75 transition-colors">
                            <td class="py-3 px-4">
                                <p class="font-bold text-slate-800">{{ $usage->tanggal_pemakaian->format('d/m/Y') }}</p>
                                <p class="text-[11px] text-slate-400">{{ $usage->tanggal_pemakaian->diffForHumans() }}</p>
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $usage->nama_guru }}</td>
                            <td class="py-3 px-4">
                                @if($usage->kelas)
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                        {{ $usage->kelas }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px] italic">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-700">{{ $usage->keperluan_jobsheet ?: '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                     -@formatJumlah($usage->jumlah) {{ $usage->satuan }}
                                 </span>
                             </td>
                             <td class="py-3 px-4 text-center font-mono">
                                 <span class="text-slate-400">@formatJumlah($usage->stok_sebelum)</span>
                                 <i class="bi bi-arrow-right mx-1 text-slate-300"></i>
                                 <strong class="text-blue-700">@formatJumlah($usage->stok_sesudah)</strong>
                             </td>
                             <td class="py-3 px-4 text-slate-500">{{ $usage->catatan ?? '-' }}</td>
                             <td class="py-3 px-4 text-right">
                                 <a href="{{ route('usages.edit', $usage) }}" 
                                    class="p-1.5 rounded-lg text-slate-600 hover:text-blue-600 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 transition-all shadow-xs inline-flex items-center" 
                                    title="Edit Data Pemakaian">
                                     <i class="bi bi-pencil text-xs"></i>
                                 </a>
                             </td>
                         </tr>
                     @empty
                         <tr>
                             <td colspan="8" class="py-8 text-center text-slate-400">
                                 Belum ada riwayat pemakaian bahan ini.
                             </td>
                         </tr>
                     @endforelse
                 </tbody>
             </table>
         </div>
     </div>

     <!-- SECTION RIWAYAT RE-STOK MASUK (JIKA BAHAN) -->
     <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs mb-6 overflow-hidden">
         <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
             <div>
                 <h3 class="text-sm font-bold text-slate-900">Riwayat Re-stok Masuk (Pengadaan Baru)</h3>
                 <p class="text-[11px] text-slate-400">Pencatatan penambahan stok bahan dari pengadaan / pembelanjaan.</p>
             </div>
             <button type="button" onclick="openModal('restokBahanModal')" 
                     class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs transition-all active:scale-95">
                 <i class="bi bi-box-arrow-in-down text-xs"></i>
                 <span>Re-stok Bahan Baru</span>
             </button>
         </div>
         <div class="overflow-x-auto">
             <table class="w-full text-left text-xs">
                 <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px]">
                     <tr>
                         <th class="py-3 px-4">Tanggal Masuk</th>
                         <th class="py-3 px-4">Petugas / Pencatat</th>
                         <th class="py-3 px-4">Sumber Dana</th>
                         <th class="py-3 px-4">Pemasok / Toko</th>
                         <th class="py-3 px-4 text-center">Jumlah Masuk</th>
                         <th class="py-3 px-4 text-center">Perubahan Stok</th>
                         <th class="py-3 px-4">Catatan</th>
                         <th class="py-3 px-4 text-right">Aksi</th>
                     </tr>
                 </thead>
                 <tbody class="divide-y divide-slate-100">
                     @forelse($item->restocks->sortByDesc('tanggal_masuk') as $restock)
                         <tr class="hover:bg-slate-50/75 transition-colors">
                             <td class="py-3 px-4 whitespace-nowrap">
                                 <p class="font-bold text-slate-800">{{ $restock->tanggal_masuk->format('d/m/Y') }}</p>
                                 <p class="text-[11px] text-slate-400">{{ $restock->tanggal_masuk->diffForHumans() }}</p>
                             </td>
                             <td class="py-3 px-4 font-medium text-slate-800">{{ $restock->user->name ?? '-' }}</td>
                             <td class="py-3 px-4">
                                 @if($restock->sumber_dana)
                                     <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                         {{ $restock->sumber_dana }}
                                     </span>
                                 @else
                                     <span class="text-slate-400">-</span>
                                 @endif
                             </td>
                             <td class="py-3 px-4 text-slate-700">{{ $restock->pemasok ?? '-' }}</td>
                             <td class="py-3 px-4 text-center">
                                 <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                     +@formatJumlah($restock->jumlah) {{ $restock->satuan }}
                                 </span>
                             </td>
                             <td class="py-3 px-4 text-center font-mono">
                                 <span class="text-slate-400">@formatJumlah($restock->stok_sebelum)</span>
                                 <i class="bi bi-arrow-right mx-1 text-slate-300"></i>
                                 <strong class="text-emerald-700">@formatJumlah($restock->stok_sesudah)</strong>
                             </td>
                            <td class="py-3 px-4 text-slate-500">{{ $restock->catatan ?? '-' }}</td>
                            <td class="py-3 px-4 text-right">
                                <button type="button" 
                                        onclick="confirmDeleteRestock('{{ route('restocks.destroy', $restock) }}', '{{ $restock->jumlah }} {{ $restock->satuan }}', '{{ $restock->tanggal_masuk->format('d/m/Y') }}')"
                                        class="p-1.5 rounded-lg text-rose-600 hover:text-rose-700 hover:bg-rose-50 border border-rose-200 transition-colors shadow-xs" 
                                        title="Batalkan Re-stok">
                                    <i class="bi bi-trash text-xs"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                Belum ada riwayat re-stok masuk untuk bahan ini. Klik tombol "Re-stok Bahan Baru" untuk menambah stok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL RE-STOK BAHAN HABIS PAKAI -->
    <div id="restokBahanModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" role="dialog" aria-modal="true">
        <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200">
            <form action="{{ route('items.restock.store', $item) }}" method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm border border-emerald-200/60">
                            <i class="bi bi-box-arrow-in-down"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Re-stok Bahan Habis Pakai</h4>
                            <p class="text-[10px] text-slate-400">{{ $item->nama_barang }} (Stok saat ini: {{ $item->jumlah }} {{ $item->satuan }})</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModal('restokBahanModal')" class="text-slate-400 hover:text-slate-600 p-1">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-start gap-2">
                        <i class="bi bi-info-circle-fill text-emerald-600 text-sm mt-0.5 shrink-0"></i>
                        <div>
                            Jumlah yang dimasukkan akan <strong>menambah stok total</strong> secara otomatis dan dicatat dalam audit trail riwayat pengadaan.
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Jumlah Masuk <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="jumlah" min="0.01" step="any" required
                                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 pr-12"
                                       placeholder="10 atau 1.5">
                                <span class="absolute right-3 top-2 text-xs text-slate-400 pointer-events-none">{{ $item->satuan }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Tanggal Masuk <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_masuk" value="{{ now()->toDateString() }}" required
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Sumber Dana</label>
                            <select name="sumber_dana" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                <option value="">-- Pilih Sumber --</option>
                                <option value="BOS Reguler" {{ $item->sumber_dana === 'BOS Reguler' ? 'selected' : '' }}>BOS Reguler</option>
                                <option value="BOS Kinerja" {{ $item->sumber_dana === 'BOS Kinerja' ? 'selected' : '' }}>BOS Kinerja</option>
                                <option value="APBD Provinsi" {{ $item->sumber_dana === 'APBD Provinsi' ? 'selected' : '' }}>APBD Provinsi</option>
                                <option value="Komite Sekolah" {{ $item->sumber_dana === 'Komite Sekolah' ? 'selected' : '' }}>Komite Sekolah</option>
                                <option value="Bantuan Industri / CSR">Bantuan Industri / CSR</option>
                                <option value="Swadana Jurusan">Swadana Jurusan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pemasok / Toko</label>
                            <input type="text" name="pemasok"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                                   placeholder="Contoh: Toko Elektronik Jaya">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Pengadaan</label>
                        <textarea name="catatan" rows="2"
                                  class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                                  placeholder="No. Nota / Faktur, spesifikasi batch, atau keterangan lainnya..."></textarea>
                    </div>
                </div>
                <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeModal('restokBahanModal')" class="px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl">Batal</button>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs">
                        <i class="bi bi-box-arrow-in-down text-xs"></i>
                        <span>Simpan Re-stok</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<!-- RIWAYAT PEMINJAMAN ALAT -->
@if($item->jenis === 'alat')
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs mb-6 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Riwayat Peminjaman Alat Ini</h3>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600">
                    {{ $item->borrowings->count() }} Transaksi
                </span>
                <a href="{{ route('borrowings.create', ['item_id' => $item->id]) }}" 
                   class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-2.5 py-1 rounded-lg transition-colors">
                    <i class="bi bi-plus-lg"></i>
                    <span>Pinjamkan</span>
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px]">
                    <tr>
                        <th class="py-3 px-4">Peminjam</th>
                        <th class="py-3 px-4">Unit yang Dipinjam</th>
                        <th class="py-3 px-4">Tgl Pinjam</th>
                        <th class="py-3 px-4">Tgl Kembali</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($item->borrowings as $b)
                        <tr class="hover:bg-slate-50/75 transition-colors">
                            <td class="py-3 px-4">
                                <p class="font-bold text-slate-800">{{ $b->nama_peminjam }}</p>
                                <p class="text-[11px] text-slate-400">{{ $b->kelas_atau_jabatan ?? '-' }}</p>
                            </td>
                            <td class="py-3 px-4">
                                @if($b->itemUnit)
                                    <span class="font-mono font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                        {{ $b->itemUnit->unit_code }}
                                    </span>
                                    @if($b->itemUnit->nomor_meja)
                                        <span class="text-slate-400 ml-1">({{ $b->itemUnit->nomor_meja }})</span>
                                    @endif
                                @else
                                    <span class="text-slate-600">{{ $b->jumlah }} {{ $item->satuan }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $b->tanggal_pinjam->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $b->tanggal_kembali ? $b->tanggal_kembali->format('d/m/Y') : '-' }}</td>
                            <td class="py-3 px-4">
                                @if($b->status === 'dipinjam')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Dipinjam</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Kembali</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">Belum pernah dipinjam.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- MODAL HAPUS BARANG (THEMED POPUP) -->
<div id="deleteItemModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-200" role="dialog" aria-modal="true" aria-labelledby="deleteItemTitle">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200 transform transition-all">
        <!-- Header -->
        <div class="p-6 pb-4 flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shrink-0 border border-rose-200/60 shadow-xs">
                <i class="bi bi-trash3-fill"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 id="deleteItemTitle" class="text-base font-bold text-slate-900">Hapus Barang Inventaris</h3>
                    <button type="button" onclick="closeDeleteItemModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition-colors" aria-label="Tutup">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Konfirmasi penghapusan data barang inventaris</p>
            </div>
        </div>

        <!-- Body -->
        <div class="px-6 py-2 space-y-4">
            <p class="text-xs text-slate-600 leading-relaxed">
                Apakah Anda yakin ingin menghapus barang inventaris berikut secara permanen?
            </p>

            <!-- Card Ringkasan Barang yang akan dihapus -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <span id="deleteItemKode" class="inline-block px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-200 text-slate-700"></span>
                        <h4 id="deleteItemNama" class="text-sm font-bold text-slate-900 mt-1 break-words"></h4>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-2 border-t border-slate-200/60 text-xs text-slate-600">
                    <div class="flex items-center gap-1">
                        <i class="bi bi-tag text-slate-400"></i>
                        <span id="deleteItemKategori"></span>
                    </div>
                    <span class="text-slate-300">•</span>
                    <div class="flex items-center gap-1">
                        <i class="bi bi-box-seam text-slate-400"></i>
                        <span id="deleteItemStok"></span>
                    </div>
                </div>
            </div>

            <!-- Danger Warning Callout -->
            <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-200/80 flex items-start gap-3">
                <i class="bi bi-exclamation-triangle-fill text-rose-500 text-base shrink-0 mt-0.5"></i>
                <div class="text-xs text-rose-800 leading-relaxed">
                    <p class="font-semibold text-rose-900">Peringatan: Tindakan tidak dapat dibatalkan!</p>
                    <p class="mt-0.5 text-rose-700">Data barang beserta seluruh data unit fisik dan riwayat pemeliharaannya akan dihapus dari sistem.</p>
                </div>
            </div>
        </div>

        <!-- Footer / Action Buttons -->
        <form id="deleteItemForm" action="" method="POST" class="p-6 pt-4 flex items-center justify-end gap-2 bg-slate-50/50 border-t border-slate-100">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteItemModal()" 
                    class="px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl transition-all">
                Batal
            </button>
            <button type="submit" 
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs transition-all active:scale-95">
                <i class="bi bi-trash3-fill text-xs"></i>
                <span>Ya, Hapus Barang</span>
            </button>
        </form>
    </div>
</div>

<!-- MODAL HAPUS UNIT (THEMED POPUP) -->
<div id="deleteUnitModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-200" role="dialog" aria-modal="true" aria-labelledby="deleteUnitTitle">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200 transform transition-all">
        <!-- Header -->
        <div class="p-6 pb-4 flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shrink-0 border border-rose-200/60 shadow-xs">
                <i class="bi bi-trash3-fill"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 id="deleteUnitTitle" class="text-base font-bold text-slate-900">Hapus Unit Fisik</h3>
                    <button type="button" onclick="closeDeleteUnitModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition-colors" aria-label="Tutup">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Konfirmasi penghapusan data unit fisik</p>
            </div>
        </div>

        <!-- Body -->
        <div class="px-6 py-2 space-y-4">
            <p class="text-xs text-slate-600 leading-relaxed">
                Apakah Anda yakin ingin menghapus unit fisik barang berikut secara permanen?
            </p>

            <!-- Card Ringkasan Unit yang akan dihapus -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                <div class="flex items-center justify-between">
                    <span id="deleteUnitCode" class="inline-block px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200"></span>
                    <span id="deleteUnitMeja" class="text-xs text-slate-600 font-semibold"></span>
                </div>
            </div>

            <!-- Danger Warning Callout -->
            <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-200/80 flex items-start gap-3">
                <i class="bi bi-exclamation-triangle-fill text-rose-500 text-base shrink-0 mt-0.5"></i>
                <div class="text-xs text-rose-800 leading-relaxed">
                    <p class="font-semibold text-rose-900">Perhatian:</p>
                    <p class="mt-0.5 text-rose-700">Stok barang induk akan berkurang 1 unit dan seluruh log riwayat servis pada unit ini akan ikut terhapus.</p>
                </div>
            </div>
        </div>

        <!-- Footer / Action Buttons -->
        <form id="deleteUnitForm" action="" method="POST" class="p-6 pt-4 flex items-center justify-end gap-2 bg-slate-50/50 border-t border-slate-100">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteUnitModal()" 
                    class="px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl transition-all">
                Batal
            </button>
            <button type="submit" 
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs transition-all active:scale-95">
                <i class="bi bi-trash3-fill text-xs"></i>
                <span>Ya, Hapus Unit</span>
            </button>
        </form>
    </div>
</div>

<!-- MODAL HAPUS / BATALKAN RE-STOK (THEMED POPUP) -->
<div id="deleteRestockModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-200" role="dialog" aria-modal="true" aria-labelledby="deleteRestockTitle">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200 transform transition-all">
        <!-- Header -->
        <div class="p-6 pb-4 flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shrink-0 border border-rose-200/60 shadow-xs">
                <i class="bi bi-trash3-fill"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 id="deleteRestockTitle" class="text-base font-bold text-slate-900">Batalkan Riwayat Re-stok</h3>
                    <button type="button" onclick="closeDeleteRestockModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition-colors" aria-label="Tutup">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Konfirmasi pembatalan pencatatan re-stok bahan</p>
            </div>
        </div>

        <!-- Body -->
        <div class="px-6 py-2 space-y-4">
            <p class="text-xs text-slate-600 leading-relaxed">
                Apakah Anda yakin ingin membatalkan transaksi re-stok berikut?
            </p>

            <!-- Card Ringkasan Transaksi -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500">Jumlah Re-stok:</span>
                    <span id="deleteRestockJumlah" class="text-xs font-bold text-emerald-700"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500">Tanggal Masuk:</span>
                    <span id="deleteRestockTanggal" class="text-xs font-semibold text-slate-700"></span>
                </div>
            </div>

            <!-- Danger Warning Callout -->
            <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-200/80 flex items-start gap-3">
                <i class="bi bi-exclamation-triangle-fill text-rose-500 text-base shrink-0 mt-0.5"></i>
                <div class="text-xs text-rose-800 leading-relaxed">
                    <p class="font-semibold text-rose-900">Perhatian:</p>
                    <p class="mt-0.5 text-rose-700">Stok barang saat ini akan dikurangi kembali sesuai jumlah re-stok ini.</p>
                </div>
            </div>
        </div>

        <!-- Footer / Action Buttons -->
        <form id="deleteRestockForm" action="" method="POST" class="p-6 pt-4 flex items-center justify-end gap-2 bg-slate-50/50 border-t border-slate-100">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteRestockModal()" 
                    class="px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl transition-all">
                Batal
            </button>
            <button type="submit" 
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs transition-all active:scale-95">
                <i class="bi bi-trash3-fill text-xs"></i>
                <span>Ya, Batalkan Re-stok</span>
            </button>
        </form>
    </div>
</div>

<!-- MODAL BATALKAN SERVIS (THEMED POPUP) -->
<div id="cancelMaintenanceModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-200" role="dialog" aria-modal="true" aria-labelledby="cancelMaintenanceTitle">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200 transform transition-all">
        <!-- Header -->
        <div class="p-6 pb-4 flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl shrink-0 border border-amber-200/60 shadow-xs">
                <i class="bi bi-arrow-counterclockwise"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 id="cancelMaintenanceTitle" class="text-base font-bold text-slate-900">Batalkan Status Servis</h3>
                    <button type="button" onclick="closeCancelMaintenanceModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition-colors" aria-label="Tutup">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Kembalikan status unit ke Tersedia</p>
            </div>
        </div>

        <!-- Body -->
        <div class="px-6 py-2 space-y-4">
            <p class="text-xs text-slate-600 leading-relaxed">
                Apakah Anda yakin ingin membatalkan status perbaikan/servis untuk unit <strong id="cancelMaintenanceUnitCode" class="text-slate-900 font-mono"></strong>?
            </p>

            <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200/80 flex items-start gap-3">
                <i class="bi bi-info-circle-fill text-amber-500 text-base shrink-0 mt-0.5"></i>
                <div class="text-xs text-amber-800 leading-relaxed">
                    <p class="font-semibold text-amber-900">Efek Pembatalan:</p>
                    <p class="mt-0.5 text-amber-700">Pencatatan servis yang sedang berjalan akan dibatalkan/dihapus, dan status operasional unit akan kembali menjadi <strong>Tersedia (Baik)</strong>.</p>
                </div>
            </div>
        </div>

        <!-- Footer / Action Buttons -->
        <form id="cancelMaintenanceForm" action="" method="POST" class="p-6 pt-4 flex items-center justify-end gap-2 bg-slate-50/50 border-t border-slate-100">
            @csrf
            @method('PATCH')
            <button type="button" onclick="closeCancelMaintenanceModal()" 
                    class="px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl transition-all">
                Kembali
            </button>
            <button type="submit" 
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl shadow-xs transition-all active:scale-95">
                <i class="bi bi-arrow-counterclockwise text-xs"></i>
                <span>Ya, Batalkan Servis</span>
            </button>
        </form>
    </div>
</div>

<!-- MODAL HAPUS RIWAYAT SERVIS (THEMED POPUP) -->
<div id="deleteMaintenanceLogModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-200" role="dialog" aria-modal="true" aria-labelledby="deleteMaintenanceLogTitle">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200 transform transition-all">
        <!-- Header -->
        <div class="p-6 pb-4 flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shrink-0 border border-rose-200/60 shadow-xs">
                <i class="bi bi-trash3-fill"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 id="deleteMaintenanceLogTitle" class="text-base font-bold text-slate-900">Hapus Riwayat Servis</h3>
                    <button type="button" onclick="closeDeleteMaintenanceLogModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition-colors" aria-label="Tutup">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Konfirmasi penghapusan catatan perbaikan</p>
            </div>
        </div>

        <!-- Body -->
        <div class="px-6 py-2 space-y-4">
            <p class="text-xs text-slate-600 leading-relaxed">
                Apakah Anda yakin ingin menghapus catatan servis tanggal <strong id="deleteMaintenanceLogTanggal" class="text-slate-900"></strong> untuk unit <strong id="deleteMaintenanceLogUnitCode" class="text-slate-900 font-mono"></strong>?
            </p>

            <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-200/80 flex items-start gap-3">
                <i class="bi bi-exclamation-triangle-fill text-rose-500 text-base shrink-0 mt-0.5"></i>
                <div class="text-xs text-rose-800 leading-relaxed">
                    <p class="font-semibold text-rose-900">Perhatian:</p>
                    <p class="mt-0.5 text-rose-700">Tindakan ini tidak dapat dibatalkan. Jika catatan ini adalah perbaikan yang sedang berjalan, status unit akan otomatis dikembalikan ke Tersedia.</p>
                </div>
            </div>
        </div>

        <!-- Footer / Action Buttons -->
        <form id="deleteMaintenanceLogForm" action="" method="POST" class="p-6 pt-4 flex items-center justify-end gap-2 bg-slate-50/50 border-t border-slate-100">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteMaintenanceLogModal()" 
                    class="px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl transition-all">
                Batal
            </button>
            <button type="submit" 
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs transition-all active:scale-95">
                <i class="bi bi-trash3-fill text-xs"></i>
                <span>Ya, Hapus Riwayat</span>
            </button>
        </form>
    </div>
</div>

<!-- Simple Vanilla JS Modal Controller -->
<script>
    function openModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function confirmDeleteItem(actionUrl, nama, kode, kategori, stok) {
        const modal = document.getElementById('deleteItemModal');
        const form = document.getElementById('deleteItemForm');
        const namaEl = document.getElementById('deleteItemNama');
        const kodeEl = document.getElementById('deleteItemKode');
        const kategoriEl = document.getElementById('deleteItemKategori');
        const stokEl = document.getElementById('deleteItemStok');

        if (form) form.action = actionUrl;
        if (namaEl) namaEl.textContent = nama;
        if (kodeEl) kodeEl.textContent = kode;
        if (kategoriEl) kategoriEl.textContent = kategori;
        if (stokEl) stokEl.textContent = stok;

        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeDeleteItemModal() {
        const modal = document.getElementById('deleteItemModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function confirmDeleteUnit(actionUrl, unitCode, meja) {
        const modal = document.getElementById('deleteUnitModal');
        const form = document.getElementById('deleteUnitForm');
        const codeEl = document.getElementById('deleteUnitCode');
        const mejaEl = document.getElementById('deleteUnitMeja');

        if (form) form.action = actionUrl;
        if (codeEl) codeEl.textContent = unitCode;
        if (mejaEl) mejaEl.textContent = (meja && meja !== '-') ? meja : '';

        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeDeleteUnitModal() {
        const modal = document.getElementById('deleteUnitModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function confirmDeleteRestock(actionUrl, jumlah, tanggal) {
        const modal = document.getElementById('deleteRestockModal');
        const form = document.getElementById('deleteRestockForm');
        const jumlahEl = document.getElementById('deleteRestockJumlah');
        const tanggalEl = document.getElementById('deleteRestockTanggal');

        if (form) form.action = actionUrl;
        if (jumlahEl) jumlahEl.textContent = jumlah;
        if (tanggalEl) tanggalEl.textContent = tanggal;

        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeDeleteRestockModal() {
        const modal = document.getElementById('deleteRestockModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function confirmCancelMaintenance(actionUrl, unitCode) {
        const modal = document.getElementById('cancelMaintenanceModal');
        const form = document.getElementById('cancelMaintenanceForm');
        const codeEl = document.getElementById('cancelMaintenanceUnitCode');

        if (form) form.action = actionUrl;
        if (codeEl) codeEl.textContent = unitCode;

        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeCancelMaintenanceModal() {
        const modal = document.getElementById('cancelMaintenanceModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function confirmDeleteMaintenanceLog(actionUrl, unitCode, tanggal) {
        const modal = document.getElementById('deleteMaintenanceLogModal');
        const form = document.getElementById('deleteMaintenanceLogForm');
        const codeEl = document.getElementById('deleteMaintenanceLogUnitCode');
        const tanggalEl = document.getElementById('deleteMaintenanceLogTanggal');

        if (form) form.action = actionUrl;
        if (codeEl) codeEl.textContent = unitCode;
        if (tanggalEl) tanggalEl.textContent = tanggal;

        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeDeleteMaintenanceLogModal() {
        const modal = document.getElementById('deleteMaintenanceLogModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[role="dialog"]').forEach(modal => {
                if (!modal.classList.contains('hidden')) {
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
            });
        }
    });

    document.addEventListener('click', function(e) {
        const itemModal = document.getElementById('deleteItemModal');
        if (itemModal && !itemModal.classList.contains('hidden') && e.target === itemModal) {
            closeDeleteItemModal();
        }
        const unitModal = document.getElementById('deleteUnitModal');
        if (unitModal && !unitModal.classList.contains('hidden') && e.target === unitModal) {
            closeDeleteUnitModal();
        }
        const restockModal = document.getElementById('deleteRestockModal');
        if (restockModal && !restockModal.classList.contains('hidden') && e.target === restockModal) {
            closeDeleteRestockModal();
        }
        const cancelMaintenanceModal = document.getElementById('cancelMaintenanceModal');
        if (cancelMaintenanceModal && !cancelMaintenanceModal.classList.contains('hidden') && e.target === cancelMaintenanceModal) {
            closeCancelMaintenanceModal();
        }
        const deleteMaintenanceLogModal = document.getElementById('deleteMaintenanceLogModal');
        if (deleteMaintenanceLogModal && !deleteMaintenanceLogModal.classList.contains('hidden') && e.target === deleteMaintenanceLogModal) {
            closeDeleteMaintenanceLogModal();
        }
    });
</script>
@endsection
