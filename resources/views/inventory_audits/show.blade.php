@extends('layouts.app')

@section('title', 'Lembar Verifikasi Audit ' . $inventoryAudit->audit_code . ' - SIM Sarpras')

@section('content')
<div class="space-y-6">
    <!-- Top Action & Nav -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-3 border-b border-slate-200/80">
        <div class="flex items-center gap-3">
            <a href="{{ route('inventory-audits.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $inventoryAudit->judul }}</h1>
                    <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                        {{ $inventoryAudit->audit_code }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Unit Kerja: <strong class="text-slate-800">{{ $inventoryAudit->jurusan->nama ?? '-' }}</strong> • 
                    Tgl Audit: {{ $inventoryAudit->tanggal_audit ? $inventoryAudit->tanggal_audit->format('d F Y') : '-' }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Tombol Cetak Dokumen Berita Acara A4 -->
            <a href="{{ route('inventory-audits.print', $inventoryAudit) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-xs shadow-xs transition-colors">
                <i class="bi bi-printer text-sm"></i>
                <span>Cetak Dokumen (A4)</span>
            </a>

            @if(Auth::user()->isSarprasOrKepalaSekolah())
                @if($inventoryAudit->status === 'draft')
                    <!-- Tarik Ulang Data Barang -->
                    <button type="button" onclick="openPullItemsModal()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-800 font-bold text-xs transition-colors" title="Sinkronkan dengan master barang terbaru">
                        <i class="bi bi-arrow-repeat"></i>
                        <span>Tarik Ulang Data</span>
                    </button>
                    <form id="pullItemsForm" action="{{ route('inventory-audits.pull-items', $inventoryAudit) }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @else
                    <!-- Buka Kunci Audit (Bisa dibuka baik saat Selesai maupun Final) -->
                    <button type="button" onclick="openUnlockAuditModal()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 font-bold text-xs transition-colors">
                        <i class="bi bi-unlock"></i>
                        <span>Buka Kunci (Revisi)</span>
                    </button>
                    <form id="unlockAuditForm" action="{{ route('inventory-audits.unlock', $inventoryAudit) }}" method="POST" class="hidden">
                        @csrf
                        @method('PATCH')
                    </form>
                @endif
            @endif
        </div>
    </div>

    <!-- Overview Status Card & Box Ringkasan -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <!-- Status & Timeline Pengesahan -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status Sesi Audit</span>
                    <div class="mt-0.5 flex items-center gap-2">
                        @if($inventoryAudit->status === 'final')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="bi bi-patch-check-fill text-sm"></i> FINAL — Kedua Belah Pihak Telah Menandatangani
                            </span>
                        @elseif($inventoryAudit->status === 'selesai')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                <i class="bi bi-check-circle-fill text-sm"></i> TERKUNCI — Menunggu Penandatanganan Pengesahan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                <i class="bi bi-pencil-fill text-sm"></i> DRAFT — Pemeriksaan &amp; Verifikasi Sedang Berlangsung
                            </span>
                        @endif
                    </div>
                </div>

                <div class="text-right">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Aset Tercatat</span>
                    <p class="text-xl font-black text-slate-900 mt-0.5">{{ $inventoryAudit->items->count() }} <span class="text-xs font-medium text-slate-500">Barang</span></p>
                </div>
            </div>

            <!-- Petugas & Penanggung Jawab -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Auditor (Pemeriksa Sarpras)</span>
                        @if($inventoryAudit->ttd_auditor)
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100/70 px-1.5 py-0.5 rounded">✓ Sudah TTD</span>
                        @else
                            <span class="text-[10px] font-bold text-slate-400 bg-slate-200/60 px-1.5 py-0.5 rounded">Belum TTD</span>
                        @endif
                    </div>
                    <p class="font-bold text-slate-900 text-sm">{{ $inventoryAudit->auditor_nama }}</p>
                    <p class="text-slate-500">{{ $inventoryAudit->auditor_jabatan }}</p>
                    @if($inventoryAudit->auditor_nip)<p class="text-[11px] text-slate-400 font-mono mt-0.5">NIP: {{ $inventoryAudit->auditor_nip }}</p>@endif
                    
                    @if($inventoryAudit->ttd_auditor)
                        <div class="mt-2 pt-2 border-t border-slate-200/60 flex items-center justify-between">
                            <span class="text-[10px] text-slate-400">Waktu: {{ $inventoryAudit->ttd_auditor_at->format('d/m/Y H:i') }}</span>
                            @if(Auth::user()->isStaffSarpras())
                                <button type="button" 
                                        onclick="openCancelAuditSignatureModal('{{ route('inventory-audits.cancel-signature', [$inventoryAudit, 'type' => 'auditor']) }}', 'Auditor (Sarpras)')"
                                        class="text-[10px] text-rose-600 hover:underline font-bold">
                                    Batalkan TTD
                                </button>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Auditee (Penanggung Jawab Unit)</span>
                        @if($inventoryAudit->ttd_auditee)
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100/70 px-1.5 py-0.5 rounded">✓ Sudah TTD</span>
                        @else
                            <span class="text-[10px] font-bold text-slate-400 bg-slate-200/60 px-1.5 py-0.5 rounded">Belum TTD</span>
                        @endif
                    </div>
                    <p class="font-bold text-slate-900 text-sm">{{ $inventoryAudit->auditee_nama ?: ($inventoryAudit->jurusan->kepala_bengkel ?? 'Penanggung Jawab') }}</p>
                    <p class="text-slate-500">{{ $inventoryAudit->auditee_jabatan ?: 'Kepala ' . ($inventoryAudit->jurusan->nama ?? 'Unit Kerja') }}</p>
                    @if($inventoryAudit->auditee_nip)<p class="text-[11px] text-slate-400 font-mono mt-0.5">NIP: {{ $inventoryAudit->auditee_nip }}</p>@endif

                    @if($inventoryAudit->ttd_auditee)
                        <div class="mt-2 pt-2 border-t border-slate-200/60 flex items-center justify-between">
                            <span class="text-[10px] text-slate-400">Waktu: {{ $inventoryAudit->ttd_auditee_at->format('d/m/Y H:i') }}</span>
                            @if(Auth::user()->isJurusan() || Auth::user()->isStaffSarpras())
                                <button type="button" 
                                        onclick="openCancelAuditSignatureModal('{{ route('inventory-audits.cancel-signature', [$inventoryAudit, 'type' => 'auditee']) }}', 'Penanggung Jawab Unit Kerja (Auditee)')"
                                        class="text-[10px] text-rose-600 hover:underline font-bold">
                                    Batalkan TTD
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if($inventoryAudit->catatan_umum)
                <div class="p-3 rounded-xl bg-blue-50/50 border border-blue-100 text-xs text-slate-700">
                    <strong class="font-bold text-blue-900">Catatan / Arahan Sesi:</strong> {{ $inventoryAudit->catatan_umum }}
                </div>
            @endif
        </div>

        <!-- Box Rekap Hasil Temuan Fisik -->
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col justify-between space-y-4">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Ringkasan Hasil Temuan</h3>
                <div class="space-y-2.5 text-xs">
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-emerald-50/70 border border-emerald-100">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-check-circle-fill text-emerald-600 text-base"></i>
                            <span class="font-semibold text-emerald-900">Fisik Sesuai &amp; Baik</span>
                        </div>
                        <span class="font-black text-emerald-700 text-sm">{{ $inventoryAudit->total_barang_sesuai }}</span>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-amber-50/70 border border-amber-100">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-amber-600 text-base"></i>
                            <span class="font-semibold text-amber-900">Kondisi Rusak</span>
                        </div>
                        <span class="font-black text-amber-700 text-sm">{{ $inventoryAudit->total_barang_rusak }}</span>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-indigo-50/70 border border-indigo-100">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-arrow-left-right text-indigo-600 text-base"></i>
                            <span class="font-semibold text-indigo-900">Selisih Kuantitas</span>
                        </div>
                        <span class="font-black text-indigo-700 text-sm">{{ $inventoryAudit->total_barang_selisih }}</span>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-rose-50/70 border border-rose-100">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-x-circle-fill text-rose-600 text-base"></i>
                            <span class="font-semibold text-rose-900">Tidak Ditemukan / Hilang</span>
                        </div>
                        <span class="font-black text-rose-700 text-sm">{{ $inventoryAudit->total_barang_hilang }}</span>
                    </div>
                </div>
            </div>

            <!-- Panel Tombol TTD -->
            <div class="pt-3 border-t border-slate-100 space-y-2">
                @if(Auth::user()->isSarprasOrKepalaSekolah() && !$inventoryAudit->ttd_auditor && $inventoryAudit->status !== 'draft')
                    <button type="button" onclick="openAuditorSignModal()"
                            class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all flex items-center justify-center gap-2 active:scale-95">
                        <i class="bi bi-pen-fill"></i>
                        <span>Tanda Tangani Sebagai Auditor (Sarpras)</span>
                    </button>
                @endif

                @if((Auth::user()->isJurusan() || Auth::user()->isSarprasOrKepalaSekolah()) && !$inventoryAudit->ttd_auditee && $inventoryAudit->status !== 'draft')
                    <button type="button" onclick="openAuditeeSignModal()"
                            class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all flex items-center justify-center gap-2 active:scale-95">
                        <i class="bi bi-pen-fill"></i>
                        <span>Tanda Tangani Sebagai Unit Kerja (Auditee)</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Lembar Kerja Verifikasi Fisik Barang -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">
                    Lembar Checklist &amp; Verifikasi Fisik Barang
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    @if($inventoryAudit->status === 'draft')
                        Periksa keberadaan fisik dan kondisi aset di lapangan, lalu isi kolom hasil verifikasi di bawah ini.
                    @else
                        Hasil verifikasi fisik barang telah dikunci. Anda dapat melihat detail temuan kondisi fisik per barang.
                    @endif
                </p>
            </div>

            @if(Auth::user()->isSarprasOrKepalaSekolah() && $inventoryAudit->status === 'draft')
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Mode Edit Aktif
                    </span>
                </div>
            @endif
        </div>

        @php
            // Kumpulkan daftar kategori unik yang ada pada barang audit ini
            $kategoriList = $inventoryAudit->items->pluck('kategori_nama')->filter()->unique()->values();
        @endphp

        <!-- BILAH PENCARIAN & FILTER KATEGORI BARANG (LIVE FILTER) -->
        <div class="p-3.5 sm:p-4 bg-slate-50/80 border-b border-slate-200/80 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 text-xs">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1">
                <!-- Search Box -->
                <div class="relative flex-1 max-w-md">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" id="auditSearchInput" placeholder="Cari nama, kode barang, atau ruang..." 
                           class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>

                <!-- Filter Kategori Dropdown -->
                <div class="relative min-w-[170px]">
                    <select id="auditKategoriFilter" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 cursor-pointer">
                        <option value="">Semua Kategori ({{ $inventoryAudit->items->count() }})</option>
                        @foreach($kategoriList as $kat)
                            @php
                                $cnt = $inventoryAudit->items->where('kategori_nama', $kat)->count();
                            @endphp
                            <option value="{{ strtolower($kat) }}">{{ $kat }} ({{ $cnt }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status Verifikasi -->
                <div class="relative min-w-[160px]">
                    <select id="auditStatusFilter" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 cursor-pointer">
                        <option value="">Semua Status Fisik</option>
                        <option value="sesuai">Fisik Sesuai</option>
                        <option value="rusak">Kondisi Rusak</option>
                        <option value="selisih">Selisih Kuantitas</option>
                        <option value="hilang">Tidak Ditemukan / Hilang</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between sm:justify-end gap-2.5 text-[11px] text-slate-500 pt-1 sm:pt-0">
                <span id="auditItemCountDisplay" class="font-medium text-slate-600">
                    Menampilkan <strong id="visibleItemCount">{{ $inventoryAudit->items->count() }}</strong> dari {{ $inventoryAudit->items->count() }} barang
                </span>
                <button type="button" id="resetAuditFilterBtn" onclick="resetAuditFilters()" class="text-blue-600 hover:text-blue-700 font-bold hidden hover:underline">
                    Reset Filter
                </button>
            </div>
        </div>

        @if(Auth::user()->isSarprasOrKepalaSekolah() && $inventoryAudit->status === 'draft')
            <!-- Form Pengisian Verifikasi (Mode Edit Draft) -->
            <form id="auditItemsForm" action="{{ route('inventory-audits.update-items', $inventoryAudit) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600" id="auditItemsTable">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="py-3 px-3 w-10 text-center">No</th>
                                <th class="py-3 px-3 w-44">Nama Barang &amp; Kode</th>
                                <th class="py-3 px-3 w-32 text-center">Kategori Barang</th>
                                <th class="py-3 px-3 w-28 text-center bg-slate-100/70">Kondisi &amp; Qty Sistem</th>
                                <th class="py-3 px-3 w-28 text-center bg-blue-50/60 text-blue-900">Qty Fisik</th>
                                <th class="py-3 px-3 w-32 bg-blue-50/60 text-blue-900">Kondisi Fisik</th>
                                <th class="py-3 px-3 w-36">Status Verifikasi</th>
                                <th class="py-3 px-3">Catatan Temuan Lapangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="auditItemsTableBody">
                            @forelse($inventoryAudit->items as $idx => $item)
                                <tr class="audit-row hover:bg-slate-50/50" 
                                    data-nama="{{ strtolower($item->nama_barang) }}"
                                    data-kode="{{ strtolower($item->kode_barang) }}"
                                    data-ruang="{{ strtolower($item->lokasi_sistem ?? '') }}"
                                    data-kategori="{{ strtolower($item->kategori_nama ?? '') }}"
                                    data-status="{{ strtolower($item->status_verifikasi ?? 'sesuai') }}">
                                    <td class="py-3 px-3 text-center text-slate-400 font-medium audit-row-num">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="font-bold text-slate-900 block leading-snug">{{ $item->nama_barang }}</span>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="font-mono text-[10px] text-blue-600 font-semibold">{{ $item->kode_barang }}</span>
                                        </div>
                                        <span class="text-[10px] text-slate-500 block mt-0.5">Ruang: {{ $item->lokasi_sistem ?: '-' }}</span>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        @php
                                            $katName = $item->kategori_nama ?: 'Umum';
                                            $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                                            if (stripos($katName, 'kebersihan') !== false) {
                                                $badgeClass = 'bg-teal-50 text-teal-700 border-teal-200';
                                            } elseif (stripos($katName, 'atk') !== false) {
                                                $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                            } elseif (stripos($katName, 'elektronik') !== false || stripos($katName, 'komputer') !== false) {
                                                $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                            } elseif (stripos($katName, 'mesin') !== false || stripos($katName, 'alat') !== false) {
                                                $badgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                            }
                                        @endphp
                                        <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $badgeClass }}">
                                            {{ $katName }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-center bg-slate-50/50 font-medium">
                                        <span class="font-extrabold text-slate-800 text-sm">@formatJumlah($item->jumlah_sistem)</span>
                                        <span class="text-[11px] text-slate-500">{{ $item->satuan }}</span>
                                        <div class="mt-0.5">
                                            @if($item->kondisi_sistem === 'baik')
                                                <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700">Baik</span>
                                            @elseif($item->kondisi_sistem === 'rusak_ringan')
                                                <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700">R. Ringan</span>
                                            @else
                                                <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700">R. Berat</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 text-center bg-blue-50/30">
                                        <input type="number" step="any" min="0" 
                                               name="items[{{ $item->id }}][jumlah_fisik]" 
                                               value="{{ $item->jumlah_fisik }}"
                                               class="w-20 px-2 py-1.5 text-center text-xs font-bold rounded-lg border border-slate-300 focus:outline-none focus:border-blue-500">
                                    </td>
                                    <td class="py-3 px-3 bg-blue-50/30">
                                        <select name="items[{{ $item->id }}][kondisi_fisik]" 
                                                class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-300 focus:outline-none focus:border-blue-500 font-medium">
                                            <option value="baik" {{ $item->kondisi_fisik === 'baik' ? 'selected' : '' }}>Baik (Normal)</option>
                                            <option value="rusak_ringan" {{ $item->kondisi_fisik === 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                            <option value="rusak_berat" {{ $item->kondisi_fisik === 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                                        </select>
                                    </td>
                                    <td class="py-3 px-3">
                                        <select name="items[{{ $item->id }}][status_verifikasi]" 
                                                onchange="updateRowStatusData(this)"
                                                class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-300 focus:outline-none focus:border-blue-500 font-semibold">
                                            <option value="sesuai" {{ $item->status_verifikasi === 'sesuai' ? 'selected' : '' }}>Sesuai (Cocok)</option>
                                            <option value="rusak" {{ $item->status_verifikasi === 'rusak' ? 'selected' : '' }}>Rusak</option>
                                            <option value="selisih" {{ $item->status_verifikasi === 'selisih' ? 'selected' : '' }}>Selisih Jumlah</option>
                                            <option value="hilang" {{ $item->status_verifikasi === 'hilang' ? 'selected' : '' }}>Tidak Ada / Hilang</option>
                                        </select>
                                    </td>
                                    <td class="py-3 px-3">
                                        <input type="text" name="items[{{ $item->id }}][catatan]" 
                                               value="{{ $item->catatan }}" 
                                               placeholder="Catatan temuan..."
                                               class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-blue-500">
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyAuditRow">
                                    <td colspan="8" class="py-8 text-center text-slate-400">
                                        Tidak ada data barang di unit kerja ini.
                                    </td>
                                </tr>
                            @endforelse
                            <tr id="noFilterMatchRow" class="hidden">
                                <td colspan="8" class="py-8 text-center text-slate-400">
                                    <i class="bi bi-search text-2xl text-slate-300 block mb-1"></i>
                                    Tidak ada barang yang cocok dengan filter / pencarian.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Bagian Bawah: Catatan Khusus dari Sarpras -->
                <div class="p-4 sm:p-5 bg-white border-t border-slate-200/80 space-y-2">
                    <label for="catatanUmumField" class="block text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="bi bi-chat-left-text-fill text-blue-600"></i>
                        <span>Catatan Khusus / Rekomendasi Hasil Audit dari Sarpras:</span>
                    </label>
                    <textarea name="catatan_umum" id="catatanUmumField" rows="3" 
                              placeholder="Contoh: Seluruh barang dalam kondisi baik. Diperlukan pengadaan ulang 3 unit sapu dan pemeliharaan dinamo mesin bubut di ruang bengkel." 
                              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">{{ old('catatan_umum', $inventoryAudit->catatan_umum) }}</textarea>
                    <p class="text-[11px] text-slate-400">
                        Catatan ini akan tercantum di lembar pengesahan Berita Acara Audit dan terbaca oleh Penanggung Jawab Unit Kerja.
                    </p>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-xs text-slate-500">
                        <i class="bi bi-info-circle text-blue-600 mr-1"></i>
                        Simpan perubahan secara berkala. Jika seluruh barang sudah selesai dicek, pilih <strong>Selesaikan &amp; Kunci Audit</strong>.
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit" name="action" value="save" 
                                class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs shadow-xs transition-colors">
                            <i class="bi bi-floppy mr-1"></i> Simpan Sementara (Draft)
                        </button>
                        <button type="button" 
                                onclick="openCompleteAuditModal()"
                                class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all active:scale-95">
                            <i class="bi bi-check2-circle mr-1"></i> Selesaikan &amp; Kunci Audit
                        </button>
                    </div>
                </div>
            </form>
        @else
            <!-- Mode Read-Only (Audit Terkunci / Ditandatangani) -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600" id="auditItemsTable">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Nama Barang &amp; Identitas</th>
                            <th class="py-3 px-4 text-center">Kategori Barang</th>
                            <th class="py-3 px-4 text-center">Qty Sistem</th>
                            <th class="py-3 px-4 text-center">Qty Fisik</th>
                            <th class="py-3 px-4 text-center">Kondisi Fisik</th>
                            <th class="py-3 px-4 text-center">Status Verifikasi</th>
                            <th class="py-3 px-4">Catatan Temuan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="auditItemsTableBody">
                        @forelse($inventoryAudit->items as $idx => $item)
                            <tr class="audit-row hover:bg-slate-50/50"
                                data-nama="{{ strtolower($item->nama_barang) }}"
                                data-kode="{{ strtolower($item->kode_barang) }}"
                                data-ruang="{{ strtolower($item->lokasi_sistem ?? '') }}"
                                data-kategori="{{ strtolower($item->kategori_nama ?? '') }}"
                                data-status="{{ strtolower($item->status_verifikasi ?? 'sesuai') }}">
                                <td class="py-3 px-4 text-center text-slate-400 font-medium audit-row-num">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4">
                                    <span class="font-bold text-slate-900 block leading-snug">{{ $item->nama_barang }}</span>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="font-mono text-[10px] text-blue-600 font-semibold">{{ $item->kode_barang }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-500 block mt-0.5">Ruang: {{ $item->lokasi_sistem ?: '-' }}</span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @php
                                        $katName = $item->kategori_nama ?: 'Umum';
                                        $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                                        if (stripos($katName, 'kebersihan') !== false) {
                                            $badgeClass = 'bg-teal-50 text-teal-700 border-teal-200';
                                        } elseif (stripos($katName, 'atk') !== false) {
                                            $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                        } elseif (stripos($katName, 'elektronik') !== false || stripos($katName, 'komputer') !== false) {
                                            $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                        } elseif (stripos($katName, 'mesin') !== false || stripos($katName, 'alat') !== false) {
                                            $badgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                        }
                                    @endphp
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $badgeClass }}">
                                        {{ $katName }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center font-medium">
                                    <span class="font-bold text-slate-800">@formatJumlah($item->jumlah_sistem)</span>
                                    <span class="text-[11px] text-slate-400">{{ $item->satuan }}</span>
                                </td>
                                <td class="py-3 px-4 text-center font-bold {{ $item->jumlah_fisik != $item->jumlah_sistem ? 'text-amber-600' : 'text-slate-900' }}">
                                    @formatJumlah($item->jumlah_fisik) {{ $item->satuan }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($item->kondisi_fisik === 'baik')
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">Baik</span>
                                    @elseif($item->kondisi_fisik === 'rusak_ringan')
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700">R. Ringan</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700">R. Berat</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($item->status_verifikasi === 'sesuai')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                            <i class="bi bi-check-circle"></i> Sesuai
                                        </span>
                                    @elseif($item->status_verifikasi === 'rusak')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700">
                                            <i class="bi bi-exclamation-triangle"></i> Rusak
                                        </span>
                                    @elseif($item->status_verifikasi === 'selisih')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700">
                                            <i class="bi bi-arrow-left-right"></i> Selisih
                                        </span>
                                    @elseif($item->status_verifikasi === 'hilang')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700">
                                            <i class="bi bi-x-circle"></i> Hilang
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[10px] italic">Belum dicek</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-xs text-slate-600">
                                    {{ $item->catatan ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyAuditRow">
                                <td colspan="8" class="py-8 text-center text-slate-400">
                                    Tidak ada data barang yang diaudit.
                                </td>
                            </tr>
                        @endforelse
                        <tr id="noFilterMatchRow" class="hidden">
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                <i class="bi bi-search text-2xl text-slate-300 block mb-1"></i>
                                Tidak ada barang yang cocok dengan filter / pencarian.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($inventoryAudit->catatan_umum)
                <!-- Tampilan Catatan Khusus Sarpras pada Mode Read Only -->
                <div class="p-4 sm:p-5 bg-blue-50/40 border-t border-blue-100 flex items-start gap-3 text-xs">
                    <i class="bi bi-chat-left-quote-fill text-blue-600 text-lg shrink-0 mt-0.5"></i>
                    <div class="space-y-1">
                        <strong class="font-bold text-blue-950 uppercase tracking-wider text-[11px] block">Catatan Khusus / Rekomendasi Sarpras:</strong>
                        <p class="text-slate-700 leading-relaxed">{{ $inventoryAudit->catatan_umum }}</p>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

<!-- MODAL TTD AUDITOR (SARPRAS) -->
<div id="auditorSignModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="relative bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full max-h-[92vh] flex flex-col overflow-hidden text-left my-auto">
        <div class="h-1.5 bg-gradient-to-r from-blue-600 via-indigo-500 to-amber-400 shrink-0"></div>
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Tanda Tangan Petugas Auditor (Sarpras)</h3>
                <p class="text-xs text-slate-500 mt-0.5">Pengesahan berita acara hasil verifikasi fisik inventaris.</p>
            </div>
            <button type="button" onclick="closeAuditorSignModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <form action="{{ route('inventory-audits.sign-auditor', $inventoryAudit) }}" method="POST" id="auditorSignForm" class="p-5 sm:p-6 space-y-4">
            @csrf
            @method('PATCH')

            @if(Auth::user()->signature)
                <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-blue-900">
                        <input type="checkbox" name="use_saved_signature" value="1" id="useSavedAuditorSig" checked onchange="toggleAuditorPad(this)"
                               class="rounded border-blue-300 text-blue-600 focus:ring-blue-500">
                        <span>Gunakan tanda tangan profil saya</span>
                    </label>
                    <div id="savedAuditorPreview" class="mt-2 p-2 bg-white rounded-lg border border-blue-100 inline-block">
                        <img src="{{ Storage::url(Auth::user()->signature) }}" alt="TTD Profil" class="h-14 object-contain">
                    </div>
                </div>
            @endif

            <div id="auditorCanvasWrapper" class="{{ Auth::user()->signature ? 'hidden' : '' }} space-y-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Goreskan Tanda Tangan Digital</label>
                <div class="border border-slate-300 rounded-xl bg-slate-50 relative overflow-hidden">
                    <canvas id="auditorCanvas" class="w-full h-40 touch-none bg-white cursor-crosshair"></canvas>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <button type="button" onclick="clearAuditorCanvas()" class="text-slate-500 hover:text-rose-600 font-semibold">
                        <i class="bi bi-eraser mr-1"></i> Bersihkan Canvas
                    </button>
                    @if(!Auth::user()->signature)
                        <label class="flex items-center gap-1.5 cursor-pointer text-[11px] text-slate-600">
                            <input type="checkbox" name="save_signature_profile" value="1" class="rounded border-slate-300 text-blue-600">
                            <span>Simpan ke profil akun saya</span>
                        </label>
                    @endif
                </div>
                <input type="hidden" name="signature_data" id="auditorSignatureData">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeAuditorSignModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" onclick="return prepareAuditorSignatureSubmit()" 
                        class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 active:scale-95">
                    Simpan Tanda Tangan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TTD AUDITEE (UNIT KERJA) -->
<div id="auditeeSignModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="relative bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full max-h-[92vh] flex flex-col overflow-hidden text-left my-auto">
        <div class="h-1.5 bg-gradient-to-r from-emerald-600 via-teal-500 to-amber-400 shrink-0"></div>
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Tanda Tangan Penanggung Jawab Unit Kerja</h3>
                <p class="text-xs text-slate-500 mt-0.5">Persetujuan &amp; pengesahan hasil verifikasi fisik inventaris.</p>
            </div>
            <button type="button" onclick="closeAuditeeSignModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <form action="{{ route('inventory-audits.sign-auditee', $inventoryAudit) }}" method="POST" id="auditeeSignForm" class="p-5 sm:p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Nama Penanggung Jawab</label>
                    <input type="text" name="auditee_nama" value="{{ old('auditee_nama', $inventoryAudit->auditee_nama ?: ($inventoryAudit->jurusan->kepala_bengkel ?? Auth::user()->name)) }}" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 font-semibold focus:outline-none focus:border-emerald-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-600 mb-1">NIP / NPY</label>
                    <input type="text" name="auditee_nip" value="{{ old('auditee_nip', $inventoryAudit->auditee_nip) }}" placeholder="NIP/NPY..."
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-emerald-500">
                </div>
            </div>

            @if(Auth::user()->signature)
                <div class="p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-200">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-emerald-900">
                        <input type="checkbox" name="use_saved_signature" value="1" id="useSavedAuditeeSig" checked onchange="toggleAuditeePad(this)"
                               class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                        <span>Gunakan tanda tangan profil saya</span>
                    </label>
                    <div id="savedAuditeePreview" class="mt-2 p-2 bg-white rounded-lg border border-emerald-100 inline-block">
                        <img src="{{ Storage::url(Auth::user()->signature) }}" alt="TTD Profil" class="h-14 object-contain">
                    </div>
                </div>
            @endif

            <div id="auditeeCanvasWrapper" class="{{ Auth::user()->signature ? 'hidden' : '' }} space-y-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Goreskan Tanda Tangan Digital</label>
                <div class="border border-slate-300 rounded-xl bg-slate-50 relative overflow-hidden">
                    <canvas id="auditeeCanvas" class="w-full h-40 touch-none bg-white cursor-crosshair"></canvas>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <button type="button" onclick="clearAuditeeCanvas()" class="text-slate-500 hover:text-rose-600 font-semibold">
                        <i class="bi bi-eraser mr-1"></i> Bersihkan Canvas
                    </button>
                    @if(!Auth::user()->signature)
                        <label class="flex items-center gap-1.5 cursor-pointer text-[11px] text-slate-600">
                            <input type="checkbox" name="save_signature_profile" value="1" class="rounded border-slate-300 text-emerald-600">
                            <span>Simpan ke profil akun saya</span>
                        </label>
                    @endif
                </div>
                <input type="hidden" name="signature_data" id="auditeeSignatureData">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeAuditeeSignModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" onclick="return prepareAuditeeSignatureSubmit()" 
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 active:scale-95">
                    Simpan Tanda Tangan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL KONFIRMASI SELESAIKAN & KUNCI AUDIT (SESUAI TEMA APLIKASI) -->
<div id="completeAuditModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity" role="dialog" aria-modal="true" aria-labelledby="completeAuditModalTitle">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200 transform transition-all animate-in fade-in zoom-in-95 duration-150">
        <!-- Accent Top Stripe -->
        <div class="h-1.5 bg-gradient-to-r from-emerald-600 via-teal-500 to-cyan-500"></div>

        <div class="p-6 text-center">
            <!-- Close Button in corner -->
            <button type="button" onclick="closeCompleteAuditModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors" aria-label="Tutup">
                <i class="bi bi-x-lg text-xs"></i>
            </button>

            <!-- Icon with urgency ring -->
            <div class="mx-auto mb-4 w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shadow-xs">
                <i class="bi bi-check2-circle text-2xl"></i>
            </div>

            <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" id="completeAuditModalTitle">Selesaikan &amp; Kunci Audit</h3>
            <p class="text-xs sm:text-sm text-slate-500 mb-4 leading-relaxed">
                Apakah Anda yakin ingin menyelesaikan dan mengunci sesi audit inventaris ini?
            </p>

            <!-- Warning Detail Box -->
            <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 text-left mb-3.5 text-xs text-emerald-800 leading-relaxed flex items-start gap-2.5">
                <i class="bi bi-lock-fill text-emerald-600 text-sm shrink-0 mt-0.5"></i>
                <div class="space-y-0.5">
                    <p class="font-bold text-emerald-950">Catatan Penting:</p>
                    <p class="text-emerald-800 text-[11px]">Setelah dikunci, hasil verifikasi fisik barang akan dibekukan untuk proses penandatanganan Berita Acara Audit oleh Auditor &amp; Auditee.</p>
                </div>
            </div>

            <!-- Input Catatan Khusus dari Sarpras saat Mengunci -->
            <div class="text-left mb-4 space-y-1.5">
                <label for="modalCatatanUmum" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1">
                    <i class="bi bi-pencil-square text-emerald-600"></i>
                    <span>Catatan Khusus dari Sarpras (Opsional):</span>
                </label>
                <textarea id="modalCatatanUmum" rows="2" 
                          placeholder="Tuliskan catatan akhir/rekomendasi hasil audit (misal: perlu perbaikan/pengadaan barang)..."
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-1">
                <button type="button" onclick="closeCompleteAuditModal()" 
                        class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition-all active:scale-95">
                    Batal
                </button>
                <button type="button" onclick="submitCompleteAudit()" 
                        class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-500/20 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                    <i class="bi bi-check-lg text-sm"></i>
                    <span>Ya, Kunci Audit</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI TARIK ULANG DATA (SESUAI TEMA APLIKASI) -->
<div id="pullItemsModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity" role="dialog" aria-modal="true" aria-labelledby="pullItemsModalTitle">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200 transform transition-all animate-in fade-in zoom-in-95 duration-150">
        <!-- Accent Top Stripe -->
        <div class="h-1.5 bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-400"></div>

        <div class="p-6 text-center">
            <!-- Close Button in corner -->
            <button type="button" onclick="closePullItemsModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors" aria-label="Tutup">
                <i class="bi bi-x-lg text-xs"></i>
            </button>

            <!-- Icon with urgency ring -->
            <div class="mx-auto mb-4 w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center shadow-xs">
                <i class="bi bi-arrow-repeat text-2xl"></i>
            </div>

            <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" id="pullItemsModalTitle">Tarik Ulang Data Barang</h3>
            <p class="text-xs sm:text-sm text-slate-500 mb-4 leading-relaxed">
                Apakah Anda ingin menarik ulang data aset dari unit kerja ini?
            </p>

            <!-- Warning Detail Box -->
            <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200/80 text-left mb-5 text-xs text-amber-900 leading-relaxed flex items-start gap-2.5">
                <i class="bi bi-exclamation-triangle-fill text-amber-600 text-sm shrink-0 mt-0.5"></i>
                <div class="space-y-0.5">
                    <p class="font-bold text-amber-950">Perhatian:</p>
                    <p class="text-amber-800 text-[11px]">Daftar barang akan disinkronkan kembali dengan data master barang terkini. Hasil checklist yang belum disimpan permanen mungkin akan direset.</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-1">
                <button type="button" onclick="closePullItemsModal()" 
                        class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition-all active:scale-95">
                    Batal
                </button>
                <button type="button" onclick="submitPullItems()" 
                        class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 shadow-md shadow-amber-500/20 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                    <i class="bi bi-arrow-repeat text-sm"></i>
                    <span>Tarik Sekarang</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI PEMBATALAN TANDA TANGAN (SESUAI TEMA APLIKASI) -->
<div id="cancelAuditSignatureModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity" role="dialog" aria-modal="true" aria-labelledby="cancelAuditSigTitle">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200 transform transition-all animate-in fade-in zoom-in-95 duration-150">
        <!-- Accent Top Stripe -->
        <div class="h-1.5 bg-gradient-to-r from-rose-600 via-amber-500 to-amber-400"></div>

        <div class="p-6 text-center">
            <!-- Close Button in corner -->
            <button type="button" onclick="closeCancelAuditSignatureModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors" aria-label="Tutup">
                <i class="bi bi-x-lg text-xs"></i>
            </button>

            <!-- Icon with urgency ring -->
            <div class="mx-auto mb-4 w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center shadow-xs">
                <i class="bi bi-arrow-counterclockwise text-2xl"></i>
            </div>

            <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" id="cancelAuditSigTitle">Batalkan Tanda Tangan</h3>
            <p class="text-xs sm:text-sm text-slate-500 mb-4 leading-relaxed" id="cancelAuditSigDescription">
                Apakah Anda yakin ingin membatalkan tanda tangan ini?
            </p>

            <!-- Warning Detail Box -->
            <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-200/80 text-left mb-5 text-xs text-rose-800 leading-relaxed flex items-start gap-2.5">
                <i class="bi bi-exclamation-triangle-fill text-rose-500 text-sm shrink-0 mt-0.5"></i>
                <div class="space-y-0.5">
                    <p class="font-bold text-rose-900">Perhatian:</p>
                    <p id="cancelAuditSigSubtext" class="text-rose-700 text-[11px]">Tanda tangan digital akan dihapus dari dokumen Berita Acara Audit ini.</p>
                </div>
            </div>

            <form id="cancelAuditSignatureForm" method="POST" action="" class="m-0">
                @csrf
                @method('DELETE')
                <div class="grid grid-cols-2 gap-3 pt-1">
                    <button type="button" onclick="closeCancelAuditSignatureModal()" 
                            class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition-all active:scale-95">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-200 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                        <i class="bi bi-arrow-counterclockwise text-sm"></i>
                        <span>Ya, Batalkan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI BUKA KUNCI AUDIT (SESUAI TEMA APLIKASI) -->
<div id="unlockAuditModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity" role="dialog" aria-modal="true" aria-labelledby="unlockAuditModalTitle">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200 transform transition-all animate-in fade-in zoom-in-95 duration-150">
        <!-- Accent Top Stripe -->
        <div class="h-1.5 bg-gradient-to-r from-amber-500 via-orange-500 to-rose-500"></div>

        <div class="p-6 text-center">
            <!-- Close Button in corner -->
            <button type="button" onclick="closeUnlockAuditModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors" aria-label="Tutup">
                <i class="bi bi-x-lg text-xs"></i>
            </button>

            <!-- Icon with urgency ring -->
            <div class="mx-auto mb-4 w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center shadow-xs">
                <i class="bi bi-unlock-fill text-2xl"></i>
            </div>

            <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" id="unlockAuditModalTitle">Buka Kunci Sesi Audit</h3>
            <p class="text-xs sm:text-sm text-slate-500 mb-4 leading-relaxed">
                Apakah Anda yakin ingin membuka kembali sesi audit ini ke status <strong>Draft (Mode Revisi)</strong>?
            </p>

            @if($inventoryAudit->ttd_auditor || $inventoryAudit->ttd_auditee)
                <!-- Warning Detail Box jika sudah ada TTD -->
                <div class="p-3.5 rounded-2xl bg-rose-50/80 border border-rose-200/90 text-left mb-5 text-xs text-rose-800 leading-relaxed flex items-start gap-2.5">
                    <i class="bi bi-exclamation-triangle-fill text-rose-500 text-sm shrink-0 mt-0.5"></i>
                    <div class="space-y-0.5">
                        <p class="font-bold text-rose-950">Konsekuensi Tanda Tangan:</p>
                        <p class="text-rose-700 text-[11px]">Audit ini telah ditandatangani. Membuka kunci akan <strong>mereset tanda tangan</strong> yang ada, sehingga dokumen perlu ditandatangani ulang setelah revisi selesai.</p>
                    </div>
                </div>
            @else
                <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200/80 text-left mb-5 text-xs text-amber-800 leading-relaxed flex items-start gap-2.5">
                    <i class="bi bi-info-circle-fill text-amber-600 text-sm shrink-0 mt-0.5"></i>
                    <div class="space-y-0.5">
                        <p class="font-bold text-amber-950">Mode Revisi:</p>
                        <p class="text-amber-800 text-[11px]">Anda dapat mengubah kembali kuantitas fisik, kondisi, dan status verifikasi per barang.</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-3 pt-1">
                <button type="button" onclick="closeUnlockAuditModal()" 
                        class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition-all active:scale-95">
                    Batal
                </button>
                <button type="button" onclick="submitUnlockAudit()" 
                        class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 shadow-md shadow-amber-500/20 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                    <i class="bi bi-unlock text-sm"></i>
                    <span>Ya, Buka Kunci</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let auditorPad = null;
let auditeePad = null;

// Unlock Audit Modal Functions
function openUnlockAuditModal() {
    const modal = document.getElementById('unlockAuditModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}
function closeUnlockAuditModal() {
    const modal = document.getElementById('unlockAuditModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}
function submitUnlockAudit() {
    const form = document.getElementById('unlockAuditForm');
    if (form) form.submit();
}

// Complete / Lock Audit Modal Functions
function openCompleteAuditModal() {
    const modal = document.getElementById('completeAuditModal');
    const fieldPage = document.getElementById('catatanUmumField');
    const fieldModal = document.getElementById('modalCatatanUmum');
    
    // Sinkronkan isi catatan jika sudah diisi di halaman bawah
    if (fieldPage && fieldModal) {
        fieldModal.value = fieldPage.value;
    }

    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}
function closeCompleteAuditModal() {
    const modal = document.getElementById('completeAuditModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}
function submitCompleteAudit() {
    const form = document.getElementById('auditItemsForm');
    if (!form) return;
    
    // Ambil catatan dari modal jika diedit di dalam modal
    const fieldPage = document.getElementById('catatanUmumField');
    const fieldModal = document.getElementById('modalCatatanUmum');
    if (fieldModal && fieldPage) {
        fieldPage.value = fieldModal.value;
    }

    // Set or create hidden action input
    let actionInput = form.querySelector('input[name="action"]');
    if (!actionInput) {
        actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        form.appendChild(actionInput);
    }
    actionInput.value = 'complete';
    form.submit();
}

// Live Filter & Pencarian Barang Audit
function applyAuditFilters() {
    const searchVal = (document.getElementById('auditSearchInput')?.value || '').toLowerCase().trim();
    const katVal = (document.getElementById('auditKategoriFilter')?.value || '').toLowerCase().trim();
    const statusVal = (document.getElementById('auditStatusFilter')?.value || '').toLowerCase().trim();

    const rows = document.querySelectorAll('.audit-row');
    const noMatchRow = document.getElementById('noFilterMatchRow');
    const resetBtn = document.getElementById('resetAuditFilterBtn');
    const countDisplay = document.getElementById('visibleItemCount');

    let visibleCount = 0;

    rows.forEach(row => {
        const nama = row.getAttribute('data-nama') || '';
        const kode = row.getAttribute('data-kode') || '';
        const ruang = row.getAttribute('data-ruang') || '';
        const kategori = row.getAttribute('data-kategori') || '';
        const status = row.getAttribute('data-status') || '';

        const matchSearch = !searchVal || nama.includes(searchVal) || kode.includes(searchVal) || ruang.includes(searchVal);
        const matchKat = !katVal || kategori === katVal;
        const matchStatus = !statusVal || status === statusVal;

        if (matchSearch && matchKat && matchStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Perbarui penomoran urut baris yang tampil
    let currentNum = 1;
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const numCell = row.querySelector('.audit-row-num');
            if (numCell) numCell.textContent = currentNum++;
        }
    });

    if (countDisplay) countDisplay.textContent = visibleCount;
    if (noMatchRow) {
        if (visibleCount === 0 && rows.length > 0) {
            noMatchRow.classList.remove('hidden');
        } else {
            noMatchRow.classList.add('hidden');
        }
    }

    // Tampilkan tombol reset filter jika ada kriteria yang aktif
    if (resetBtn) {
        if (searchVal || katVal || statusVal) {
            resetBtn.classList.remove('hidden');
        } else {
            resetBtn.classList.add('hidden');
        }
    }
}

function resetAuditFilters() {
    const s = document.getElementById('auditSearchInput');
    const k = document.getElementById('auditKategoriFilter');
    const st = document.getElementById('auditStatusFilter');
    if (s) s.value = '';
    if (k) k.value = '';
    if (st) st.value = '';
    applyAuditFilters();
}

function updateRowStatusData(selectEl) {
    const tr = selectEl.closest('.audit-row');
    if (tr) {
        tr.setAttribute('data-status', (selectEl.value || '').toLowerCase());
    }
}

// Bind event live filter saat DOM selesai dimuat
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('auditSearchInput');
    const katFilter = document.getElementById('auditKategoriFilter');
    const statusFilter = document.getElementById('auditStatusFilter');

    if (searchInput) searchInput.addEventListener('input', applyAuditFilters);
    if (katFilter) katFilter.addEventListener('change', applyAuditFilters);
    if (statusFilter) statusFilter.addEventListener('change', applyAuditFilters);
});

// Pull Items Modal Functions
function openPullItemsModal() {
    const modal = document.getElementById('pullItemsModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}
function closePullItemsModal() {
    const modal = document.getElementById('pullItemsModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}
function submitPullItems() {
    const form = document.getElementById('pullItemsForm');
    if (form) {
        form.submit();
    }
}

// Cancel Signature Modal Functions
function openCancelAuditSignatureModal(actionUrl, roleName) {
    const modal = document.getElementById('cancelAuditSignatureModal');
    const form = document.getElementById('cancelAuditSignatureForm');
    const desc = document.getElementById('cancelAuditSigDescription');
    const subtext = document.getElementById('cancelAuditSigSubtext');

    if (form && actionUrl) {
        form.action = actionUrl;
    }
    if (desc && roleName) {
        desc.textContent = `Apakah Anda yakin ingin membatalkan tanda tangan ${roleName}?`;
    }
    if (subtext && roleName) {
        subtext.textContent = `Tanda tangan digital ${roleName} akan dihapus dan perlu ditandatangani ulang.`;
    }

    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}
function closeCancelAuditSignatureModal() {
    const modal = document.getElementById('cancelAuditSignatureModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

// Close modals when clicking backdrop
window.addEventListener('click', function(e) {
    const unlockModal = document.getElementById('unlockAuditModal');
    const completeModal = document.getElementById('completeAuditModal');
    const pullModal = document.getElementById('pullItemsModal');
    const cancelModal = document.getElementById('cancelAuditSignatureModal');
    
    if (e.target === unlockModal) closeUnlockAuditModal();
    if (e.target === completeModal) closeCompleteAuditModal();
    if (e.target === pullModal) closePullItemsModal();
    if (e.target === cancelModal) closeCancelAuditSignatureModal();
});

function setupCanvas(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return null;

    const ctx = canvas.getContext('2d');
    let drawing = false;

    // Set canvas dimensions
    function resize() {
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width;
        canvas.height = rect.height;
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
    }
    resize();

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        if (e.touches && e.touches.length > 0) {
            return {
                x: e.touches[0].clientX - rect.left,
                y: e.touches[0].clientY - rect.top
            };
        }
        return {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top
        };
    }

    function start(e) {
        drawing = true;
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
        e.preventDefault();
    }

    function draw(e) {
        if (!drawing) return;
        const pos = getPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
        e.preventDefault();
    }

    function stop() {
        drawing = false;
    }

    canvas.addEventListener('mousedown', start);
    canvas.addEventListener('mousemove', draw);
    window.addEventListener('mouseup', stop);

    canvas.addEventListener('touchstart', start, { passive: false });
    canvas.addEventListener('touchmove', draw, { passive: false });
    window.addEventListener('touchend', stop);

    return {
        canvas,
        ctx,
        clear: function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        },
        isEmpty: function() {
            const blank = document.createElement('canvas');
            blank.width = canvas.width;
            blank.height = canvas.height;
            return canvas.toDataURL() === blank.toDataURL();
        },
        toDataURL: function() {
            return canvas.toDataURL('image/png');
        },
        resize
    };
}

// Modal Auditor
function openAuditorSignModal() {
    document.getElementById('auditorSignModal').classList.remove('hidden');
    document.getElementById('auditorSignModal').classList.add('flex');
    setTimeout(() => {
        if (!auditorPad) {
            auditorPad = setupCanvas('auditorCanvas');
        } else {
            auditorPad.resize();
        }
    }, 150);
}
function closeAuditorSignModal() {
    document.getElementById('auditorSignModal').classList.add('hidden');
    document.getElementById('auditorSignModal').classList.remove('flex');
}
function clearAuditorCanvas() {
    if (auditorPad) auditorPad.clear();
}
function toggleAuditorPad(cb) {
    const wrap = document.getElementById('auditorCanvasWrapper');
    if (cb.checked) {
        wrap.classList.add('hidden');
    } else {
        wrap.classList.remove('hidden');
        if (auditorPad) auditorPad.resize();
    }
}
function prepareAuditorSignatureSubmit() {
    const useSaved = document.getElementById('useSavedAuditorSig');
    if (useSaved && useSaved.checked) return true;

    if (!auditorPad || auditorPad.isEmpty()) {
        alert('Silakan goreskan tanda tangan Anda pada canvas terlebih dahulu.');
        return false;
    }
    document.getElementById('auditorSignatureData').value = auditorPad.toDataURL();
    return true;
}

// Modal Auditee
function openAuditeeSignModal() {
    document.getElementById('auditeeSignModal').classList.remove('hidden');
    document.getElementById('auditeeSignModal').classList.add('flex');
    setTimeout(() => {
        if (!auditeePad) {
            auditeePad = setupCanvas('auditeeCanvas');
        } else {
            auditeePad.resize();
        }
    }, 150);
}
function closeAuditeeSignModal() {
    document.getElementById('auditeeSignModal').classList.add('hidden');
    document.getElementById('auditeeSignModal').classList.remove('flex');
}
function clearAuditeeCanvas() {
    if (auditeePad) auditeePad.clear();
}
function toggleAuditeePad(cb) {
    const wrap = document.getElementById('auditeeCanvasWrapper');
    if (cb.checked) {
        wrap.classList.add('hidden');
    } else {
        wrap.classList.remove('hidden');
        if (auditeePad) auditeePad.resize();
    }
}
function prepareAuditeeSignatureSubmit() {
    const useSaved = document.getElementById('useSavedAuditeeSig');
    if (useSaved && useSaved.checked) return true;

    if (!auditeePad || auditeePad.isEmpty()) {
        alert('Silakan goreskan tanda tangan Anda pada canvas terlebih dahulu.');
        return false;
    }
    document.getElementById('auditeeSignatureData').value = auditeePad.toDataURL();
    return true;
}
</script>
@endpush
@endsection
