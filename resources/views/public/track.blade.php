<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Status Pengaduan - SMK Dr. Sutomo Temanggung</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS & Icons CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#0f172a',
                        },
                        accent: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans bg-slate-50 text-slate-800 min-h-screen flex flex-col">

    <!-- Navbar Putih Biru Bersih -->
    <nav class="bg-white border-b border-slate-200/80 sticky top-0 z-30 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo SMK Dr. Sutomo" class="w-10 h-10 object-contain">
                <div>
                    <span class="font-extrabold text-blue-700 text-sm tracking-tight block leading-tight">SMK Dr. SUTOMO</span>
                    <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">TEMANGGUNG</span>
                </div>
            </a>
            <div class="flex items-center gap-1.5 sm:gap-3">
                <a href="{{ route('welcome') }}" class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-full text-xs font-semibold text-slate-600 hover:text-blue-600 border border-slate-200 hover:bg-slate-50 transition-colors">
                    <i class="bi bi-arrow-left"></i>
                    <span class="hidden sm:inline">Kembali ke Beranda</span>
                    <span class="sm:hidden">Beranda</span>
                </a>
                <a href="{{ route('welcome') }}?action=lapor" class="inline-flex items-center gap-1.5 px-3 sm:px-3.5 py-1.5 rounded-full text-xs font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 shadow-xs transition-colors">
                    <i class="bi bi-pencil-square"></i>
                    <span class="hidden sm:inline">Lapor Kendala Baru</span>
                    <span class="sm:hidden">Lapor</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-8 flex-1 w-full space-y-6">

        <!-- Notifikasi Sukses Pembuatan Laporan Baru -->
        @if(session('complaint_success'))
            @php $cs = session('complaint_success'); @endphp
            <div class="bg-white rounded-2xl border border-emerald-200 p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-check-lg text-2xl font-bold"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-emerald-800">Pengaduan Berhasil Dikirimkan!</h2>
                        <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                            Terima kasih <strong>{{ $cs['nama'] }}</strong>. Laporan kendala <em>"{{ $cs['judul'] }}"</em> telah diterima oleh tim Sarpras sekolah.
                        </p>
                    </div>
                </div>
                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 text-left md:text-right shrink-0">
                    <span class="block font-mono text-[10px] text-slate-400 font-bold uppercase tracking-wider">KODE TIKET ANDA:</span>
                    <span class="font-mono text-lg font-black text-blue-600">{{ $cs['ticket_code'] }}</span>
                    <span class="block text-[11px] text-slate-500 mt-0.5">Gunakan kode ini untuk memantau progres.</span>
                </div>
            </div>
        @endif

        <!-- Pencarian Tiket Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6">
            <div class="flex items-center gap-2.5 mb-2">
                <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="bi bi-search text-sm"></i>
                </span>
                <h1 class="text-base sm:text-lg font-bold text-slate-900">Lacak Status Penanganan Kendala</h1>
            </div>
            <p class="text-xs text-slate-500 mb-4">Masukkan Kode Tiket atau Nomor WhatsApp yang Anda gunakan saat melapor:</p>
            <form action="{{ route('public.track') }}" method="GET" class="flex flex-col sm:flex-row gap-2.5">
                <input type="text" name="ticket" class="flex-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono uppercase text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                       placeholder="Contoh: ADU-2609-XXXX / 081234567890" 
                       value="{{ $ticketCode }}" required>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xs transition-colors shrink-0">
                    <i class="bi bi-search"></i>
                    <span>Lacak</span>
                </button>
            </form>
        </div>

        @if($complaint)
            <!-- Hasil Pelacakan Tiket -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
                <!-- Header Info -->
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-5 border-b border-slate-100">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-2">
                            TIKET: {{ $complaint->ticket_code }}
                        </span>
                        <h2 class="text-xl font-bold text-slate-900 leading-snug">{{ $complaint->judul_kendala }}</h2>
                        <div class="flex flex-wrap items-center gap-2 mt-2 text-xs text-slate-500">
                            <span class="inline-flex items-center gap-1 text-slate-600">
                                <i class="bi bi-geo-alt-fill text-blue-600"></i>
                                <span>{{ $complaint->lokasi_ruang }}</span>
                            </span>
                            @if($complaint->jurusan)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md font-semibold bg-slate-100 text-slate-700">
                                    {{ $complaint->jurusan->nama }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="sm:text-right shrink-0">
                        @if($complaint->status === 'menunggu')
                            @if($complaint->tingkat_urgensi === 'tinggi_darurat')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-300 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                                    <i class="bi bi-exclamation-octagon-fill text-rose-600"></i>
                                    <span>Menunggu (Darurat KBM)</span>
                                </span>
                            @elseif($complaint->tingkat_urgensi === 'sedang')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <i class="bi bi-clock-history text-amber-500"></i>
                                    <span>Menunggu Verifikasi (Sedang)</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    <i class="bi bi-clock text-slate-400"></i>
                                    <span>Menunggu Verifikasi (Rendah)</span>
                                </span>
                            @endif
                        @elseif($complaint->status === 'diproses')
                            @if($complaint->tingkat_urgensi === 'tinggi_darurat')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                    <i class="bi bi-tools text-rose-600"></i>
                                    <span>Sedang Ditangani (Prioritas Darurat)</span>
                                </span>
                            @elseif($complaint->tingkat_urgensi === 'sedang')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                    <i class="bi bi-tools text-amber-600"></i>
                                    <span>Sedang Ditangani (Sedang)</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    <i class="bi bi-tools"></i>
                                    <span>Sedang Ditangani</span>
                                </span>
                            @endif
                        @elseif($complaint->status === 'selesai')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="bi bi-check2-circle text-emerald-600"></i>
                                <span>Tuntas Selesai</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                <i class="bi bi-x-circle"></i>
                                <span>Ditolak</span>
                            </span>
                        @endif
                        <span class="block text-[11px] text-slate-400 mt-1.5">
                            Dibuat {{ $complaint->created_at->format('d M Y, H:i') }} WIB
                        </span>
                    </div>
                </div>

                <!-- Timeline Progres Penanganan -->
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 mb-4">
                        <i class="bi bi-activity text-blue-600"></i>
                        <span>Tahapan Penanganan Teknisi</span>
                    </h3>

                    <div class="relative pl-8 space-y-6 before:absolute before:left-3.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                        <!-- Step 1 -->
                        <div class="relative">
                            <span class="absolute -left-8 top-0.5 w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold ring-4 ring-white">
                                <i class="bi bi-check-lg"></i>
                            </span>
                            <div>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900">1. Laporan Pengaduan Diterima</h4>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Laporan didaftarkan oleh <strong>{{ $complaint->nama_pelapor }}</strong> pada {{ $complaint->created_at->format('d/m/Y H:i') }} WIB.
                                </p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="relative">
                            @if(in_array($complaint->status, ['diproses', 'selesai']))
                                <span class="absolute -left-8 top-0.5 w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold ring-4 ring-white">
                                    <i class="bi bi-tools"></i>
                                </span>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900">2. Peninjauan &amp; Penanganan Teknisi</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Teknisi / Toolman yang ditugaskan: <strong>{{ $complaint->teknisi_penanganan ?: 'Tim Sarpras & Teknisi Jurusan' }}</strong>.
                                    </p>
                                </div>
                            @else
                                <span class="absolute -left-8 top-0.5 w-7 h-7 rounded-full bg-slate-100 text-slate-400 border border-slate-300 flex items-center justify-center text-xs font-bold ring-4 ring-white">
                                    <i class="bi bi-hourglass-split"></i>
                                </span>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-400">2. Menunggu Peninjauan Teknisi</h4>
                                    <p class="text-xs text-slate-400 mt-0.5">Tim Sarpras sedang mengalokasikan teknisi/toolman ke lokasi fasilitas.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Step 3 -->
                        <div class="relative">
                            @if($complaint->status === 'selesai')
                                <span class="absolute -left-8 top-0.5 w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold ring-4 ring-white">
                                    <i class="bi bi-check2-all"></i>
                                </span>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-bold text-emerald-700">3. Penanganan Selesai</h4>
                                    <p class="text-xs text-slate-600 mt-0.5">
                                        Kendala telah berhasil diselesaikan pada {{ $complaint->tanggal_selesai ? $complaint->tanggal_selesai->format('d/m/Y H:i') . ' WIB' : 'Hari ini' }}.
                                    </p>
                                    @if($complaint->tindak_lanjut)
                                        <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-100 mt-2 text-xs">
                                            <strong class="block text-emerald-800 font-bold mb-0.5">Catatan Tindak Lanjut dari Petugas:</strong>
                                            <span class="text-slate-700">{{ $complaint->tindak_lanjut }}</span>
                                        </div>
                                    @endif
                                </div>
                            @elseif($complaint->status === 'ditolak')
                                <span class="absolute -left-8 top-0.5 w-7 h-7 rounded-full bg-rose-600 text-white flex items-center justify-center text-xs font-bold ring-4 ring-white">
                                    <i class="bi bi-x-lg"></i>
                                </span>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-bold text-rose-700">3. Laporan Ditolak / Dibatalkan</h4>
                                    @if($complaint->tindak_lanjut)
                                        <p class="text-xs text-slate-500 mt-0.5">Alasan: {{ $complaint->tindak_lanjut }}</p>
                                    @endif
                                </div>
                            @else
                                <span class="absolute -left-8 top-0.5 w-7 h-7 rounded-full bg-slate-100 text-slate-400 border border-slate-300 flex items-center justify-center text-xs font-bold ring-4 ring-white">
                                    <i class="bi bi-flag"></i>
                                </span>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-400">3. Penyelesaian &amp; Solusi</h4>
                                    <p class="text-xs text-slate-400 mt-0.5">Menunggu perbaikan tuntas oleh teknisi yang bertugas.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Rincian Masalah Pelapor -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/70 text-xs sm:text-sm space-y-2">
                    <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Deskripsi Masalah yang Dilaporkan:</h4>
                    <p class="text-slate-700 whitespace-pre-line leading-relaxed">{{ $complaint->deskripsi }}</p>
                    
                    @if($complaint->foto)
                        <div class="pt-2">
                            <span class="block text-xs font-semibold text-slate-600 mb-1.5">Foto Bukti Lampiran:</span>
                            <img src="{{ asset('storage/' . $complaint->foto) }}" alt="Bukti Kendala" class="rounded-xl border border-slate-200 max-h-60 object-cover shadow-xs">
                        </div>
                    @endif
                </div>
            </div>
        @elseif(!empty($ticketCode))
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-8 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto">
                    <i class="bi bi-search text-xl"></i>
                </div>
                <h2 class="text-base font-bold text-slate-900">Data Pengaduan Tidak Ditemukan</h2>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Tidak ditemukan pengaduan dengan kode tiket atau nomor kontak <strong>"{{ $ticketCode }}"</strong>. Pastikan Anda memasukkan kode tiket dengan benar.
                </p>
                <div class="pt-2">
                    <a href="{{ route('welcome') }}?action=lapor" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition-colors">
                        <i class="bi bi-pencil-square"></i>
                        <span>Buat Pengaduan Baru</span>
                    </a>
                </div>
            </div>
        @endif
    </main>

    <!-- Footer Putih-Biru Rapi -->
    <footer class="bg-white py-6 border-t border-slate-200/80 text-center text-xs text-slate-500">
        <div class="max-w-6xl mx-auto px-4 flex flex-col items-center gap-2">
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-6 h-6 object-contain">
                <span class="font-bold text-slate-900">SMK Dr. Sutomo Temanggung</span>
            </div>
            <p>&copy; {{ date('Y') }} Layanan Pengaduan Sarana Prasarana Sekolah.</p>
        </div>
    </footer>
</body>
</html>
