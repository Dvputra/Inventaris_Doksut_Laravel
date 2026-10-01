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
            <div class="flex items-center gap-2.5">
                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $officialReport->jenis === 'barang_rusak' ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                    {{ $officialReport->jenis_label }}
                </span>
                <span class="text-xs font-medium text-slate-400">|</span>
                <span class="text-xs font-semibold text-slate-600">No. {{ $officialReport->nomor_surat }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 mt-1">{{ $officialReport->judul }}</h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('official-reports.print', $officialReport) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xs transition-colors">
                <i class="bi bi-printer text-base"></i>
                <span>Cetak Surat Dinas</span>
            </a>
            <form action="{{ route('official-reports.destroy', $officialReport) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip berita acara ini?');" class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center p-2.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-sm transition-colors" title="Hapus Dokumen">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
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
                <span class="text-sm font-bold text-slate-800 mt-0.5 block">{{ $officialReport->user->name }}</span>
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-700 block mb-1">Mengetahui / Mengesahkan</span>
                <p class="text-sm font-bold text-slate-900">{{ $officialReport->mengetahui_nama }}</p>
                <p class="text-xs text-slate-600">{{ $officialReport->mengetahui_jabatan }}</p>
                @if($officialReport->mengetahui_nip)
                    <p class="text-[11px] text-slate-400 mt-1">NIP: {{ $officialReport->mengetahui_nip }}</p>
                @endif
            </div>
        </div>

        <!-- Latar Belakang & Catatan -->
        @if($officialReport->latar_belakang)
            <div class="p-4 rounded-xl bg-slate-50/50 border border-slate-200/60">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-700 block mb-1">Latar Belakang / Dasar Pemeriksaan:</span>
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

        <div class="overflow-x-auto">
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
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ in_array($item->kondisi_saat_lapor, ['rusak_berat', 'rusak_total', 'hilang']) ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ ucwords(str_replace('_', ' ', $item->kondisi_saat_lapor)) }}
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
    </div>
</div>
@endsection
