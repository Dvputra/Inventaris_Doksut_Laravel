@extends('layouts.app')

@section('title', 'Audit & Verifikasi Inventaris - SIM Sarpras')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2">
                <span class="p-2 rounded-xl bg-amber-500/10 text-amber-600 border border-amber-500/20">
                    <i class="bi bi-clipboard-check-fill text-xl"></i>
                </span>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Audit &amp; Verifikasi Inventaris</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        @if(Auth::user()->isJurusan())
                            Riwayat audit fisik dan status verifikasi barang unit kerja {{ Auth::user()->jurusan->nama ?? '' }}.
                        @else
                            Pemeriksaan dan verifikasi fisik kondisi aset sekolah secara berkala per unit kerja / laboratorium.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        @if(Auth::user()->isSarprasOrKepalaSekolah())
            <div class="flex items-center gap-2">
                <a href="{{ route('inventory-audits.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all duration-200 active:scale-95">
                    <i class="bi bi-plus-lg text-sm"></i>
                    <span>Mulai Audit Baru</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Statistik Ringkas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Sesi Audit</span>
                <p class="text-2xl font-black text-slate-900 mt-0.5">{{ $totalAudits }}</p>
                <span class="text-[11px] text-slate-500">Tercatat di sistem</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="bi bi-folder2-open"></i>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Audit Berjalan (Draft)</span>
                <p class="text-2xl font-black text-amber-600 mt-0.5">{{ $totalDraft }}</p>
                <span class="text-[11px] text-slate-500">Sedang diverifikasi</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Audit Selesai</span>
                <p class="text-2xl font-black text-emerald-600 mt-0.5">{{ $totalSelesai }}</p>
                <span class="text-[11px] text-slate-500">Terverifikasi &amp; Bertanda tangan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="bi bi-patch-check-fill"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4">
        <form action="{{ route('inventory-audits.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3">
            <div class="sm:col-span-2 md:col-span-5">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Cari Kode / Judul / Auditor</label>
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari kode audit, nama sesi, atau auditor..."
                           class="w-full pl-8 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            @if(!Auth::user()->isJurusan())
                <div class="sm:col-span-1 md:col-span-3">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Unit Kerja / Jurusan</label>
                    <select name="jurusan_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
                        <option value="">-- Semua Unit Kerja --</option>
                        @foreach($jurusans as $j)
                            <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                                {{ $j->kode }} - {{ $j->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="sm:col-span-1 md:col-span-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
                    <option value="">-- Semua Status --</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft (Berjalan)</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai Verifikasi</option>
                    <option value="final" {{ request('status') === 'final' ? 'selected' : '' }}>Final (Lengkap TTD)</option>
                </select>
            </div>

            <div class="sm:col-span-2 md:col-span-2 flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition-all shadow-xs flex items-center justify-center gap-1.5 min-h-[38px]">
                    <i class="bi bi-filter"></i>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'jurusan_id', 'status']))
                    <a href="{{ route('inventory-audits.index') }}" class="py-2 px-3 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all flex items-center justify-center min-h-[38px]" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Riwayat Audit -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">Kode &amp; Sesi Audit</th>
                        <th class="px-4 py-3">Unit Kerja</th>
                        <th class="px-4 py-3 text-center">Tgl Pelaksanaan</th>
                        <th class="px-4 py-3 text-center">Temuan Barang</th>
                        <th class="px-4 py-3 text-center">Status Audit</th>
                        <th class="px-4 py-3 text-center">Tanda Tangan</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($audits as $idx => $audit)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-4 py-3 text-center font-medium text-slate-400">
                                {{ $audits->firstItem() + $idx }}
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('inventory-audits.show', $audit) }}" class="font-bold text-slate-900 hover:text-blue-600 transition-colors block">
                                    {{ $audit->judul }}
                                </a>
                                <span class="font-mono text-[10px] text-blue-600 font-semibold bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">
                                    {{ $audit->audit_code }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-800">{{ $audit->jurusan->nama ?? '-' }}</div>
                                <span class="text-[11px] text-slate-400">PJ: {{ $audit->auditee_nama ?: ($audit->jurusan->kepala_bengkel ?? '-') }}</span>
                            </td>
                            <td class="px-4 py-3 text-center font-mono text-slate-700">
                                {{ $audit->tanggal_audit ? $audit->tanggal_audit->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5 text-[11px]">
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-bold" title="Total Barang Terdata">
                                        {{ $audit->total_barang_tercatat }} item
                                    </span>
                                    @if($audit->total_barang_rusak > 0)
                                        <span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 font-bold" title="Barang Rusak">
                                            {{ $audit->total_barang_rusak }} R
                                        </span>
                                    @endif
                                    @if($audit->total_barang_hilang > 0)
                                        <span class="px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 font-bold" title="Barang Tidak Ditemukan / Hilang">
                                            {{ $audit->total_barang_hilang }} H
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($audit->status === 'final')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="bi bi-patch-check-fill"></i> Final (Lengkap)
                                    </span>
                                @elseif($audit->status === 'selesai')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="bi bi-check-circle"></i> Selesai Verifikasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="bi bi-pencil"></i> Sedang Dicek (Draft)
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1 text-[11px]">
                                    <span class="inline-block px-1.5 py-0.5 rounded {{ $audit->ttd_auditor ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-400' }}" title="TTD Auditor Sarpras">
                                        Sarpras: {{ $audit->ttd_auditor ? '✓' : '—' }}
                                    </span>
                                    <span class="inline-block px-1.5 py-0.5 rounded {{ $audit->ttd_auditee ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-400' }}" title="TTD Penanggung Jawab Unit Kerja">
                                        Unit: {{ $audit->ttd_auditee ? '✓' : '—' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('inventory-audits.show', $audit) }}" 
                                       class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 border border-transparent hover:border-blue-200 transition-all" 
                                       title="Lihat / Verifikasi Lembar Audit">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('inventory-audits.print', $audit) }}" target="_blank"
                                       class="p-1.5 rounded-lg text-slate-600 hover:bg-slate-100 border border-transparent hover:border-slate-200 transition-all" 
                                       title="Cetak Berita Acara Audit A4">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    @if(Auth::user()->isSarprasOrKepalaSekolah() && $audit->status === 'draft')
                                        <button type="button" 
                                                onclick="openDeleteAuditModal('{{ route('inventory-audits.destroy', $audit) }}', '{{ $audit->audit_code }}', '{{ addslashes($audit->judul) }}')"
                                                class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-200 transition-all" title="Hapus Audit">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                                <i class="bi bi-clipboard-x text-3xl block mb-2 text-slate-300"></i>
                                Belum ada riwayat sesi audit inventaris yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($audits->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $audits->links() }}
            </div>
        @endif
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS AUDIT (SESUAI TEMA APLIKASI) -->
<div id="deleteAuditModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity" role="dialog" aria-modal="true">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200 transform transition-all">
        <!-- Accent Top Stripe -->
        <div class="h-1.5 bg-gradient-to-r from-rose-600 via-amber-500 to-rose-400"></div>

        <div class="p-6 text-center">
            <button type="button" onclick="closeDeleteAuditModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                <i class="bi bi-x-lg text-xs"></i>
            </button>

            <!-- Icon Ring -->
            <div class="mx-auto mb-4 w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center shadow-xs">
                <i class="bi bi-trash3 text-2xl"></i>
            </div>

            <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5">Hapus Sesi Audit</h3>
            <p class="text-xs sm:text-sm text-slate-500 mb-4 leading-relaxed">
                Apakah Anda yakin ingin menghapus arsip audit <strong id="deleteAuditCodeText" class="text-slate-800"></strong>?
            </p>

            <!-- Warning Box -->
            <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-200/80 text-left mb-5 text-xs text-rose-800 leading-relaxed flex items-start gap-2.5">
                <i class="bi bi-exclamation-triangle-fill text-rose-500 text-sm shrink-0 mt-0.5"></i>
                <div class="space-y-0.5">
                    <p class="font-bold text-rose-900">Perhatian:</p>
                    <p id="deleteAuditTitleText" class="text-rose-700">Seluruh checklist pemeriksaan barang di sesi audit ini akan dihapus secara permanen.</p>
                </div>
            </div>

            <form id="deleteAuditForm" method="POST" action="" class="m-0">
                @csrf
                @method('DELETE')
                <div class="grid grid-cols-2 gap-3 pt-1">
                    <button type="button" onclick="closeDeleteAuditModal()" 
                            class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition-all active:scale-95">
                        Batal
                    </button>
                    <button type="submit" 
                            class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-200 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                        <i class="bi bi-trash3 text-sm"></i>
                        <span>Ya, Hapus Audit</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openDeleteAuditModal(actionUrl, code, title) {
    const modal = document.getElementById('deleteAuditModal');
    const form = document.getElementById('deleteAuditForm');
    const codeEl = document.getElementById('deleteAuditCodeText');
    const titleEl = document.getElementById('deleteAuditTitleText');

    if (modal && form) {
        form.action = actionUrl;
        if (codeEl) codeEl.textContent = code;
        if (titleEl) titleEl.textContent = 'Seluruh checklist barang pada "' + title + '" akan dihapus secara permanen.';
        modal.classList.remove('hidden');
    }
}

function closeDeleteAuditModal() {
    const modal = document.getElementById('deleteAuditModal');
    if (modal) modal.classList.add('hidden');
}

// Close on backdrop click
document.getElementById('deleteAuditModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDeleteAuditModal();
});
</script>
@endpush
@endsection
