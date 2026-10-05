@extends('layouts.app')

@section('title', 'Detail Berita Acara - ' . $officialReport->nomor_surat)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('official-reports.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors mb-2">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Daftar Berita Acara</span>
            </a>
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $officialReport->jenis === 'barang_rusak' ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                    {{ $officialReport->jenis_label }}
                </span>
                <span class="text-xs font-medium text-slate-400">|</span>
                <span class="text-xs font-semibold text-slate-600">No. {{ $officialReport->nomor_surat }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 mt-2">{{ $officialReport->judul }}</h1>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            @if(Auth::user()->isStaffSarpras())
                <a href="{{ route('official-reports.edit', $officialReport) }}" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs sm:text-sm shadow-xs transition-colors" title="Edit Berita Acara">
                    <i class="bi bi-pencil-square text-amber-500"></i>
                    <span>Edit Dokumen</span>
                </a>
            @endif
            <a href="{{ route('official-reports.print', $officialReport) }}" target="_blank" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm shadow-xs transition-colors">
                <i class="bi bi-printer text-base"></i>
                <span>Cetak Surat Dinas</span>
            </a>
            @php
                $repAccSarpras = ($officialReport->ttd_pihak_pertama !== null);
                $repCanDelete = Auth::user()->isSarpras() || ($officialReport->jurusan_id && $officialReport->jurusan_id === Auth::user()->jurusan_id && ! $repAccSarpras);
            @endphp
            @if($repCanDelete)
                <form action="{{ route('official-reports.destroy', $officialReport) }}" method="POST" class="inline-block shrink-0"
                      data-confirm="Apakah Anda yakin ingin menghapus arsip Berita Acara {{ addslashes($officialReport->nomor_surat) }}?"
                      data-confirm-title="Hapus Berita Acara"
                      data-confirm-type="danger"
                      data-confirm-btn="Ya, Hapus Arsip"
                      data-confirm-icon="bi bi-trash3-fill text-2xl">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center p-2.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-sm transition-colors" title="Hapus Dokumen">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>



    <!-- KOTAK TANDA TANGAN & PERSETUJUAN ONLINE -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                <i class="bi bi-pen-fill text-amber-500"></i>
                <span>Status Pengesahan &amp; Tanda Tangan Online</span>
            </h2>
            <span class="text-xs text-slate-500">Legalitas Dokumen Digital</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 1. Tanda Tangan Pihak Pertama (Sarpras / Penyerah) -->
            <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-700">Pihak Pertama (Sarpras)</span>
                        @if($officialReport->ttd_pihak_pertama)
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                <i class="bi bi-check-circle-fill"></i> Tertanda Tangan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-500 bg-slate-200/60 px-2 py-0.5 rounded-full">
                                Belum TTD
                            </span>
                        @endif
                    </div>
                    <p class="text-sm font-bold text-slate-900">{{ $officialReport->pihak_pertama_nama }}</p>
                    <p class="text-xs text-slate-600">{{ $officialReport->pihak_pertama_jabatan }}</p>
                    @if($officialReport->pihak_pertama_nip)
                        <p class="text-[11px] text-slate-400 mt-0.5">NIP/NIY: {{ $officialReport->pihak_pertama_nip }}</p>
                    @endif
                </div>

                <div class="my-4 text-center">
                    @if($officialReport->ttd_pihak_pertama)
                        <div class="inline-block p-2 bg-white rounded-xl border border-slate-200 shadow-2xs">
                            <img src="{{ Storage::url($officialReport->ttd_pihak_pertama) }}" alt="TTD Sarpras" class="h-24 max-w-[200px] object-contain mx-auto">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Ditandatangani: {{ $officialReport->ttd_pihak_pertama_at ? $officialReport->ttd_pihak_pertama_at->translatedFormat('d M Y, H:i') : '-' }}</p>
                    @else
                        <div class="h-24 flex flex-col items-center justify-center border-2 border-dashed border-slate-300 rounded-xl text-slate-400 text-xs">
                            <i class="bi bi-vector-pen text-xl mb-1"></i>
                            <span>Tanda tangan belum dibubuhkan</span>
                        </div>
                    @endif
                </div>

                @if(Auth::user()->isStaffSarpras())
                    @if(! $officialReport->ttd_pihak_pertama)
                        <button type="button" onclick="openSignatureModal('sarpras')" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition-colors flex items-center justify-center gap-1.5">
                            <i class="bi bi-pen"></i>
                            <span>Tanda Tangani Berita Acara (Sarpras)</span>
                        </button>
                    @else
                        <button type="button" onclick="confirmCancelReportSignature('pihak_pertama')" class="w-full py-2 px-3 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition-colors flex items-center justify-center gap-1.5">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            <span>Batalkan TTD Sarpras</span>
                        </button>
                    @endif
                @endif
            </div>

            <!-- 2. Tanda Tangan Pihak Kedua (Jurusan / Penerima / Saksi / Pembeli) -->
            @if($officialReport->pihak_kedua_nama)
                <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200 flex flex-col justify-between">
                    <div>
                        @php
                            $isSaksiPenjualan = ($officialReport->pihak_kedua_peran === 'saksi') || str_contains(strtolower($officialReport->pihak_kedua_jabatan ?? ''), 'saksi');
                        @endphp
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">
                                @if($officialReport->jenis === 'serah_terima')
                                    Pihak Kedua (Penerima Barang)
                                @elseif($officialReport->jenis === 'penjualan')
                                    @if($isSaksiPenjualan)
                                        Pihak Kedua (Saksi Penjualan)
                                    @else
                                        Pihak Kedua (Pembeli / Pihak Ketiga)
                                    @endif
                                @else
                                    Pihak Kedua (Saksi / Jurusan / Umum)
                                @endif
                            </span>
                            @if($officialReport->ttd_pihak_kedua)
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                    <i class="bi bi-check-circle-fill"></i> Tertanda Tangan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                                    Menunggu TTD Pihak Kedua
                                </span>
                            @endif
                        </div>
                        <p class="text-sm font-bold text-slate-900">{{ $officialReport->pihak_kedua_nama }}</p>
                        <p class="text-xs text-slate-600">{{ $officialReport->pihak_kedua_jabatan }}</p>
                        @if($officialReport->pihak_kedua_nip)
                            <p class="text-[11px] text-slate-400 mt-0.5">NIP/NIY: {{ $officialReport->pihak_kedua_nip }}</p>
                        @endif
                    </div>

                    <div class="my-4 text-center">
                        @if($officialReport->ttd_pihak_kedua)
                            <div class="inline-block p-2 bg-white rounded-xl border border-slate-200 shadow-2xs">
                                <img src="{{ Storage::url($officialReport->ttd_pihak_kedua) }}" alt="TTD Pihak Kedua" class="h-24 max-w-[200px] object-contain mx-auto">
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Ditandatangani: {{ $officialReport->ttd_pihak_kedua_at ? $officialReport->ttd_pihak_kedua_at->translatedFormat('d M Y, H:i') : '-' }}</p>
                        @else
                            <div class="h-24 flex flex-col items-center justify-center border-2 border-dashed border-emerald-300 bg-emerald-50/30 rounded-xl text-emerald-700/70 text-xs">
                                <i class="bi bi-pen text-xl mb-1"></i>
                                <span>Menunggu verifikasi &amp; tanda tangan Pihak Kedua</span>
                            </div>
                        @endif
                    </div>

                    <!-- Tombol Aksi Jurusan atau Sarpras (Sarpras bisa mewakili) -->
                    @php
                        $canSignJurusan = Auth::user()->isStaffSarpras() || (Auth::user()->isJurusan() && $officialReport->jurusan_id && Auth::user()->jurusan_id === $officialReport->jurusan_id);
                        $signBtnText = 'Verifikasi & Tanda Tangani';
                        if ($officialReport->jenis === 'serah_terima') {
                            $signBtnText = 'Tanda Tangani Penerimaan (Pihak Kedua / Jurusan)';
                        } elseif ($officialReport->jenis === 'penjualan') {
                            if ($isSaksiPenjualan) {
                                $signBtnText = 'Tanda Tangani Saksi Penjualan';
                            } else {
                                $signBtnText = 'Tanda Tangani Pihak Pembeli';
                            }
                        } else {
                            $signBtnText = 'Tanda Tangani Saksi / Pelapor Unit';
                        }
                    @endphp
                    @if($canSignJurusan)
                        @if(! $officialReport->ttd_pihak_kedua)
                            <button type="button" onclick="openSignatureModal('jurusan')" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-colors flex items-center justify-center gap-1.5">
                                <i class="bi bi-pen"></i>
                                <span>{{ $signBtnText }}</span>
                            </button>
                        @else
                            <button type="button" onclick="confirmCancelReportSignature('pihak_kedua')" class="w-full py-2 px-3 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition-colors flex items-center justify-center gap-1.5">
                                <i class="bi bi-arrow-counterclockwise"></i>
                                <span>Batalkan TTD Pihak Kedua</span>
                            </button>
                        @endif
                    @endif
                </div>
            @endif


        </div>
    </div>

    <!-- Metadata Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Tanggal Surat</span>
                <span class="text-sm font-bold text-slate-800 mt-0.5 block">{{ $officialReport->tanggal->translatedFormat('d F Y') }}</span>
            </div>
            <div>
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Unit Kerja / Jurusan</span>
                <span class="text-sm font-bold text-slate-800 mt-0.5 block">{{ $officialReport->jurusan ? $officialReport->jurusan->nama : 'Sarpras Pusat & Umum' }}</span>
            </div>
            <div>
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Dibuat Oleh</span>
                <span class="text-sm font-bold text-slate-800 mt-0.5 block">{{ $officialReport->user?->name ?? 'Admin' }}</span>
            </div>
            <div>
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Hasil</span>
                @if($officialReport->jenis === 'penjualan')
                    <span class="text-base font-extrabold text-emerald-600 mt-0.5 block">Rp {{ number_format($officialReport->total_nominal, 0, ',', '.') }}</span>
                @else
                    <span class="text-sm font-bold text-slate-700 mt-0.5 block">{{ $officialReport->items->count() }} Aset Terdata</span>
                @endif
            </div>
        </div>

        <!-- Pihak-Pihak Bertandatangan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700 block mb-1">Pihak Pertama</span>
                <p class="text-sm font-bold text-slate-900">{{ $officialReport->pihak_pertama_nama }}</p>
                <p class="text-xs text-slate-600">{{ $officialReport->pihak_pertama_jabatan }}</p>
                @if($officialReport->pihak_pertama_nip)
                    <p class="text-[11px] text-slate-400 mt-1">NIP: {{ $officialReport->pihak_pertama_nip }}</p>
                @endif
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700 block mb-1">Pihak Kedua</span>
                <p class="text-sm font-bold text-slate-900">{{ $officialReport->pihak_kedua_nama }}</p>
                <p class="text-xs text-slate-600">{{ $officialReport->pihak_kedua_jabatan }}</p>
                @if($officialReport->pihak_kedua_instansi)
                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $officialReport->pihak_kedua_instansi }}</p>
                @endif
            </div>
        </div>

        <!-- Latar Belakang & Catatan -->
        @if($officialReport->latar_belakang)
            <div class="p-4 rounded-xl bg-slate-50/50 border border-slate-200/60">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-700 block mb-1">
                    @if($officialReport->jenis === 'serah_terima')
                        Latar Belakang / Dasar Serah Terima:
                    @elseif($officialReport->jenis === 'penjualan')
                        Latar Belakang / Dasar Pelepasan Aset:
                    @else
                        Latar Belakang / Dasar Pemeriksaan:
                    @endif
                </span>
                <p class="text-xs sm:text-sm text-slate-700 whitespace-pre-line leading-relaxed">{{ $officialReport->latar_belakang }}</p>
            </div>
        @endif

        @if($officialReport->catatan)
            <div class="text-xs text-slate-500">
                <strong class="text-slate-700">Catatan:</strong> {{ $officialReport->catatan }}
            </div>
        @endif

        @if($officialReport->file_lampiran)
            <div class="flex items-center gap-2 p-3 rounded-xl bg-blue-50/60 border border-blue-200/60 text-xs">
                <i class="bi bi-paperclip text-blue-600 text-base"></i>
                <span class="font-medium text-slate-700">Lampiran Dokumen/Foto:</span>
                <a href="{{ Storage::url($officialReport->file_lampiran) }}" target="_blank" class="text-blue-600 hover:underline font-bold">
                    Lihat Berkas Lampiran
                </a>
            </div>
        @endif
    </div>

    <!-- Tabel Daftar Barang -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">
                Daftar Barang &amp; Aset yang Tercatat
            </h2>
            <span class="text-xs font-bold text-slate-600">{{ $officialReport->items->count() }} Total Barang</span>
        </div>

        <!-- Desktop Table View -->
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Barang / Identitas Aset</th>
                        <th class="py-3 px-4">Kode Barang</th>
                        <th class="py-3 px-4 text-center">Jumlah</th>
                        <th class="py-3 px-4">Kondisi</th>
                        @if($officialReport->jenis === 'penjualan')
                            <th class="py-3 px-4 text-right">Harga Satuan</th>
                            <th class="py-3 px-4 text-right">Subtotal</th>
                        @endif
                        <th class="py-3 px-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($officialReport->items as $idx => $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3 px-4 text-center text-slate-400 font-medium">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900">{{ $item->nama_barang }}</span>
                                @if($item->unit_code)
                                    <div class="text-[11px] font-mono text-slate-500 mt-0.5">Unit: {{ $item->unit_code }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono text-xs text-slate-600">
                                {{ $item->kode_barang ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-800">
                                {{ $item->jumlah }} {{ $item->satuan }}
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $kondisi = $item->kondisi_saat_lapor;
                                    $kondisiClass = 'bg-slate-100 text-slate-700';
                                    if (in_array($kondisi, ['rusak_berat', 'rusak_total', 'hilang'])) {
                                        $kondisiClass = 'bg-rose-50 text-rose-700 border border-rose-200';
                                    } elseif (in_array($kondisi, ['baik', 'lengkap'])) {
                                        $kondisiClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                                    } elseif (in_array($kondisi, ['rusak_ringan', 'bekas_layak'])) {
                                        $kondisiClass = 'bg-amber-50 text-amber-700 border border-amber-200';
                                    }
                                @endphp
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $kondisiClass }}">
                                    {{ ucwords(str_replace('_', ' ', $kondisi)) }}
                                </span>
                            </td>
                            @if($officialReport->jenis === 'penjualan')
                                <td class="py-3 px-4 text-right font-medium text-slate-700">
                                    Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-emerald-600">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            @endif
                            <td class="py-3 px-4 text-xs text-slate-600">
                                {{ $item->keterangan ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                @if($officialReport->jenis === 'penjualan')
                    <tfoot>
                        <tr class="bg-slate-50 font-bold border-t border-slate-200">
                            <td colspan="6" class="py-3 px-4 text-right text-slate-800 uppercase tracking-wider text-xs">Total Hasil Penjualan:</td>
                            <td class="py-3 px-4 text-right text-emerald-700 text-sm">Rp {{ number_format($officialReport->total_nominal, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="divide-y divide-slate-100 md:hidden">
            @foreach($officialReport->items as $idx => $item)
                @php
                    $kondisi = $item->kondisi_saat_lapor;
                    $kondisiClass = 'bg-slate-100 text-slate-700';
                    if (in_array($kondisi, ['rusak_berat', 'rusak_total', 'hilang'])) {
                        $kondisiClass = 'bg-rose-50 text-rose-700 border border-rose-200';
                    } elseif (in_array($kondisi, ['baik', 'lengkap'])) {
                        $kondisiClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                    } elseif (in_array($kondisi, ['rusak_ringan', 'bekas_layak'])) {
                        $kondisiClass = 'bg-amber-50 text-amber-700 border border-amber-200';
                    }
                @endphp
                <div class="p-4 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-md bg-slate-100 text-slate-500 font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">
                                {{ $idx + 1 }}
                            </span>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm leading-snug">{{ $item->nama_barang }}</h4>
                                @if($item->unit_code)
                                    <div class="text-[11px] font-mono text-slate-500 mt-0.5">Unit: {{ $item->unit_code }}</div>
                                @endif
                                @if($item->kode_barang)
                                    <div class="text-[11px] font-mono text-slate-400">Kode: {{ $item->kode_barang }}</div>
                                @endif
                            </div>
                        </div>
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 shrink-0">
                            {{ $item->jumlah }} {{ $item->satuan }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-100">
                        <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $kondisiClass }}">
                            {{ ucwords(str_replace('_', ' ', $kondisi)) }}
                        </span>
                        @if($officialReport->jenis === 'penjualan')
                            <div class="text-right">
                                <span class="text-[11px] text-slate-500">Subtotal:</span>
                                <span class="text-xs font-bold text-emerald-700">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    @if($item->keterangan)
                        <div class="text-[11px] text-slate-500 bg-slate-50 p-2 rounded-lg">
                            <span class="font-semibold text-slate-600">Ket:</span> {{ $item->keterangan }}
                        </div>
                    @endif
                </div>
            @endforeach

            @if($officialReport->jenis === 'penjualan')
                <div class="p-4 bg-emerald-50/70 border-t border-emerald-200/80 flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Total Penjualan:</span>
                    <span class="text-base font-extrabold text-emerald-700">Rp {{ number_format($officialReport->total_nominal, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- MODAL TTD SARPRAS -->
<div id="sarprasSignModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="relative bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full max-h-[92vh] flex flex-col overflow-hidden text-left my-auto">
        <div class="h-1.5 bg-gradient-to-r from-blue-600 via-indigo-500 to-amber-400 shrink-0"></div>

        <div class="p-4 sm:p-6 space-y-4 overflow-y-auto flex-1">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-pen-fill text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Tanda Tangan Pihak Pertama</h3>
                        <p class="text-[11px] text-slate-400">Tim Sarpras Sekolah</p>
                    </div>
                </div>
                <button type="button" onclick="closeSignatureModal('sarpras')" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <form action="{{ route('official-reports.sign-pihak-pertama', $officialReport) }}" method="POST" id="sarprasSignForm" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="signature_data" id="sarprasSignatureData">

                @if(Auth::user()->signature)
                    <div class="p-3 bg-blue-50/70 border border-blue-200/80 rounded-2xl space-y-2">
                        <span class="block text-xs font-bold text-blue-900">Pilihan Tanda Tangan:</span>
                        <div class="flex flex-col gap-2">
                            <label class="flex items-center gap-2.5 text-xs text-slate-800 cursor-pointer">
                                <input type="radio" name="sarpras_sig_choice" value="saved" checked onchange="toggleReportSigMode('sarpras')" class="text-blue-600 focus:ring-blue-500">
                                <span class="font-semibold">Gunakan Tanda Tangan Tersimpan</span>
                            </label>
                            <div id="sarprasSavedPreview" class="ml-6 p-2 bg-white rounded-xl border border-blue-200 inline-block w-fit">
                                <img src="{{ Storage::url(Auth::user()->signature) }}" alt="TTD Tersimpan" class="h-16 max-w-[180px] object-contain">
                            </div>
                            <label class="flex items-center gap-2.5 text-xs text-slate-800 cursor-pointer mt-1">
                                <input type="radio" name="sarpras_sig_choice" value="draw" onchange="toggleReportSigMode('sarpras')" class="text-blue-600 focus:ring-blue-500">
                                <span>Goreskan Tanda Tangan Baru</span>
                            </label>
                        </div>
                    </div>
                @endif

                <div id="sarprasDrawSection" class="{{ Auth::user()->signature ? 'hidden' : '' }} space-y-2">
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Gunakan jari (pada layar sentuh HP) atau kursor mouse/touchpad untuk menandatangani di dalam kotak berikut:
                    </p>
                    <div class="border-2 border-dashed border-slate-300 rounded-2xl overflow-hidden bg-slate-50 touch-none flex justify-center p-1">
                        <canvas id="sarprasCanvas" width="420" height="180" class="cursor-crosshair bg-white rounded-xl shadow-inner w-full max-w-[420px] h-[150px] sm:h-[180px] block"></canvas>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <button type="button" onclick="clearCanvas('sarpras')" class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold flex items-center gap-1.5 transition-colors">
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
                    <button type="submit" onclick="submitSignature('sarpras', event)" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-200 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                        <i class="bi bi-check2"></i>
                        <span>Simpan &amp; Bubuhkan Tanda Tangan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL TTD JURUSAN / PIHAK KEDUA -->
@if($officialReport->pihak_kedua_nama)
<div id="jurusanSignModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="relative bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full max-h-[92vh] flex flex-col overflow-hidden text-left my-auto">
        <div class="h-1.5 bg-gradient-to-r from-emerald-600 via-teal-500 to-amber-400 shrink-0"></div>

        <div class="p-4 sm:p-6 space-y-4 overflow-y-auto flex-1">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-pen-fill text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Tanda Tangan Pihak Kedua</h3>
                        <p class="text-[11px] text-slate-400">{{ $officialReport->pihak_kedua_nama }} ({{ $officialReport->pihak_kedua_jabatan }})</p>
                    </div>
                </div>
                <button type="button" onclick="closeSignatureModal('jurusan')" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <form action="{{ route('official-reports.sign-pihak-kedua', $officialReport) }}" method="POST" id="jurusanSignForm" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="signature_data" id="jurusanSignatureData">

                @php
                    $availableJurusanSig = Auth::user()->isJurusan() 
                        ? Auth::user()->signature 
                        : ($jurusanUser?->signature ?: Auth::user()->signature);
                    $jurusanAccountName = Auth::user()->isStaffSarpras()
                        ? ($jurusanUser ? $jurusanUser->name . ' / TTD Sarpras' : Auth::user()->name . ' (Sarpras)')
                        : ($jurusanUser ? $jurusanUser->name : ($officialReport->jurusan ? $officialReport->jurusan->nama : 'Pihak Kedua'));
                @endphp

                @if($availableJurusanSig)
                    <div class="p-3 bg-emerald-50/70 border border-emerald-200/80 rounded-2xl space-y-2">
                        <span class="block text-xs font-bold text-emerald-900">Pilihan Tanda Tangan:</span>
                        <div class="flex flex-col gap-2">
                            <label class="flex items-center gap-2.5 text-xs text-slate-800 cursor-pointer">
                                <input type="radio" name="jurusan_sig_choice" value="saved" checked onchange="toggleReportSigMode('jurusan')" class="text-emerald-600 focus:ring-emerald-500">
                                <span class="font-semibold">Gunakan Tanda Tangan Tersimpan ({{ $jurusanAccountName }})</span>
                            </label>
                            <div id="jurusanSavedPreview" class="ml-6 p-2 bg-white rounded-xl border border-emerald-200 inline-block w-fit">
                                <img src="{{ Storage::url($availableJurusanSig) }}" alt="TTD Tersimpan" class="h-16 max-w-[180px] object-contain">
                            </div>
                            <label class="flex items-center gap-2.5 text-xs text-slate-800 cursor-pointer mt-1">
                                <input type="radio" name="jurusan_sig_choice" value="draw" onchange="toggleReportSigMode('jurusan')" class="text-emerald-600 focus:ring-emerald-500">
                                <span>Goreskan Tanda Tangan Baru</span>
                            </label>
                        </div>
                    </div>
                @endif

                <div id="jurusanDrawSection" class="{{ $availableJurusanSig ? 'hidden' : '' }} space-y-2">
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Gunakan jari (pada layar sentuh HP) atau kursor mouse/touchpad untuk menandatangani di dalam kotak berikut:
                    </p>
                    <div class="border-2 border-dashed border-emerald-300 rounded-2xl overflow-hidden bg-slate-50 touch-none flex justify-center p-1">
                        <canvas id="jurusanCanvas" width="420" height="180" class="cursor-crosshair bg-white rounded-xl shadow-inner w-full max-w-[420px] h-[150px] sm:h-[180px] block"></canvas>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <button type="button" onclick="clearCanvas('jurusan')" class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold flex items-center gap-1.5 transition-colors">
                            <i class="bi bi-eraser text-xs"></i>
                            <span>Bersihkan</span>
                        </button>
                        <label class="flex items-center gap-1.5 text-[11px] text-slate-600 cursor-pointer">
                            <input type="checkbox" name="save_signature_profile" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span>Simpan ke profil akun ({{ Auth::user()->isStaffSarpras() ? Auth::user()->name : $jurusanAccountName }})</span>
                        </label>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" onclick="submitSignature('jurusan', event)" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-200 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                        <i class="bi bi-check2"></i>
                        <span>Simpan &amp; Verifikasi Tanda Tangan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif



<!-- MODAL KONFIRMASI PEMBATALAN TANDA TANGAN BERITA ACARA (SESUAI TEMA APLIKASI) -->
<div id="cancelReportSignatureModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity" role="dialog" aria-modal="true" aria-labelledby="cancelReportModalTitle">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200 transform transition-all animate-in fade-in zoom-in-95 duration-150">
        <!-- Accent Top Stripe -->
        <div class="h-1.5 bg-gradient-to-r from-rose-600 via-amber-500 to-amber-400"></div>

        <div class="p-6 text-center">
            <!-- Close Button in corner -->
            <button type="button" onclick="closeCancelReportSignatureModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors" aria-label="Tutup">
                <i class="bi bi-x-lg text-xs"></i>
            </button>

            <!-- Icon with urgency ring -->
            <div class="mx-auto mb-4 w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center shadow-xs">
                <i class="bi bi-arrow-counterclockwise text-2xl"></i>
            </div>

            <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" id="cancelReportModalTitle">Batalkan Tanda Tangan</h3>
            <p class="text-xs sm:text-sm text-slate-500 mb-4 leading-relaxed" id="cancelReportModalDescription">
                Apakah Anda yakin ingin membatalkan tanda tangan ini?
            </p>

            <!-- Warning Detail Box -->
            <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-200/80 text-left mb-5 text-xs text-rose-800 leading-relaxed flex items-start gap-2.5">
                <i class="bi bi-exclamation-triangle-fill text-rose-500 text-sm shrink-0 mt-0.5"></i>
                <div class="space-y-0.5">
                    <p class="font-bold text-rose-900">Perhatian:</p>
                    <p id="cancelReportModalSubtext" class="text-rose-700">Tanda tangan digital akan dihapus dari dokumen Berita Acara ini.</p>
                </div>
            </div>

            <form id="cancelReportSignatureForm" method="POST" action="" class="m-0">
                @csrf
                @method('DELETE')
                <div class="grid grid-cols-2 gap-3 pt-1">
                    <button type="button" onclick="closeCancelReportSignatureModal()" 
                            class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition-all active:scale-95">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-200 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                        <i class="bi bi-arrow-counterclockwise text-sm"></i>
                        <span id="cancelReportModalBtnText">Ya, Batalkan TTD</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Logic Canvas Signature Pad
let isDrawing = false;
let sarprasDrawn = false;
let jurusanDrawn = false;

function setupCanvas(canvasId, type) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    ctx.strokeStyle = '#0f172a';
    ctx.lineWidth = 2.5;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';

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
        if (type === 'sarpras') sarprasDrawn = true;
        else if (type === 'jurusan') jurusanDrawn = true;
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
    setupCanvas('sarprasCanvas', 'sarpras');
    setupCanvas('jurusanCanvas', 'jurusan');
});

function clearCanvas(type) {
    const canvasId = type === 'sarpras' ? 'sarprasCanvas' : 'jurusanCanvas';
    const canvas = document.getElementById(canvasId);
    if (canvas) {
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        if (type === 'sarpras') sarprasDrawn = false;
        else if (type === 'jurusan') jurusanDrawn = false;
    }
}

function openSignatureModal(type) {
    if (type === 'sarpras') {
        const modal = document.getElementById('sarprasSignModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    } else if (type === 'jurusan') {
        const modal = document.getElementById('jurusanSignModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }
}

function closeSignatureModal(type) {
    if (type === 'sarpras') {
        const modal = document.getElementById('sarprasSignModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    } else if (type === 'jurusan') {
        const modal = document.getElementById('jurusanSignModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
}

function toggleReportSigMode(type) {
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

function submitSignature(type, event) {
    const radio = document.querySelector(`input[name="${type}_sig_choice"]:checked`);
    const useSaved = radio ? (radio.value === 'saved') : false;

    let formId = type === 'jurusan' ? 'jurusanSignForm' : 'sarprasSignForm';

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
        return; // langsung submit form menggunakan ttd tersimpan
    }

    if (type === 'sarpras') {
        if (!sarprasDrawn) {
            event.preventDefault();
            alert('Silakan goreskan tanda tangan Anda pada canvas terlebih dahulu.');
            return;
        }
        const dataUrl = document.getElementById('sarprasCanvas').toDataURL('image/png');
        document.getElementById('sarprasSignatureData').value = dataUrl;
    } else if (type === 'jurusan') {
        if (!jurusanDrawn) {
            event.preventDefault();
            alert('Silakan goreskan tanda tangan pihak Jurusan pada canvas terlebih dahulu.');
            return;
        }
        const dataUrl = document.getElementById('jurusanCanvas').toDataURL('image/png');
        document.getElementById('jurusanSignatureData').value = dataUrl;
    }
}

function confirmCancelReportSignature(type) {
    const modal = document.getElementById('cancelReportSignatureModal');
    const form = document.getElementById('cancelReportSignatureForm');
    const title = document.getElementById('cancelReportModalTitle');
    const desc = document.getElementById('cancelReportModalDescription');
    const subtext = document.getElementById('cancelReportModalSubtext');
    const btnText = document.getElementById('cancelReportModalBtnText');

    const baseUrl = "{{ route('official-reports.cancel-signature', $officialReport) }}";
    form.action = `${baseUrl}?type=${type}`;

    if (type === 'pihak_pertama') {
        title.textContent = 'Batalkan Tanda Tangan Pihak Pertama';
        desc.textContent = 'Apakah Anda yakin ingin membatalkan tanda tangan Pihak Pertama (Sarpras) pada Berita Acara ini?';
        subtext.textContent = 'Tanda tangan akan dihapus dari Berita Acara ini. Anda dapat membubuhkan tanda tangan kembali sewaktu-waktu.';
        btnText.textContent = 'Ya, Batalkan TTD Sarpras';
    } else if (type === 'pihak_kedua') {
        title.textContent = 'Batalkan Tanda Tangan Pihak Kedua';
        desc.textContent = 'Apakah Anda yakin ingin membatalkan tanda tangan Pihak Kedua (Jurusan) pada Berita Acara ini?';
        subtext.textContent = 'Tanda tangan jurusan akan dihapus dari Berita Acara ini. Pihak jurusan dapat membubuhkan tanda tangan kembali sewaktu-waktu.';
        btnText.textContent = 'Ya, Batalkan TTD Jurusan';
    }

    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeCancelReportSignatureModal() {
    const modal = document.getElementById('cancelReportSignatureModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}
</script>
@endpush
@endsection
