@extends('layouts.app')

@section('title', 'Usulan Pengadaan Barang')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 ring-1 ring-amber-500/20">
                    <i class="bi bi-cart-plus text-base"></i>
                </span>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Usulan Pengadaan Barang &amp; Bahan</h1>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                @if(Auth::user()->isSarpras())
                    Verifikasi, persetujuan, cetak dan kelola usulan pengadaan barang atau bahan dari 5 jurusan ke Sarpras pusat.
                @else
                    Daftar pengajuan permohonan pengadaan peralatan atau bahan praktik ke Sarpras pusat. Anda dapat menambah, mengedit, dan mencetak surat usulan.
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('procurements.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-sm shadow-xs transition-colors">
                <i class="bi bi-plus-lg text-sm"></i>
                <span>Buat Usulan Pengadaan</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5">
        <form action="{{ route('procurements.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            @if(Auth::user()->isSarpras())
                <div class="sm:col-span-5">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Filter Jurusan</label>
                    <select name="jurusan_id" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusans as $j)
                            <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                                {{ $j->kode }} - {{ $j->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="{{ Auth::user()->isSarpras() ? 'sm:col-span-4' : 'sm:col-span-8' }}">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Filter Status</label>
                <select name="status" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="{{ Auth::user()->isSarpras() ? 'sm:col-span-3' : 'sm:col-span-4' }} flex gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-xs transition-colors">
                    <i class="bi bi-filter"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('procurements.index') }}" class="inline-flex items-center justify-center w-10 h-9.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Usulan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 text-xs font-semibold uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4 min-w-[130px]">Jurusan / No. Surat</th>
                        <th class="py-3 px-4 min-w-[220px]">Usulan &amp; Rincian Barang</th>
                        <th class="py-3 px-4 text-center">Jumlah Item</th>
                        <th class="py-3 px-4">Perkiraan Biaya</th>
                        <th class="py-3 px-4 min-w-[180px]">Alasan / Urgensi</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right min-w-[150px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($procurements as $index => $p)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 text-center text-slate-400 font-mono">
                                {{ $procurements->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
                                    {{ $p->jurusan->kode }}
                                </span>
                                <div class="font-mono text-[11px] text-slate-500 font-semibold mt-1">
                                    {{ $p->nomor_usulan ?? 'UP-' . $p->id }}
                                </div>
                                <span class="block text-[11px] text-slate-400">
                                    {{ $p->created_at->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 leading-snug">
                                    {{ $p->judul_pengadaan ?: $p->summary_barang }}
                                </div>
                                @if($p->items->count() > 1)
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                            <i class="bi bi-boxes"></i> {{ $p->items->count() }} macam barang
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-1">
                                        {{ $p->items->pluck('nama_barang')->take(3)->join(', ') }}{{ $p->items->count() > 3 ? '...' : '' }}
                                    </p>
                                @else
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">
                                        {{ Str::limit($p->spesifikasi ?? 'Tidak ada spesifikasi khusus', 50) }}
                                    </p>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-center">
                                @if($p->items->count() > 1)
                                    <span class="font-bold text-slate-900">{{ number_format($p->items->sum('jumlah')) }}</span>
                                    <span class="block text-[11px] text-slate-400">total unit/pcs</span>
                                @else
                                    <span class="font-bold text-slate-900">{{ number_format($p->jumlah) }}</span>
                                    <span class="text-xs text-slate-500">{{ $p->satuan }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($p->perkiraan_biaya)
                                    <span class="font-bold text-slate-900">Rp {{ number_format($p->perkiraan_biaya, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-xs text-slate-600 line-clamp-2">{{ Str::limit($p->alasan, 60) }}</span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    @if($p->status === 'menunggu')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="bi bi-hourglass-split"></i>
                                            <span>Menunggu Sarpras</span>
                                        </span>
                                    @elseif($p->status === 'disetujui')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="bi bi-check-circle-fill"></i>
                                            <span>ACC Sarpras</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="bi bi-x-circle-fill"></i>
                                            <span>Ditolak Sarpras</span>
                                        </span>
                                    @endif

                                    @if($p->status === 'disetujui')
                                        <div>
                                            @if($p->status_kepsek === 'disetujui')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <i class="bi bi-award-fill"></i> ACC Kepsek
                                                </span>
                                            @elseif($p->status_kepsek === 'ditolak')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    <i class="bi bi-x-octagon-fill"></i> Ditolak Kepsek
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                    <i class="bi bi-clock"></i> Tunggu Kepsek
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Aksi Menu -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Detail & Pengesahan Tanda Tangan -->
                                    <a href="{{ route('procurements.show', $p) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200/80 transition-colors" title="Lihat Detail & Pengesahan TTD">
                                        <i class="bi bi-eye text-xs"></i>
                                    </a>

                                    <!-- Cetak PDF/Dokumen Resmi -->
                                    <a href="{{ route('procurements.print', $p) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200/80 transition-colors" title="Cetak Surat Usulan (A4)">
                                        <i class="bi bi-printer text-xs"></i>
                                    </a>

                                    <!-- Tombol Edit Usulan (Sarpras atau Unit Pemilik saat Menunggu) -->
                                    @if(Auth::user()->isSarpras() || ($p->jurusan_id === Auth::user()->jurusan_id && $p->status === 'menunggu'))
                                        <a href="{{ route('procurements.edit', $p) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200/80 transition-colors" title="Edit Usulan Pengadaan">
                                            <i class="bi bi-pencil text-xs"></i>
                                        </a>
                                    @endif

                                    <!-- Tombol Hapus Usulan (Sarpras atau Unit Pemilik jika belum disetujui) -->
                                    @php
                                        $pIsApproved = ($p->status === 'disetujui' || $p->ttd_sarpras !== null || $p->status_kepsek === 'disetujui' || $p->ttd_kepsek !== null);
                                    @endphp
                                    @if(Auth::user()->isSarpras() || ($p->jurusan_id === Auth::user()->jurusan_id && ! $pIsApproved))
                                        <form action="{{ route('procurements.destroy', $p) }}" method="POST" class="inline"
                                              data-confirm="Apakah Anda yakin ingin menghapus usulan pengadaan [{{ $p->nomor_usulan ?? 'UP-'.$p->id }}] {{ addslashes($p->summary_barang) }}?"
                                              data-confirm-title="Hapus Usulan Pengadaan"
                                              data-confirm-type="danger"
                                              data-confirm-btn="Ya, Hapus Usulan">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/80 transition-colors" title="Hapus Usulan Pengadaan">
                                                <i class="bi bi-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <i class="bi bi-inbox text-2xl"></i>
                                </div>
                                <p class="text-slate-500 text-sm font-medium">Tidak ada data usulan pengadaan barang.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (Reflow on small screens < 768px) -->
        <div class="block md:hidden divide-y divide-slate-100">
            @forelse($procurements as $index => $p)
                <div class="p-4 hover:bg-slate-50/75 transition-colors">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
                                    {{ $p->jurusan->kode }}
                                </span>
                                <span class="font-mono text-[11px] text-slate-500 font-semibold">
                                    {{ $p->nomor_usulan ?? 'UP-' . $p->id }}
                                </span>
                            </div>
                            <span class="block text-[11px] text-slate-400 mt-0.5">
                                Diajukan: {{ $p->created_at->format('d/m/Y') }}
                            </span>
                        </div>
                        <div>
                            @if($p->status === 'menunggu')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu
                                </span>
                            @elseif($p->status === 'disetujui')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="bi bi-check-circle-fill text-emerald-500"></i>
                                    Disetujui
                                </span>
                            @elseif($p->status === 'ditolak')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <i class="bi bi-x-circle-fill text-rose-500"></i>
                                    Ditolak
                                </span>
                            @endif
                        </div>
                    </div>

                    <h4 class="font-bold text-slate-900 text-sm leading-snug mt-1">
                        {{ $p->judul_pengadaan ?: $p->summary_barang }}
                    </h4>

                    @if($p->items->count() > 1)
                        <div class="flex items-center gap-1.5 mt-1.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                <i class="bi bi-boxes"></i> {{ $p->items->count() }} macam barang
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                            {{ $p->items->pluck('nama_barang')->join(', ') }}
                        </p>
                    @else
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                            {{ $p->spesifikasi ?? 'Tidak ada spesifikasi khusus' }}
                        </p>
                    @endif

                    <!-- Detail strip -->
                    <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs mt-3">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-medium">Total Item</span>
                            <span class="font-bold text-slate-800">
                                @if($p->items->count() > 1)
                                    {{ number_format($p->items->sum('jumlah')) }} unit/pcs
                                @else
                                    {{ number_format($p->jumlah) }} {{ $p->satuan }}
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-medium">Estimasi Biaya</span>
                            <span class="font-bold text-slate-900">
                                @if($p->perkiraan_biaya)
                                    Rp {{ number_format($p->perkiraan_biaya, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    </div>

                    @if($p->alasan)
                        <p class="text-xs text-slate-600 mt-2 line-clamp-2 bg-slate-50/50 p-2 rounded-lg border border-slate-100">
                            <span class="font-semibold text-slate-700">Urgensi:</span> {{ $p->alasan }}
                        </p>
                    @endif

                    @if($p->status === 'ditolak' && $p->catatan_sarpras)
                        <div class="mt-2 p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700">
                            <strong>Catatan Penolakan:</strong> {{ $p->catatan_sarpras }}
                        </div>
                    @endif

                    <!-- Action Footer -->
                    <div class="flex flex-wrap items-center justify-end gap-2 mt-3 pt-2.5 border-t border-slate-100">
                        <a href="{{ route('procurements.show', $p) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold border border-blue-200 transition-colors shadow-xs" 
                           title="Lihat Detail & Tanda Tangan">
                            <i class="bi bi-eye text-xs"></i>
                            <span>Detail & TTD</span>
                        </a>

                        <a href="{{ route('procurements.print', $p) }}" target="_blank" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 text-xs font-semibold border border-slate-200 transition-colors shadow-xs" 
                           title="Cetak Berkas Usulan">
                            <i class="bi bi-printer text-xs"></i>
                            <span>Cetak</span>
                        </a>

                        @php
                            $canEdit = Auth::user()->isSarpras() || (Auth::user()->isJurusan() && $p->status === 'menunggu' && $p->jurusan_id === Auth::user()->jurusan_id);
                            $pIsApprovedMobile = ($p->status === 'disetujui' || $p->ttd_sarpras !== null || $p->status_kepsek === 'disetujui' || $p->ttd_kepsek !== null);
                            $canDelete = Auth::user()->isSarpras() || (Auth::user()->isJurusan() && ! $pIsApprovedMobile && $p->jurusan_id === Auth::user()->jurusan_id);
                        @endphp

                        @if($canEdit)
                            <a href="{{ route('procurements.edit', $p) }}" 
                               class="p-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 transition-colors shadow-xs" 
                               title="Edit Data Usulan">
                                <i class="bi bi-pencil text-xs"></i>
                            </a>
                        @endif

                        @if($canDelete)
                            <form action="{{ route('procurements.destroy', $p) }}" method="POST" class="inline"
                                  data-confirm="Apakah Anda yakin ingin menghapus berkas usulan pengadaan '{{ addslashes($p->summary_barang) }}' ini?"
                                  data-confirm-title="Hapus Usulan Pengadaan"
                                  data-confirm-type="danger"
                                  data-confirm-btn="Ya, Hapus Usulan"
                                  data-confirm-icon="bi bi-trash3 text-2xl">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 transition-colors shadow-xs" 
                                        title="Hapus Usulan">
                                    <i class="bi bi-trash text-xs"></i>
                                </button>
                            </form>
                        @endif


                    </div>
                </div>
            @empty
                <div class="py-12 px-4 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <i class="bi bi-inbox text-2xl"></i>
                    </div>
                    <p class="text-slate-500 text-sm font-medium">Tidak ada data usulan pengadaan barang.</p>
                </div>
            @endforelse
        </div>

        @if($procurements->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $procurements->links() }}
            </div>
        @endif
    </div>
</div>


@endsection
