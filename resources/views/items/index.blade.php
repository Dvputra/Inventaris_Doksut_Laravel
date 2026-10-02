@extends('layouts.app')

@section('title', 'Data Barang Inventaris')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
            <span>Data Barang & Aset Inventaris</span>
            <span class="inline-block text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800 px-2.5 py-0.5 rounded-full">
                {{ $items->total() }} Item
            </span>
        </h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            @if(Auth::user()->isSarprasOrKepalaSekolah() || !Auth::user()->jurusan_id)
                Menampilkan daftar seluruh aset, mesin bengkel, lab komputer, dan bahan praktik di lingkungan sekolah.
            @else
                Menampilkan daftar inventaris bengkel / laboratorium <strong class="text-slate-700">{{ Auth::user()->jurusan ? Auth::user()->jurusan->nama : 'Unit' }}</strong>.
            @endif
        </p>
    </div>
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
        <!-- Log Pemakaian Bahan (Urgency: Neutral Secondary) -->
        <a href="{{ route('usages.index') }}" 
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3.5 py-2.5 sm:py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-xs transition-all active:scale-95">
            <i class="bi bi-droplet-half text-sky-600"></i>
            <span>Log Pemakaian Bahan</span>
        </a>
        <!-- Tambah Barang Baru (Urgency: Primary Action / Blue) -->
        <a href="{{ route('items.create') }}" 
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 sm:py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-200 rounded-xl transition-all active:scale-95">
            <i class="bi bi-plus-lg text-sm"></i>
            <span>Tambah Barang Baru</span>
        </a>
    </div>
</div>

<!-- Filter & Pencarian Bar -->
<div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-4 mb-6">
    <form action="{{ route('items.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-12 gap-3 items-end">
        <!-- Keyword Search -->
        <div class="lg:col-span-3">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Nama / Kode / Spesifikasi</label>
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="q" 
                       class="w-full pl-8 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50" 
                       placeholder="Contoh: Bubut, Core i5, TP-MSN..." 
                       value="{{ request('q') }}">
            </div>
        </div>

        @if(Auth::user()->isSarpras())
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Jurusan / Unit</label>
                <select name="jurusan_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                    <option value="">Semua Jurusan & Unit</option>
                    @foreach($jurusans as $j)
                        <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                            [{{ $j->kode }}] {{ $j->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="{{ Auth::user()->isSarpras() ? 'lg:col-span-2' : 'lg:col-span-3' }}">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Kategori</label>
            <select name="category_id" id="categoryFilterSelect" onchange="handleCategoryFilter(this)" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50 font-medium">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ (request('category_id') == $cat->id || request('category') == $cat->kode || request('category_id') == $cat->kode) ? 'selected' : '' }}>
                        [{{ $cat->kode }}] {{ $cat->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="lg:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Kondisi</label>
            <select name="kondisi" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                <option value="">Semua Kondisi</option>
                <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                <option value="rusak_ringan" {{ request('kondisi') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                <option value="rusak_berat" {{ request('kondisi') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
            </select>
        </div>

        <div class="lg:col-span-1">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Jenis</label>
            <select name="jenis" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                <option value="">Semua</option>
                <option value="alat" {{ request('jenis') == 'alat' ? 'selected' : '' }}>Alat</option>
                <option value="bahan" {{ request('jenis') == 'bahan' ? 'selected' : '' }}>Bahan</option>
            </select>
        </div>

        <div class="lg:col-span-1">
            <label class="block text-xs font-semibold text-slate-600 mb-1">PC</label>
            <select name="is_computer" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                <option value="">Semua</option>
                <option value="1" {{ request('is_computer') === '1' ? 'selected' : '' }}>PC</option>
                <option value="0" {{ request('is_computer') === '0' ? 'selected' : '' }}>Bukan PC</option>
            </select>
        </div>

        <!-- Filter Action Buttons: Apply (Urgency: Amber Accent) & Reset (Neutral) -->
        <div class="lg:col-span-1 flex items-center gap-1.5 w-full">
            <button type="submit" 
                    class="flex-1 py-2 px-3 text-xs font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 rounded-xl shadow-xs transition-all active:scale-95 flex items-center justify-center gap-1.5 min-h-[38px]" 
                    title="Terapkan Filter">
                <i class="bi bi-filter text-sm"></i>
                <span class="inline lg:hidden text-xs">Terapkan</span>
            </button>
            <a href="{{ route('items.index') }}" 
               class="py-2 px-3 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-xl transition-all active:scale-95 flex items-center justify-center min-h-[38px]" 
               title="Reset Semua Filter">
                <i class="bi bi-arrow-counterclockwise text-sm"></i>
            </a>
        </div>
    </form>

    @php
        $activeFilters = [];
        if (request('q')) $activeFilters[] = ['label' => 'Kata Kunci: "' . request('q') . '"', 'param' => 'q'];
        if (request('category_id') || request('category')) {
            $catIdVal = request('category_id') ?? request('category');
            $activeCat = is_numeric($catIdVal) ? $categories->firstWhere('id', (int)$catIdVal) : $categories->firstWhere('kode', strtoupper($catIdVal));
            $activeFilters[] = ['label' => 'Kategori: ' . ($activeCat ? '['.$activeCat->kode.'] '.$activeCat->nama : $catIdVal), 'param' => 'category_id'];
        }
        if (request('jurusan_id') && Auth::user()->isSarpras()) {
            $activeJur = $jurusans->firstWhere('id', request('jurusan_id'));
            $activeFilters[] = ['label' => 'Unit: ' . ($activeJur ? $activeJur->kode : request('jurusan_id')), 'param' => 'jurusan_id'];
        }
        if (request('kondisi')) $activeFilters[] = ['label' => 'Kondisi: ' . ucfirst(str_replace('_', ' ', request('kondisi'))), 'param' => 'kondisi'];
        if (request('jenis')) $activeFilters[] = ['label' => 'Jenis: ' . ucfirst(request('jenis')), 'param' => 'jenis'];
        if (request('is_computer') !== null && request('is_computer') !== '') {
            $activeFilters[] = ['label' => request('is_computer') == '1' ? 'PC: Ya' : 'PC: Bukan', 'param' => 'is_computer'];
        }

        if (request('sort')) {
            $sortLabels = [
                'nama_barang' => 'Nama Barang',
                'kode_barang' => 'Kode Barang',
                'jurusan' => 'Jurusan',
                'lokasi' => 'Lokasi',
                'kategori' => 'Kategori',
                'jumlah' => 'Jumlah Stok',
                'units_count' => 'Unit Fisik',
                'kondisi' => 'Kondisi',
            ];
            $dirLabel = request('direction') === 'desc' ? 'Z-A / Terbesar' : 'A-Z / Terkecil';
            $activeFilters[] = [
                'label' => 'Urut: ' . ($sortLabels[request('sort')] ?? request('sort')) . ' (' . $dirLabel . ')',
                'param' => 'sort',
            ];
        }
    @endphp

    @if(count($activeFilters) > 0)
        <div class="mt-3 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2">
            <span class="text-[11px] font-semibold text-slate-500">Filter Aktif:</span>
            @foreach($activeFilters as $af)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    <span>{{ $af['label'] }}</span>
                    @php
                        $queryWithoutParam = request()->except([$af['param'], $af['param'] === 'category_id' ? 'category' : '']);
                    @endphp
                    <a href="{{ route('items.index', $queryWithoutParam) }}" class="text-blue-500 hover:text-blue-800 font-bold ml-0.5" title="Hapus filter ini">&times;</a>
                </span>
            @endforeach
            <a href="{{ route('items.index') }}" class="text-[11px] font-bold text-rose-600 hover:text-rose-700 ml-1">
                Reset Semua
            </a>
        </div>
    @endif
</div>

<!-- Table Data Barang Container -->
<div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
    <div class="overflow-x-auto hidden md:block">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                <tr>
                    <th class="py-3.5 px-4 w-12 text-center text-slate-400">No</th>
                    
                    {{-- Kode & Nama Barang --}}
                    @php
                        $isNameActive = request('sort') === 'nama_barang';
                        $nextNameDir = ($isNameActive && request('direction') === 'asc') ? 'desc' : 'asc';
                    @endphp
                    <th class="py-3.5 px-4">
                        <a href="{{ route('items.index', array_merge(request()->query(), ['sort' => 'nama_barang', 'direction' => $nextNameDir])) }}"
                           class="inline-flex items-center gap-1.5 hover:text-blue-600 transition-colors group {{ $isNameActive ? 'text-blue-700 font-bold' : '' }}">
                            <span>Kode & Nama Barang</span>
                            <span class="inline-flex flex-col text-[10px] leading-none text-slate-400 group-hover:text-blue-600">
                                @if($isNameActive)
                                    <i class="bi {{ request('direction') === 'desc' ? 'bi-sort-alpha-down-alt text-blue-600 font-bold' : 'bi-sort-alpha-down text-blue-600 font-bold' }}"></i>
                                @else
                                    <i class="bi bi-arrow-down-up text-[10px] opacity-40 group-hover:opacity-100"></i>
                                @endif
                            </span>
                        </a>
                    </th>

                    {{-- Jurusan & Lokasi --}}
                    @php
                        $isJurActive = request('sort') === 'jurusan';
                        $nextJurDir = ($isJurActive && request('direction') === 'asc') ? 'desc' : 'asc';
                    @endphp
                    <th class="py-3.5 px-4">
                        <a href="{{ route('items.index', array_merge(request()->query(), ['sort' => 'jurusan', 'direction' => $nextJurDir])) }}"
                           class="inline-flex items-center gap-1.5 hover:text-blue-600 transition-colors group {{ $isJurActive ? 'text-blue-700 font-bold' : '' }}">
                            <span>Jurusan & Lokasi</span>
                            <span class="inline-flex text-[10px] leading-none text-slate-400 group-hover:text-blue-600">
                                @if($isJurActive)
                                    <i class="bi {{ request('direction') === 'desc' ? 'bi-sort-alpha-down-alt text-blue-600 font-bold' : 'bi-sort-alpha-down text-blue-600 font-bold' }}"></i>
                                @else
                                    <i class="bi bi-arrow-down-up text-[10px] opacity-40 group-hover:opacity-100"></i>
                                @endif
                            </span>
                        </a>
                    </th>

                    {{-- Kategori --}}
                    @php
                        $isCatActive = request('sort') === 'kategori';
                        $nextCatDir = ($isCatActive && request('direction') === 'asc') ? 'desc' : 'asc';
                    @endphp
                    <th class="py-3.5 px-4">
                        <a href="{{ route('items.index', array_merge(request()->query(), ['sort' => 'kategori', 'direction' => $nextCatDir])) }}"
                           class="inline-flex items-center gap-1.5 hover:text-blue-600 transition-colors group {{ $isCatActive ? 'text-blue-700 font-bold' : '' }}">
                            <span>Kategori</span>
                            <span class="inline-flex text-[10px] leading-none text-slate-400 group-hover:text-blue-600">
                                @if($isCatActive)
                                    <i class="bi {{ request('direction') === 'desc' ? 'bi-sort-alpha-down-alt text-blue-600 font-bold' : 'bi-sort-alpha-down text-blue-600 font-bold' }}"></i>
                                @else
                                    <i class="bi bi-arrow-down-up text-[10px] opacity-40 group-hover:opacity-100"></i>
                                @endif
                            </span>
                        </a>
                    </th>

                    {{-- Jumlah Stok --}}
                    @php
                        $isStockActive = request('sort') === 'jumlah';
                        $nextStockDir = ($isStockActive && request('direction') === 'desc') ? 'asc' : 'desc';
                    @endphp
                    <th class="py-3.5 px-4">
                        <a href="{{ route('items.index', array_merge(request()->query(), ['sort' => 'jumlah', 'direction' => $nextStockDir])) }}"
                           class="inline-flex items-center gap-1.5 hover:text-blue-600 transition-colors group {{ $isStockActive ? 'text-blue-700 font-bold' : '' }}">
                            <span>Jumlah Stok</span>
                            <span class="inline-flex text-[10px] leading-none text-slate-400 group-hover:text-blue-600">
                                @if($isStockActive)
                                    <i class="bi {{ request('direction') === 'asc' ? 'bi-sort-numeric-down text-blue-600 font-bold' : 'bi-sort-numeric-down-alt text-blue-600 font-bold' }}"></i>
                                @else
                                    <i class="bi bi-arrow-down-up text-[10px] opacity-40 group-hover:opacity-100"></i>
                                @endif
                            </span>
                        </a>
                    </th>

                    {{-- Unit Fisik --}}
                    @php
                        $isUnitActive = request('sort') === 'units_count';
                        $nextUnitDir = ($isUnitActive && request('direction') === 'desc') ? 'asc' : 'desc';
                    @endphp
                    <th class="py-3.5 px-4">
                        <a href="{{ route('items.index', array_merge(request()->query(), ['sort' => 'units_count', 'direction' => $nextUnitDir])) }}"
                           class="inline-flex items-center gap-1.5 hover:text-blue-600 transition-colors group {{ $isUnitActive ? 'text-blue-700 font-bold' : '' }}">
                            <span>Unit Fisik</span>
                            <span class="inline-flex text-[10px] leading-none text-slate-400 group-hover:text-blue-600">
                                @if($isUnitActive)
                                    <i class="bi {{ request('direction') === 'asc' ? 'bi-sort-numeric-down text-blue-600 font-bold' : 'bi-sort-numeric-down-alt text-blue-600 font-bold' }}"></i>
                                @else
                                    <i class="bi bi-arrow-down-up text-[10px] opacity-40 group-hover:opacity-100"></i>
                                @endif
                            </span>
                        </a>
                    </th>

                    {{-- Kondisi --}}
                    @php
                        $isKondisiActive = request('sort') === 'kondisi';
                        $nextKondisiDir = ($isKondisiActive && request('direction') === 'asc') ? 'desc' : 'asc';
                    @endphp
                    <th class="py-3.5 px-4">
                        <a href="{{ route('items.index', array_merge(request()->query(), ['sort' => 'kondisi', 'direction' => $nextKondisiDir])) }}"
                           class="inline-flex items-center gap-1.5 hover:text-blue-600 transition-colors group {{ $isKondisiActive ? 'text-blue-700 font-bold' : '' }}">
                            <span>Kondisi</span>
                            <span class="inline-flex text-[10px] leading-none text-slate-400 group-hover:text-blue-600">
                                @if($isKondisiActive)
                                    <i class="bi {{ request('direction') === 'desc' ? 'bi-sort-down-alt text-blue-600 font-bold' : 'bi-sort-down text-blue-600 font-bold' }}"></i>
                                @else
                                    <i class="bi bi-arrow-down-up text-[10px] opacity-40 group-hover:opacity-100"></i>
                                @endif
                            </span>
                        </a>
                    </th>

                    <th class="py-3.5 px-4 text-right w-36">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($items as $index => $item)
                    <tr class="hover:bg-slate-50/75 transition-colors">
                        <td class="py-3 px-4 text-center text-slate-400 font-medium">
                            {{ $items->firstItem() + $index }}
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                @if($item->foto)
                                    <a href="{{ route('items.show', $item) }}" class="shrink-0">
                                        <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama_barang }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 shadow-2xs hover:scale-105 transition-transform">
                                    </a>
                                @else
                                    <div class="w-10 h-10 rounded-xl {{ $item->is_computer ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-slate-100 text-slate-400 border border-slate-200/80' }} flex items-center justify-center shrink-0">
                                        <i class="bi {{ $item->is_computer ? 'bi-display text-base' : 'bi-box-seam text-base' }}"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('items.show', $item) }}" class="font-bold text-slate-900 hover:text-blue-600 text-sm">
                                            {{ $item->nama_barang }}
                                        </a>
                                        @if($item->is_computer)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 border border-blue-200">
                                                <i class="bi bi-display text-xs"></i> PC
                                            </span>
                                        @endif
                                    </div>
                                    <span class="inline-block mt-0.5 font-mono text-[11px] font-semibold px-2 py-0.2 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ $item->kode_barang }}
                                    </span>
                                    @if($item->is_computer && $item->processor)
                                        <p class="text-[11px] text-slate-400 mt-0.5 truncate max-w-xs">
                                            {{ $item->processor }} • {{ $item->ram }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            @if($item->jurusan->kode === 'SAR')
                                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-300">
                                    <i class="bi bi-shield-check text-xs"></i> SARPRAS
                                </span>
                            @else
                                <span class="inline-block text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $item->jurusan->kode }}
                                </span>
                            @endif
                            <p class="text-[11px] text-slate-400 mt-1">{{ $item->lokasi ?? 'Lokasi belum diisi' }}</p>
                        </td>
                        <td class="py-3 px-4">
                            @if($item->category)
                                <a href="{{ route('items.index', ['category_id' => $item->category->id]) }}" 
                                   class="group block hover:opacity-80 transition-opacity" 
                                   title="Filter berdasarkan kategori {{ $item->category->nama }}">
                                    <span class="font-mono text-[10px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 group-hover:bg-blue-100 group-hover:text-blue-700 text-slate-600 transition-colors">
                                        {{ $item->category->kode }}
                                    </span>
                                    <p class="text-slate-700 group-hover:text-blue-600 font-medium mt-0.5 transition-colors">{{ $item->category->nama }}</p>
                                </a>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-sm font-extrabold text-slate-900">{{ number_format($item->jumlah) }}</span>
                            <span class="text-slate-500 text-xs">{{ $item->satuan }}</span>

                            @if($item->jenis === 'bahan' && $item->min_stok > 0 && $item->jumlah <= $item->min_stok)
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded bg-rose-50 text-rose-600 border border-rose-200 mt-1 block w-fit">
                                    <i class="bi bi-exclamation-circle"></i> Stok Kritis
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($item->jenis === 'alat')
                                <a href="{{ route('items.show', $item) }}" 
                                   class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-2.5 py-1 rounded-lg transition-colors">
                                    <i class="bi bi-upc-scan text-xs"></i>
                                    <span>{{ $item->units->count() }} Unit Fisik</span>
                                </a>
                            @else
                                <span class="text-[11px] text-slate-500 bg-slate-100 px-2 py-0.5 rounded">Bahan (Habis Pakai)</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($item->kondisi === 'baik')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Baik
                                </span>
                            @elseif($item->kondisi === 'rusak_ringan')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    Rusak Ringan
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    Rusak Berat
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right">
                            <div class="inline-flex items-center gap-1">
                                <!-- Detail (Urgency: Neutral Info) -->
                                <a href="{{ route('items.show', $item) }}" 
                                   class="p-1.5 rounded-lg text-sky-600 hover:text-sky-700 hover:bg-sky-50 border border-transparent hover:border-sky-200 transition-all" 
                                   title="Lihat Detail & Unit">
                                    <i class="bi bi-eye text-sm"></i>
                                </a>
                                @if($item->jenis === 'alat')
                                    <!-- Pinjamkan Alat (Urgency: Neutral Blue) -->
                                    <a href="{{ route('borrowings.create', ['item_id' => $item->id]) }}" 
                                       class="p-1.5 rounded-lg text-blue-600 hover:text-blue-700 hover:bg-blue-50 border border-transparent hover:border-blue-200 transition-all" 
                                       title="Pinjamkan Alat">
                                        <i class="bi bi-arrow-left-right text-sm"></i>
                                    </a>
                                @elseif($item->jenis === 'bahan')
                                    <!-- Catat Pemakaian (Urgency: Success / Emerald) -->
                                    <a href="{{ route('usages.create', ['item_id' => $item->id]) }}" 
                                       class="p-1.5 rounded-lg text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 border border-transparent hover:border-emerald-200 transition-all" 
                                       title="Catat Pemakaian Bahan">
                                        <i class="bi bi-droplet-half text-sm"></i>
                                    </a>
                                @endif
                                <!-- Edit (Urgency: Primary Blue) -->
                                <a href="{{ route('items.edit', $item) }}" 
                                   class="p-1.5 rounded-lg text-blue-600 hover:text-blue-700 hover:bg-blue-50 border border-transparent hover:border-blue-200 transition-all" 
                                   title="Ubah Data">
                                    <i class="bi bi-pencil text-sm"></i>
                                </a>
                                <!-- Hapus (Urgency: Danger / Rose) -->
                                <button type="button" 
                                        onclick="confirmDeleteItem('{{ route('items.destroy', $item) }}', '{{ addslashes($item->nama_barang) }}', '{{ $item->kode_barang }}', '{{ addslashes($item->category->nama ?? '-') }}', '{{ $item->jumlah }} {{ $item->satuan }}')"
                                        class="p-1.5 rounded-lg text-rose-600 hover:text-rose-700 hover:bg-rose-50 border border-transparent hover:border-rose-200 transition-all" 
                                        title="Hapus Barang">
                                    <i class="bi bi-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            <i class="bi bi-inbox text-3xl block mb-2 text-slate-300"></i>
                            <p class="text-sm font-semibold text-slate-600">Tidak ada data barang inventaris yang sesuai.</p>
                            <p class="text-xs text-slate-400 mt-0.5">Coba sesuaikan filter kategori, kondisi, atau kata kunci pencarian Anda.</p>
                            
                            <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                                @if(request('category_id') || request('category'))
                                    @php
                                        $catIdParam = request('category_id') ?? request('category');
                                        $targetCat = is_numeric($catIdParam) ? $categories->firstWhere('id', (int)$catIdParam) : $categories->firstWhere('kode', strtoupper($catIdParam));
                                    @endphp
                                    <a href="{{ route('items.index', ['category_id' => $targetCat?->id ?? 1]) }}" 
                                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition-colors">
                                        <i class="bi bi-collection"></i>
                                        <span>Tampilkan Semua Barang Kategori {{ $targetCat ? '['.$targetCat->kode.']' : 'KOM' }}</span>
                                    </a>
                                @endif

                                @if(request()->anyFilled(['q', 'category_id', 'category', 'jurusan_id', 'kondisi', 'jenis', 'is_computer']))
                                    <a href="{{ route('items.index') }}" 
                                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                        <span>Reset Semua Filter</span>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View (Reflow on small screens < 768px) -->
    <div class="block md:hidden divide-y divide-slate-100">
        @forelse($items as $index => $item)
            <div class="p-4 hover:bg-slate-50/75 transition-colors">
                <div class="flex items-start gap-3">
                    @if($item->foto)
                        <a href="{{ route('items.show', $item) }}" class="shrink-0 mt-0.5">
                            <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama_barang }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-2xs">
                        </a>
                    @else
                        <div class="w-12 h-12 rounded-xl {{ $item->is_computer ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-slate-100 text-slate-400 border border-slate-200/80' }} flex items-center justify-center shrink-0 mt-0.5">
                            <i class="bi {{ $item->is_computer ? 'bi-display text-lg' : 'bi-box-seam text-lg' }}"></i>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('items.show', $item) }}" class="font-bold text-slate-900 hover:text-blue-600 text-sm line-clamp-2">
                                    {{ $item->nama_barang }}
                                </a>
                                <span class="inline-block mt-1 font-mono text-[11px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $item->kode_barang }}
                                </span>
                            </div>
                            @if($item->jurusan->kode === 'SAR')
                                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-300 shrink-0">
                                    SARPRAS
                                </span>
                            @else
                                <span class="inline-block text-[11px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 shrink-0">
                                    {{ $item->jurusan->kode }}
                                </span>
                            @endif
                        </div>

                        <!-- Lokasi & Kategori -->
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2 text-[11px] text-slate-500">
                            <span class="inline-flex items-center gap-1">
                                <i class="bi bi-geo-alt text-slate-400"></i>
                                <span class="truncate">{{ $item->lokasi ?? 'Lokasi -' }}</span>
                            </span>
                            @if($item->category)
                                <span class="inline-flex items-center gap-1">
                                    <i class="bi bi-tag text-slate-400"></i>
                                    <span class="truncate">{{ $item->category->nama }}</span>
                                </span>
                            @endif
                        </div>

                        <!-- Stock, Unit, Condition Summary Badges -->
                        <div class="grid grid-cols-3 gap-2 mt-3 p-2 rounded-xl bg-slate-50 border border-slate-100 text-center">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-medium">Stok</span>
                                <span class="text-xs font-extrabold text-slate-900">{{ number_format($item->jumlah) }} {{ $item->satuan }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-medium">Unit Fisik</span>
                                @if($item->jenis === 'alat')
                                    <span class="text-xs font-bold text-blue-700">{{ $item->units_count }} Unit</span>
                                @else
                                    <span class="text-xs text-slate-400">Bahan</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-medium">Kondisi</span>
                                @if($item->kondisi === 'baik')
                                    <span class="text-[11px] font-bold text-emerald-700">Baik</span>
                                @elseif($item->kondisi === 'rusak_ringan')
                                    <span class="text-[11px] font-bold text-amber-700">R. Ringan</span>
                                @else
                                    <span class="text-[11px] font-bold text-rose-700">R. Berat</span>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons Bar -->
                        <div class="flex flex-wrap items-center justify-end gap-2 mt-3 pt-2.5 border-t border-slate-100">
                            <a href="{{ route('items.show', $item) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 rounded-xl transition-all shadow-xs" 
                               title="Lihat Detail & Unit">
                                <i class="bi bi-eye"></i>
                                <span>Detail</span>
                            </a>
                            @if($item->jenis === 'alat')
                                <a href="{{ route('borrowings.create', ['item_id' => $item->id]) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-xl transition-all shadow-xs" 
                                   title="Pinjamkan Alat">
                                    <i class="bi bi-arrow-left-right"></i>
                                    <span>Pinjam</span>
                                </a>
                            @elseif($item->jenis === 'bahan')
                                <a href="{{ route('usages.create', ['item_id' => $item->id]) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl transition-all shadow-xs" 
                                   title="Catat Pemakaian Bahan">
                                    <i class="bi bi-droplet-half"></i>
                                    <span>Pakai</span>
                                </a>
                            @endif
                            <a href="{{ route('items.edit', $item) }}" 
                               class="p-2 rounded-xl text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-colors shadow-xs" 
                               title="Ubah Data">
                                <i class="bi bi-pencil text-xs"></i>
                            </a>
                            <button type="button" 
                                    onclick="confirmDeleteItem('{{ route('items.destroy', $item) }}', '{{ addslashes($item->nama_barang) }}', '{{ $item->kode_barang }}', '{{ addslashes($item->category->nama ?? '-') }}', '{{ $item->jumlah }} {{ $item->satuan }}')"
                                    class="p-2 rounded-xl text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors shadow-xs" 
                                    title="Hapus Barang">
                                <i class="bi bi-trash text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-12 px-4 text-center text-slate-400">
                <i class="bi bi-inbox text-3xl block mb-2 text-slate-300"></i>
                <p class="text-sm font-semibold text-slate-600">Tidak ada data barang inventaris yang sesuai.</p>
                <p class="text-xs text-slate-400 mt-0.5">Coba sesuaikan kata kunci pencarian atau reset filter.</p>
            </div>
        @endforelse
    </div>

    @if($items->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $items->links() }}
        </div>
    @endif
</div>

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
@endsection

@push('scripts')
<script>
function handleCategoryFilter(select) {
    const form = select.form;
    const catVal = select.value;
    
    // Kategori non-bahan (KOM=1, MSN=2, TLS=3, UKR=4, ELK=5, APD=7): auto reset 'bahan' agar tidak menyebabkan 0 hasil
    if (catVal && catVal !== '6') {
        if (form.jenis && form.jenis.value === 'bahan') {
            form.jenis.value = '';
        }
    }
    // Jika mengganti kategori selain KOM, reset filter is_computer
    if (catVal !== '1') {
        if (form.is_computer && form.is_computer.value !== '') {
            form.is_computer.value = '';
        }
    }
    form.submit();
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

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteItemModal();
    }
});

document.addEventListener('click', function(e) {
    const modal = document.getElementById('deleteItemModal');
    if (modal && !modal.classList.contains('hidden') && e.target === modal) {
        closeDeleteItemModal();
    }
});
</script>
@endpush
