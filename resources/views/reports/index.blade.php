@extends('layouts.app')

@section('title', 'Pusat Laporan & Cetak Rekapitulasi')

@section('content')
<div class="space-y-6">
    <!-- Header & Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-sm">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-semibold ring-1 ring-blue-400/30 backdrop-blur-xs">
                    <i class="bi bi-printer"></i>
                    <span>Modul Cetak Dokumen & SPJ Resmi</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    Pusat Laporan &amp; Cetak Rekapitulasi
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                    Filter inventaris sarpras, tinjau tabel pratinjau data secara interaktif, lalu cetak dokumen formal berformat A4 lengkap dengan kop surat sekolah dan lembar tanda tangan pengesahan.
                </p>
            </div>

            <!-- Quick Stats & Status Hub -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <a href="{{ route('reports.index', ['tab' => 'items']) }}"
                   class="p-3.5 rounded-2xl transition-all border {{ $tab === 'items' ? 'bg-blue-600/30 border-blue-400/40 ring-1 ring-blue-400/30' : 'bg-white/5 border-white/10 hover:bg-white/10' }}">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="bi bi-boxes text-blue-400 text-base"></i>
                        <span class="text-[11px] font-semibold text-slate-300">Inventaris</span>
                    </div>
                    <div class="text-lg sm:text-xl font-extrabold text-white">{{ $previewItems->count() }}</div>
                    <div class="text-[10px] text-slate-400">Data terpilih</div>
                </a>

                <a href="{{ route('reports.index', ['tab' => 'usages']) }}"
                   class="p-3.5 rounded-2xl transition-all border {{ $tab === 'usages' ? 'bg-cyan-600/30 border-cyan-400/40 ring-1 ring-cyan-400/30' : 'bg-white/5 border-white/10 hover:bg-white/10' }}">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="bi bi-droplet-half text-cyan-400 text-base"></i>
                        <span class="text-[11px] font-semibold text-slate-300">Bahan Habis Pakai</span>
                    </div>
                    <div class="text-lg sm:text-xl font-extrabold text-white">{{ $previewUsages->count() }}</div>
                    <div class="text-[10px] text-slate-400">Transaksi log</div>
                </a>

                @if(Auth::user()->isSarpras())
                    <a href="{{ route('reports.index', ['tab' => 'complaints']) }}"
                       class="col-span-2 sm:col-span-1 p-3.5 rounded-2xl transition-all border {{ $tab === 'complaints' ? 'bg-rose-600/30 border-rose-400/40 ring-1 ring-rose-400/30' : 'bg-white/5 border-white/10 hover:bg-white/10' }}">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="bi bi-exclamation-octagon text-rose-400 text-base"></i>
                            <span class="text-[11px] font-semibold text-slate-300">Pengaduan</span>
                        </div>
                        <div class="text-lg sm:text-xl font-extrabold text-white">{{ $previewComplaints->count() }}</div>
                        <div class="text-[10px] text-slate-400">Tiket masuk</div>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Segmented Tab Navigation -->
    <div class="bg-slate-100/80 p-1.5 rounded-2xl border border-slate-200/80 inline-flex flex-wrap gap-1.5 w-full sm:w-auto shadow-2xs">
        <a href="{{ route('reports.index', ['tab' => 'items']) }}"
           class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all {{ $tab === 'items' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
            <i class="bi bi-boxes text-base"></i>
            <span>Laporan Inventaris Aset &amp; Barang</span>
            <span class="px-2 py-0.5 text-[11px] rounded-full {{ $tab === 'items' ? 'bg-blue-100 text-blue-700' : 'bg-slate-200 text-slate-600' }}">{{ $previewItems->count() }}</span>
        </a>

        <a href="{{ route('reports.index', ['tab' => 'usages']) }}"
           class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all {{ $tab === 'usages' ? 'bg-white text-cyan-700 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
            <i class="bi bi-droplet-half text-base"></i>
            <span>Laporan Pemakaian Bahan</span>
            <span class="px-2 py-0.5 text-[11px] rounded-full {{ $tab === 'usages' ? 'bg-cyan-100 text-cyan-700' : 'bg-slate-200 text-slate-600' }}">{{ $previewUsages->count() }}</span>
        </a>

        @if(Auth::user()->isSarpras())
            <a href="{{ route('reports.index', ['tab' => 'complaints']) }}"
               class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all {{ $tab === 'complaints' ? 'bg-white text-rose-700 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
                <i class="bi bi-exclamation-octagon text-base"></i>
                <span>Laporan Pengaduan &amp; Servis</span>
                <span class="px-2 py-0.5 text-[11px] rounded-full {{ $tab === 'complaints' ? 'bg-rose-100 text-rose-700' : 'bg-slate-200 text-slate-600' }}">{{ $previewComplaints->count() }}</span>
            </a>
        @endif
    </div>

    <!-- PANEL FILTER & KONTROL SESUAI TAB -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 bg-slate-50/70 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center shadow-2xs {{ $tab === 'items' ? 'bg-blue-600 text-white' : ($tab === 'usages' ? 'bg-cyan-600 text-white' : 'bg-rose-600 text-white') }}">
                    <i class="bi bi-sliders text-xl"></i>
                </div>
                <div>
                    <h2 class="text-sm sm:text-base font-extrabold text-slate-900">
                        Filter &amp; Parameter {{ $tab === 'items' ? 'Laporan Inventaris Barang' : ($tab === 'usages' ? 'Laporan Pemakaian Bahan' : 'Laporan Pengaduan') }}
                    </h2>
                    <p class="text-xs text-slate-500">Sesuaikan kriteria data sebelum diekspor atau dicetak</p>
                </div>
            </div>

            <!-- Tombol Aksi Cetak & Export Excel -->
            @php
                $isUnitMode = request('view_mode') === 'unit';
                $printRoute = route('reports.items.print', request()->except('tab'));
                $excelRoute = route('reports.items.export-excel', array_merge(request()->except('tab'), ['mode' => $isUnitMode ? 'detail' : 'summary']));
                if ($tab === 'usages') {
                    $printRoute = route('reports.usages.print', request()->except('tab'));
                    $excelRoute = route('reports.usages.export-excel', request()->except('tab'));
                } elseif ($tab === 'complaints') {
                    $printRoute = route('reports.complaints.print', request()->except('tab'));
                    $excelRoute = route('reports.complaints.export-excel', request()->except('tab'));
                }
                $canExportPrint = ($tab !== 'items') || ($tab === 'items' && $hasFiltered && $previewItems->count() > 0);
            @endphp
            <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 w-full md:w-auto">
                @if($canExportPrint)
                    <a href="{{ $excelRoute }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-xs sm:text-sm shadow-xs transition-all">
                        <i class="bi bi-file-earmark-spreadsheet text-base"></i>
                        <span>Export Excel ({{ $tab === 'items' && $isUnitMode ? 'Per Unit' : ($tab === 'items' ? 'Per Barang' : '.csv') }})</span>
                    </a>
                    <a href="{{ $printRoute }}" target="_blank" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 active:scale-95 text-white font-bold text-xs sm:text-sm shadow-sm shadow-blue-200 transition-all">
                        <i class="bi bi-printer text-base"></i>
                        <span>Cetak Dokumen Resmi (A4)</span>
                    </a>
                @else
                    <div class="flex items-center gap-2 text-xs text-amber-700 bg-amber-50 border border-amber-200/80 px-3 py-2 rounded-xl">
                        <i class="bi bi-info-circle text-amber-600"></i>
                        <span>Terapkan filter terlebih dahulu untuk mencetak</span>
                    </div>
                    <button type="button" disabled class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl bg-slate-100 text-slate-400 font-semibold text-xs sm:text-sm cursor-not-allowed">
                        <i class="bi bi-printer text-base"></i>
                        <span>Cetak Dokumen</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- FORM FILTER -->
        <div class="p-5 sm:p-6">
            <form action="{{ route('reports.index') }}" method="GET" class="space-y-4" id="reportFilterForm">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="hidden" name="filter_applied" value="1">

                @if($tab === 'items')
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5 flex items-center gap-1.5">
                                <i class="bi bi-layout-text-window-reverse text-blue-600"></i>
                                <span>Tampilan Data</span>
                            </label>
                            <div class="grid grid-cols-2 p-1 bg-slate-100/90 rounded-xl border border-slate-200">
                                <label class="cursor-pointer select-none">
                                    <input type="radio" name="view_mode" value="barang" class="peer sr-only" {{ request('view_mode', 'barang') === 'barang' ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-lg text-xs font-bold text-slate-600 transition-all peer-checked:bg-white peer-checked:text-blue-700 peer-checked:shadow-xs">
                                        <i class="bi bi-stack text-sm"></i>
                                        <span>Per Barang</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer select-none">
                                    <input type="radio" name="view_mode" value="unit" class="peer sr-only" {{ request('view_mode') === 'unit' ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-lg text-xs font-bold text-slate-600 transition-all peer-checked:bg-white peer-checked:text-blue-700 peer-checked:shadow-xs">
                                        <i class="bi bi-upc-scan text-sm"></i>
                                        <span>Per Unit</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        @if(Auth::user()->isSarpras())
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jurusan / Ruang</label>
                                <select name="jurusan_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                    <option value="">-- Semua Jurusan --</option>
                                    @foreach($jurusans as $j)
                                        <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->kode }} - {{ $j->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kategori Barang</label>
                            <select name="category_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                <option value="">-- Semua Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>[{{ $cat->kode }}] {{ $cat->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jenis</label>
                            <select name="jenis" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                <option value="">Semua Jenis</option>
                                <option value="alat" {{ request('jenis') == 'alat' ? 'selected' : '' }}>Alat/Mesin</option>
                                <option value="bahan" {{ request('jenis') == 'bahan' ? 'selected' : '' }}>Bahan (Habis Pakai)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kondisi</label>
                            <select name="kondisi" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                <option value="">Semua Kondisi</option>
                                <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                                <option value="rusak_ringan" {{ request('kondisi') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                <option value="rusak_berat" {{ request('kondisi') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Khusus Komputer</label>
                            <select name="is_computer" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                <option value="">Semua</option>
                                <option value="1" {{ request('is_computer') === '1' ? 'selected' : '' }}>Hanya PC / Lab</option>
                                <option value="0" {{ request('is_computer') === '0' ? 'selected' : '' }}>Bukan Komputer</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5 flex items-center gap-1.5">
                                <i class="bi bi-calendar3 text-blue-600"></i>
                                <span>Periode Waktu</span>
                            </label>
                            <select name="periode" id="selectPeriode" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                <option value="semua" {{ request('periode', 'semua') === 'semua' ? 'selected' : '' }}>Semua Waktu</option>
                                <option value="hari_ini" {{ request('periode') === 'hari_ini' ? 'selected' : '' }}>Harian (Hari Ini)</option>
                                <option value="minggu_ini" {{ request('periode') === 'minggu_ini' ? 'selected' : '' }}>Mingguan (Minggu Ini)</option>
                                <option value="bulan_ini" {{ request('periode') === 'bulan_ini' ? 'selected' : '' }}>Bulanan (Bulan Ini)</option>
                                <option value="kustom" {{ request('periode') === 'kustom' || (request()->filled('tgl_mulai') && !in_array(request('periode'), ['hari_ini', 'minggu_ini', 'bulan_ini', 'semua'])) ? 'selected' : '' }}>Rentang Tanggal Khusus</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Mulai Tanggal</label>
                                <input type="date" name="tgl_mulai" id="inputTglMulai" value="{{ request('tgl_mulai') }}" class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Sampai Tanggal</label>
                                <input type="date" name="tgl_selesai" id="inputTglSelesai" value="{{ request('tgl_selesai') }}" class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                            </div>
                        </div>
                    </div>
                @elseif($tab === 'usages')
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                        @if(Auth::user()->isSarpras())
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Jurusan</label>
                                <select name="jurusan_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                    <option value="">-- Semua Jurusan --</option>
                                    @foreach($jurusans as $j)
                                        <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->kode }} - {{ $j->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Bulan Pemakaian</label>
                            <select name="bulan" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                <option value="">-- Semua Bulan --</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ sprintf('%02d', $m) }}" {{ request('bulan') == sprintf('%02d', $m) ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                @endfor
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tahun</label>
                            <input type="number" name="tahun" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" value="{{ request('tahun', date('Y')) }}" min="2020" max="{{ date('Y') + 1 }}">
                        </div>
                    </div>
                @elseif($tab === 'complaints' && Auth::user()->isSarpras())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Jurusan / Sarpras Umum</label>
                            <select name="jurusan_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                <option value="">-- Semua Jurusan &amp; Umum --</option>
                                @foreach($jurusans as $j)
                                    <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->kode }} - {{ $j->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status Penanganan</label>
                            <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                <option value="">-- Semua Status --</option>
                                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Tuntas Selesai</option>
                                <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Sedang Ditangani</option>
                                <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                    </div>
                @endif

                <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100">
                    <a href="{{ route('reports.index', ['tab' => $tab]) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 text-center sm:text-left py-2 sm:py-0">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                    </a>
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm shadow-xs transition-colors">
                        <i class="bi bi-funnel"></i>
                        <span>Terapkan Filter &amp; Tampilkan Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL HASIL DATA LAPORAN -->
    <div id="data-table-container" class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    @if($canExportPrint)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                            Data Dimuat
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                            Menunggu Filter
                        </span>
                    @endif
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-900">
                        Hasil Data: {{ $tab === 'items' ? ($isUnitMode ? 'Inventaris Fisik Per Unit' : 'Inventaris Rekapitulasi Per Barang') : ($tab === 'usages' ? 'Riwayat Pemakaian Bahan' : 'Pengaduan Sarpras') }}
                    </h3>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    @if($tab === 'items')
                        @if(!$hasFiltered)
                            Pilih periode waktu atau kriteria filter di atas lalu klik tombol <strong>Terapkan Filter &amp; Tampilkan Data</strong>.
                        @else
                            @php
                                $totalPhysicalUnits = $previewItems->sum(function($item) {
                                    return $item->units->count() > 0 ? $item->units->count() : $item->jumlah;
                                });
                            @endphp
                            @if($isUnitMode)
                                Menampilkan <strong>{{ $totalPhysicalUnits }} baris unit fisik</strong> dari <strong>{{ $previewItems->count() }} item barang</strong> (Klik judul kolom untuk sortir urutan).
                            @else
                                Menampilkan <strong>{{ $previewItems->count() }} item barang</strong> (Total stok fisik: <strong>{{ $previewItems->sum('jumlah') }} unit</strong> • Klik judul kolom untuk sortir urutan).
                            @endif
                        @endif
                    @elseif($tab === 'usages')
                        Menampilkan <strong>{{ $previewUsages->count() }} transaksi pemakaian</strong> (Total bahan terpakai: <strong>{{ $previewUsages->sum('jumlah') }}</strong> • Klik judul kolom untuk sortir urutan).
                    @elseif($tab === 'complaints')
                        Menampilkan <strong>{{ $previewComplaints->count() }} tiket pengaduan</strong> (Klik judul kolom untuk sortir urutan).
                    @endif
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            @if($tab === 'items' && !$hasFiltered)
                <!-- BANNER PETUNJUK: FILTER PERIODE TERLEBIH DAHULU -->
                <div class="p-8 sm:p-12 text-center max-w-xl mx-auto">
                    <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-4 text-2xl shadow-inner">
                        <i class="bi bi-calendar-range"></i>
                    </div>
                    <h4 class="text-base font-bold text-slate-800 mb-1.5">Tentukan Periode Waktu &amp; Tanggal Laporan</h4>
                    <p class="text-xs text-slate-500 mb-6 leading-relaxed">
                        Data inventaris belum dimuat secara otomatis agar lebih cepat dan terarah. Silakan pilih opsi periode (Harian, Mingguan, Bulanan, atau Rentang Tanggal Khusus) pada formulir filter di atas, kemudian klik <strong>Terapkan Filter &amp; Tampilkan Data</strong>.
                    </p>
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">
                        <i class="bi bi-info-circle text-blue-500"></i>
                        <span>Pilih opsi "Semua Waktu" jika ingin merangkum seluruh aset tanpa batasan tanggal.</span>
                    </div>
                </div>
            @elseif($tab === 'items')
                @if($isUnitMode)
                    <!-- TABEL PREVIEW PER UNIT FISIK -->
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-100 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3">Kode Unit Fisik</th>
                                <th class="px-4 py-3">Nama Barang Induk</th>
                                <th class="px-4 py-3 text-center">No. Meja / Seri</th>
                                <th class="px-4 py-3">Jurusan / Lokasi</th>
                                <th class="px-4 py-3 text-center">Tgl Masuk</th>
                                <th class="px-4 py-3 text-center">Kondisi</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3">Spesifikasi &amp; Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php $unitCounter = 1; @endphp
                            @forelse($previewItems as $item)
                                @if($item->units->count() > 0)
                                    @foreach($item->units as $u)
                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                            <td class="px-4 py-3 text-center font-medium text-slate-400">{{ $unitCounter++ }}</td>
                                            <td class="px-4 py-3 font-mono font-bold text-blue-600">
                                                {{ $u->unit_code }}
                                                @if($item->is_computer)
                                                    <span class="inline-block px-1.5 py-0.5 rounded bg-sky-100 text-sky-700 font-semibold text-[10px] ml-1">PC</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="font-semibold text-slate-900">{{ $item->nama_barang }}</div>
                                                <span class="text-[11px] text-slate-400 font-mono">{{ $item->kode_barang }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($u->nomor_meja)<strong class="text-slate-800">{{ $u->nomor_meja }}</strong>@endif
                                                @if($u->nomor_seri)<div class="text-[10px] text-slate-400 font-mono">SN: {{ $u->nomor_seri }}</div>@endif
                                                @if(!$u->nomor_meja && !$u->nomor_seri)<span class="text-slate-400">-</span>@endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="font-semibold text-slate-800">{{ $item->jurusan->kode ?? 'Umum' }}</div>
                                                <span class="text-[11px] text-slate-400">{{ $u->lokasi_penempatan ?: ($item->lokasi ?? '-') }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-center text-slate-600 whitespace-nowrap">
                                                @if($u->tanggal_masuk)
                                                    <span class="font-medium">{{ $u->tanggal_masuk->format('d/m/Y') }}</span>
                                                @elseif($item->tahun_pengadaan)
                                                    <span class="text-slate-400">Thn {{ $item->tahun_pengadaan }}</span>
                                                @else
                                                    <span class="text-slate-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @php $uCond = $u->kondisi ?: $item->kondisi; @endphp
                                                @if($uCond === 'baik')
                                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">Baik</span>
                                                @elseif($uCond === 'rusak_ringan')
                                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700">R. Ringan</span>
                                                @else
                                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700">R. Berat</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($u->status === 'tersedia')
                                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700">Tersedia</span>
                                                @elseif($u->status === 'dipinjam')
                                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700">Dipinjam</span>
                                                @elseif($u->status === 'dalam_perbaikan')
                                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700">Perbaikan</span>
                                                @else
                                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">{{ $u->status }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($item->is_computer)
                                                    <div class="text-[11px] text-slate-500 space-x-1">
                                                        @if($u->processor || $item->processor)<span>Proc: {{ $u->processor ?: $item->processor }}</span> |@endif
                                                        @if($u->ram || $item->ram)<span>RAM: {{ $u->ram ?: $item->ram }}</span> |@endif
                                                        @if($u->storage || $item->storage)<span>Disk: {{ $u->storage ?: $item->storage }}</span>@endif
                                                    </div>
                                                @elseif($item->spesifikasi)
                                                    <div class="text-[11px] text-slate-500">{{ Str::limit($item->spesifikasi, 50) }}</div>
                                                @endif
                                                @if($u->catatan)
                                                    <div class="text-[11px] text-rose-600 italic">Ket: {{ $u->catatan }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="px-4 py-3 text-center font-medium text-slate-400">{{ $unitCounter++ }}</td>
                                        <td class="px-4 py-3 font-mono font-bold text-slate-700">
                                            {{ $item->kode_barang }} (Bulk)
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-slate-900">{{ $item->nama_barang }}</div>
                                            <span class="text-[11px] text-slate-400 font-mono">{{ $item->kode_barang }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-slate-400">-</td>
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-slate-800">{{ $item->jurusan->kode ?? 'Umum' }}</div>
                                            <span class="text-[11px] text-slate-400">{{ $item->lokasi ?? '-' }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-slate-600 whitespace-nowrap">
                                            @if($item->tahun_pengadaan)
                                                <span class="font-medium">Thn {{ $item->tahun_pengadaan }}</span>
                                            @else
                                                <span class="text-slate-400">{{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($item->kondisi === 'baik')
                                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">Baik</span>
                                            @elseif($item->kondisi === 'rusak_ringan')
                                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700">R. Ringan</span>
                                            @else
                                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700">R. Berat</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">Stok: {{ $item->jumlah }} {{ $item->satuan }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-[11px] text-slate-500">
                                            {{ $item->spesifikasi ?: 'Pencatatan bulk/akumulasi stok' }}
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-slate-400">
                                        <i class="bi bi-inbox text-3xl block mb-1"></i>
                                        Tidak ada data unit fisik yang sesuai dengan filter yang dipilih.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <!-- TABEL PREVIEW REKAP PER BARANG (DEFAULT) -->
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-100 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3">Kode Barang</th>
                                <th class="px-4 py-3">Nama Barang &amp; Spesifikasi</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3">Jurusan / Lokasi</th>
                                <th class="px-4 py-3 text-center">Thn / Tgl Masuk</th>
                                <th class="px-4 py-3 text-center">Jenis</th>
                                <th class="px-4 py-3 text-center">Stok</th>
                                <th class="px-4 py-3 text-center">Kondisi</th>
                                <th class="px-4 py-3">Unit Fisik / Meja Lab</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($previewItems as $index => $item)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-4 py-3 text-center font-medium text-slate-400">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <span class="font-mono font-bold text-blue-600">{{ $item->kode_barang }}</span>
                                        @if($item->is_computer)
                                            <span class="inline-block px-1.5 py-0.5 rounded bg-sky-100 text-sky-700 font-semibold text-[10px] ml-1">PC/Lab</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-slate-900">{{ $item->nama_barang }}</div>
                                        @if($item->is_computer)
                                            <div class="text-[11px] text-slate-500 mt-0.5 space-x-1">
                                                @if($item->processor)<span>Proc: {{ $item->processor }}</span> |@endif
                                                @if($item->ram)<span>RAM: {{ $item->ram }}</span> |@endif
                                                @if($item->storage)<span>Disk: {{ $item->storage }}</span> |@endif
                                                @if($item->gpu_vga)<span>GPU: {{ $item->gpu_vga }}</span>@endif
                                            </div>
                                        @elseif($item->spesifikasi)
                                            <div class="text-[11px] text-slate-500 mt-0.5">{{ Str::limit($item->spesifikasi, 60) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $item->category->nama ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-slate-800">{{ $item->jurusan->kode ?? 'Umum' }}</div>
                                        <span class="text-[11px] text-slate-400">{{ $item->lokasi ?? '-' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-slate-600 whitespace-nowrap">
                                        @if($item->tahun_pengadaan)
                                            <span class="font-medium text-slate-800">{{ $item->tahun_pengadaan }}</span>
                                        @elseif($item->created_at)
                                            <span class="text-slate-500">{{ $item->created_at->format('d/m/Y') }}</span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold {{ $item->jenis === 'alat' ? 'bg-indigo-50 text-indigo-700' : 'bg-amber-50 text-amber-700' }}">
                                            {{ ucfirst($item->jenis) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <strong class="text-slate-900">{{ $item->jumlah }}</strong>
                                        <span class="text-[11px] text-slate-400">{{ $item->satuan }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($item->kondisi === 'baik')
                                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">Baik</span>
                                        @elseif($item->kondisi === 'rusak_ringan')
                                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700">R. Ringan</span>
                                        @else
                                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700">R. Berat</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($item->units->count() > 0)
                                            <div class="flex flex-wrap gap-1 max-w-xs">
                                                @foreach($item->units->take(4) as $u)
                                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] font-mono border border-slate-200">
                                                        {{ $u->unit_code }}
                                                        @if($u->nomor_meja)<span class="text-slate-500">({{ $u->nomor_meja }})</span>@endif
                                                    </span>
                                                @endforeach
                                                @if($item->units->count() > 4)
                                                    <span class="text-[10px] text-slate-400">+{{ $item->units->count() - 4 }} unit</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">Pencatatan bulk/stok</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-slate-400">
                                        <i class="bi bi-inbox text-3xl block mb-1"></i>
                                        Tidak ada data barang yang sesuai dengan filter yang dipilih.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            @elseif($tab === 'usages')
                <!-- TABEL PREVIEW PEMAKAIAN BAHAN -->
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-4 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3 text-center">Tgl Pemakaian</th>
                            <th class="px-4 py-3">Nama Bahan</th>
                            <th class="px-4 py-3 text-center">Kode Barang</th>
                            <th class="px-4 py-3">Guru / Penanggung Jawab</th>
                            <th class="px-4 py-3 text-center">Kelas / Nama</th>
                            <th class="px-4 py-3">Jobsheet / Unit Kerja</th>
                            <th class="px-4 py-3 text-center">Dipakai</th>
                            <th class="px-4 py-3 text-center">Sisa Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($previewUsages as $index => $u)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-4 py-3 text-center font-medium text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-center font-mono text-slate-700">
                                    {{ \Carbon\Carbon::parse($u->tanggal_pemakaian)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900">{{ $u->item->nama_barang ?? '-' }}</div>
                                    <span class="text-[11px] text-slate-400">{{ $u->jurusan->kode ?? 'Umum' }}</span>
                                </td>
                                <td class="px-4 py-3 text-center font-mono font-semibold text-blue-600">
                                    {{ $u->item->kode_barang ?? '-' }}
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $u->nama_guru }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold text-[11px]">
                                        {{ $u->kelas ?: '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-slate-800">{{ $u->keperluan_jobsheet ?: '-' }}</div>
                                    @if($u->catatan)
                                        <div class="text-[11px] text-slate-400 italic">Ket: {{ $u->catatan }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-bold text-rose-600">-{{ $u->jumlah }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $u->item->satuan ?? 'unit' }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <strong class="text-slate-900">{{ $u->stok_sesudah }}</strong>
                                    <span class="text-[11px] text-slate-400">{{ $u->item->satuan ?? 'unit' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-slate-400">
                                    <i class="bi bi-inbox text-3xl block mb-1"></i>
                                    Tidak ada riwayat pemakaian bahan praktik pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @elseif($tab === 'complaints' && Auth::user()->isSarpras())
                <!-- TABEL PREVIEW PENGADUAN -->
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-4 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3 text-center">No. Tiket</th>
                            <th class="px-4 py-3 text-center">Tgl Lapor</th>
                            <th class="px-4 py-3">Pelapor</th>
                            <th class="px-4 py-3">Lokasi / Ruang</th>
                            <th class="px-4 py-3">Uraian Kendala</th>
                            <th class="px-4 py-3 text-center">Urgensi</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3">Tindak Lanjut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($previewComplaints as $index => $c)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-4 py-3 text-center font-medium text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-center font-mono font-bold text-blue-600">
                                    {{ $c->ticket_code }}
                                </td>
                                <td class="px-4 py-3 text-center font-mono text-slate-700">
                                    {{ $c->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900">{{ $c->nama_pelapor }}</div>
                                    @if($c->kontak)<div class="text-[11px] text-slate-400">{{ $c->kontak }}</div>@endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-slate-800">{{ $c->lokasi_ruang }}</div>
                                    <span class="text-[11px] text-slate-400">{{ $c->jurusan->kode ?? 'Umum' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900">{{ $c->judul_kendala }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ Str::limit($c->deskripsi, 60) }}</div>
                                </td>
                                <td class="px-4 py-3 text-center capitalize">
                                    @if($c->tingkat_urgensi === 'darurat')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">Darurat</span>
                                    @elseif($c->tingkat_urgensi === 'tinggi')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700">Tinggi</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600">{{ $c->tingkat_urgensi }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($c->status === 'selesai')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Selesai</span>
                                    @elseif($c->status === 'diproses')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-700">Diproses</span>
                                    @elseif($c->status === 'ditolak')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">Ditolak</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700">Menunggu</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-[11px]">
                                    @if($c->teknisi)
                                        <div class="font-semibold text-slate-800">Teknisi: {{ $c->teknisi }}</div>
                                    @endif
                                    @if($c->catatan_penanganan)
                                        <div class="text-slate-500">{{ Str::limit($c->catatan_penanganan, 40) }}</div>
                                    @endif
                                    @if(!$c->teknisi && !$c->catatan_penanganan)
                                        <span class="text-slate-400 italic">Belum ada catatan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-slate-400">
                                    <i class="bi bi-inbox text-3xl block mb-1"></i>
                                    Tidak ada laporan pengaduan yang sesuai dengan filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectPeriode = document.getElementById('selectPeriode');
    const inputTglMulai = document.getElementById('inputTglMulai');
    const inputTglSelesai = document.getElementById('inputTglSelesai');

    if (!selectPeriode || !inputTglMulai || !inputTglSelesai) {
        return;
    }

    function formatDate(d) {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    selectPeriode.addEventListener('change', function () {
        const val = this.value;
        const now = new Date();

        if (val === 'hari_ini') {
            const todayStr = formatDate(now);
            inputTglMulai.value = todayStr;
            inputTglSelesai.value = todayStr;
        } else if (val === 'minggu_ini') {
            // Hitung awal minggu (Senin) dan akhir minggu (Minggu)
            const currentDay = now.getDay(); // 0 is Sunday
            const distanceToMonday = currentDay === 0 ? -6 : 1 - currentDay;
            const monday = new Date(now);
            monday.setDate(now.getDate() + distanceToMonday);

            const sunday = new Date(monday);
            sunday.setDate(monday.getDate() + 6);

            inputTglMulai.value = formatDate(monday);
            inputTglSelesai.value = formatDate(sunday);
        } else if (val === 'bulan_ini') {
            // Awal bulan s/d akhir bulan ini
            const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

            inputTglMulai.value = formatDate(firstDay);
            inputTglSelesai.value = formatDate(lastDay);
        } else if (val === 'semua') {
            inputTglMulai.value = '';
            inputTglSelesai.value = '';
        }
    });

    // Jika pengguna mengubah tgl_mulai atau tgl_selesai manual, set dropdown periode ke 'kustom'
    function handleManualDateInput() {
        if (selectPeriode.value !== 'kustom' && (inputTglMulai.value || inputTglSelesai.value)) {
            // Cek apakah cocok dengan opsi cepat, jika tidak ubah ke 'kustom'
            selectPeriode.value = 'kustom';
        }
    }

    // ==========================================
    // HIGH-PERFORMANCE CLIENT-SIDE TABLE COLUMN SORTING PADA HASIL DATA
    // ==========================================
    const dataTables = document.querySelectorAll('#data-table-container table');
    dataTables.forEach((table) => {
        const thead = table.querySelector('thead');
        const tbody = table.querySelector('tbody');
        if (!thead || !tbody) return;

        const thList = thead.querySelectorAll('th');
        let isSorting = false;

        thList.forEach((th, colIdx) => {
            th.style.cursor = 'pointer';
            th.style.userSelect = 'none';
            th.classList.add('hover:bg-slate-100', 'transition-colors');
            th.title = 'Klik untuk mengurutkan (A-Z / 0-9)';

            const originalContent = th.innerHTML;
            th.innerHTML = `
                <div class="inline-flex items-center gap-1.5 justify-between w-full">
                    <span>${originalContent}</span>
                    <i class="sort-icon bi bi-arrow-down-up text-[10px] text-slate-400 shrink-0"></i>
                </div>
            `;

            th.addEventListener('click', () => {
                if (isSorting) return;

                const currentOrder = th.getAttribute('data-sort-order') || 'none';
                const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';

                // Reset indikator di header lain
                thList.forEach(otherTh => {
                    otherTh.removeAttribute('data-sort-order');
                    const otherIcon = otherTh.querySelector('.sort-icon');
                    if (otherIcon) {
                        otherIcon.className = 'sort-icon bi bi-arrow-down-up text-[10px] text-slate-400 shrink-0';
                    }
                });

                // Update ikon aktif
                th.setAttribute('data-sort-order', newOrder);
                const icon = th.querySelector('.sort-icon');
                if (icon) {
                    icon.className = newOrder === 'asc'
                        ? 'sort-icon bi bi-sort-down-alt text-xs text-blue-600 font-black shrink-0'
                        : 'sort-icon bi bi-sort-up text-xs text-blue-600 font-black shrink-0';
                }

                // Ambil semua tr valid
                const trElements = Array.from(tbody.querySelectorAll('tr')).filter(tr => tr.children.length > 1);
                if (trElements.length <= 1) return;

                isSorting = true;
                table.style.opacity = '0.5';

                // Gunakan requestAnimationFrame + setTimeout agar browser me-render perubahan ikon terlebih dahulu dan tidak hang
                requestAnimationFrame(() => {
                    setTimeout(() => {
                        const dateRegex = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;

                        // 1. PRE-COMPUTE & CACHE VALUES (O(N)):
                        // Membaca teks cell 1x saja, bukan berulang-ulang di dalam O(N log N) sorting loop
                        const items = trElements.map(tr => {
                            const raw = (tr.children[colIdx]?.textContent || '').trim();
                            let parsedType = 'string';
                            let parsedVal = raw.toLowerCase();

                            const dateMatch = raw.match(dateRegex);
                            if (dateMatch) {
                                parsedType = 'date';
                                parsedVal = new Date(dateMatch[3], dateMatch[2] - 1, dateMatch[1]).getTime();
                            } else {
                                const cleanNum = raw.replace(/[^0-9.,-]/g, '').replace(',', '.');
                                const num = parseFloat(cleanNum);
                                if (!isNaN(num) && cleanNum !== '' && !cleanNum.includes('/') && !raw.includes('/')) {
                                    parsedType = 'number';
                                    parsedVal = num;
                                }
                            }

                            return {
                                tr: tr,
                                noCell: tr.children[0],
                                type: parsedType,
                                val: parsedVal
                            };
                        });

                        // 2. FAST IN-MEMORY SORT
                        items.sort((a, b) => {
                            if (a.type === 'number' && b.type === 'number') {
                                return newOrder === 'asc' ? a.val - b.val : b.val - a.val;
                            }
                            if (a.type === 'date' && b.type === 'date') {
                                return newOrder === 'asc' ? a.val - b.val : b.val - a.val;
                            }
                            if (a.val < b.val) return newOrder === 'asc' ? -1 : 1;
                            if (a.val > b.val) return newOrder === 'asc' ? 1 : -1;
                            return 0;
                        });

                        // 3. FAST BATCH DOM RE-INSERTION dengan DocumentFragment (hanya 1x reflow layout)
                        const fragment = document.createDocumentFragment();
                        const len = items.length;
                        for (let i = 0; i < len; i++) {
                            const item = items[i];
                            if (item.noCell && /^\d+$/.test(item.noCell.textContent.trim())) {
                                item.noCell.textContent = i + 1;
                            }
                            fragment.appendChild(item.tr);
                        }
                        tbody.appendChild(fragment);

                        table.style.opacity = '1';
                        isSorting = false;
                    }, 10);
                });
            });
        });
    });
});
</script>
@endpush
@endsection
