@extends('layouts.app')

@section('title', 'Kelola Unit Kerja & Jurusan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 ring-1 ring-blue-500/20">
                    <i class="bi bi-building-gear text-base"></i>
                </span>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Kelola Unit Kerja & Jurusan</h1>
            </div>
            <p class="text-sm text-slate-500 mt-1">Kelola data program keahlian (jurusan), unit kerja sekolah, serta nama kepala bengkel / kepala unit kerja.</p>
        </div>
        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('jurusans.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xs transition-colors">
                <i class="bi bi-plus-circle text-sm"></i>
                <span>Tambah Unit Kerja Baru</span>
            </a>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i class="bi bi-diagram-3 text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500">Total Unit / Jurusan</p>
                <h3 class="text-xl font-bold text-slate-900">{{ $jurusans->count() }} Unit</h3>
            </div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i class="bi bi-box-seam text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500">Total Aset Terdata</p>
                <h3 class="text-xl font-bold text-slate-900">{{ $jurusans->sum('items_count') }} Item</h3>
            </div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <i class="bi bi-person-badge text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500">Akun Pengguna Terkait</p>
                <h3 class="text-xl font-bold text-slate-900">{{ $jurusans->sum('users_count') }} Akun</h3>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5">
        <form action="{{ route('jurusans.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 items-center">
            <div class="flex-1 w-full relative rounded-xl shadow-xs">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="bi bi-search text-slate-400 text-xs"></i>
                </div>
                <input type="text" name="q" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Cari nama unit kerja, kode, atau kepala bengkel/unit..." value="{{ request('q') }}">
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs sm:text-sm shadow-xs transition-colors">
                    <i class="bi bi-filter"></i>
                    <span>Cari</span>
                </button>
                @if(request()->filled('q'))
                    <a href="{{ route('jurusans.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs sm:text-sm transition-colors" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise mr-1"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Jurusans / Units -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <!-- Desktop Table (md: and up) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 text-xs font-semibold uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4 w-24">Kode</th>
                        <th class="py-3 px-4">Nama Unit Kerja / Jurusan</th>
                        <th class="py-3 px-4">Kepala Bengkel / Kepala Unit</th>
                        <th class="py-3 px-4">Deskripsi / Ruang Lingkup</th>
                        <th class="py-3 px-4 text-center">Jumlah Barang</th>
                        <th class="py-3 px-4 text-center">Akun Login</th>
                        <th class="py-3 px-4 text-right w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jurusans as $index => $j)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 text-center text-slate-400 font-mono">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($j->kode === 'SAR')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold bg-amber-100 text-amber-900 border border-amber-300 px-2.5 py-0.5 rounded-full shadow-xs">
                                        <i class="bi bi-shield-check text-xs"></i> SAR
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-0.5 rounded-full shadow-xs">
                                        {{ $j->kode }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $j->nama }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5 font-semibold text-slate-800">
                                    <i class="bi bi-person-badge text-amber-500 text-xs"></i>
                                    <span>{{ $j->kepala_bengkel ?? '-' }}</span>
                                </div>
                                @if($j->nip)
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5 ml-4">
                                        NIP/NIY: {{ $j->nip }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 max-w-xs truncate" title="{{ $j->deskripsi }}">
                                {{ $j->deskripsi ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <a href="{{ route('items.index', ['jurusan_id' => $j->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-700 font-bold text-xs transition-colors">
                                    <span>{{ $j->items_count }} Item</span>
                                    <i class="bi bi-arrow-right text-[10px]"></i>
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-xs font-semibold">
                                    <i class="bi bi-people text-[11px]"></i> {{ $j->users_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Tombol Edit (Kuning / Amber Aksen) -->
                                    <a href="{{ route('jurusans.edit', $j) }}" class="p-1.5 rounded-lg border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-800 transition-colors" title="Ubah Nama Unit / Kepala Bengkel">
                                        <i class="bi bi-pencil-square text-xs"></i>
                                    </a>

                                    @if($j->kode !== 'SAR')
                                        <!-- Tombol Hapus (Rose / Merah) -->
                                        <form action="{{ route('jurusans.destroy', $j) }}" method="POST" class="inline"
                                              data-confirm="Apakah Anda yakin ingin menghapus unit kerja {{ addslashes($j->nama) }} ({{ $j->kode }})?{{ ($j->items_count > 0 || $j->users_count > 0) ? ' Perhatian: Unit ini masih memiliki ' . ($j->items_count > 0 ? $j->items_count . ' barang ' : '') . ($j->users_count > 0 ? $j->users_count . ' akun terhubung.' : '') : '' }}"
                                              data-confirm-title="Hapus Unit Kerja"
                                              data-confirm-type="danger"
                                              data-confirm-btn="Ya, Hapus Unit">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 transition-colors" title="Hapus Unit Kerja">
                                                <i class="bi bi-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                                    <i class="bi bi-building-x text-2xl"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-600">Tidak ada unit kerja yang ditemukan.</p>
                                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (Reflow on small screens < 768px) -->
        <div class="block md:hidden divide-y divide-slate-100">
            @forelse($jurusans as $index => $j)
                <div class="p-4 hover:bg-slate-50/75 transition-colors">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-900 text-sm">
                                {{ $j->nama }}
                            </h3>
                            <div class="flex items-center gap-1.5 mt-1">
                                @if($j->kode === 'SAR')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300 px-2 py-0.5 rounded-full shadow-xs">
                                        <i class="bi bi-shield-check text-xs"></i> SARPRAS
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-full shadow-xs">
                                        {{ $j->kode }}
                                    </span>
                                @endif
                                <span class="text-[11px] text-slate-400 font-medium">Unit Kerja #{{ $index + 1 }}</span>
                            </div>
                        </div>
                        <div class="shrink-0 flex items-center gap-1.5">
                            <a href="{{ route('jurusans.edit', $j) }}" class="p-2 rounded-xl border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-800 transition-colors shadow-xs active:scale-95" title="Ubah Nama Unit">
                                <i class="bi bi-pencil-square text-xs"></i>
                            </a>
                            @if($j->kode !== 'SAR')
                                <form action="{{ route('jurusans.destroy', $j) }}" method="POST" class="inline"
                                      data-confirm="Apakah Anda yakin ingin menghapus unit kerja {{ addslashes($j->nama) }} ({{ $j->kode }})?{{ ($j->items_count > 0 || $j->users_count > 0) ? ' Perhatian: Unit ini masih memiliki ' . ($j->items_count > 0 ? $j->items_count . ' barang ' : '') . ($j->users_count > 0 ? $j->users_count . ' akun terhubung.' : '') : '' }}"
                                      data-confirm-title="Hapus Unit Kerja"
                                      data-confirm-type="danger"
                                      data-confirm-btn="Ya, Hapus Unit">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 transition-colors shadow-xs active:scale-95" title="Hapus Unit">
                                        <i class="bi bi-trash text-xs"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-medium">Kepala Bengkel / Unit</span>
                            <span class="font-semibold text-slate-800 truncate block">{{ $j->kepala_bengkel ?? '-' }}</span>
                            @if($j->nip)
                                <span class="text-[10px] text-slate-500 font-mono block">NIP: {{ $j->nip }}</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-medium">Akun Pengguna</span>
                            <span class="font-semibold text-slate-700">{{ $j->users_count }} akun terhubung</span>
                        </div>
                    </div>

                    @if($j->deskripsi)
                        <p class="text-[11px] text-slate-500 mt-2 line-clamp-2">
                            {{ $j->deskripsi }}
                        </p>
                    @endif

                    <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-slate-100">
                        <span class="text-xs font-bold text-slate-700">
                            {{ $j->items_count }} Item Barang
                        </span>
                        <a href="{{ route('items.index', ['jurusan_id' => $j->id]) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold transition-colors shadow-xs active:scale-95">
                            <span>Buka Aset</span>
                            <i class="bi bi-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-12 px-4 text-center text-slate-400">
                    <i class="bi bi-building-x text-3xl block mb-2 text-slate-300"></i>
                    <p class="text-sm font-semibold text-slate-600">Tidak ada unit kerja yang ditemukan.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
