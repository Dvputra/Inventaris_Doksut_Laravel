@extends('layouts.app')

@section('title', 'Dashboard ' . $jurusan->kode)

@section('content')
<div class="space-y-6">
    <!-- Jurusan Header Banner -->
    <div class="rounded-3xl bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 text-white p-6 sm:p-7 shadow-md shadow-blue-900/10 border border-blue-500/30 relative overflow-hidden">
        <!-- Subtle ambient glow decoration -->
        <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-white text-blue-800 shadow-xs">
                        <i class="bi bi-building text-amber-500"></i>
                        <span>AKUN PROGRAM KEAHLIAN / UNIT KERJA</span>
                    </span>
                    <span class="text-xs font-mono font-bold bg-blue-800/80 px-2 py-0.5 rounded text-blue-100 border border-blue-400/30">
                        {{ $jurusan->kode }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mb-2">
                    {{ $jurusan->nama }}
                </h1>
                <div class="flex flex-wrap items-center gap-2.5 mb-2">
                    <p class="text-blue-100 text-xs sm:text-sm flex items-center gap-1.5">
                        <i class="bi bi-person-badge text-amber-300"></i>
                        <span>Kepala Bengkel / Unit:</span>
                        <strong class="text-white font-bold">{{ $jurusan->kepala_bengkel ?? 'Belum ditentukan' }}</strong>
                        @if($jurusan->nip)
                            <span class="text-blue-200 text-xs font-mono">({{ $jurusan->nip }})</span>
                        @endif
                    </p>
                    <button type="button" onclick="openModal('modalEditKepalaUnit')" 
                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white/20 hover:bg-white/30 text-white text-xs font-semibold backdrop-blur-xs transition-colors border border-white/20 cursor-pointer shadow-xs"
                            title="Ubah Nama Kepala Bengkel / Unit & NIP">
                        <i class="bi bi-pencil-square text-[11px] text-amber-300"></i>
                        <span>Ubah Profil Unit</span>
                    </button>
                </div>
                @if($jurusan->deskripsi)
                    <p class="text-blue-100/90 text-xs sm:text-sm max-w-2xl leading-relaxed mt-1">
                        {{ $jurusan->deskripsi }}
                    </p>
                @endif
            </div>

            <!-- Quick Actions with Urgency Colors -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 shrink-0 w-full md:w-auto">
                <!-- Tambah Barang (Urgency: Primary Action / White on Blue) -->
                <a href="{{ route('items.create') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-blue-800 bg-white hover:bg-blue-50 rounded-xl shadow-sm transition-all active:scale-95">
                    <i class="bi bi-plus-circle text-blue-600"></i>
                    <span>Tambah Barang</span>
                </a>
                <!-- Catat Pinjam (Urgency: Neutral White Outline) -->
                <a href="{{ route('borrowings.create') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-white/15 hover:bg-white/25 border border-white/30 rounded-xl transition-all active:scale-95 backdrop-blur-xs">
                    <i class="bi bi-arrow-left-right text-amber-300"></i>
                    <span>Catat Pinjam</span>
                </a>
                <!-- Usul ke Sarpras (Urgency: Warning / Amber Accent) -->
                <a href="{{ route('procurements.create') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 rounded-xl shadow-sm transition-all active:scale-95 ring-2 ring-amber-300/40">
                    <i class="bi bi-send-plus"></i>
                    <span>Usul ke Sarpras</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 4 Ringkasan Statistik Utama -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- Kartu 1: Total Unit Barang -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-colors">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Fisik Unit</span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">
                        {{ number_format($totalUnit) }}
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                    <i class="bi bi-boxes text-lg"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>{{ $totalItems }} jenis barang terdata</span>
                <a href="{{ route('items.index') }}" class="font-semibold text-blue-600 hover:text-blue-700">Lihat Semua &rarr;</a>
            </div>
        </div>

        <!-- Kartu 2: Peminjaman Alat Aktif -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-colors">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Peminjaman Aktif</span>
                    <div class="flex items-baseline gap-2 mt-1">
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-purple-600">{{ $peminjamanAktif }}</h3>
                        <span class="text-xs font-semibold text-slate-500">Alat Dipinjam</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 border border-purple-100">
                    <i class="bi bi-arrow-left-right text-lg"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Sedang dipakai siswa/guru</span>
                <a href="{{ route('borrowings.index') }}" class="font-semibold text-blue-600 hover:text-blue-700">Daftar &rarr;</a>
            </div>
        </div>

        <!-- Kartu 3: Usulan Pengadaan Menunggu -->
        <div class="p-5 rounded-2xl {{ $pengajuanMenunggu > 0 ? 'bg-amber-50/60 border-amber-200/80 ring-1 ring-amber-300/30' : 'bg-white border-slate-200/90' }} border shadow-xs flex flex-col justify-between transition-colors">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider {{ $pengajuanMenunggu > 0 ? 'text-amber-800' : 'text-slate-400' }}">Usulan ke Sarpras</span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold {{ $pengajuanMenunggu > 0 ? 'text-amber-700' : 'text-slate-900' }} mt-1">
                        {{ $pengajuanMenunggu }} <span class="text-sm font-semibold {{ $pengajuanMenunggu > 0 ? 'text-amber-600' : 'text-slate-400' }}">Menunggu</span>
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-xl {{ $pengajuanMenunggu > 0 ? 'bg-amber-400 text-slate-950 font-bold shadow-xs' : 'bg-slate-50 text-slate-400 border border-slate-200' }} flex items-center justify-center shrink-0">
                    <i class="bi bi-clock-history text-lg"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t {{ $pengajuanMenunggu > 0 ? 'border-amber-200/80' : 'border-slate-100' }} flex items-center justify-between text-xs">
                <span class="{{ $pengajuanMenunggu > 0 ? 'text-amber-800' : 'text-slate-500' }}">
                    Total {{ $pengajuanCount }} usulan diajukan
                </span>
                <a href="{{ route('procurements.index') }}" class="font-bold text-amber-700 hover:text-amber-800">Status &rarr;</a>
            </div>
        </div>

        <!-- Kartu 4: Peringatan Stok Menipis -->
        <div class="p-5 rounded-2xl {{ $lowStockCount > 0 ? 'bg-rose-50/70 border-rose-200' : 'bg-white border-slate-200/90' }} border shadow-xs flex flex-col justify-between transition-colors">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider {{ $lowStockCount > 0 ? 'text-rose-700' : 'text-slate-400' }}">Bahan Menipis</span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold {{ $lowStockCount > 0 ? 'text-rose-700' : 'text-slate-900' }} mt-1">
                        {{ $lowStockCount }} <span class="text-sm font-semibold {{ $lowStockCount > 0 ? 'text-rose-600' : 'text-slate-400' }}">Bahan</span>
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-xl {{ $lowStockCount > 0 ? 'bg-rose-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-600 border border-emerald-100' }} flex items-center justify-center shrink-0">
                    <i class="bi {{ $lowStockCount > 0 ? 'bi-exclamation-triangle-fill' : 'bi-shield-check' }} text-lg"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="{{ $lowStockCount > 0 ? 'text-rose-700 font-medium' : 'text-slate-500' }}">
                    {{ $lowStockCount > 0 ? 'Perlu usulan restok' : 'Stok dalam batas aman' }}
                </span>
                <a href="{{ route('items.index', ['jenis' => 'bahan']) }}" class="font-semibold text-blue-600 hover:text-blue-700">Cek Bahan &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Kelayakan Fisik & Fasilitas Komputer (2 Kolom Rapi) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Visual Health Bar Kondisi Barang -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/80 shadow-xs p-5 sm:p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="bi bi-shield-check text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Status Kelayakan Fisik Alat & Fasilitas</h3>
                            <p class="text-xs text-slate-400">Tingkat kesiapan operasional sarana praktikum unit {{ $jurusan->kode }}</p>
                        </div>
                    </div>
                    @php
                        $persenBaik = $totalUnit > 0 ? round(($baikCount / $totalUnit) * 100) : 0;
                        $persenRusakRingan = $totalUnit > 0 ? round(($rusakRinganCount / $totalUnit) * 100) : 0;
                        $persenRusakBerat = $totalUnit > 0 ? round(($rusakBeratCount / $totalUnit) * 100) : 0;
                    @endphp
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ $persenBaik }}% Prima
                    </span>
                </div>

                <!-- Progress Bar Multi Segment -->
                <div class="w-full bg-slate-100 rounded-full h-3 flex overflow-hidden shadow-inner my-4">
                    <div class="bg-emerald-500 transition-all duration-500" style="width: {{ $persenBaik }}%" title="Baik: {{ number_format($baikCount) }} unit"></div>
                    <div class="bg-amber-400 transition-all duration-500" style="width: {{ $persenRusakRingan }}%" title="Rusak Ringan: {{ number_format($rusakRinganCount) }} unit"></div>
                    <div class="bg-rose-500 transition-all duration-500" style="width: {{ $persenRusakBerat }}%" title="Rusak Berat: {{ number_format($rusakBeratCount) }} unit"></div>
                </div>

                <!-- Breakdown 3 Kolom -->
                <div class="grid grid-cols-3 gap-3 pt-2">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 text-center">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 mb-1"></span>
                        <p class="text-[11px] font-semibold text-slate-500">Siap Praktikum (Baik)</p>
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
                <span class="text-slate-400">Total butuh penanganan: {{ number_format($rusakRinganCount + $rusakBeratCount) }} unit</span>
                <a href="{{ route('items.index', ['kondisi' => 'rusak_ringan']) }}" class="font-semibold text-blue-600 hover:text-blue-700">
                    Buka Log Perbaikan Unit &rarr;
                </a>
            </div>
        </div>

        <!-- Komputer & Log Pemakaian Bahan -->
        <div class="lg:col-span-4 space-y-4">
            <!-- Workstation Lab IT -->
            <div class="p-4 rounded-3xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                        <i class="bi bi-display text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Workstation PC Lab</p>
                        <h4 class="text-base font-bold text-slate-900 mt-0.5">{{ number_format($totalComputers) }} Unit</h4>
                    </div>
                </div>
                <a href="{{ route('items.index', ['is_computer' => 1]) }}" class="px-3 py-1.5 rounded-xl border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs transition-colors shadow-xs">
                    Kelola
                </a>
            </div>

            <!-- Log Pemakaian Bahan -->
            <div class="p-4 rounded-3xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 border border-sky-100">
                        <i class="bi bi-droplet-half text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Pemakaian Bahan</p>
                        <h4 class="text-base font-bold text-slate-900 mt-0.5">{{ number_format($totalUsages) }} Kali Digunakan</h4>
                    </div>
                </div>
                <a href="{{ route('usages.index') }}" class="px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition-colors shadow-xs">
                    Buka Log
                </a>
            </div>

            <!-- Laporan Resmi Unit -->
            <div class="p-4 rounded-3xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100">
                        <i class="bi bi-printer text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Cetak Laporan Unit</p>
                        <h4 class="text-xs font-bold text-slate-900 mt-0.5">Format Rekapitulasi SPJ</h4>
                    </div>
                </div>
                <a href="{{ route('reports.index') }}" class="px-3 py-1.5 rounded-xl border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs transition-colors shadow-xs">
                    Cetak
                </a>
            </div>
        </div>
    </div>

    <!-- Aktivitas Unit: Riwayat Usulan & Peminjaman Alat (Grid 2 Kolom) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Riwayat Usulan Pengadaan ke Sarpras -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/80 shadow-xs flex flex-col overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i class="bi bi-clipboard2-check text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Usulan Pengadaan Barang</h3>
                        <p class="text-xs text-slate-400">Pengajuan kebutuhan alat & bahan ke Sarpras Pusat</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('procurements.create') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                        + Ajukan Baru
                    </a>
                    <span class="text-slate-300">|</span>
                    <a href="{{ route('procurements.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">Semua</a>
                </div>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-3 px-4">Nama Barang</th>
                            <th class="py-3 px-4 text-center">Jumlah</th>
                            <th class="py-3 px-4">Estimasi Biaya</th>
                            <th class="py-3 px-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentProcurements as $proc)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4">
                                    <p class="font-bold text-slate-900">{{ $proc->nama_barang }}</p>
                                    <p class="text-[11px] text-slate-400 truncate max-w-xs">{{ Str::limit($proc->alasan, 35) }}</p>
                                </td>
                                <td class="py-3 px-4 text-center font-semibold text-slate-700 whitespace-nowrap">
                                    {{ $proc->jumlah }} {{ $proc->satuan }}
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-600 whitespace-nowrap">
                                    {{ $proc->estimasi_biaya ? 'Rp ' . number_format($proc->estimasi_biaya, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    @if($proc->status === 'menunggu')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Menunggu
                                        </span>
                                    @elseif($proc->status === 'disetujui')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Disetujui
                                        </span>
                                    @else
                                        <div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                Ditolak
                                            </span>
                                            @if($proc->catatan_sarpras)
                                                <p class="text-[10px] text-rose-600 mt-1 max-w-[130px] truncate" title="{{ $proc->catatan_sarpras }}">
                                                    {{ $proc->catatan_sarpras }}
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400 text-xs">
                                    Belum ada usulan pengadaan yang diajukan ke Sarpras Pusat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Peminjaman Alat di Unit Ini -->
        <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200/80 shadow-xs flex flex-col overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="bi bi-arrow-left-right text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Peminjaman Alat</h3>
                        <p class="text-xs text-slate-400">Peminjaman terkini di unit ini</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('borrowings.create') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                        + Catat
                    </a>
                    <span class="text-slate-300">|</span>
                    <a href="{{ route('borrowings.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">Semua</a>
                </div>
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
                                    <p class="text-[11px] text-slate-400">{{ $b->kelas_atau_jabatan }}</p>
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
                                    Belum ada transaksi peminjaman alat di unit ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ubah Nama Kepala Bengkel / Unit Kerja -->
<div id="modalEditKepalaUnit" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg bg-white rounded-3xl border border-slate-200 shadow-2xl p-6 sm:p-7 text-left transform transition-all">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="bi bi-pencil-square text-lg"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Ubah Data Kepala Unit Kerja</h3>
                    <p class="text-xs text-slate-400">Unit: {{ $jurusan->nama }} ({{ $jurusan->kode }})</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalEditKepalaUnit')" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <form action="{{ route('jurusan.my-unit.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="space-y-4">
                <div>
                    <label for="modal_kepala_bengkel" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nama Kepala Bengkel / Kepala Unit Kerja <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="kepala_bengkel" id="modal_kepala_bengkel" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                           placeholder="Contoh: Bpk. Budi Santoso, S.Pd / Waka Bidang..." 
                           value="{{ old('kepala_bengkel', $jurusan->kepala_bengkel) }}" required maxlength="255">
                    <p class="text-[11px] text-slate-400 mt-1">Nama ini akan tercetak pada lembar pengesahan tanda tangan laporan inventaris.</p>
                </div>

                <div>
                    <label for="modal_nip" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        NIP / NIY / NPY Kepala Unit (Opsional)
                    </label>
                    <input type="text" name="nip" id="modal_nip" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-mono text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                           placeholder="Contoh: 198507..." 
                           value="{{ old('nip', $jurusan->nip) }}" maxlength="50">
                    <p class="text-[11px] text-slate-400 mt-1">NIP/NIY akan tercetak otomatis di berkas cetak PDF laporan dan usulan pengadaan.</p>
                </div>

                <div>
                    <label for="modal_deskripsi" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Deskripsi / Ruang Lingkup Fasilitas (Opsional)
                    </label>
                    <textarea name="deskripsi" id="modal_deskripsi" rows="3" 
                              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                              placeholder="Deskripsi singkat fasilitas atau ruang lingkup unit...">{{ old('deskripsi', $jurusan->deskripsi) }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-5 mt-5 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalEditKepalaUnit')" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition-colors">
                    <i class="bi bi-save"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('modalEditKepalaUnit');
        }
    });
</script>
@endsection
