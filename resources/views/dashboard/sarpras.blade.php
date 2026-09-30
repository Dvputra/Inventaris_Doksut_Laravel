@extends('layouts.app')

@section('title', 'Dashboard Sarpras Pusat')

@section('content')
<div class="space-y-6">
    <!-- Header / Welcome Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider bg-amber-400 text-slate-950 px-2.5 py-0.5 rounded-full shadow-xs">
                    <i class="bi bi-shield-check"></i> ADMIN PUSAT
                </span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs font-medium text-slate-500">SMK Dr. Sutomo</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                Pusat Kendali & Logistik Sarpras
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                Pantau seluruh aset inventaris, kelayakan sarana, usulan pengadaan, serta pergerakan logistik di seluruh unit kerja sekolah.
            </p>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 w-full md:w-auto">
            <a href="{{ route('jurusans.index') }}" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3.5 py-2.5 text-xs font-semibold text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-all shadow-xs active:scale-95">
                <i class="bi bi-building-gear text-slate-500 text-sm"></i>
                <span>Unit Kerja ({{ $totalJurusans }})</span>
            </a>
            <a href="{{ route('users.index') }}" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3.5 py-2.5 text-xs font-semibold text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-all shadow-xs active:scale-95">
                <i class="bi bi-people text-slate-500 text-sm"></i>
                <span>Kelola Akun ({{ $totalUsers }})</span>
            </a>
            <a href="{{ route('items.create') }}" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-200 rounded-xl transition-all active:scale-95">
                <i class="bi bi-plus-lg text-sm"></i>
                <span>Tambah Barang</span>
            </a>
        </div>
    </div>

    <!-- Ringkasan Statistik Utama (4 Kartu Terpadu) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- Kartu 1: Total Aset Fisik -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-colors">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Akumulasi Fisik</span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">
                        {{ number_format($totalUnit) }}
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                    <i class="bi bi-boxes text-lg"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span class="font-medium text-slate-700">{{ $totalItems }} jenis item terdaftar</span>
                <a href="{{ route('items.index') }}" class="font-semibold text-blue-600 hover:text-blue-700">Detail &rarr;</a>
            </div>
        </div>

        <!-- Kartu 2: Unit Kerja & Program Keahlian -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-colors">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Unit Kerja & Jurusan</span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">
                        {{ $totalJurusans }} <span class="text-sm font-semibold text-slate-400">Unit</span>
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100">
                    <i class="bi bi-diagram-3 text-lg"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Bengkel & Fasilitas Umum</span>
                <a href="{{ route('jurusans.index') }}" class="font-semibold text-blue-600 hover:text-blue-700">Kelola &rarr;</a>
            </div>
        </div>

        <!-- Kartu 3: Peminjaman & Pemakaian Aktif -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-colors">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Sirkulasi Alat & Bahan</span>
                    <div class="flex items-baseline gap-2 mt-1">
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $peminjamanAktif }}</h3>
                        <span class="text-xs font-semibold text-slate-500">Alat Dipinjam</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 border border-purple-100">
                    <i class="bi bi-arrow-left-right text-lg"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>{{ $totalUsages }} transaksi pemakaian</span>
                <a href="{{ route('borrowings.index') }}" class="font-semibold text-blue-600 hover:text-blue-700">Daftar &rarr;</a>
            </div>
        </div>

        <!-- Kartu 4: Perlu Perhatian / Antrean Tindakan (Urgency: Amber Accent) -->
        @php
            $totalPerhatian = $pengajuanMenunggu + $pengaduanMenunggu + $lowStockCount;
        @endphp
        <div class="p-5 rounded-2xl {{ $totalPerhatian > 0 ? 'bg-amber-50/60 border-amber-200/80 ring-1 ring-amber-300/30' : 'bg-white border-slate-200/90' }} border shadow-xs flex flex-col justify-between transition-colors">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider {{ $totalPerhatian > 0 ? 'text-amber-800' : 'text-slate-400' }}">
                        Antrean Verifikasi
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold {{ $totalPerhatian > 0 ? 'text-amber-700' : 'text-slate-900' }} mt-1">
                        {{ $totalPerhatian }} <span class="text-sm font-semibold {{ $totalPerhatian > 0 ? 'text-amber-600' : 'text-slate-400' }}">Tugas</span>
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-xl {{ $totalPerhatian > 0 ? 'bg-amber-400 text-slate-950 font-bold shadow-xs' : 'bg-slate-50 text-slate-400 border border-slate-200' }} flex items-center justify-center shrink-0">
                    <i class="bi bi-bell-fill text-base"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t {{ $totalPerhatian > 0 ? 'border-amber-200/80' : 'border-slate-100' }} flex items-center justify-between text-xs">
                <span class="{{ $totalPerhatian > 0 ? 'text-amber-800 font-medium' : 'text-slate-500' }}">
                    {{ $pengajuanMenunggu }} Pengajuan, {{ $pengaduanMenunggu }} Tiket
                </span>
                <a href="{{ route('procurements.index') }}" class="font-bold text-amber-700 hover:text-amber-800">Proses &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Panel Status Kelayakan Aset & Fasilitas Kunci (2 Kolom Rapi) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Kolom Kiri: Status Kelayakan Fisik Barang (Visual Health Bar) -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/80 shadow-xs p-5 sm:p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="bi bi-shield-check text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Kelayakan Fisik Barang Inventaris</h3>
                            <p class="text-xs text-slate-400">Kondisi operasional dari {{ number_format($totalUnit) }} unit barang tercatat</p>
                        </div>
                    </div>
                    @php
                        $persenBaik = $totalUnit > 0 ? round(($baikCount / $totalUnit) * 100) : 0;
                    @endphp
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ $persenBaik }}% Prima
                    </span>
                </div>

                <!-- Multi-segment Progress Bar -->
                @php
                    $persenRusakRingan = $totalUnit > 0 ? round(($rusakRinganCount / $totalUnit) * 100) : 0;
                    $persenRusakBerat = $totalUnit > 0 ? round(($rusakBeratCount / $totalUnit) * 100) : 0;
                @endphp
                <div class="w-full bg-slate-100 rounded-full h-3 flex overflow-hidden shadow-inner my-4">
                    <div class="bg-emerald-500 transition-all duration-500" style="width: {{ $persenBaik }}%" title="Baik: {{ number_format($baikCount) }} unit"></div>
                    <div class="bg-amber-400 transition-all duration-500" style="width: {{ $persenRusakRingan }}%" title="Rusak Ringan: {{ number_format($rusakRinganCount) }} unit"></div>
                    <div class="bg-rose-500 transition-all duration-500" style="width: {{ $persenRusakBerat }}%" title="Rusak Berat: {{ number_format($rusakBeratCount) }} unit"></div>
                </div>

                <!-- Condition Breakdown Mini Cards -->
                <div class="grid grid-cols-3 gap-3 pt-2">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 mb-1"></span>
                        <p class="text-[11px] font-semibold text-slate-500">Kondisi Baik</p>
                        <h4 class="text-base font-extrabold text-slate-900 mt-0.5">{{ number_format($baikCount) }}</h4>
                        <span class="text-[10px] text-emerald-600 font-semibold">{{ $persenBaik }}%</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-400 mb-1"></span>
                        <p class="text-[11px] font-semibold text-slate-500">Rusak Ringan</p>
                        <h4 class="text-base font-extrabold text-slate-900 mt-0.5">{{ number_format($rusakRinganCount) }}</h4>
                        <span class="text-[10px] text-amber-600 font-semibold">{{ $persenRusakRingan }}%</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-rose-500 mb-1"></span>
                        <p class="text-[11px] font-semibold text-slate-500">Rusak Berat</p>
                        <h4 class="text-base font-extrabold text-slate-900 mt-0.5">{{ number_format($rusakBeratCount) }}</h4>
                        <span class="text-[10px] text-rose-600 font-semibold">{{ $persenRusakBerat }}%</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-400">Butuh perbaikan: {{ number_format($rusakRinganCount + $rusakBeratCount) }} unit</span>
                <a href="{{ route('items.index', ['kondisi' => 'rusak_ringan']) }}" class="font-semibold text-blue-600 hover:text-blue-700">
                    Buka Riwayat Perbaikan &rarr;
                </a>
            </div>
        </div>

        <!-- Kolom Kanan: Sorotan Fasilitas Khusus (IT Workstation & Stok Kritis) -->
        <div class="lg:col-span-5 space-y-4">
            <!-- Komputer & PC Lab -->
            <div class="p-4 rounded-3xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                        <i class="bi bi-display text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Komputer Lab & Server</p>
                        <h4 class="text-lg font-bold text-slate-900 mt-0.5">{{ number_format($totalComputers) }} Unit</h4>
                        <p class="text-[11px] text-slate-400">Workstation Lab & CBT/ANBK</p>
                    </div>
                </div>
                <a href="{{ route('items.index', ['is_computer' => 1]) }}" class="px-3 py-1.5 rounded-xl border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs transition-colors shadow-xs">
                    Kelola
                </a>
            </div>

            <!-- Stok Bahan Menipis / Kritis -->
            <div class="p-4 rounded-3xl {{ $lowStockCount > 0 ? 'bg-rose-50/70 border-rose-200' : 'bg-white border-slate-200/80' }} border shadow-xs flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl {{ $lowStockCount > 0 ? 'bg-rose-600 text-white' : 'bg-emerald-50 text-emerald-600 border border-emerald-100' }} flex items-center justify-center shrink-0">
                        <i class="bi {{ $lowStockCount > 0 ? 'bi-exclamation-triangle-fill' : 'bi-shield-check' }} text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold {{ $lowStockCount > 0 ? 'text-rose-700' : 'text-slate-500' }} uppercase tracking-wider">Bahan Menipis (Kritis)</p>
                        <h4 class="text-lg font-bold {{ $lowStockCount > 0 ? 'text-rose-700' : 'text-slate-900' }} mt-0.5">{{ $lowStockCount }} Bahan</h4>
                        <p class="text-[11px] {{ $lowStockCount > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                            {{ $lowStockCount > 0 ? 'Perlu usulan pengadaan bahan' : 'Seluruh stok dalam batas aman' }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('items.index', ['jenis' => 'bahan']) }}" class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors shadow-xs">
                    Cek Bahan
                </a>
            </div>

            <!-- Gudang Sarpras & Inventaris Umum -->
            <div class="p-4 rounded-3xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200">
                        <i class="bi bi-box-seam text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Gudang & Fasilitas Umum</p>
                        <h4 class="text-sm font-bold text-slate-900 mt-0.5">Sentral Logistik Sekolah</h4>
                        <p class="text-[11px] text-slate-400">Peralatan umum, kelas, & cadangan</p>
                    </div>
                </div>
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('sarpras.gudang') }}" class="px-2.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition-colors">
                        Gudang
                    </a>
                    <a href="{{ route('sarpras.umum') }}" class="px-2.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition-colors">
                        Umum
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Rekap Inventaris Per Unit & Jurusan (Grid Rapi) -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="bi bi-diagram-3-fill text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Distribusi Aset Per Unit Kerja & Program Keahlian</h3>
                    <p class="text-xs text-slate-400">Ringkasan penanggung jawab dan kuantitas inventaris terdata</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('jurusans.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold text-xs transition-colors">
                    <i class="bi bi-gear text-slate-500"></i>
                    <span>Kelola Semua Unit</span>
                </a>
                <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-full">
                    {{ $jurusans->count() }} Unit
                </span>
            </div>
        </div>

        <div class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">
                @foreach($jurusans as $j)
                    <div class="p-3.5 rounded-2xl {{ $j->kode === 'SAR' ? 'bg-amber-50/70 border-amber-300 ring-2 ring-amber-400/20' : 'bg-slate-50/70 border-slate-200/80' }} border flex flex-col justify-between hover:border-blue-300 hover:shadow-xs transition-all">
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-2">
                                @if($j->kode === 'SAR')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-extrabold bg-amber-400 text-slate-950 px-2 py-0.5 rounded-full shadow-xs">
                                        <i class="bi bi-shield-check text-[10px]"></i> SARPRAS
                                    </span>
                                @else
                                    <span class="inline-block text-[10px] font-extrabold bg-blue-600 text-white px-2.5 py-0.5 rounded-full shadow-xs">
                                        {{ $j->kode }}
                                    </span>
                                @endif
                                <span class="text-[10px] font-bold text-slate-500 bg-white px-1.5 py-0.5 rounded border border-slate-200">
                                    {{ $j->items_count }} item
                                </span>
                            </div>
                            <h4 class="font-bold text-xs text-slate-900 truncate" title="{{ $j->nama }}">
                                {{ $j->nama }}
                            </h4>
                            <p class="text-[11px] text-slate-500 mt-1 truncate flex items-center gap-1" title="{{ $j->kepala_bengkel ?? 'Belum ditentukan' }}">
                                <i class="bi bi-person text-amber-500 text-xs shrink-0"></i>
                                <span>{{ $j->kepala_bengkel ?? '-' }}</span>
                            </p>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-200/80">
                            <a href="{{ route('items.index', ['jurusan_id' => $j->id]) }}" 
                               class="w-full inline-flex items-center justify-center gap-1 py-1.5 text-[11px] font-semibold text-blue-700 bg-white hover:bg-blue-50 border border-blue-200 rounded-xl shadow-xs transition-colors">
                                <span>Buka Aset</span>
                                <i class="bi bi-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Aktivitas Terbaru: Usulan Pengadaan & Peminjaman Alat (Grid 2 Kolom) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Usulan Pengadaan Terbaru -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/80 shadow-xs flex flex-col overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i class="bi bi-clipboard2-check text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Usulan Pengadaan Terbaru</h3>
                        <p class="text-xs text-slate-400">Permohonan alat & bahan dari unit kerja</p>
                    </div>
                </div>
                <a href="{{ route('procurements.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <i class="bi bi-arrow-right text-[10px]"></i>
                </a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-3 px-4">Unit</th>
                            <th class="py-3 px-4">Nama Barang</th>
                            <th class="py-3 px-4 text-center">Jumlah</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentProcurements as $proc)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $proc->jurusan->kode }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="font-bold text-slate-900">{{ $proc->nama_barang }}</p>
                                    <p class="text-[11px] text-slate-400 truncate max-w-xs">{{ Str::limit($proc->alasan, 35) }}</p>
                                </td>
                                <td class="py-3 px-4 text-center font-semibold text-slate-700 whitespace-nowrap">
                                    {{ $proc->jumlah }} {{ $proc->satuan }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if($proc->status === 'menunggu')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Menunggu
                                        </span>
                                    @elseif($proc->status === 'disetujui')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Disetujui
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    @if($proc->status === 'menunggu')
                                        <div class="inline-flex items-center gap-1.5">
                                            <form action="{{ route('procurements.approve', $proc) }}" method="POST" class="inline"
                                                  data-confirm="Setujui permohonan usulan pengadaan {{ addslashes($proc->summary_barang) }} untuk jurusan {{ $proc->jurusan->kode }}?"
                                                  data-confirm-title="Persetujuan Usulan Pengadaan"
                                                  data-confirm-type="success"
                                                  data-confirm-btn="Ya, Setujui"
                                                  data-confirm-icon="bi bi-check2-circle text-2xl">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs shadow-xs" title="Setujui Usulan">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('procurements.index') }}" class="p-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 text-xs shadow-xs" title="Detail / Tolak">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                                    Belum ada usulan pengadaan barang masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Peminjaman Alat Terkini -->
        <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200/80 shadow-xs flex flex-col overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="bi bi-arrow-left-right text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Peminjaman Alat</h3>
                        <p class="text-xs text-slate-400">Sirkulasi peminjaman terkini</p>
                    </div>
                </div>
                <a href="{{ route('borrowings.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    <span>Semua</span>
                    <i class="bi bi-arrow-right text-[10px]"></i>
                </a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-3 px-4">Peminjam</th>
                            <th class="py-3 px-4">Alat</th>
                            <th class="py-3 px-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentBorrowings as $b)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <p class="font-bold text-slate-900">{{ $b->nama_peminjam }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $b->kelas_atau_jabatan ?? $b->jurusan->kode }}</p>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="text-slate-800 font-medium truncate max-w-[140px]">{{ $b->item->nama_barang ?? '-' }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $b->jumlah }} unit</p>
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    @if($b->status === 'dipinjam')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Dipinjam
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Kembali
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-slate-400 text-xs">
                                    Belum ada transaksi peminjaman alat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pengaduan Kendala Fasilitas Masuk dari Guru -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i class="bi bi-exclamation-octagon text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Pengaduan Guru & Kendala Fasilitas</h3>
                    <p class="text-xs text-slate-400">Laporan kerusakan fasilitas masuk via portal publik sekolah</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if(isset($pengaduanMenunggu) && $pengaduanMenunggu > 0)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        {{ $pengaduanMenunggu }} Menunggu Ditangani
                    </span>
                @endif
                <a href="{{ route('complaints.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    <span>Lihat Semua Pengaduan</span>
                    <i class="bi bi-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-4">No. Tiket</th>
                        <th class="py-3 px-4">Nama Pelapor</th>
                        <th class="py-3 px-4">Lokasi & Unit</th>
                        <th class="py-3 px-4">Kendala Fasilitas</th>
                        <th class="py-3 px-4">Status Tindak Lanjut</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentComplaints as $c)
                        <tr class="hover:bg-slate-50/70 transition-colors {{ $c->tingkat_urgensi === 'tinggi_darurat' && $c->status === 'menunggu' ? 'bg-rose-50/30' : '' }}">
                            <td class="py-3 px-4 whitespace-nowrap">
                                <a href="{{ route('complaints.show', $c) }}" class="font-mono font-bold text-blue-600 hover:underline">
                                    {{ $c->ticket_code }}
                                </a>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $c->created_at->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <p class="font-bold text-slate-900">{{ $c->nama_pelapor }}</p>
                                <p class="text-[11px] text-slate-400">{{ $c->kontak }}</p>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <p class="text-slate-800 font-medium">{{ $c->lokasi_ruang }}</p>
                                <p class="text-[11px] text-slate-400">{{ $c->jurusan->kode ?? 'Sarpras Umum' }}</p>
                            </td>
                            <td class="py-3 px-4">
                                <p class="font-bold text-slate-900 truncate max-w-xs">{{ $c->judul_kendala }}</p>
                                <p class="text-[11px] text-slate-400 truncate max-w-xs">{{ $c->deskripsi }}</p>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($c->status === 'menunggu')
                                    @if($c->tingkat_urgensi === 'tinggi_darurat')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-300 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                                            Menunggu (Darurat)
                                        </span>
                                    @elseif($c->tingkat_urgensi === 'sedang')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Menunggu (Sedang)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            Menunggu (Rendah)
                                        </span>
                                    @endif
                                @elseif($c->status === 'diproses')
                                    @if($c->tingkat_urgensi === 'tinggi_darurat')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                            <i class="bi bi-tools text-[10px] text-rose-600"></i>
                                            Diproses (Darurat)
                                        </span>
                                    @elseif($c->tingkat_urgensi === 'sedang')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                                            <i class="bi bi-tools text-[10px] text-amber-600"></i>
                                            Diproses (Sedang)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                            Diproses
                                        </span>
                                    @endif
                                @elseif($c->status === 'selesai')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="bi bi-check-circle-fill text-[10px] text-emerald-600"></i>
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                @if($c->tingkat_urgensi === 'tinggi_darurat' && $c->status === 'menunggu')
                                    <a href="{{ route('complaints.show', $c) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-xs transition-all active:scale-95">
                                        <i class="bi bi-lightning-fill text-[11px]"></i>
                                        <span>Tindak Lanjut</span>
                                    </a>
                                @else
                                    <a href="{{ route('complaints.show', $c) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-colors shadow-xs">
                                        <span>Tindak Lanjut</span>
                                        <i class="bi bi-chevron-right text-[10px]"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                                Belum ada pengaduan kendala masuk dari guru/tendik.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
