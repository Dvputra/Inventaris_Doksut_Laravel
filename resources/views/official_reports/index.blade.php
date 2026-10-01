@extends('layouts.app')

@section('title', 'Berita Acara Sarpras')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 ring-1 ring-amber-500/20">
                    <i class="bi bi-file-earmark-ruled text-base"></i>
                </span>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Berita Acara Sarana &amp; Prasarana</h1>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                Pencatatan dan pengarsipan formal dokumen Berita Acara Kerusakan / Penghapusan Barang dan Penjualan / Lelang Aset Barang.
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('official-reports.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-sm shadow-xs transition-colors">
                <i class="bi bi-plus-lg text-sm"></i>
                <span>Buat Berita Acara Baru</span>
            </a>
        </div>
    </div>

    <!-- Statistik Ringkas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Dokumen</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="bi bi-files"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Barang Rusak / Afkir</p>
                <p class="text-2xl font-bold text-rose-600 mt-1">{{ number_format($stats['barang_rusak']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
                <i class="bi bi-trash3-fill"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Penjualan / Lelang</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($stats['penjualan']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Nilai Penjualan</p>
                <p class="text-lg font-bold text-slate-900 mt-1">Rp {{ number_format($stats['total_penjualan'], 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="bi bi-wallet2"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5">
        <form action="{{ route('official-reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Pencarian</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <i class="bi bi-search text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nomor surat, judul, pihak..." class="w-full pl-9 pr-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
            </div>

            <div class="sm:col-span-3">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jenis Berita Acara</label>
                <select name="jenis" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">Semua Jenis</option>
                    <option value="barang_rusak" {{ request('jenis') == 'barang_rusak' ? 'selected' : '' }}>Barang Rusak / Afkir</option>
                    <option value="penjualan" {{ request('jenis') == 'penjualan' ? 'selected' : '' }}>Penjualan / Lelang</option>
                </select>
            </div>

            <div class="sm:col-span-3">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jurusan / Unit</label>
                <select name="jurusan_id" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">Semua Jurusan / Unit</option>
                    @foreach($jurusans as $j)
                        <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->kode }} - {{ $j->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-xs transition-colors">
                    <i class="bi bi-filter"></i>
                    <span>Cari</span>
                </button>
                <a href="{{ route('official-reports.index') }}" class="inline-flex items-center justify-center w-10 h-9.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Arsip Berita Acara -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nomor &amp; Judul Berita Acara</th>
                        <th class="py-3 px-4">Jenis</th>
                        <th class="py-3 px-4">Tanggal &amp; Unit</th>
                        <th class="py-3 px-4">Pihak Terkait</th>
                        <th class="py-3 px-4 text-center">Rincian Barang</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reports as $index => $rep)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4 text-center text-slate-500 font-medium">
                                {{ $reports->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4">
                                <a href="{{ route('official-reports.show', $rep) }}" class="font-bold text-slate-900 hover:text-blue-600 transition-colors block">
                                    {{ $rep->nomor_surat }}
                                </a>
                                <div class="text-xs text-slate-600 line-clamp-1 mt-0.5 font-medium">
                                    {{ $rep->judul }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($rep->jenis === 'barang_rusak')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/60">
                                        <i class="bi bi-trash3 text-xs"></i>
                                        <span>Barang Rusak</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                        <i class="bi bi-cash-coin text-xs"></i>
                                        <span>Penjualan</span>
                                    </span>
                                    @if($rep->total_nominal > 0)
                                        <div class="text-[11px] font-bold text-emerald-600 mt-1">
                                            Rp {{ number_format($rep->total_nominal, 0, ',', '.') }}
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-800">
                                    {{ $rep->tanggal->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    {{ $rep->jurusan ? $rep->jurusan->kode : 'Sarpras Pusat' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <div class="text-slate-800 font-medium">
                                    <span class="text-slate-500 font-normal">P1:</span> {{ $rep->pihak_pertama_nama }}
                                </div>
                                <div class="text-slate-600 mt-0.5">
                                    <span class="text-slate-500 font-normal">P2:</span> {{ $rep->pihak_kedua_nama }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $rep->items->count() }} Item
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('official-reports.print', $rep) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition-colors" title="Cetak Surat Dinas">
                                        <i class="bi bi-printer text-xs"></i>
                                        <span>Cetak</span>
                                    </a>
                                    <a href="{{ route('official-reports.show', $rep) }}" class="inline-flex items-center justify-center w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs transition-colors" title="Lihat Rincian">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('official-reports.destroy', $rep) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip berita acara ini?');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-7 h-7 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs transition-colors" title="Hapus Dokumen">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 px-4 text-center">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                                    <i class="bi bi-inbox"></i>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800">Belum Ada Berita Acara</h3>
                                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                    Belum ada dokumen Berita Acara barang rusak atau penjualan aset yang tercatat.
                                </p>
                                <a href="{{ route('official-reports.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 mt-3 rounded-lg bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-xs">
                                    <i class="bi bi-plus-lg"></i>
                                    <span>Buat Berita Acara Sekarang</span>
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tampilan Mobile (Card List) -->
        <div class="divide-y divide-slate-100 md:hidden">
            @forelse($reports as $rep)
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $rep->jenis === 'barang_rusak' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $rep->jenis === 'barang_rusak' ? 'Barang Rusak' : 'Penjualan' }}
                            </span>
                            <h3 class="text-sm font-bold text-slate-900 mt-1">
                                <a href="{{ route('official-reports.show', $rep) }}">{{ $rep->nomor_surat }}</a>
                            </h3>
                            <p class="text-xs text-slate-600 line-clamp-1">{{ $rep->judul }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-xs font-semibold text-slate-500">{{ $rep->tanggal->format('d/m/Y') }}</span>
                            @if($rep->total_nominal > 0)
                                <div class="text-xs font-bold text-emerald-600">
                                    Rp {{ number_format($rep->total_nominal, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="text-xs text-slate-500 bg-slate-50 p-2.5 rounded-xl space-y-1">
                        <div><strong class="text-slate-700">Pihak 1:</strong> {{ $rep->pihak_pertama_nama }} ({{ $rep->pihak_pertama_jabatan }})</div>
                        <div><strong class="text-slate-700">Pihak 2:</strong> {{ $rep->pihak_kedua_nama }} ({{ $rep->pihak_kedua_jabatan }})</div>
                        <div><strong class="text-slate-700">Total Barang:</strong> {{ $rep->items->count() }} Item</div>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-1">
                        <a href="{{ route('official-reports.print', $rep) }}" target="_blank" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 text-xs font-medium">
                            <i class="bi bi-printer me-1"></i> Cetak
                        </a>
                        <a href="{{ route('official-reports.show', $rep) }}" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-medium">
                            Detail
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500 text-sm">
                    Belum ada data berita acara.
                </div>
            @endforelse
        </div>

        @if($reports->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
