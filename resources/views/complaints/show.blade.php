@extends('layouts.app')

@section('title', 'Tindak Lanjut Pengaduan - ' . $complaint->ticket_code)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <a href="{{ route('complaints.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors mb-3">
            <i class="bi bi-arrow-left text-sm"></i>
            <span>Kembali ke Daftar Pengaduan</span>
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">{{ $complaint->judul_kendala }}</h1>
                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $complaint->ticket_code }}
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/60">
                        {{ $complaint->jurusan->nama ?? 'Sarpras Umum' }}
                    </span>
                </div>
            </div>
            <div>
                @if($complaint->status === 'menunggu')
                    @if($complaint->tingkat_urgensi === 'tinggi_darurat')
                        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-300 shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                            <i class="bi bi-exclamation-octagon-fill text-rose-600"></i>
                            <span>Status: Menunggu (Prioritas Darurat KBM)</span>
                        </span>
                    @elseif($complaint->tingkat_urgensi === 'sedang')
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            <i class="bi bi-hourglass-split text-amber-500"></i>
                            <span>Status: Menunggu Penanganan (Sedang)</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                            <i class="bi bi-clock text-slate-400"></i>
                            <span>Status: Menunggu Penanganan (Rendah)</span>
                        </span>
                    @endif
                @elseif($complaint->status === 'diproses')
                    @if($complaint->tingkat_urgensi === 'tinggi_darurat')
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300 shadow-2xs">
                            <i class="bi bi-tools text-rose-600"></i>
                            <span>Status: Sedang Ditangani (Prioritas Darurat)</span>
                        </span>
                    @elseif($complaint->tingkat_urgensi === 'sedang')
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                            <i class="bi bi-tools text-amber-600"></i>
                            <span>Status: Sedang Ditangani (Sedang)</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            <i class="bi bi-tools"></i>
                            <span>Status: Sedang Ditangani</span>
                        </span>
                    @endif
                @elseif($complaint->status === 'selesai')
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <i class="bi bi-check-circle-fill text-emerald-600"></i>
                        <span>Status: Tuntas Selesai</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        <i class="bi bi-x-circle text-slate-400"></i>
                        <span>Status: Ditolak</span>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- DETAIL KELUHAN DARI GURU -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="bi bi-file-earmark-text text-blue-600"></i>
                        <span>Rincian Laporan Kendala Fasilitas</span>
                    </h2>
                </div>
                <div class="divide-y divide-slate-100 text-xs sm:text-sm">
                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <span class="text-slate-500 sm:w-44 font-medium">Nama Pelapor</span>
                        <span class="font-bold text-slate-900 sm:text-right flex-1">{{ $complaint->nama_pelapor }}</span>
                    </div>

                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <span class="text-slate-500 sm:w-44 font-medium">Kontak WhatsApp</span>
                        <div class="sm:text-right flex-1">
                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $complaint->kontak)) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-mono font-semibold text-xs border border-emerald-200/60 transition-colors">
                                <i class="bi bi-whatsapp"></i>
                                <span>{{ $complaint->kontak }} (Hubungi Pelapor)</span>
                            </a>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <span class="text-slate-500 sm:w-44 font-medium">Waktu Lapor</span>
                        <span class="text-slate-800 sm:text-right flex-1">
                            {{ $complaint->created_at->format('d F Y, H:i') }} WIB 
                            <span class="text-slate-400 text-xs">({{ $complaint->created_at->diffForHumans() }})</span>
                        </span>
                    </div>

                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <span class="text-slate-500 sm:w-44 font-medium">Lokasi Spesifik Ruang</span>
                        <span class="font-bold text-rose-600 sm:text-right flex-1">{{ $complaint->lokasi_ruang }}</span>
                    </div>

                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <span class="text-slate-500 sm:w-44 font-medium">Kategori Kendala</span>
                        <div class="sm:text-right flex-1">
                            @if($complaint->kategori === 'komputer_it')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/60">
                                    Komputer &amp; Lab IT
                                </span>
                            @elseif($complaint->kategori === 'kelistrikan')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    Kelistrikan &amp; Daya
                                </span>
                            @elseif($complaint->kategori === 'mesin_peralatan')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-cyan-50 text-cyan-700 border border-cyan-200">
                                    Mesin Bengkel
                                </span>
                            @elseif($complaint->kategori === 'sarana_gedung')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    Sarana Gedung &amp; Kelas
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    Lain-lain
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <span class="text-slate-500 sm:w-44 font-medium">Tingkat Urgensi</span>
                        <div class="sm:text-right flex-1">
                            @if($complaint->tingkat_urgensi === 'tinggi_darurat')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <span>Darurat (Mengganggu KBM)</span>
                                </span>
                            @elseif($complaint->tingkat_urgensi === 'sedang')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    Sedang
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    Rendah
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($complaint->item)
                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <span class="text-slate-500 sm:w-44 font-medium">Barang Inventaris Terkait</span>
                            <div class="sm:text-right flex-1">
                                <a href="{{ route('items.show', $complaint->item_id) }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                                    [{{ $complaint->item->kode_barang }}] {{ $complaint->item->nama_barang }}
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="p-4 sm:p-5">
                        <span class="block text-slate-500 font-medium mb-1.5">Uraian Gejala Kendala</span>
                        <div class="p-3.5 bg-slate-50 rounded-xl text-slate-800 text-xs sm:text-sm whitespace-pre-line leading-relaxed border border-slate-200/60">
                            {{ $complaint->deskripsi }}
                        </div>
                    </div>

                    @if($complaint->foto)
                        <div class="p-4 sm:p-5">
                            <span class="block text-slate-500 font-medium mb-2">Foto Bukti Lampiran</span>
                            <a href="{{ asset('storage/' . $complaint->foto) }}" target="_blank" class="block group relative rounded-xl overflow-hidden border border-slate-200 shadow-xs max-w-sm">
                                <img src="{{ asset('storage/' . $complaint->foto) }}" alt="Bukti Kendala" class="w-full max-h-64 object-cover group-hover:scale-105 transition-transform duration-200">
                                <div class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-medium transition-opacity">
                                    <i class="bi bi-arrows-fullscreen mr-1.5"></i> Perbesar Foto
                                </div>
                            </a>
                            <span class="block text-[11px] text-slate-400 mt-1.5">Klik gambar untuk melihat ukuran penuh.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- FORM TINDAK LANJUT TEKNISI / PETUGAS -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center">
                        <i class="bi bi-pencil-square text-sm"></i>
                    </span>
                    <h2 class="text-sm font-bold text-slate-900">Form Tindak Lanjut Petugas</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('complaints.update', $complaint) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="status" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Status Tindak Lanjut <span class="text-rose-500">*</span>
                            </label>
                            <select name="status" id="status" class="w-full px-3.5 py-2.5 bg-slate-50 border @error('status') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required>
                                <option value="menunggu" {{ old('status', $complaint->status) === 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                <option value="diproses" {{ old('status', $complaint->status) === 'diproses' ? 'selected' : '' }}>Sedang Ditangani Teknisi</option>
                                <option value="selesai" {{ old('status', $complaint->status) === 'selesai' ? 'selected' : '' }}>Tuntas Selesai</option>
                                <option value="ditolak" {{ old('status', $complaint->status) === 'ditolak' ? 'selected' : '' }}>Ditolak / Dibatalkan</option>
                            </select>
                            @error('status')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="teknisi_penanganan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nama Teknisi / Toolman yang Menangani
                            </label>
                            <input type="text" name="teknisi_penanganan" id="teknisi_penanganan" 
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border @error('teknisi_penanganan') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                                   placeholder="Contoh: Bpk. Agus (Toolman TP), Tim IT Sarpras..." 
                                   value="{{ old('teknisi_penanganan', $complaint->teknisi_penanganan) }}">
                            @error('teknisi_penanganan')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tindak_lanjut" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Catatan Solusi / Tindak Lanjut Perbaikan
                            </label>
                            <textarea name="tindak_lanjut" id="tindak_lanjut" rows="4" 
                                      class="w-full px-3.5 py-2.5 bg-slate-50 border @error('tindak_lanjut') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                                      placeholder="Tuliskan tindakan perbaikan yang dilakukan, part yang diganti, atau alasan jika pengaduan ditolak...">{{ old('tindak_lanjut', $complaint->tindak_lanjut) }}</textarea>
                            <span class="block text-[11px] text-slate-400 mt-1">Catatan ini dapat dilihat langsung oleh guru pelapor saat melacak tiket.</span>
                            @error('tindak_lanjut')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xs transition-colors">
                                <i class="bi bi-save"></i>
                                <span>Perbarui Status &amp; Simpan Tindak Lanjut</span>
                            </button>
                        </div>
                    </form>

                    <div class="my-6 border-t border-slate-100"></div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                        <form action="{{ route('complaints.destroy', $complaint) }}" method="POST"
                              data-confirm="Apakah Anda yakin ingin menghapus tiket pengaduan #{{ $complaint->ticket_code }} secara permanen?"
                              data-confirm-title="Hapus Tiket Pengaduan"
                              data-confirm-type="danger"
                              data-confirm-btn="Ya, Hapus Tiket"
                              class="w-full sm:w-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold border border-rose-200/80 transition-colors">
                                <i class="bi bi-trash"></i>
                                <span>Hapus Pengaduan</span>
                            </button>
                        </form>
                        <a href="{{ route('public.track', ['ticket' => $complaint->ticket_code]) }}" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition-colors">
                            <i class="bi bi-eye"></i>
                            <span>Pratinjau Publik</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
