@extends('layouts.app')

@section('title', 'Stok di Gudang Sarpras')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1.5">
            <span class="inline-flex items-center gap-1 text-[11px] font-extrabold px-2.5 py-0.5 rounded-full bg-amber-400 text-slate-950 shadow-xs">
                <i class="bi bi-archive-fill"></i> GUDANG PUSAT
            </span>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Stok & Logistik di Gudang Sarpras</h2>
        </div>
        <p class="text-xs sm:text-sm text-slate-500">
            Monitoring ketersediaan bahan habis pakai pemeliharaan gedung, suku cadang kelistrikan, dan peralatan cadangan di Gudang Sarpras.
        </p>
    </div>
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full md:w-auto">
        <!-- Catat Pengeluaran Bahan (Urgency: Neutral Secondary) -->
        <a href="{{ route('usages.create') }}" 
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3.5 py-2.5 sm:py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-xs transition-all active:scale-95">
            <i class="bi bi-dash-circle text-rose-500"></i>
            <span>Catat Pengeluaran Bahan</span>
        </a>
        <!-- Tambah Barang / Stok Baru (Urgency: Warning / Amber Accent) -->
        <a href="{{ route('items.create', ['jurusan_id' => $sarJurusan->id ?? '', 'penempatan_sarpras' => 'gudang']) }}" 
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 sm:py-2 text-xs font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 rounded-xl shadow-sm transition-all active:scale-95 ring-2 ring-amber-300/40">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Barang / Stok Baru</span>
        </a>
    </div>
</div>

<!-- Stat Cards Gudang Sarpras -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
    <!-- Stat 1: Total Item di Gudang -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Item di Gudang</span>
            <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalItemGudang }} <span class="text-sm font-normal text-slate-500">Jenis</span></h3>
            <p class="text-xs text-slate-500 mt-1">Bahan habis pakai & alat cadangan</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100 text-xl">
            <i class="bi bi-boxes"></i>
        </div>
    </div>

    <!-- Stat 2: Total Kuantitas Fisik -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Kuantitas Fisik</span>
            <h3 class="text-2xl font-extrabold text-blue-700 mt-1">{{ number_format($totalStokFisik) }}</h3>
            <p class="text-xs text-slate-500 mt-1">Akumulasi kuantitas seluruh stok</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 border border-sky-100 text-xl">
            <i class="bi bi-layers"></i>
        </div>
    </div>

    <!-- Stat 3: Stok Menipis (Kritis) -->
    <div class="p-5 rounded-2xl {{ $kritisCount > 0 ? 'bg-rose-50/70 border-rose-200 text-rose-900' : 'bg-white border-slate-200/90' }} border shadow-xs flex items-center justify-between">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wider {{ $kritisCount > 0 ? 'text-rose-600' : 'text-slate-400' }}">Stok Menipis (Kritis)</span>
            <h3 class="text-2xl font-extrabold mt-1 {{ $kritisCount > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $kritisCount }} Bahan</h3>
            <p class="text-xs mt-1">
                @if($kritisCount > 0)
                    <a href="{{ route('sarpras.gudang', ['kritis' => 1]) }}" class="text-rose-700 font-bold hover:underline">
                        Filter stok kritis &rarr;
                    </a>
                @else
                    <span class="text-slate-500">Seluruh stok gudang aman</span>
                @endif
            </p>
        </div>
        <div class="w-12 h-12 rounded-2xl {{ $kritisCount > 0 ? 'bg-rose-600 text-white' : 'bg-emerald-50 text-emerald-600 border border-emerald-100' }} flex items-center justify-center shrink-0 text-xl">
            <i class="bi {{ $kritisCount > 0 ? 'bi-exclamation-triangle-fill' : 'bi-shield-check' }}"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
    <!-- Tabel Stok Gudang -->
    <div class="xl:col-span-8 flex flex-col gap-4">
        <!-- Filter Box -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-4">
            <form action="{{ route('sarpras.gudang') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-5">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Nama / Kode / Rak</label>
                    <div class="relative">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="q" 
                               class="w-full pl-8 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50" 
                               placeholder="Contoh: Lampu, Kabel, Rak B1..." 
                               value="{{ request('q') }}">
                    </div>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Kategori</label>
                    <select name="category_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                [{{ $cat->kode }}] {{ $cat->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Jenis Barang</label>
                    <select name="jenis" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                        <option value="">Semua Jenis</option>
                        <option value="bahan" {{ request('jenis') == 'bahan' ? 'selected' : '' }}>Bahan Habis Pakai</option>
                        <option value="alat" {{ request('jenis') == 'alat' ? 'selected' : '' }}>Alat / Suku Cadang</option>
                    </select>
                </div>

                <div class="md:col-span-1 flex items-center gap-1.5">
                    <button type="submit" class="flex-1 py-2 px-3 text-xs font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 rounded-xl shadow-xs transition-all active:scale-95 flex items-center justify-center" title="Terapkan Filter">
                        <i class="bi bi-filter text-sm"></i>
                    </button>
                    <a href="{{ route('sarpras.gudang') }}" class="py-2 px-3 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-xl transition-all active:scale-95 flex items-center justify-center" title="Reset">
                        <i class="bi bi-arrow-counterclockwise text-sm"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Container -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto hidden md:block">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px]">
                        <tr>
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4">Kode & Nama Barang</th>
                            <th class="py-3.5 px-4">Posisi Rak / Ruang</th>
                            <th class="py-3.5 px-4">Jenis</th>
                            <th class="py-3.5 px-4">Stok Tersedia</th>
                            <th class="py-3.5 px-4">Batas Min</th>
                            <th class="py-3.5 px-4 text-right w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items as $index => $item)
                            @php
                                $isCritical = ($item->jenis === 'bahan' && $item->min_stok > 0 && $item->jumlah <= $item->min_stok);
                            @endphp
                            <tr class="{{ $isCritical ? 'bg-rose-50/60' : 'hover:bg-slate-50/75' }} transition-colors">
                                <td class="py-3 px-4 text-center text-slate-400 font-medium">
                                    {{ $items->firstItem() + $index }}
                                </td>
                                <td class="py-3 px-4">
                                    <a href="{{ route('items.show', $item) }}" class="font-bold text-slate-900 hover:text-blue-600 text-sm">
                                        {{ $item->nama_barang }}
                                    </a>
                                    <span class="inline-block mt-1 font-mono text-[11px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ $item->kode_barang }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="font-semibold text-slate-800">{{ $item->lokasi ?? 'Gudang Sarpras' }}</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $item->category->nama ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-4">
                                    @if($item->jenis === 'bahan')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                            Bahan Habis Pakai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            Alat / Suku Cadang
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-sm font-extrabold {{ $isCritical ? 'text-rose-600' : 'text-slate-900' }}">
                                            {{ $item->jumlah }}
                                        </span>
                                        <span class="text-xs text-slate-500">{{ $item->satuan }}</span>
                                    </div>
                                    @if($isCritical)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded bg-rose-600 text-white mt-1 shadow-xs">
                                            <i class="bi bi-exclamation-triangle-fill"></i> Stok Menipis
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($item->min_stok > 0)
                                        <span class="font-mono text-[11px] text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                            {{ $item->min_stok }} {{ $item->satuan }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        @if($item->jenis === 'bahan' && $item->jumlah > 0)
                                            <a href="{{ route('usages.create', ['item_id' => $item->id]) }}" 
                                               class="p-1.5 rounded-lg text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 border border-transparent hover:border-emerald-200 transition-all" 
                                               title="Catat Pengeluaran Bahan">
                                                <i class="bi bi-box-arrow-right text-xs"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('items.show', $item) }}" 
                                           class="p-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-transparent hover:border-slate-200 transition-all" 
                                           title="Detail">
                                            <i class="bi bi-eye text-xs"></i>
                                        </a>
                                        @if(Auth::user()->isSarpras())
                                            <a href="{{ route('items.edit', $item) }}" 
                                               class="p-1.5 rounded-lg text-blue-600 hover:text-blue-700 hover:bg-blue-50 border border-transparent hover:border-blue-200 transition-all" 
                                               title="Edit">
                                            <i class="bi bi-pencil text-xs"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <i class="bi bi-box2 text-3xl d-block mb-2 text-slate-300"></i>
                                    <p class="font-semibold text-slate-700 text-sm">Belum ada data barang di Gudang Sarpras</p>
                                    <p class="text-xs text-slate-400 mt-1 mb-4">Gunakan tombol di bawah untuk memasukkan material atau suku cadang baru.</p>
                                    <a href="{{ route('items.create', ['jurusan_id' => $sarJurusan->id ?? '', 'penempatan_sarpras' => 'gudang']) }}" 
                                       class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 rounded-xl shadow-xs transition-all">
                                        <i class="bi bi-plus-lg"></i>
                                        <span>Tambah Stok Baru ke Gudang</span>
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View (Reflow on small screens < 768px) -->
            <div class="block md:hidden divide-y divide-slate-100">
                @forelse($items as $index => $item)
                    @php
                        $isCritical = ($item->jenis === 'bahan' && $item->min_stok > 0 && $item->jumlah <= $item->min_stok);
                    @endphp
                    <div class="p-4 {{ $isCritical ? 'bg-rose-50/40' : 'hover:bg-slate-50/75' }} transition-colors">
                        <div class="flex items-start justify-between gap-2 mb-1.5">
                            <div class="min-w-0">
                                <a href="{{ route('items.show', $item) }}" class="font-bold text-slate-900 hover:text-blue-600 text-sm block truncate">
                                    {{ $item->nama_barang }}
                                </a>
                                <span class="inline-block mt-0.5 font-mono text-[10px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $item->kode_barang }}
                                </span>
                            </div>
                            <div class="shrink-0">
                                @if($item->jenis === 'bahan')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                        Bahan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        Alat
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 p-2 rounded-xl bg-slate-50 border border-slate-100 text-xs mt-2">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-medium">Stok Tersedia</span>
                                <div class="flex items-center gap-1">
                                    <span class="font-extrabold {{ $isCritical ? 'text-rose-600' : 'text-slate-900' }}">
                                        {{ $item->jumlah }} {{ $item->satuan }}
                                    </span>
                                    @if($isCritical)
                                        <i class="bi bi-exclamation-triangle-fill text-rose-500 text-xs" title="Kritis"></i>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-medium">Posisi Rak</span>
                                <span class="font-medium text-slate-700 truncate block">{{ $item->lokasi ?? 'Gudang Sarpras' }}</span>
                            </div>
                        </div>

                        <!-- Action Footer -->
                        <div class="flex items-center justify-end gap-2 mt-3 pt-2.5 border-t border-slate-100">
                            @if($item->jenis === 'bahan' && $item->jumlah > 0)
                                <a href="{{ route('usages.create', ['item_id' => $item->id]) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-semibold transition-colors shadow-xs" 
                                   title="Catat Pengeluaran Bahan">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Keluarkan</span>
                                </a>
                            @endif
                            <a href="{{ route('items.show', $item) }}" 
                               class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 transition-colors shadow-xs" 
                               title="Detail">
                                <i class="bi bi-eye text-xs"></i>
                            </a>
                            @if(Auth::user()->isSarpras())
                                <a href="{{ route('items.edit', $item) }}" 
                                   class="p-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 transition-colors shadow-xs" 
                                   title="Edit">
                                    <i class="bi bi-pencil text-xs"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-12 px-4 text-center text-slate-400">
                        <i class="bi bi-box2 text-3xl block mb-2 text-slate-300"></i>
                        <p class="text-sm font-semibold text-slate-600">Belum ada data barang di Gudang Sarpras.</p>
                    </div>
                @endforelse
            </div>

            @if($items->hasPages())
                <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Panel Riwayat Mutasi Keluar Gudang -->
    <div class="xl:col-span-4">
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 text-sm">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Mutasi Keluar Gudang</h3>
                </div>
                <a href="{{ route('usages.index', ['jurusan_id' => $sarJurusan->id ?? '']) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Lihat Semua &rarr;</a>
            </div>
            <div class="p-5">
                <p class="text-xs text-slate-400 mb-3">5 riwayat pemakaian material sarpras terakhir:</p>
                <div class="space-y-3">
                    @forelse($recentUsages as $usage)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70">
                            <div class="flex justify-between items-start mb-1">
                                <p class="text-xs font-bold text-slate-800 truncate max-w-[180px]">{{ $usage->item->nama_barang ?? '-' }}</p>
                                <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded bg-rose-50 text-rose-600 border border-rose-200">
                                    -{{ $usage->jumlah }} {{ $usage->item->satuan ?? 'unit' }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-500 space-y-0.5">
                                <p><i class="bi bi-person mr-1 text-slate-400"></i>{{ $usage->nama_guru }}</p>
                                <p><i class="bi bi-geo-alt mr-1 text-slate-400"></i>{{ $usage->kelas }}</p>
                                @if($usage->keperluan_jobsheet)
                                    <p class="text-slate-400 italic">{{ Str::limit($usage->keperluan_jobsheet, 50) }}</p>
                                @endif
                            </div>
                            <div class="text-right mt-1.5 pt-1.5 border-t border-slate-200/50">
                                <span class="text-[10px] text-slate-400">
                                    {{ \Carbon\Carbon::parse($usage->tanggal_pemakaian)->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            <i class="bi bi-inbox text-2xl d-block mb-1 text-slate-300"></i>
                            Belum ada riwayat pengeluaran material dari gudang.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
