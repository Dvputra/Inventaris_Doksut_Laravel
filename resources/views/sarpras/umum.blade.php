@extends('layouts.app')

@section('title', 'Inventaris Fasilitas Umum Sekolah')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1.5">
            <span class="inline-flex items-center gap-1 text-[11px] font-extrabold px-2.5 py-0.5 rounded-full bg-blue-600 text-white shadow-xs">
                <i class="bi bi-shield-check"></i> SARPRAS PUSAT
            </span>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Inventaris Fasilitas Umum Sekolah</h2>
        </div>
        <p class="text-xs sm:text-sm text-slate-500">
            Monitoring seluruh aset dan peralatan terpasang di ruang bersama sekolah (Lab CBT/Umum, Aula, Ruang Guru, Ruang TU, Genset, dan Ruang Kelas).
        </p>
        <div class="mt-2 inline-flex items-center gap-1.5 text-xs text-blue-700 bg-blue-50/80 border border-blue-200/80 px-3 py-1 rounded-xl">
            <i class="bi bi-info-circle"></i>
            <span>Halaman ini memuat fasilitas umum kelolaan Sarpras. Untuk melihat seluruh barang di semua unit & bengkel, buka <a href="{{ route('items.index') }}" class="font-bold underline hover:text-blue-900">Data Barang Inventaris</a>.</span>
        </div>
    </div>
    <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 w-full md:w-auto">
        <!-- Buka Stok di Gudang (Urgency: Amber Accent) -->
        <a href="{{ route('sarpras.gudang') }}" 
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3.5 py-2.5 text-xs font-bold text-amber-950 bg-amber-400 hover:bg-amber-300 rounded-xl shadow-xs transition-all active:scale-95">
            <i class="bi bi-archive-fill"></i>
            <span>Buka Stok di Gudang</span>
        </a>
        <!-- Tambah Aset Fasilitas Umum (Urgency: Primary Action / Blue) -->
        <a href="{{ route('items.create', ['jurusan_id' => $sarJurusan->id ?? '', 'penempatan_sarpras' => 'umum']) }}" 
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-200 rounded-xl transition-all active:scale-95">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Aset Fasilitas Umum</span>
        </a>
    </div>
</div>

<!-- Stat Cards Fasilitas Umum -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
    <!-- Stat 1: Total Item -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Item Fasilitas Umum</span>
            <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalItems }} <span class="text-sm font-normal text-slate-500">Item</span></h3>
            <p class="text-xs text-slate-500 mt-1">Aset terpasang aktif di sekolah</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100 text-xl">
            <i class="bi bi-building-check"></i>
        </div>
    </div>

    <!-- Stat 2: Komputer -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Komputer</span>
            <h3 class="text-2xl font-extrabold text-blue-700 mt-1">@formatJumlah($totalComputers) Unit</h3>
            <p class="text-xs mt-1">
                <a href="{{ route('sarpras.umum', ['is_computer' => 1]) }}" class="text-blue-600 font-semibold hover:underline">
                    Filter komputer umum &rarr;
                </a>
            </p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 border border-sky-100 text-xl">
            <i class="bi bi-display"></i>
        </div>
    </div>

    <!-- Stat 3: Kondisi Siap Pakai -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kondisi Siap Pakai (Baik)</span>
            <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">@formatJumlah($totalBaik) Unit</h3>
            <p class="text-xs text-slate-500 mt-1">Fasilitas dalam kondisi prima</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100 text-xl">
            <i class="bi bi-check-circle"></i>
        </div>
    </div>
</div>

<!-- Filter Box -->
<div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-4 mb-6">
    <form action="{{ route('sarpras.umum') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3 items-end">
        <div class="md:col-span-4">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Aset / Ruang / Spesifikasi</label>
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="q" 
                       class="w-full pl-8 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50" 
                       placeholder="Contoh: CBT, Aula, Core i5, SAR-KOM..." 
                       value="{{ request('q') }}">
            </div>
        </div>

        <div class="md:col-span-3">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Kategori Barang</label>
            <select name="category_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        [{{ $cat->kode }}] {{ $cat->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Kondisi</label>
            <select name="kondisi" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                <option value="">Semua Kondisi</option>
                <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                <option value="rusak_ringan" {{ request('kondisi') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                <option value="rusak_berat" {{ request('kondisi') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Tipe Fasilitas</label>
            <select name="is_computer" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                <option value="">Semua Fasilitas</option>
                <option value="1" {{ request('is_computer') == '1' ? 'selected' : '' }}>PC</option>
                <option value="0" {{ request('is_computer') == '0' ? 'selected' : '' }}>Bukan PC</option>
            </select>
        </div>

        <div class="md:col-span-1 flex items-center gap-1.5">
            <button type="submit" class="flex-1 py-2 px-3 text-xs font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 rounded-xl shadow-xs transition-all active:scale-95 flex items-center justify-center min-h-[38px]" title="Terapkan Filter">
                <i class="bi bi-filter text-sm"></i>
            </button>
            <a href="{{ route('sarpras.umum') }}" class="py-2 px-3 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-xl transition-all active:scale-95 flex items-center justify-center min-h-[38px]" title="Reset Filter">
                <i class="bi bi-arrow-counterclockwise text-sm"></i>
            </a>
        </div>
    </form>
</div>

<!-- Table Data Inventaris Fasilitas Umum -->
<div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
    <!-- Desktop Table (md: and up) -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px]">
                <tr>
                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                    <th class="py-3.5 px-4">Kode & Nama Fasilitas</th>
                    <th class="py-3.5 px-4">Lokasi Ruang / Fasilitas</th>
                    <th class="py-3.5 px-4">Kategori</th>
                    <th class="py-3.5 px-4">Jumlah Fisik</th>
                    <th class="py-3.5 px-4">Rincian Unit / Meja</th>
                    <th class="py-3.5 px-4">Kondisi</th>
                    <th class="py-3.5 px-4 text-right w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($items as $index => $item)
                    <tr class="hover:bg-slate-50/75 transition-colors">
                        <td class="py-3 px-4 text-center text-slate-400 font-medium">
                            {{ $items->firstItem() + $index }}
                        </td>
                        <td class="py-3 px-4">
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
                            <span class="inline-block mt-1 font-mono text-[11px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                {{ $item->kode_barang }}
                            </span>
                            @if($item->is_computer && $item->processor)
                                <p class="text-[11px] text-slate-400 mt-1 truncate max-w-xs">
                                    {{ $item->processor }} • {{ $item->ram }} • {{ $item->storage }}
                                </p>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <p class="font-semibold text-slate-800">{{ $item->lokasi ?? 'Umum Sekolah' }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1">
                                <i class="bi bi-geo-alt text-amber-500"></i>
                                <span>Aset Terpasang</span>
                            </p>
                        </td>
                        <td class="py-3 px-4">
                            @if($item->category)
                                <span class="font-mono text-[10px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">
                                    {{ $item->category->kode }}
                                </span>
                                <p class="text-slate-700 font-medium mt-0.5">{{ $item->category->nama }}</p>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-sm font-extrabold text-slate-900">@formatJumlah($item->jumlah)</span>
                            <span class="text-slate-500 text-xs">{{ $item->satuan }}</span>
                        </td>
                        <td class="py-3 px-4">
                            @if($item->units->count() > 0)
                                <div class="flex flex-wrap gap-1 max-w-xs">
                                    @foreach($item->units->take(4) as $unit)
                                        <span class="font-mono text-[10px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $unit->unit_code }}
                                            @if($unit->nomor_meja)
                                                ({{ $unit->nomor_meja }})
                                            @endif
                                        </span>
                                    @endforeach
                                    @if($item->units->count() > 4)
                                        <span class="text-[10px] text-slate-400 block w-full">+{{ $item->units->count() - 4 }} unit lainnya</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-slate-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($item->kondisi == 'baik')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="bi bi-check-circle mr-1"></i> Baik
                                </span>
                            @elseif($item->kondisi == 'rusak_ringan')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <i class="bi bi-exclamation-circle mr-1"></i> R. Ringan
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <i class="bi bi-x-circle mr-1"></i> R. Berat
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right">
                            <div class="inline-flex items-center gap-1">
                                <a href="{{ route('items.show', $item) }}" 
                                   class="p-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-transparent hover:border-slate-200 transition-all" 
                                   title="Detail Unit">
                                    <i class="bi bi-eye text-xs"></i>
                                </a>
                                @if(Auth::user()->isSarpras())
                                    <a href="{{ route('items.edit', $item) }}" 
                                       class="p-1.5 rounded-lg text-blue-600 hover:text-blue-700 hover:bg-blue-50 border border-transparent hover:border-blue-200 transition-all" 
                                       title="Edit Aset">
                                        <i class="bi bi-pencil text-xs"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            <i class="bi bi-building-slash text-3xl d-block mb-2 text-slate-300"></i>
                            <p class="font-semibold text-slate-700 text-sm">Belum ada data fasilitas umum yang tercatat</p>
                            <p class="text-xs text-slate-400 mt-1 mb-4">Klik tombol di bawah untuk menambahkan fasilitas Lab CBT, Aula, atau Ruang Guru.</p>
                            <a href="{{ route('items.create', ['jurusan_id' => $sarJurusan->id ?? '', 'penempatan_sarpras' => 'umum']) }}" 
                               class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs transition-all">
                                <i class="bi bi-plus-lg"></i>
                                <span>Tambah Aset Fasilitas Umum</span>
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
            <div class="p-4 hover:bg-slate-50/75 transition-colors">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <a href="{{ route('items.show', $item) }}" class="font-bold text-slate-900 hover:text-blue-600 text-sm block">
                                {{ $item->nama_barang }}
                            </a>
                            @if($item->is_computer)
                                <span class="inline-flex items-center gap-0.5 text-[9px] font-bold px-1.5 py-0.2 rounded bg-blue-100 text-blue-800 border border-blue-200">
                                    <i class="bi bi-display"></i> PC
                                </span>
                            @endif
                        </div>
                        <span class="inline-block mt-0.5 font-mono text-[10px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                            {{ $item->kode_barang }}
                        </span>
                    </div>
                    <div class="shrink-0">
                        @if($item->kondisi == 'baik')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Baik
                            </span>
                        @elseif($item->kondisi == 'rusak_ringan')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                R. Ringan
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                R. Berat
                            </span>
                        @endif
                    </div>
                </div>

                @if($item->is_computer && $item->processor)
                    <p class="text-[11px] text-slate-500 bg-slate-50 p-1.5 rounded-lg border border-slate-100 mb-2 truncate">
                        <i class="bi bi-cpu mr-1 text-slate-400"></i>{{ $item->processor }} • {{ $item->ram }} • {{ $item->storage }}
                    </p>
                @endif

                <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <div>
                        <span class="text-[10px] text-slate-400 block font-medium">Ruang / Lokasi</span>
                        <span class="font-semibold text-slate-700 truncate block">{{ $item->lokasi ?? 'Umum Sekolah' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block font-medium">Jumlah Fisik</span>
                        <span class="font-extrabold text-slate-900">@formatJumlah($item->jumlah) {{ $item->satuan }}</span>
                    </div>
                    @if($item->category)
                        <div class="col-span-2 pt-1 border-t border-slate-200/50 flex items-center justify-between text-[11px]">
                            <span class="text-slate-400">Kategori:</span>
                            <span class="font-medium text-slate-700">{{ $item->category->nama }}</span>
                        </div>
                    @endif
                </div>

                <!-- Action Footer -->
                <div class="flex items-center justify-end gap-2 mt-3 pt-2.5 border-t border-slate-100">
                    <a href="{{ route('items.show', $item) }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-semibold transition-colors shadow-xs active:scale-95" 
                       title="Detail Unit">
                        <i class="bi bi-eye"></i>
                        <span>Detail</span>
                    </a>
                    @if(Auth::user()->isSarpras())
                        <a href="{{ route('items.edit', $item) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-semibold transition-colors shadow-xs active:scale-95" 
                           title="Edit Aset">
                            <i class="bi bi-pencil"></i>
                            <span>Edit</span>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-12 px-4 text-center text-slate-400">
                <i class="bi bi-building-slash text-3xl block mb-2 text-slate-300"></i>
                <p class="text-sm font-semibold text-slate-600">Belum ada data fasilitas umum.</p>
            </div>
        @endforelse
    </div>

    @if($items->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection
