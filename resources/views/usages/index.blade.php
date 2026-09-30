@extends('layouts.app')

@section('title', 'Pemakaian Bahan')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Log Pemakaian Bahan</h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Pencatatan konsumsi bahan habis pakai, ATK, dan material operasional unit kerja.</p>
    </div>
    <div>
        <!-- Catat Pemakaian Bahan (Urgency: Primary Action / Blue) -->
        <a href="{{ route('usages.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-200 rounded-xl transition-all active:scale-95">
            <i class="bi bi-plus-lg"></i>
            <span>Catat Pemakaian Bahan</span>
        </a>
    </div>
</div>

@if($lowStockItems->isNotEmpty())
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200/80 mb-6 flex items-start gap-3 shadow-xs">
        <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 text-base mt-0.5">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div class="flex-1">
            <h4 class="text-xs font-bold text-amber-900 mb-1">Peringatan: Stok Bahan Habis Pakai Menipis!</h4>
            <p class="text-[11px] text-amber-800/80 mb-2">Bahan-bahan berikut berada pada atau di bawah batas minimum stok. Segera ajukan usulan pengadaan:</p>
            <div class="flex flex-wrap gap-2">
                @foreach($lowStockItems as $lsi)
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold bg-white text-rose-700 border border-rose-200 px-2.5 py-1 rounded-lg shadow-xs">
                        <strong class="font-mono text-slate-900">{{ $lsi->kode_barang }}</strong>
                        <span>{{ $lsi->nama_barang }}</span>
                        <span class="text-rose-600 font-bold">(Sisa: {{ $lsi->jumlah }} {{ $lsi->satuan }}, Min: {{ $lsi->min_stok }})</span>
                    </span>
                @endforeach
            </div>
        </div>
    </div>
@endif

<!-- Filter & Pencarian -->
<div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-4 mb-6">
    <form action="{{ route('usages.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3 items-end">
        <div class="md:col-span-5">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Guru / Kelas / Nama / Jobsheet / Unit Kerja</label>
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="q" 
                       class="w-full pl-8 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50" 
                       placeholder="Contoh: Bpk. Eko, XII TKR 1, Budi, Jobsheet, Tata Usaha..." 
                       value="{{ request('q') }}">
            </div>
        </div>

        @if(Auth::user()->isSarpras())
            <div class="md:col-span-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Jurusan</label>
                <select name="jurusan_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusans as $j)
                        <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->kode }} - {{ $j->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="{{ Auth::user()->isSarpras() ? 'md:col-span-3' : 'md:col-span-7' }} flex items-center gap-1.5">
            <button type="submit" class="flex-1 py-2 px-3 text-xs font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 rounded-xl shadow-xs transition-all active:scale-95 flex items-center justify-center gap-1">
                <i class="bi bi-filter"></i>
                <span>Filter</span>
            </button>
            <a href="{{ route('usages.index') }}" class="py-2 px-3 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-xl transition-all active:scale-95 flex items-center justify-center" title="Reset">
                <i class="bi bi-arrow-counterclockwise text-sm"></i>
            </a>
        </div>
    </form>
</div>

<!-- Tabel Log Pemakaian -->
<div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
    <div class="overflow-x-auto hidden md:block">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px]">
                <tr>
                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                    <th class="py-3.5 px-4">Tgl Pakai</th>
                    <th class="py-3.5 px-4">Nama Bahan</th>
                    <th class="py-3.5 px-4">Guru / Penanggung Jawab</th>
                    <th class="py-3.5 px-4">Keperluan / Jobsheet / Unit Kerja</th>
                    <th class="py-3.5 px-4 text-center">Jumlah Dipakai</th>
                    <th class="py-3.5 px-4 text-center">Perubahan Stok</th>
                    <th class="py-3.5 px-4 text-center w-20">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($usages as $index => $u)
                    <tr class="hover:bg-slate-50/75 transition-colors">
                        <td class="py-3 px-4 text-center text-slate-400 font-medium">
                            {{ $usages->firstItem() + $index }}
                        </td>
                        <td class="py-3 px-4">
                            <p class="font-bold text-slate-900">{{ $u->tanggal_pemakaian->format('d M Y') }}</p>
                            <p class="text-[11px] text-slate-400">{{ $u->tanggal_pemakaian->diffForHumans() }}</p>
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('items.show', $u->item_id) }}" class="font-bold text-blue-700 hover:underline">
                                {{ $u->item->nama_barang ?? 'Barang Terhapus' }}
                            </a>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="font-mono text-[10px] text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">
                                    {{ $u->item->kode_barang ?? '-' }}
                                </span>
                                @if(Auth::user()->isSarpras() && $u->jurusan)
                                    <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">
                                        {{ $u->jurusan->kode }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <p class="font-semibold text-slate-800 flex items-center gap-1">
                                <i class="bi bi-person text-slate-400"></i>
                                <span>{{ $u->nama_guru }}</span>
                            </p>
                            @if($u->kelas)
                                <span class="inline-block mt-0.5 text-[10px] font-semibold text-sky-800 bg-sky-50 px-2 py-0.5 rounded border border-sky-200">
                                    {{ $u->kelas }}
                                </span>
                            @else
                                <span class="text-[11px] text-slate-400 italic">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <p class="font-medium text-slate-800">{{ $u->keperluan_jobsheet ?: '-' }}</p>
                            @if($u->catatan)
                                <p class="text-[11px] text-slate-400 truncate max-w-xs mt-0.5">
                                    <i class="bi bi-chat-left-dots mr-1"></i>{{ $u->catatan }}
                                </p>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                -{{ number_format($u->jumlah) }} {{ $u->satuan }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center font-mono">
                            <div class="text-xs">
                                <span class="text-slate-400">{{ $u->stok_sebelum }}</span>
                                <i class="bi bi-arrow-right mx-1 text-slate-300"></i>
                                <strong class="text-blue-700">{{ $u->stok_sesudah }}</strong>
                            </div>
                            <span class="text-[10px] text-slate-400">sisa stok</span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="inline-flex items-center justify-center gap-1">
                                <!-- Edit Pemakaian (Urgency: Neutral) -->
                                <a href="{{ route('usages.edit', $u) }}" 
                                   class="p-1.5 rounded-lg text-slate-600 hover:text-blue-600 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 transition-all shadow-xs" 
                                   title="Edit Data Pemakaian">
                                    <i class="bi bi-pencil text-xs"></i>
                                </a>
                                <!-- Hapus Pemakaian (Urgency: Danger / Rose) -->
                                <form action="{{ route('usages.destroy', $u) }}" method="POST"
                                      data-confirm="Batalkan catatan pemakaian bahan ini? Stok barang akan otomatis dikembalikan sebanyak {{ $u->jumlah }} {{ $u->satuan }}."
                                      data-confirm-title="Batalkan Pemakaian Bahan"
                                      data-confirm-type="warning"
                                      data-confirm-btn="Ya, Batalkan & Kembalikan Stok"
                                      data-confirm-icon="bi bi-arrow-counterclockwise text-2xl">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-1.5 rounded-lg text-rose-600 hover:text-rose-700 hover:bg-rose-50 border border-slate-200 hover:border-rose-200 transition-all shadow-xs" 
                                            title="Batalkan Pemakaian & Kembalikan Stok">
                                        <i class="bi bi-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            <i class="bi bi-inbox text-3xl d-block mb-2 text-slate-300"></i>
                            <p class="text-sm">Belum ada catatan pemakaian bahan praktik.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View (Reflow on small screens < 768px) -->
    <div class="block md:hidden divide-y divide-slate-100">
        @forelse($usages as $index => $u)
            <div class="p-4 hover:bg-slate-50/75 transition-colors">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('items.show', $u->item_id) }}" class="font-bold text-slate-900 hover:text-blue-600 text-sm leading-tight block truncate">
                            {{ $u->item->nama_barang ?? 'Barang Terhapus' }}
                        </a>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="font-mono text-[10px] text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">
                                {{ $u->item->kode_barang ?? '-' }}
                            </span>
                            @if(Auth::user()->isSarpras() && $u->jurusan)
                                <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">
                                    {{ $u->jurusan->kode }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            -{{ number_format($u->jumlah) }} {{ $u->satuan }}
                        </span>
                        <div class="text-[10px] text-slate-400 mt-1">
                            Sisa: <strong class="text-blue-700">{{ $u->stok_sesudah }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Info Pemakai & Keperluan -->
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs mt-2 space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Guru / Penanggung Jawab:</span>
                        <span class="font-semibold text-slate-800">{{ $u->nama_guru }} {{ $u->kelas ? '('.$u->kelas.')' : '' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Keperluan / Unit Kerja:</span>
                        <span class="font-medium text-slate-700">{{ $u->keperluan_jobsheet ?: '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] pt-1 border-t border-slate-200/60">
                        <span class="text-slate-400">{{ $u->tanggal_pemakaian->format('d/m/Y') }}</span>
                        <span class="text-slate-400">{{ $u->tanggal_pemakaian->diffForHumans() }}</span>
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="flex items-center justify-end gap-2 mt-3 pt-2.5 border-t border-slate-100">
                    <a href="{{ route('usages.edit', $u) }}" 
                       class="p-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 transition-colors shadow-xs" 
                       title="Edit Data Pemakaian">
                        <i class="bi bi-pencil text-xs"></i>
                    </a>

                    <form action="{{ route('usages.destroy', $u) }}" method="POST" class="inline"
                          data-confirm="Batalkan catatan pemakaian bahan ini? Stok barang akan otomatis dikembalikan sebanyak {{ $u->jumlah }} {{ $u->satuan }}."
                          data-confirm-title="Batalkan Pemakaian Bahan"
                          data-confirm-type="warning"
                          data-confirm-btn="Ya, Batalkan & Kembalikan Stok"
                          data-confirm-icon="bi bi-arrow-counterclockwise text-2xl">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 transition-colors shadow-xs" 
                                title="Batalkan Pemakaian & Kembalikan Stok">
                            <i class="bi bi-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="py-12 px-4 text-center text-slate-400">
                <i class="bi bi-inbox text-3xl block mb-2 text-slate-300"></i>
                <p class="text-sm font-semibold text-slate-600">Belum ada catatan pemakaian bahan praktik.</p>
            </div>
        @endforelse
    </div>

    @if($usages->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $usages->links() }}
        </div>
    @endif
</div>
@endsection
