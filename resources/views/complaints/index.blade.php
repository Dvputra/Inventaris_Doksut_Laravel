@extends('layouts.app')

@section('title', 'Daftar Pengaduan Kendala Fasilitas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 ring-1 ring-blue-500/20">
                    <i class="bi bi-chat-left-dots text-base"></i>
                </span>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Pengaduan Kendala Fasilitas Guru</h1>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                @if(Auth::user()->isSarprasOrKepalaSekolah() || !Auth::user()->jurusan_id)
                    Daftar laporan kendala sarana, komputer, dan fasilitas sekolah yang diajukan oleh guru &amp; staf.
                @else
                    Daftar laporan kendala fasilitas dan bengkel pada lingkup <strong>{{ Auth::user()->jurusan ? Auth::user()->jurusan->nama : 'Unit Kerja' }}</strong>.
                @endif
            </p>
        </div>
        <div class="w-full sm:w-auto">
            <a href="{{ route('welcome') }}" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-blue-200 bg-white hover:bg-blue-50 text-blue-700 font-semibold text-sm shadow-xs transition-colors">
                <i class="bi bi-box-arrow-up-right text-sm"></i>
                <span>Buka Portal Pengaduan Publik</span>
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('complaints.index', ['status' => 'menunggu']) }}" class="group block bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:border-amber-300 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Menunggu Verifikasi</span>
                    <h3 class="text-3xl font-extrabold text-amber-600 mt-1 tracking-tight">{{ $menungguCount }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Butuh alokasi teknisi</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <i class="bi bi-clock-history text-2xl"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('complaints.index', ['status' => 'diproses']) }}" class="group block bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:border-blue-300 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sedang Ditangani</span>
                    <h3 class="text-3xl font-extrabold text-blue-600 mt-1 tracking-tight">{{ $diprosesCount }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Dalam proses perbaikan</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <i class="bi bi-tools text-2xl"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('complaints.index', ['status' => 'selesai']) }}" class="group block bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:border-emerald-300 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tuntas Selesai</span>
                    <h3 class="text-3xl font-extrabold text-emerald-600 mt-1 tracking-tight">{{ $selesaiCount }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Kendala telah diselesaikan</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <i class="bi bi-check2-circle text-2xl"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5">
        <form action="{{ route('complaints.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Cari Tiket / Pelapor / Judul / Ruang</label>
                <div class="relative rounded-xl shadow-xs">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-search text-slate-400 text-xs"></i>
                    </div>
                    <input type="text" name="q" class="w-full pl-8 pr-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="ADU-2609..., Bpk. Budi, PC Lab 1..." value="{{ request('q') }}">
                </div>
            </div>

            @if(Auth::user()->isSarpras())
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jurusan</label>
                    <select name="jurusan_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusans as $j)
                            <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                                {{ $j->kode }} - {{ $j->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="{{ Auth::user()->isSarpras() ? 'sm:col-span-2' : 'sm:col-span-3' }}">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Status</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="{{ Auth::user()->isSarpras() ? 'sm:col-span-2' : 'sm:col-span-3' }}">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kategori</label>
                <select name="kategori" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">Semua Kategori</option>
                    <option value="komputer_it" {{ request('kategori') === 'komputer_it' ? 'selected' : '' }}>Komputer &amp; IT</option>
                    <option value="kelistrikan" {{ request('kategori') === 'kelistrikan' ? 'selected' : '' }}>Kelistrikan</option>
                    <option value="mesin_peralatan" {{ request('kategori') === 'mesin_peralatan' ? 'selected' : '' }}>Mesin Bengkel</option>
                    <option value="sarana_gedung" {{ request('kategori') === 'sarana_gedung' ? 'selected' : '' }}>Sarana Gedung</option>
                    <option value="lainnya" {{ request('kategori') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs sm:text-sm shadow-xs transition-colors">
                    <i class="bi bi-filter"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('complaints.index') }}" class="inline-flex items-center justify-center w-10 h-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Pengaduan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 text-xs font-semibold uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Tiket &amp; Tanggal</th>
                        <th class="py-3 px-4">Pelapor (Guru/Tendik)</th>
                        <th class="py-3 px-4">Lokasi &amp; Kategori</th>
                        <th class="py-3 px-4">Kendala / Kerusakan</th>
                        <th class="py-3 px-4">Urgensi</th>
                        <th class="py-3 px-4">Status Tindak Lanjut</th>
                        <th class="py-3 px-4 text-right w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($complaints as $index => $c)
                        <tr class="hover:bg-slate-50/70 transition-colors {{ $c->tingkat_urgensi === 'tinggi_darurat' && $c->status === 'menunggu' ? 'bg-rose-50/30' : '' }}">
                            <td class="py-3.5 px-4 text-center text-slate-400 font-mono">
                                {{ $complaints->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <a href="{{ route('complaints.show', $c) }}" class="font-mono font-bold text-blue-600 hover:text-blue-700 hover:underline block">
                                    {{ $c->ticket_code }}
                                </a>
                                <span class="block text-[11px] text-slate-400 mt-0.5">
                                    {{ $c->created_at->format('d/m/Y H:i') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $c->nama_pelapor }}</div>
                                <span class="inline-flex items-center gap-1 text-[11px] text-slate-500 mt-0.5">
                                    <i class="bi bi-whatsapp text-emerald-500"></i>
                                    <span>{{ $c->kontak }}</span>
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-slate-800">{{ $c->lokasi_ruang }}</div>
                                @if($c->jurusan)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 mt-0.5">
                                        {{ $c->jurusan->kode }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-600 mt-0.5">
                                        Umum
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-900">{{ $c->judul_kendala }}</div>
                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-1 max-w-xs">
                                    {{ $c->deskripsi }}
                                </p>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($c->tingkat_urgensi === 'tinggi_darurat')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="bi bi-exclamation-triangle-fill text-rose-500"></i>
                                        <span>Darurat KBM</span>
                                    </span>
                                @elseif($c->tingkat_urgensi === 'sedang')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span>Sedang</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                        <span>Rendah</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($c->status === 'menunggu')
                                    @if($c->tingkat_urgensi === 'tinggi_darurat')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-300 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                                            <i class="bi bi-exclamation-octagon-fill text-rose-600"></i>
                                            <span>Menunggu (Darurat)</span>
                                        </span>
                                    @elseif($c->tingkat_urgensi === 'sedang')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="bi bi-hourglass-split text-amber-500"></i>
                                            <span>Menunggu (Sedang)</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            <i class="bi bi-clock text-slate-400"></i>
                                            <span>Menunggu (Rendah)</span>
                                        </span>
                                    @endif
                                @elseif($c->status === 'diproses')
                                    @if($c->tingkat_urgensi === 'tinggi_darurat')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                            <i class="bi bi-tools text-rose-600"></i>
                                            <span>Diproses (Darurat)</span>
                                        </span>
                                    @elseif($c->tingkat_urgensi === 'sedang')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                                            <i class="bi bi-tools text-amber-600"></i>
                                            <span>Diproses (Sedang)</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                            <i class="bi bi-tools text-blue-600"></i>
                                            <span>Diproses (Normal)</span>
                                        </span>
                                    @endif
                                @elseif($c->status === 'selesai')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="bi bi-check-circle-fill text-emerald-600"></i>
                                        <span>Selesai</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        <i class="bi bi-x-circle text-slate-400"></i>
                                        <span>Ditolak</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                @if($c->tingkat_urgensi === 'tinggi_darurat' && $c->status === 'menunggu')
                                    <a href="{{ route('complaints.show', $c) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition-all active:scale-95" title="Tindak Lanjut Segera">
                                        <i class="bi bi-lightning-fill"></i>
                                        <span>Tindak Lanjut</span>
                                    </a>
                                @else
                                    <a href="{{ route('complaints.show', $c) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold text-xs border border-blue-200/60 transition-colors" title="Tindak Lanjut & Detail">
                                        <i class="bi bi-eye"></i>
                                        <span>Tindak Lanjut</span>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <i class="bi bi-inbox text-2xl"></i>
                                </div>
                                <p class="text-slate-500 text-sm font-medium">Tidak ada pengaduan kendala yang sesuai filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (Reflow on small screens < 768px) -->
        <div class="block md:hidden divide-y divide-slate-100">
            @forelse($complaints as $index => $c)
                <div class="p-4 hover:bg-slate-50/75 transition-colors">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <a href="{{ route('complaints.show', $c) }}" class="font-mono font-bold text-blue-600 hover:text-blue-700 hover:underline text-xs">
                                {{ $c->ticket_code }}
                            </a>
                            <span class="block text-[11px] text-slate-400 mt-0.5">
                                {{ $c->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            @if($c->tingkat_urgensi === 'tinggi_darurat')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    Darurat
                                </span>
                            @endif

                            @if($c->status === 'menunggu')
                                @if($c->tingkat_urgensi === 'tinggi_darurat')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-300 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                                        <i class="bi bi-exclamation-octagon-fill text-rose-500 text-[10px]"></i>
                                        Menunggu (Darurat)
                                    </span>
                                @elseif($c->tingkat_urgensi === 'sedang')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="bi bi-hourglass-split text-[10px]"></i>
                                        Menunggu (Sedang)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
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
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                                        <i class="bi bi-tools text-[10px] text-amber-600"></i>
                                        Diproses (Sedang)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="bi bi-tools text-[10px]"></i>
                                        Diproses
                                    </span>
                                @endif
                            @elseif($c->status === 'selesai')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="bi bi-check-circle-fill text-[10px]"></i>
                                    Selesai
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                    Ditolak
                                </span>
                            @endif
                        </div>
                    </div>

                    <h4 class="font-bold text-slate-900 text-sm leading-snug">
                        {{ $c->judul_kendala }}
                    </h4>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                        {{ $c->deskripsi }}
                    </p>

                    <!-- Info Ruang & Pelapor -->
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs mt-3 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Lokasi / Ruang:</span>
                            <span class="font-medium text-slate-800">{{ $c->lokasi_ruang }} {{ $c->jurusan ? '('.$c->jurusan->kode.')' : '' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Pelapor:</span>
                            <span class="font-medium text-slate-800">{{ $c->nama_pelapor }} ({{ $c->kontak }})</span>
                        </div>
                    </div>

                    <!-- Action Footer -->
                    <div class="flex items-center justify-end mt-3 pt-2.5 border-t border-slate-100">
                        @if($c->tingkat_urgensi === 'tinggi_darurat' && $c->status === 'menunggu')
                            <a href="{{ route('complaints.show', $c) }}" 
                               class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition-all active:scale-95 w-full sm:w-auto">
                                <i class="bi bi-lightning-fill"></i>
                                <span>Tindak Lanjut Segera</span>
                            </a>
                        @else
                            <a href="{{ route('complaints.show', $c) }}" 
                               class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs border border-blue-200 transition-colors shadow-xs w-full sm:w-auto">
                                <i class="bi bi-eye"></i>
                                <span>Tindak Lanjut &amp; Detail</span>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-12 px-4 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <i class="bi bi-inbox text-2xl"></i>
                    </div>
                    <p class="text-slate-500 text-sm font-medium">Tidak ada pengaduan kendala yang sesuai filter.</p>
                </div>
            @endforelse
        </div>

        @if($complaints->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $complaints->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
