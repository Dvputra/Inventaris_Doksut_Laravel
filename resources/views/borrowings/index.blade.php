@extends('layouts.app')

@section('title', 'Peminjaman Alat Bengkel')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Peminjaman Alat Praktikum</h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Catatan sirkulasi peminjaman alat dan perkakas bengkel oleh siswa atau guru.</p>
    </div>
    <div>
        <!-- Catat Peminjaman Baru (Urgency: Primary Action / Blue) -->
        <a href="{{ route('borrowings.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-200 rounded-xl transition-all active:scale-95">
            <i class="bi bi-plus-lg"></i>
            <span>Catat Peminjaman Baru</span>
        </a>
    </div>
</div>

<!-- Filter Box -->
<div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-4 mb-6">
    <form action="{{ route('borrowings.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3 items-end">
        <div class="md:col-span-4">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Peminjam / Alat</label>
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="q" 
                       class="w-full pl-8 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50" 
                       placeholder="Nama peminjam, kelas, nama alat..." 
                       value="{{ request('q') }}">
            </div>
        </div>

        @if(Auth::user()->isSarpras())
            <div class="md:col-span-3">
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

        <div class="{{ Auth::user()->isSarpras() ? 'md:col-span-3' : 'md:col-span-6' }}">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                <option value="">Semua Status</option>
                <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                <option value="kembali" {{ request('status') == 'kembali' ? 'selected' : '' }}>Sudah Kembali</option>
            </select>
        </div>

        <div class="md:col-span-2 flex items-center gap-1.5">
            <button type="submit" class="flex-1 py-2 px-3 text-xs font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 rounded-xl shadow-xs transition-all active:scale-95 flex items-center justify-center gap-1">
                <i class="bi bi-filter"></i>
                <span>Filter</span>
            </button>
            <a href="{{ route('borrowings.index') }}" class="py-2 px-3 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-xl transition-all active:scale-95 flex items-center justify-center" title="Reset">
                <i class="bi bi-arrow-counterclockwise text-sm"></i>
            </a>
        </div>
    </form>
</div>

<!-- Table Peminjaman -->
<div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
    <div class="overflow-x-auto hidden md:block">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px]">
                <tr>
                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                    <th class="py-3.5 px-4">Nama Peminjam</th>
                    <th class="py-3.5 px-4">Kelas / Jabatan</th>
                    <th class="py-3.5 px-4">Alat Dipinjam</th>
                    <th class="py-3.5 px-4">Jurusan</th>
                    <th class="py-3.5 px-4">Tgl Pinjam</th>
                    <th class="py-3.5 px-4">Tgl Kembali</th>
                    <th class="py-3.5 px-4">Status</th>
                    <th class="py-3.5 px-4 text-right w-36">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($borrowings as $index => $b)
                    <tr class="hover:bg-slate-50/75 transition-colors">
                        <td class="py-3 px-4 text-center text-slate-400 font-medium">
                            {{ $borrowings->firstItem() + $index }}
                        </td>
                        <td class="py-3 px-4">
                            <p class="font-bold text-slate-900 text-xs">{{ $b->nama_peminjam }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $b->kontak ?? '-' }}</p>
                        </td>
                        <td class="py-3 px-4 text-slate-600 font-medium">{{ $b->kelas_atau_jabatan ?? '-' }}</td>
                        <td class="py-3 px-4">
                            <p class="font-semibold text-slate-800">{{ $b->item->nama_barang ?? '-' }}</p>
                            @if($b->itemUnit)
                                <span class="inline-flex items-center gap-1 font-mono text-[10px] font-bold px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 mt-0.5">
                                    {{ $b->itemUnit->unit_code }}
                                </span>
                                @if($b->itemUnit->nomor_meja)
                                    <span class="text-[10px] text-slate-400 ml-1">({{ $b->itemUnit->nomor_meja }})</span>
                                @endif
                            @else
                                <span class="text-[11px] text-slate-400 block mt-0.5">{{ $b->jumlah }} {{ $b->item->satuan ?? 'unit' }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-bold text-[10px] px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                                {{ $b->jurusan->kode }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-600 font-medium">{{ $b->tanggal_pinjam->format('d/m/Y') }}</td>
                        <td class="py-3 px-4">
                            @if($b->tanggal_kembali)
                                <span class="text-emerald-700 font-semibold">{{ $b->tanggal_kembali->format('d/m/Y') }}</span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($b->status === 'dipinjam')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <i class="bi bi-clock mr-1"></i> Dipinjam
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="bi bi-check2-circle mr-1"></i> Kembali
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5 justify-end">
                                @if($b->status === 'dipinjam')
                                    <form action="{{ route('borrowings.return', $b) }}" method="POST" class="inline"
                                          data-confirm="Konfirmasi pengembalian alat {{ $b->item->nama_barang }} yang dipinjam oleh {{ $b->nama_peminjam }}?"
                                          data-confirm-title="Pengembalian Alat"
                                          data-confirm-type="success"
                                          data-confirm-btn="Ya, Kembalikan"
                                          data-confirm-icon="bi bi-arrow-return-left text-2xl">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg shadow-xs transition-colors active:scale-95" title="Tandai Sudah Kembali">
                                            <i class="bi bi-arrow-return-left"></i>
                                            <span>Kembalikan</span>
                                        </button>
                                    </form>
                                @endif

                                @if(Auth::user()->isSarpras() || $b->jurusan_id === Auth::user()->jurusan_id)
                                    <!-- Edit -->
                                    <a href="{{ route('borrowings.edit', $b) }}" 
                                       class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 transition-colors" 
                                       title="Edit Peminjaman">
                                        <i class="bi bi-pencil text-xs"></i>
                                    </a>

                                    <!-- Hapus -->
                                    <form action="{{ route('borrowings.destroy', $b) }}" method="POST" class="inline"
                                          data-confirm="Apakah Anda yakin ingin menghapus data peminjaman {{ addslashes($b->item->nama_barang ?? 'alat') }} oleh {{ addslashes($b->nama_peminjam) }}?"
                                          data-confirm-title="Hapus Data Peminjaman"
                                          data-confirm-type="danger"
                                          data-confirm-btn="Ya, Hapus">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 transition-colors" 
                                                title="Hapus Peminjaman">
                                            <i class="bi bi-trash text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-400">
                            <i class="bi bi-inbox text-3xl d-block mb-2 text-slate-300"></i>
                            <p class="text-sm">Tidak ada riwayat peminjaman alat yang ditemukan.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View (Reflow on small screens < 768px) -->
    <div class="block md:hidden divide-y divide-slate-100">
        @forelse($borrowings as $index => $b)
            <div class="p-4 hover:bg-slate-50/75 transition-colors">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm leading-tight">{{ $b->nama_peminjam }}</h4>
                        <div class="flex items-center gap-1.5 mt-0.5 text-xs text-slate-500">
                            <span>{{ $b->kelas_atau_jabatan ?? 'Umum' }}</span>
                            @if($b->kontak)
                                <span>•</span>
                                <span class="font-mono text-[11px]">{{ $b->kontak }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        @if($b->status === 'dipinjam')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                <i class="bi bi-clock mr-1"></i> Dipinjam
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="bi bi-check2-circle mr-1"></i> Kembali
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Detail Alat & Unit -->
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs mt-2 space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Alat:</span>
                        <span class="font-bold text-slate-800 text-right">{{ $b->item->nama_barang ?? '-' }}</span>
                    </div>
                    @if($b->itemUnit)
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Unit Fisik:</span>
                            <span class="font-mono font-bold text-blue-700">{{ $b->itemUnit->unit_code }} {{ $b->itemUnit->nomor_meja ? '('.$b->itemUnit->nomor_meja.')' : '' }}</span>
                        </div>
                    @else
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Jumlah:</span>
                            <span class="font-bold text-slate-800">{{ $b->jumlah }} {{ $b->item->satuan ?? 'unit' }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between text-[11px] pt-1 border-t border-slate-200/60">
                        <span class="text-slate-400">Pinjam: {{ $b->tanggal_pinjam->format('d/m/Y') }}</span>
                        <span class="text-slate-400">
                            Kembali: {{ $b->tanggal_kembali ? $b->tanggal_kembali->format('d/m/Y') : '-' }}
                        </span>
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="flex items-center justify-end gap-2 mt-3 pt-2.5 border-t border-slate-100">
                    @if($b->status === 'dipinjam')
                        <form action="{{ route('borrowings.return', $b) }}" method="POST" class="inline"
                              data-confirm="Konfirmasi pengembalian alat {{ $b->item->nama_barang }} yang dipinjam oleh {{ $b->nama_peminjam }}?"
                              data-confirm-title="Pengembalian Alat"
                              data-confirm-type="success"
                              data-confirm-btn="Ya, Kembalikan"
                              data-confirm-icon="bi bi-arrow-return-left text-2xl">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl shadow-xs transition-colors active:scale-95" title="Tandai Sudah Kembali">
                                <i class="bi bi-arrow-return-left"></i>
                                <span>Kembalikan</span>
                            </button>
                        </form>
                    @endif

                    @if(Auth::user()->isSarpras() || $b->jurusan_id === Auth::user()->jurusan_id)
                        <a href="{{ route('borrowings.edit', $b) }}" 
                           class="p-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 transition-colors shadow-xs" 
                           title="Edit Peminjaman">
                            <i class="bi bi-pencil text-xs"></i>
                        </a>

                        <form action="{{ route('borrowings.destroy', $b) }}" method="POST" class="inline"
                              data-confirm="Apakah Anda yakin ingin menghapus data peminjaman {{ addslashes($b->item->nama_barang ?? 'alat') }} oleh {{ addslashes($b->nama_peminjam) }}?"
                              data-confirm-title="Hapus Data Peminjaman"
                              data-confirm-type="danger"
                              data-confirm-btn="Ya, Hapus">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 transition-colors shadow-xs" 
                                    title="Hapus Peminjaman">
                                <i class="bi bi-trash text-xs"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-12 px-4 text-center text-slate-400">
                <i class="bi bi-inbox text-3xl block mb-2 text-slate-300"></i>
                <p class="text-sm font-semibold text-slate-600">Tidak ada riwayat peminjaman alat yang ditemukan.</p>
            </div>
        @endforelse
    </div>

    @if($borrowings->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $borrowings->links() }}
        </div>
    @endif
</div>
@endsection
