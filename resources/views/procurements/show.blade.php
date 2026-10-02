@extends('layouts.app')

@section('title', 'Detail Usulan Pengadaan - ' . ($procurement->nomor_usulan ?? 'UP-' . $procurement->id))

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('procurements.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors mb-2">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Daftar Usulan Pengadaan</span>
            </a>
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-100 text-blue-800">
                    {{ $procurement->jurusan->kode }} - {{ $procurement->jurusan->nama }}
                </span>
                <span class="text-xs font-medium text-slate-400">|</span>
                <span class="text-xs font-semibold text-slate-600 font-mono">{{ $procurement->nomor_usulan ?? 'UP-' . $procurement->id }}</span>
                <span class="text-xs font-medium text-slate-400">|</span>
                @if($procurement->status === 'disetujui')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        <i class="bi bi-check-circle-fill text-xs"></i>
                        <span>Disetujui Sarpras</span>
                    </span>
                @elseif($procurement->status === 'ditolak')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                        <i class="bi bi-x-circle-fill text-xs"></i>
                        <span>Ditolak Sarpras</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        <i class="bi bi-clock-history text-xs"></i>
                        <span>Menunggu Verifikasi Sarpras</span>
                    </span>
                @endif

                @if($procurement->status === 'disetujui')
                    <span class="text-xs font-medium text-slate-400">|</span>
                    @if($procurement->status_kepsek === 'disetujui')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            <i class="bi bi-award-fill text-xs"></i>
                            <span>Disahkan Kepala Sekolah</span>
                        </span>
                    @elseif($procurement->status_kepsek === 'ditolak')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                            <i class="bi bi-x-octagon-fill text-xs"></i>
                            <span>Ditolak Kepala Sekolah</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-300">
                            <i class="bi bi-hourglass-split text-xs"></i>
                            <span>Menunggu ACC Kepala Sekolah</span>
                        </span>
                    @endif
                @endif
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 mt-2">{{ $procurement->judul_pengadaan ?: $procurement->summary_barang }}</h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('procurements.print', $procurement) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xs transition-colors">
                <i class="bi bi-printer text-base"></i>
                <span>Cetak Dokumen Resmi (A4)</span>
            </a>
            @if(Auth::user()->isSarpras() || ($procurement->jurusan_id === Auth::user()->jurusan_id && $procurement->status === 'menunggu'))
                <a href="{{ route('procurements.edit', $procurement) }}" class="inline-flex items-center justify-center p-2.5 rounded-xl border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm transition-colors" title="Edit Usulan">
                    <i class="bi bi-pencil"></i>
                </a>
            @endif
        </div>
    </div>

    <!-- Banner Evaluasi / Catatan Verifikasi & Approval -->
    @if($procurement->catatan_sarpras)
        <div class="p-4 rounded-2xl {{ $procurement->status === 'disetujui' ? 'bg-emerald-50 border border-emerald-200 text-emerald-900' : 'bg-rose-50 border border-rose-200 text-rose-900' }}">
            <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider mb-1">
                <i class="bi {{ $procurement->status === 'disetujui' ? 'bi-check2-circle text-emerald-600' : 'bi-exclamation-octagon-fill text-rose-600' }}"></i>
                <span>Catatan Verifikasi Waka Sarpras ({{ $procurement->verifier->name ?? 'Admin Sarpras' }}):</span>
            </div>
            <p class="text-xs sm:text-sm font-medium leading-relaxed">{{ $procurement->catatan_sarpras }}</p>
            @if($procurement->tanggal_persetujuan)
                <div class="text-[11px] opacity-75 mt-1">Pada: {{ $procurement->tanggal_persetujuan->translatedFormat('d F Y') }}</div>
            @endif
        </div>
    @endif

    @if($procurement->catatan_kepsek)
        <div class="p-4 rounded-2xl {{ $procurement->status_kepsek === 'disetujui' ? 'bg-indigo-50 border border-indigo-200 text-indigo-900' : 'bg-rose-50 border border-rose-200 text-rose-900' }}">
            <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider mb-1">
                <i class="bi {{ $procurement->status_kepsek === 'disetujui' ? 'bi-shield-check text-indigo-600' : 'bi-exclamation-diamond-fill text-rose-600' }}"></i>
                <span>Catatan Pengesahan Kepala Sekolah ({{ $procurement->approverKepsek->name ?? 'Kepala Sekolah' }}):</span>
            </div>
            <p class="text-xs sm:text-sm font-medium leading-relaxed">{{ $procurement->catatan_kepsek }}</p>
            @if($procurement->kepsek_at)
                <div class="text-[11px] opacity-75 mt-1">Pada: {{ $procurement->kepsek_at->translatedFormat('d F Y, H:i') }} WIB</div>
            @endif
        </div>
    @endif

    <!-- KOTAK TANDA TANGAN & PENGESAHAN ELEKTRONIK 3 PIHAK -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                <i class="bi bi-pen-fill text-amber-500"></i>
                <span>Status Verifikasi &amp; Tanda Tangan Digital (3 Pihak)</span>
            </h2>
            <span class="text-xs text-slate-500">Dokumen Sah &amp; Terverifikasi</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- 1. Tanda Tangan Diajukan Oleh (Pemohon Unit Kerja) -->
            <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-700">1. Diajukan Oleh</span>
                        @if($procurement->ttd_pemohon)
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                <i class="bi bi-check-circle-fill"></i> Tertanda Tangan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-500 bg-slate-200/60 px-2 py-0.5 rounded-full">
                                Belum TTD
                            </span>
                        @endif
                    </div>
                    <p class="text-sm font-bold text-slate-900">{{ $procurement->jurusan->kepala_bengkel ?? ($procurement->user->name ?? 'Pemohon') }}</p>
                    <p class="text-xs text-slate-600">Kepala Unit / Bengkel {{ $procurement->jurusan->nama }}</p>
                </div>

                <div class="my-4 text-center">
                    @if($procurement->ttd_pemohon)
                        <div class="inline-block p-2 bg-white rounded-xl border border-slate-200 shadow-2xs">
                            <img src="{{ Storage::url($procurement->ttd_pemohon) }}" alt="TTD Pemohon" class="h-24 max-w-[200px] object-contain mx-auto">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Ditandatangani: {{ $procurement->ttd_pemohon_at ? $procurement->ttd_pemohon_at->translatedFormat('d M Y, H:i') : '-' }}</p>
                    @else
                        <div class="h-24 flex flex-col items-center justify-center border-2 border-dashed border-slate-300 rounded-xl text-slate-400 text-xs">
                            <i class="bi bi-vector-pen text-xl mb-1"></i>
                            <span>Tanda tangan belum dibubuhkan</span>
                        </div>
                    @endif
                </div>

                @if((Auth::user()->isJurusan() && $procurement->jurusan_id === Auth::user()->jurusan_id) || Auth::user()->isSarpras())
                    @if(! $procurement->ttd_pemohon)
                        <button type="button" onclick="openSignatureModal('pemohon')" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition-colors flex items-center justify-center gap-1.5">
                            <i class="bi bi-pen"></i>
                            <span>Bubuhkan TTD Pemohon</span>
                        </button>
                    @elseif($procurement->status !== 'disetujui')
                        <button type="button" onclick="confirmCancelSignature('pemohon')" class="w-full py-2 px-3 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition-colors flex items-center justify-center gap-1.5">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            <span>Batalkan TTD Pemohon</span>
                        </button>
                    @endif
                @endif
            </div>

            <!-- 2. Tanda Tangan Diverifikasi Oleh (Waka Sarpras) -->
            <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">2. Diverifikasi Oleh</span>
                        @if($procurement->status === 'disetujui')
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                <i class="bi bi-check-circle-fill"></i> Terverifikasi
                            </span>
                        @elseif($procurement->status === 'ditolak')
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200">
                                <i class="bi bi-x-circle-fill"></i> Ditolak
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                                Menunggu Verifikasi
                            </span>
                        @endif
                    </div>
                    <p class="text-sm font-bold text-slate-900">{{ $sarprasUnit->kepala_bengkel ?? ($procurement->verifier->name ?? ($sarprasUser->name ?? 'Waka Bidang Sarana & Prasarana')) }}</p>
                    <p class="text-xs text-slate-600">Waka Bidang Sarana &amp; Prasarana</p>
                </div>

                <div class="my-4 text-center">
                    @if($procurement->ttd_sarpras)
                        <div class="inline-block p-2 bg-white rounded-xl border border-slate-200 shadow-2xs">
                            <img src="{{ Storage::url($procurement->ttd_sarpras) }}" alt="TTD Sarpras" class="h-24 max-w-[200px] object-contain mx-auto">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Diverifikasi: {{ $procurement->ttd_sarpras_at ? $procurement->ttd_sarpras_at->translatedFormat('d M Y, H:i') : ($procurement->tanggal_persetujuan ? $procurement->tanggal_persetujuan->translatedFormat('d M Y') : '-') }}</p>
                    @elseif($procurement->status === 'disetujui')
                        <div class="h-24 flex flex-col items-center justify-center border border-emerald-200 bg-emerald-50/50 rounded-xl text-emerald-700 text-xs">
                            <i class="bi bi-patch-check-fill text-2xl mb-1 text-emerald-600"></i>
                            <span class="font-bold">Disetujui Sarpras</span>
                        </div>
                    @else
                        <div class="h-24 flex flex-col items-center justify-center border-2 border-dashed border-slate-300 rounded-xl text-slate-400 text-xs">
                            <i class="bi bi-shield-check text-xl mb-1"></i>
                            <span>Menunggu verifikasi Sarpras</span>
                        </div>
                    @endif
                </div>

                <!-- Tombol Aksi Verifikasi / Pembatalan Sarpras -->
                @if(Auth::user()->isSarpras())
                    @if($procurement->status !== 'disetujui')
                        <div class="flex gap-2">
                            <button type="button" onclick="openSignatureModal('sarpras')" class="flex-1 py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-colors flex items-center justify-center gap-1.5">
                                <i class="bi bi-check2-circle"></i>
                                <span>Verifikasi &amp; TTD</span>
                            </button>
                            <button type="button" onclick="openSarprasRejectModal()" class="py-2.5 px-3 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition-colors" title="Tolak Usulan">
                                Tolak
                            </button>
                        </div>
                    @else
                        <button type="button" onclick="confirmCancelSignature('sarpras')" class="w-full py-2 px-3 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition-colors flex items-center justify-center gap-1.5">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            <span>Batalkan Verifikasi &amp; TTD Sarpras</span>
                        </button>
                    @endif
                @endif
            </div>

            <!-- 3. Tanda Tangan Mengetahui (Kepala Sekolah) -->
            <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-purple-700">3. Mengetahui</span>
                        @if($procurement->status_kepsek === 'disetujui')
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                <i class="bi bi-award-fill"></i> Di-ACC &amp; TTD
                            </span>
                        @elseif($procurement->status_kepsek === 'ditolak')
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200">
                                <i class="bi bi-x-circle-fill"></i> Ditolak
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                                Menunggu ACC
                            </span>
                        @endif
                    </div>
                    <p class="text-sm font-bold text-slate-900">{{ $kepsekUser->name ?? ($procurement->approverKepsek->name ?? 'Bpk. Kepala Sekolah, M.Pd') }}</p>
                    <p class="text-xs text-slate-600">Kepala SMK Dr. Sutomo Temanggung</p>
                </div>

                <div class="my-4 text-center">
                    @if($procurement->ttd_kepsek)
                        <div class="inline-block p-2 bg-white rounded-xl border border-slate-200 shadow-2xs">
                            <img src="{{ Storage::url($procurement->ttd_kepsek) }}" alt="TTD Kepsek" class="h-24 max-w-[200px] object-contain mx-auto">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Disahkan: {{ $procurement->ttd_kepsek_at ? $procurement->ttd_kepsek_at->translatedFormat('d M Y, H:i') : '-' }}</p>
                    @elseif($procurement->status_kepsek === 'disetujui')
                        <div class="h-24 flex flex-col items-center justify-center border border-purple-200 bg-purple-50/50 rounded-xl text-purple-700 text-xs">
                            <i class="bi bi-patch-check-fill text-2xl mb-1 text-purple-600"></i>
                            <span class="font-bold">Disetujui Kepala Sekolah</span>
                        </div>
                    @else
                        <div class="h-24 flex flex-col items-center justify-center border-2 border-dashed border-slate-300 rounded-xl text-slate-400 text-xs">
                            <i class="bi bi-hourglass-bottom text-xl mb-1"></i>
                            <span>Menunggu pengesahan Kepala Sekolah</span>
                        </div>
                    @endif
                </div>

                <!-- Tombol Aksi Kepala Sekolah / Pembatalan -->
                @if(Auth::user()->isKepalaSekolah() || Auth::user()->isSarpras())
                    @if($procurement->status_kepsek !== 'disetujui')
                        <div class="flex gap-2">
                            <button type="button" onclick="openKepsekApprovalModal()" class="flex-1 py-2.5 px-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-xs transition-colors flex items-center justify-center gap-1.5">
                                <i class="bi bi-check2-circle"></i>
                                <span>ACC &amp; Tanda Tangan</span>
                            </button>
                            <button type="button" onclick="openKepsekRejectModal()" class="py-2.5 px-3 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition-colors" title="Tolak / Revisi">
                                Tolak
                            </button>
                        </div>
                    @else
                        <button type="button" onclick="confirmCancelSignature('kepsek')" class="w-full py-2 px-3 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition-colors flex items-center justify-center gap-1.5">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            <span>Batalkan Pengesahan Kepsek</span>
                        </button>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Metadata Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Tanggal Usulan</span>
                <span class="text-sm font-bold text-slate-800 mt-0.5 block">{{ $procurement->created_at->translatedFormat('d F Y') }}</span>
            </div>
            <div>
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Unit / Jurusan</span>
                <span class="text-sm font-bold text-slate-800 mt-0.5 block">{{ $procurement->jurusan->nama }} ({{ $procurement->jurusan->kode }})</span>
            </div>
            <div>
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Dibuat Oleh</span>
                <span class="text-sm font-bold text-slate-800 mt-0.5 block">{{ $procurement->user?->name ?? 'Pemohon' }}</span>
            </div>
            <div>
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Estimasi Total Biaya</span>
                <span class="text-base font-extrabold text-blue-700 mt-0.5 block">
                    {{ $procurement->perkiraan_biaya ? 'Rp ' . number_format($procurement->perkiraan_biaya, 0, ',', '.') : '-' }}
                </span>
            </div>
        </div>

        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Alasan / Urgensi &amp; Justifikasi:</h3>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                {{ $procurement->alasan }}
            </div>
        </div>
    </div>

    <!-- Daftar Barang yang Diusulkan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                <i class="bi bi-box-seam text-blue-600"></i>
                <span>Daftar Rincian Barang &amp; Bahan ({{ $procurement->items->count() > 0 ? $procurement->items->count() : 1 }} Item)</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Barang / Bahan</th>
                        <th class="py-3 px-4">Spesifikasi / Merk</th>
                        <th class="py-3 px-4 text-center">Jumlah</th>
                        <th class="py-3 px-4 text-center">Satuan</th>
                        <th class="py-3 px-4 text-right">Harga Satuan</th>
                        <th class="py-3 px-4 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $items = $procurement->items->count() > 0 ? $procurement->items : collect([
                            (object)[
                                'nama_barang' => $procurement->nama_barang,
                                'spesifikasi' => $procurement->spesifikasi,
                                'jumlah' => $procurement->jumlah,
                                'satuan' => $procurement->satuan,
                                'harga_satuan' => $procurement->perkiraan_biaya && $procurement->jumlah ? ($procurement->perkiraan_biaya / $procurement->jumlah) : null,
                                'perkiraan_biaya' => $procurement->perkiraan_biaya,
                                'keterangan' => null
                            ]
                        ]);
                    @endphp

                    @foreach($items as $idx => $it)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                            <td class="py-3.5 px-4 font-semibold text-slate-900">
                                {{ $it->nama_barang }}
                                @if(!empty($it->keterangan))
                                    <div class="text-[11px] text-slate-400 font-normal italic mt-0.5">
                                        Ket: {{ $it->keterangan }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $it->spesifikasi ?: '-' }}</td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-800">{{ number_format($it->jumlah) }}</td>
                            <td class="py-3.5 px-4 text-center text-slate-500">{{ $it->satuan }}</td>
                            <td class="py-3.5 px-4 text-right text-slate-600">
                                {{ $it->harga_satuan ? 'Rp ' . number_format($it->harga_satuan, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                                {{ $it->perkiraan_biaya ? 'Rp ' . number_format($it->perkiraan_biaya, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50 border-t border-slate-200 font-bold text-xs text-slate-900">
                        <td colspan="6" class="py-3 px-4 text-right uppercase tracking-wider">Total Estimasi Anggaran:</td>
                        <td class="py-3 px-4 text-right text-sm text-blue-700">
                            {{ $procurement->perkiraan_biaya ? 'Rp ' . number_format($procurement->perkiraan_biaya, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- MODAL TTD PEMOHON SUSULAN -->
<div id="pemohonSignModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden text-left">
        <div class="h-1.5 bg-gradient-to-r from-blue-600 via-indigo-500 to-amber-400"></div>

        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-pen-fill text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Tanda Tangan Pemohon</h3>
                        <p class="text-[11px] text-slate-400">Kepala Unit / Bengkel {{ $procurement->jurusan->nama }}</p>
                    </div>
                </div>
                <button type="button" onclick="closeSignatureModal('pemohon')" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <form action="{{ route('procurements.sign-pemohon', $procurement) }}" method="POST" id="pemohonSignForm" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="signature_data" id="pemohonModalSignatureData">

                @if(Auth::user()->signature)
                    <div class="p-3 bg-blue-50/70 border border-blue-200/80 rounded-2xl space-y-2">
                        <span class="block text-xs font-bold text-blue-900">Pilihan Tanda Tangan:</span>
                        <div class="flex flex-col gap-2">
                            <label class="flex items-center gap-2.5 text-xs text-slate-800 cursor-pointer">
                                <input type="radio" name="pemohon_sig_choice" value="saved" checked onchange="toggleSigMode('pemohon')" class="text-blue-600 focus:ring-blue-500">
                                <span class="font-semibold">Gunakan Tanda Tangan Tersimpan</span>
                            </label>
                            <div id="pemohonSavedPreview" class="ml-6 p-2 bg-white rounded-xl border border-blue-200 inline-block w-fit">
                                <img src="{{ Storage::url(Auth::user()->signature) }}" alt="TTD Tersimpan" class="h-16 max-w-[180px] object-contain">
                            </div>
                            <label class="flex items-center gap-2.5 text-xs text-slate-800 cursor-pointer mt-1">
                                <input type="radio" name="pemohon_sig_choice" value="draw" onchange="toggleSigMode('pemohon')" class="text-blue-600 focus:ring-blue-500">
                                <span>Goreskan Tanda Tangan Baru</span>
                            </label>
                        </div>
                    </div>
                @endif

                <div id="pemohonDrawSection" class="{{ Auth::user()->signature ? 'hidden' : '' }} space-y-2">
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Goreskan tanda tangan digital di dalam kotak berikut menggunakan mouse atau sentuhan jari:
                    </p>
                    <div class="border-2 border-dashed border-slate-300 rounded-2xl overflow-hidden bg-slate-50 touch-none flex justify-center p-1">
                        <canvas id="pemohonModalCanvas" width="380" height="180" class="cursor-crosshair bg-white rounded-xl shadow-inner"></canvas>
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="button" onclick="clearModalCanvas('pemohon')" class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold flex items-center gap-1.5 transition-colors">
                            <i class="bi bi-eraser text-xs"></i>
                            <span>Bersihkan</span>
                        </button>
                        <label class="flex items-center gap-1.5 text-[11px] text-slate-600 cursor-pointer">
                            <input type="checkbox" name="save_signature_profile" value="1" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span>Simpan ke profil akun</span>
                        </label>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" onclick="submitModalSignature('pemohon', event)" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-200 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                        <i class="bi bi-check2"></i>
                        <span>Simpan &amp; Bubuhkan Tanda Tangan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL VERIFIKASI & TTD SARPRAS -->
<div id="sarprasSignModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full overflow-hidden text-left">
        <div class="h-1.5 bg-gradient-to-r from-emerald-600 via-teal-500 to-amber-400"></div>

        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-check2-circle text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Verifikasi &amp; TTD Waka Sarpras</h3>
                        <p class="text-[11px] text-slate-400">Persetujuan usulan pengadaan ke tahap pimpinan</p>
                    </div>
                </div>
                <button type="button" onclick="closeSignatureModal('sarpras')" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <form action="{{ route('procurements.approve', $procurement) }}" method="POST" id="sarprasApproveForm" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="signature_data" id="sarprasSignatureData">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Verifikasi Sarpras (Opsional)</label>
                    <textarea name="catatan_sarpras" rows="2" placeholder="Contoh: Disetujui untuk diproses realisasi anggarannya..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">{{ old('catatan_sarpras', $procurement->catatan_sarpras) }}</textarea>
                </div>

                @if(Auth::user()->signature)
                    <div class="p-3 bg-emerald-50/70 border border-emerald-200/80 rounded-2xl space-y-2">
                        <span class="block text-xs font-bold text-emerald-900">Pilihan Tanda Tangan:</span>
                        <div class="flex flex-col gap-2">
                            <label class="flex items-center gap-2.5 text-xs text-slate-800 cursor-pointer">
                                <input type="radio" name="sarpras_sig_choice" value="saved" checked onchange="toggleSigMode('sarpras')" class="text-emerald-600 focus:ring-emerald-500">
                                <span class="font-semibold">Gunakan Tanda Tangan Tersimpan</span>
                            </label>
                            <div id="sarprasSavedPreview" class="ml-6 p-2 bg-white rounded-xl border border-emerald-200 inline-block w-fit">
                                <img src="{{ Storage::url(Auth::user()->signature) }}" alt="TTD Tersimpan" class="h-16 max-w-[180px] object-contain">
                            </div>
                            <label class="flex items-center gap-2.5 text-xs text-slate-800 cursor-pointer mt-1">
                                <input type="radio" name="sarpras_sig_choice" value="draw" onchange="toggleSigMode('sarpras')" class="text-emerald-600 focus:ring-emerald-500">
                                <span>Goreskan Tanda Tangan Baru</span>
                            </label>
                        </div>
                    </div>
                @endif

                <div id="sarprasDrawSection" class="{{ Auth::user()->signature ? 'hidden' : '' }} space-y-2">
                    <label class="block text-xs font-semibold text-slate-700">Goreskan Tanda Tangan Digital Waka Sarpras:</label>
                    <div class="border-2 border-dashed border-emerald-300 rounded-2xl overflow-hidden bg-slate-50 touch-none flex justify-center p-1">
                        <canvas id="sarprasModalCanvas" width="420" height="180" class="cursor-crosshair bg-white rounded-xl shadow-inner"></canvas>
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="button" onclick="clearModalCanvas('sarpras')" class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold flex items-center gap-1.5 transition-colors">
                            <i class="bi bi-eraser text-xs"></i>
                            <span>Bersihkan</span>
                        </button>
                        <label class="flex items-center gap-1.5 text-[11px] text-slate-600 cursor-pointer">
                            <input type="checkbox" name="save_signature_profile" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span>Simpan ke profil akun</span>
                        </label>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" onclick="submitModalSignature('sarpras', event)" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-200 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                        <i class="bi bi-check2-circle"></i>
                        <span>Verifikasi &amp; Sahkan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL TOLAK SARPRAS -->
<div id="sarprasRejectModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden text-left">
        <div class="h-1.5 bg-gradient-to-r from-rose-600 via-rose-500 to-amber-400"></div>

        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-x-circle-fill text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Tolak Usulan Pengadaan</h3>
                        <p class="text-[11px] text-slate-400">Berikan catatan evaluasi untuk pemohon</p>
                    </div>
                </div>
                <button type="button" onclick="closeSarprasRejectModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <form action="{{ route('procurements.reject', $procurement) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alasan Penolakan <span class="text-rose-500">*</span></label>
                    <textarea name="catatan_sarpras" rows="3" required placeholder="Jelaskan alasan penolakan atau revisi yang diperlukan..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="button" onclick="closeSarprasRejectModal()" class="w-full py-2.5 px-4 rounded-xl border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-all active:scale-95">
                        Batal
                    </button>
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-200 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                        <i class="bi bi-x-circle"></i>
                        <span>Kirim Penolakan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL ACC & TTD KEPALA SEKOLAH -->
<div id="kepsekApprovalModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full overflow-hidden text-left">
        <div class="h-1.5 bg-gradient-to-r from-purple-600 via-indigo-500 to-amber-400"></div>

        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-award-fill text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Pengesahan &amp; TTD Kepala Sekolah</h3>
                        <p class="text-[11px] text-slate-400">Mengetahui &amp; menyetujui pengadaan barang</p>
                    </div>
                </div>
                <button type="button" onclick="closeKepsekApprovalModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <form action="{{ route('procurements.approve-kepsek', $procurement) }}" method="POST" id="kepsekApproveForm" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="signature_data" id="kepsekSignatureData">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Kepala Sekolah (Opsional)</label>
                    <textarea name="catatan_kepsek" rows="2" placeholder="Contoh: Disetujui sesuai pagu anggaran sekolah..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all">{{ old('catatan_kepsek', $procurement->catatan_kepsek) }}</textarea>
                </div>

                @php
                    $availableKepsekSig = Auth::user()->isKepalaSekolah() ? Auth::user()->signature : ($kepsekUser?->signature ?: Auth::user()->signature);
                @endphp

                @if($availableKepsekSig)
                    <div class="p-3 bg-purple-50/70 border border-purple-200/80 rounded-2xl space-y-2">
                        <span class="block text-xs font-bold text-purple-900">Pilihan Tanda Tangan:</span>
                        <div class="flex flex-col gap-2">
                            <label class="flex items-center gap-2.5 text-xs text-slate-800 cursor-pointer">
                                <input type="radio" name="kepsek_sig_choice" value="saved" checked onchange="toggleSigMode('kepsek')" class="text-purple-600 focus:ring-purple-500">
                                <span class="font-semibold">Gunakan Tanda Tangan Tersimpan ({{ $kepsekUser->name ?? 'Kepala Sekolah' }})</span>
                            </label>
                            <div id="kepsekSavedPreview" class="ml-6 p-2 bg-white rounded-xl border border-purple-200 inline-block w-fit">
                                <img src="{{ Storage::url($availableKepsekSig) }}" alt="TTD Tersimpan" class="h-16 max-w-[180px] object-contain">
                            </div>
                            <label class="flex items-center gap-2.5 text-xs text-slate-800 cursor-pointer mt-1">
                                <input type="radio" name="kepsek_sig_choice" value="draw" onchange="toggleSigMode('kepsek')" class="text-purple-600 focus:ring-purple-500">
                                <span>Goreskan Tanda Tangan Baru</span>
                            </label>
                        </div>
                    </div>
                @endif

                <div id="kepsekDrawSection" class="{{ $availableKepsekSig ? 'hidden' : '' }} space-y-2">
                    <label class="block text-xs font-semibold text-slate-700">Goreskan Tanda Tangan Digital Kepala Sekolah:</label>
                    <div class="border-2 border-dashed border-purple-300 rounded-2xl overflow-hidden bg-slate-50 touch-none flex justify-center p-1">
                        <canvas id="kepsekModalCanvas" width="420" height="180" class="cursor-crosshair bg-white rounded-xl shadow-inner"></canvas>
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="button" onclick="clearModalCanvas('kepsek')" class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold flex items-center gap-1.5 transition-colors">
                            <i class="bi bi-eraser text-xs"></i>
                            <span>Bersihkan</span>
                        </button>
                        <label class="flex items-center gap-1.5 text-[11px] text-slate-600 cursor-pointer">
                            <input type="checkbox" name="save_signature_profile" value="1" class="rounded border-slate-300 text-purple-600 focus:ring-purple-500">
                            <span>Simpan ke profil Kepala Sekolah</span>
                        </label>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" onclick="submitModalSignature('kepsek', event)" class="w-full py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md shadow-purple-200 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                        <i class="bi bi-check2-circle"></i>
                        <span>ACC &amp; Sahkan Pengadaan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL TOLAK KEPALA SEKOLAH -->
<div id="kepsekRejectModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden text-left">
        <div class="h-1.5 bg-gradient-to-r from-rose-600 via-rose-500 to-amber-400"></div>

        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-x-circle-fill text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Tolak / Revisi Pengadaan</h3>
                        <p class="text-[11px] text-slate-400">Kembalikan usulan pengadaan ke unit kerja / Sarpras</p>
                    </div>
                </div>
                <button type="button" onclick="closeKepsekRejectModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <form action="{{ route('procurements.reject-kepsek', $procurement) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alasan Penolakan / Catatan Evaluasi <span class="text-rose-500">*</span></label>
                    <textarea name="catatan_kepsek" rows="3" required placeholder="Jelaskan alasan mengapa usulan pengadaan ini ditolak atau ditinjau ulang..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="button" onclick="closeKepsekRejectModal()" class="w-full py-2.5 px-4 rounded-xl border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-all active:scale-95">
                        Batal
                    </button>
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-200 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                        <i class="bi bi-x-circle"></i>
                        <span>Kirim Penolakan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI PEMBATALAN TANDA TANGAN (SESUAI TEMA APLIKASI) -->
<div id="cancelSignatureModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden text-left animate-in fade-in zoom-in-95 duration-150">
        <div class="h-1.5 bg-gradient-to-r from-rose-500 via-amber-500 to-rose-600"></div>

        <div class="p-6 space-y-4">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center shrink-0 shadow-2xs">
                        <i class="bi bi-exclamation-triangle-fill text-xl"></i>
                    </div>
                    <div>
                        <h3 id="cancelModalTitle" class="text-sm font-bold text-slate-900 leading-snug">Batalkan Tanda Tangan</h3>
                        <p class="text-[11px] text-slate-400">Konfirmasi pembatalan persetujuan dokumen</p>
                    </div>
                </div>
                <button type="button" onclick="closeCancelSignatureModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <div class="p-3.5 bg-rose-50/70 border border-rose-100 rounded-2xl text-xs text-slate-700 leading-relaxed space-y-1">
                <p id="cancelModalDescription" class="font-medium text-rose-950">
                    Apakah Anda yakin ingin membatalkan tanda tangan ini?
                </p>
                <p id="cancelModalSubtext" class="text-[11px] text-rose-600">
                    Tindakan ini akan menghapus stempel tanda tangan dari dokumen resmi usulan pengadaan.
                </p>
            </div>

            <form id="cancelSignatureForm" method="POST" action="" class="pt-2">
                @csrf
                @method('DELETE')
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="closeCancelSignatureModal()" class="w-full py-2.5 px-4 rounded-xl border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-all active:scale-95">
                        Tutup
                    </button>
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-200 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span id="cancelModalBtnText">Ya, Batalkan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Logic Canvas Signature Pad untuk Modals
let pemohonModalDrawn = false;
let sarprasModalDrawn = false;
let kepsekModalDrawn = false;

function setupCanvas(canvasId, type) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    ctx.strokeStyle = '#0f172a';
    ctx.lineWidth = 2.5;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';

    let isDrawing = false;

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        if (e.touches && e.touches[0]) {
            return {
                x: (e.touches[0].clientX - rect.left) * scaleX,
                y: (e.touches[0].clientY - rect.top) * scaleY
            };
        }
        return {
            x: (e.clientX - rect.left) * scaleX,
            y: (e.clientY - rect.top) * scaleY
        };
    }

    function start(e) {
        e.preventDefault();
        isDrawing = true;
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
        if (type === 'pemohon') pemohonModalDrawn = true;
        else if (type === 'sarpras') sarprasModalDrawn = true;
        else if (type === 'kepsek') kepsekModalDrawn = true;
    }

    function move(e) {
        if (!isDrawing) return;
        e.preventDefault();
        const pos = getPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
    }

    function stop(e) {
        if (isDrawing) {
            ctx.closePath();
            isDrawing = false;
        }
    }

    canvas.addEventListener('mousedown', start);
    canvas.addEventListener('mousemove', move);
    window.addEventListener('mouseup', stop);

    canvas.addEventListener('touchstart', start, { passive: false });
    canvas.addEventListener('touchmove', move, { passive: false });
    window.addEventListener('touchend', stop);
}

document.addEventListener('DOMContentLoaded', function () {
    setupCanvas('pemohonModalCanvas', 'pemohon');
    setupCanvas('sarprasModalCanvas', 'sarpras');
    setupCanvas('kepsekModalCanvas', 'kepsek');
});

function clearModalCanvas(type) {
    const canvasId = type === 'pemohon' ? 'pemohonModalCanvas' : (type === 'sarpras' ? 'sarprasModalCanvas' : 'kepsekModalCanvas');
    const canvas = document.getElementById(canvasId);
    if (canvas) {
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        if (type === 'pemohon') pemohonModalDrawn = false;
        else if (type === 'sarpras') sarprasModalDrawn = false;
        else if (type === 'kepsek') kepsekModalDrawn = false;
    }
}

function openSignatureModal(type) {
    const modalId = type === 'pemohon' ? 'pemohonSignModal' : 'sarprasSignModal';
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeSignatureModal(type) {
    const modalId = type === 'pemohon' ? 'pemohonSignModal' : 'sarprasSignModal';
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function openSarprasRejectModal() {
    const modal = document.getElementById('sarprasRejectModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeSarprasRejectModal() {
    const modal = document.getElementById('sarprasRejectModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function openKepsekApprovalModal() {
    const modal = document.getElementById('kepsekApprovalModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeKepsekApprovalModal() {
    const modal = document.getElementById('kepsekApprovalModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function openKepsekRejectModal() {
    const modal = document.getElementById('kepsekRejectModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeKepsekRejectModal() {
    const modal = document.getElementById('kepsekRejectModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function toggleSigMode(type) {
    const radio = document.querySelector(`input[name="${type}_sig_choice"]:checked`);
    const drawSection = document.getElementById(`${type}DrawSection`);
    const isSaved = radio && radio.value === 'saved';

    if (drawSection) {
        if (isSaved) {
            drawSection.classList.add('hidden');
        } else {
            drawSection.classList.remove('hidden');
        }
    }
}

function submitModalSignature(type, event) {
    const radio = document.querySelector(`input[name="${type}_sig_choice"]:checked`);
    const useSaved = radio ? (radio.value === 'saved') : false;

    // Tambah hidden input use_saved_signature ke form jika belum ada
    const formId = type === 'pemohon' ? 'pemohonSignForm' : (type === 'sarpras' ? 'sarprasApproveForm' : 'kepsekApproveForm');
    const form = document.getElementById(formId);
    let useSavedInput = form.querySelector('input[name="use_saved_signature"]');
    if (!useSavedInput) {
        useSavedInput = document.createElement('input');
        useSavedInput.type = 'hidden';
        useSavedInput.name = 'use_saved_signature';
        form.appendChild(useSavedInput);
    }
    useSavedInput.value = useSaved ? '1' : '0';

    if (useSaved) {
        // Mode tanda tangan tersimpan
        return;
    }

    if (type === 'pemohon') {
        if (!pemohonModalDrawn) {
            event.preventDefault();
            alert('Silakan goreskan tanda tangan Pemohon pada canvas terlebih dahulu.');
            return;
        }
        const dataUrl = document.getElementById('pemohonModalCanvas').toDataURL('image/png');
        document.getElementById('pemohonModalSignatureData').value = dataUrl;
    } else if (type === 'sarpras') {
        if (!sarprasModalDrawn) {
            event.preventDefault();
            alert('Silakan goreskan tanda tangan Sarpras pada canvas terlebih dahulu.');
            return;
        }
        const dataUrl = document.getElementById('sarprasModalCanvas').toDataURL('image/png');
        document.getElementById('sarprasSignatureData').value = dataUrl;
    } else if (type === 'kepsek') {
        if (!kepsekModalDrawn) {
            event.preventDefault();
            alert('Silakan goreskan tanda tangan Kepala Sekolah pada canvas terlebih dahulu.');
            return;
        }
        const dataUrl = document.getElementById('kepsekModalCanvas').toDataURL('image/png');
        document.getElementById('kepsekSignatureData').value = dataUrl;
    }
}

// Logic Modal Pop-up Pembatalan Tanda Tangan
function confirmCancelSignature(type) {
    const modal = document.getElementById('cancelSignatureModal');
    const form = document.getElementById('cancelSignatureForm');
    const title = document.getElementById('cancelModalTitle');
    const desc = document.getElementById('cancelModalDescription');
    const subtext = document.getElementById('cancelModalSubtext');
    const btnText = document.getElementById('cancelModalBtnText');

    const baseUrl = "{{ route('procurements.cancel-signature', $procurement) }}";
    form.action = `${baseUrl}?type=${type}`;

    if (type === 'pemohon') {
        title.textContent = 'Batalkan Tanda Tangan Pemohon';
        desc.textContent = 'Apakah Anda yakin ingin membatalkan tanda tangan Pemohon pada usulan pengadaan ini?';
        subtext.textContent = 'Tanda tangan Anda akan dihapus dari usulan ini. Anda dapat membubuhkan tanda tangan kembali sebelum diverifikasi oleh Sarpras.';
        btnText.textContent = 'Ya, Batalkan TTD Pemohon';
    } else if (type === 'sarpras') {
        title.textContent = 'Batalkan Verifikasi & TTD Sarpras';
        desc.textContent = 'Apakah Anda yakin ingin membatalkan verifikasi dan persetujuan Sarpras ini?';
        subtext.textContent = 'Status usulan pengadaan akan dikembalikan menjadi "Menunggu Verifikasi" dan tanda tangan Sarpras akan dihapus.';
        btnText.textContent = 'Ya, Batalkan Verifikasi';
    } else if (type === 'kepsek') {
        title.textContent = 'Batalkan Pengesahan Kepala Sekolah';
        desc.textContent = 'Apakah Anda yakin ingin membatalkan pengesahan & tanda tangan Kepala Sekolah ini?';
        subtext.textContent = 'Status pengesahan usulan pengadaan akan dikembalikan menjadi "Menunggu ACC" dan tanda tangan Kepala Sekolah akan dihapus.';
        btnText.textContent = 'Ya, Batalkan Pengesahan';
    }

    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeCancelSignatureModal() {
    const modal = document.getElementById('cancelSignatureModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}
</script>
@endpush
@endsection
