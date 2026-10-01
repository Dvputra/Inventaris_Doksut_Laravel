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
                <span class="text-xs font-medium text-slate-400">|</span>
                @if($officialReport->status_approval === 'disetujui')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        <i class="bi bi-check-circle-fill text-xs"></i>
                        <span>Disetujui (ACC) Kepala Sekolah</span>
                    </span>
                @elseif($officialReport->status_approval === 'ditolak')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                        <i class="bi bi-x-circle-fill text-xs"></i>
                        <span>Ditolak / Perlu Revisi</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        <i class="bi bi-clock-history text-xs"></i>
                        <span>Menunggu Persetujuan (ACC) Kepala Sekolah</span>
                    </span>
                @endif
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 mt-2">{{ $officialReport->judul }}</h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('official-reports.print', $officialReport) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xs transition-colors">
                <i class="bi bi-printer text-base"></i>
                <span>Cetak Surat Dinas</span>
            </a>
            @if(Auth::user()->isSarpras())
                <form action="{{ route('official-reports.destroy', $officialReport) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip berita acara ini?');" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center p-2.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-sm transition-colors" title="Hapus Dokumen">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Banner Evaluasi / Catatan Approval jika ada -->
    @if($officialReport->catatan_approval)
        <div class="p-4 rounded-2xl {{ $officialReport->status_approval === 'disetujui' ? 'bg-emerald-50 border border-emerald-200 text-emerald-900' : 'bg-rose-50 border border-rose-200 text-rose-900' }}">
            <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider mb-1">
                <i class="bi {{ $officialReport->status_approval === 'disetujui' ? 'bi-chat-left-check-fill text-emerald-600' : 'bi-exclamation-octagon-fill text-rose-600' }}"></i>
                <span>Catatan Kepala Sekolah ({{ $officialReport->approver->name ?? 'Kepala Sekolah' }}):</span>
            </div>
            <p class="text-xs sm:text-sm font-medium leading-relaxed">{{ $officialReport->catatan_approval }}</p>
            @if($officialReport->approved_at)
                <div class="text-[11px] opacity-75 mt-1">Pada: {{ $officialReport->approved_at->translatedFormat('d F Y, H:i') }} WIB</div>
            @endif
        </div>
    @endif

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
            <!-- 1. Tanda Tangan Pihak Pertama (Sarpras) -->
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

                @if(Auth::user()->isStaffSarpras() && ! $officialReport->ttd_pihak_pertama)
                    <button type="button" onclick="openSignatureModal('sarpras')" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition-colors flex items-center justify-center gap-1.5">
                        <i class="bi bi-pen"></i>
                        <span>Bubuhkan Tanda Tangan Sarpras Sekarang</span>
                    </button>
                @endif
            </div>

            <!-- 2. Tanda Tangan & ACC Mengetahui (Kepala Sekolah) -->
            <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Mengetahui (Kepala Sekolah)</span>
                        @if($officialReport->status_approval === 'disetujui')
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                <i class="bi bi-check-circle-fill"></i> Di-ACC &amp; TTD
                            </span>
                        @elseif($officialReport->status_approval === 'ditolak')
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200">
                                <i class="bi bi-x-circle-fill"></i> Ditolak
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                                Menunggu ACC
                            </span>
                        @endif
                    </div>
                    <p class="text-sm font-bold text-slate-900">{{ $officialReport->mengetahui_nama }}</p>
                    <p class="text-xs text-slate-600">{{ $officialReport->mengetahui_jabatan }}</p>
                </div>

                <div class="my-4 text-center">
                    @if($officialReport->ttd_mengetahui)
                        <div class="inline-block p-2 bg-white rounded-xl border border-slate-200 shadow-2xs">
                            <img src="{{ Storage::url($officialReport->ttd_mengetahui) }}" alt="TTD Kepsek" class="h-24 max-w-[200px] object-contain mx-auto">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Ditandatangani: {{ $officialReport->ttd_mengetahui_at ? $officialReport->ttd_mengetahui_at->translatedFormat('d M Y, H:i') : '-' }}</p>
                    @else
                        <div class="h-24 flex flex-col items-center justify-center border-2 border-dashed border-slate-300 rounded-xl text-slate-400 text-xs">
                            <i class="bi bi-shield-check text-xl mb-1"></i>
                            <span>Menunggu pengesahan Kepala Sekolah</span>
                        </div>
                    @endif
                </div>

                <!-- Tombol Aksi Kepala Sekolah -->
                @if(Auth::user()->isKepalaSekolah() && $officialReport->status_approval !== 'disetujui')
                    <div class="flex gap-2">
                        <button type="button" onclick="openKepsekApprovalModal()" class="flex-1 py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-colors flex items-center justify-center gap-1.5">
                            <i class="bi bi-check2-circle"></i>
                            <span>ACC &amp; Tanda Tangan</span>
                        </button>
                        <button type="button" onclick="openKepsekRejectModal()" class="py-2.5 px-3 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition-colors" title="Tolak / Minta Revisi">
                            Tolak
                        </button>
                    </div>
                @endif
            </div>
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

<!-- MODAL TTD SARPRAS -->
<div id="sarprasSignModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-5 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="bi bi-pen-fill text-blue-600"></i>
                <span>Tanda Tangan Pihak Pertama (Sarpras)</span>
            </h3>
            <button type="button" onclick="closeSignatureModal('sarpras')" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>
        <p class="text-xs text-slate-500">Gunakan jari (pada layar sentuh HP) atau kursor mouse/touchpad untuk menandatangani di dalam kotak berikut:</p>
        
        <div class="border-2 border-dashed border-slate-300 rounded-xl overflow-hidden bg-slate-50 touch-none flex justify-center">
            <canvas id="sarprasCanvas" width="380" height="180" class="cursor-crosshair bg-white"></canvas>
        </div>

        <div class="flex items-center justify-between">
            <button type="button" onclick="clearCanvas('sarpras')" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                <i class="bi bi-eraser me-1"></i> Bersihkan Canvas
            </button>
            <form action="{{ route('official-reports.sign-pihak-pertama', $officialReport) }}" method="POST" id="sarprasSignForm">
                @csrf
                @method('PATCH')
                <input type="hidden" name="signature_data" id="sarprasSignatureData">
                <button type="submit" onclick="submitSignature('sarpras', event)" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs">
                    Simpan Tanda Tangan
                </button>
            </form>
        </div>
    </div>
</div>

<!-- MODAL ACC & TTD KEPALA SEKOLAH -->
<div id="kepsekApprovalModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full p-5 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="bi bi-shield-check text-emerald-600"></i>
                <span>Persetujuan (ACC) &amp; TTD Kepala Sekolah</span>
            </h3>
            <button type="button" onclick="closeKepsekApprovalModal()" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form action="{{ route('official-reports.approve', $officialReport) }}" method="POST" id="kepsekApproveForm" class="space-y-4">
            @csrf
            @method('PATCH')
            <input type="hidden" name="signature_data" id="kepsekSignatureData">

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Persetujuan (Opsional)</label>
                <textarea name="catatan_approval" rows="2" placeholder="Contoh: Disetujui untuk dihapuskan dari daftar inventaris aktif..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Goreskan Tanda Tangan Digital Kepala Sekolah:</label>
                <div class="border-2 border-dashed border-emerald-300 rounded-xl overflow-hidden bg-slate-50 touch-none flex justify-center">
                    <canvas id="kepsekCanvas" width="420" height="180" class="cursor-crosshair bg-white"></canvas>
                </div>
            </div>

            <div class="flex items-center justify-between pt-1">
                <button type="button" onclick="clearCanvas('kepsek')" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                    <i class="bi bi-eraser me-1"></i> Bersihkan Canvas
                </button>
                <button type="submit" onclick="submitSignature('kepsek', event)" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20">
                    <i class="bi bi-check2-circle me-1"></i> ACC &amp; Sahkan Dokumen
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TOLAK KEPALA SEKOLAH -->
<div id="kepsekRejectModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-5 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-rose-700 flex items-center gap-2">
                <i class="bi bi-x-circle-fill"></i>
                <span>Tolak / Minta Revisi Berita Acara</span>
            </h3>
            <button type="button" onclick="closeKepsekRejectModal()" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form action="{{ route('official-reports.reject', $officialReport) }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Penolakan / Catatan Revisi <span class="text-rose-500">*</span></label>
                <textarea name="catatan_approval" rows="3" required placeholder="Jelaskan alasan mengapa berita acara ini ditolak atau data apa yang harus diperbaiki..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2">
                <button type="button" onclick="closeKepsekRejectModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs">
                    Kirim Penolakan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Logic Canvas Signature Pad
let isDrawing = false;
let sarprasDrawn = false;
let kepsekDrawn = false;

function setupCanvas(canvasId, isSarpras) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    ctx.strokeStyle = '#0f172a';
    ctx.lineWidth = 2.5;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        if (e.touches && e.touches[0]) {
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
        e.preventDefault();
        isDrawing = true;
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
        if (isSarpras) sarprasDrawn = true;
        else kepsekDrawn = true;
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
    setupCanvas('sarprasCanvas', true);
    setupCanvas('kepsekCanvas', false);
});

function clearCanvas(type) {
    const canvasId = type === 'sarpras' ? 'sarprasCanvas' : 'kepsekCanvas';
    const canvas = document.getElementById(canvasId);
    if (canvas) {
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        if (type === 'sarpras') sarprasDrawn = false;
        else kepsekDrawn = false;
    }
}

function openSignatureModal(type) {
    if (type === 'sarpras') {
        const modal = document.getElementById('sarprasSignModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeSignatureModal(type) {
    if (type === 'sarpras') {
        const modal = document.getElementById('sarprasSignModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function openKepsekApprovalModal() {
    const modal = document.getElementById('kepsekApprovalModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeKepsekApprovalModal() {
    const modal = document.getElementById('kepsekApprovalModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function openKepsekRejectModal() {
    const modal = document.getElementById('kepsekRejectModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeKepsekRejectModal() {
    const modal = document.getElementById('kepsekRejectModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function submitSignature(type, event) {
    if (type === 'sarpras') {
        if (!sarprasDrawn) {
            event.preventDefault();
            alert('Silakan goreskan tanda tangan Anda pada canvas terlebih dahulu.');
            return;
        }
        const dataUrl = document.getElementById('sarprasCanvas').toDataURL('image/png');
        document.getElementById('sarprasSignatureData').value = dataUrl;
    } else {
        if (!kepsekDrawn) {
            event.preventDefault();
            alert('Silakan goreskan tanda tangan Kepala Sekolah pada canvas terlebih dahulu.');
            return;
        }
        const dataUrl = document.getElementById('kepsekCanvas').toDataURL('image/png');
        document.getElementById('kepsekSignatureData').value = dataUrl;
    }
}
</script>
@endpush
@endsection
