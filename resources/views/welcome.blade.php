<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Pengaduan Sarana & Inventaris - SMK Dr. Sutomo Temanggung</title>
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
<body class="font-sans bg-slate-50 text-slate-800 overflow-x-hidden">

    <!-- Navbar Putih Bersih & Rapi -->
    <nav class="bg-white border-b border-slate-200/80 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo SMK Dr. Sutomo" class="w-10 h-10 object-contain">
                <div>
                    <span class="font-black text-blue-700 text-sm tracking-tight block leading-tight">SMK Dr. SUTOMO</span>
                    <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">TEMANGGUNG</span>
                </div>
            </a>

            <div class="flex items-center gap-2 sm:gap-3">
                <button type="button" onclick="openModal('modalPengaduan')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 shadow-xs transition-colors">
                    <i class="bi bi-pencil-square"></i>
                    <span>Buat Laporan</span>
                </button>
                <button type="button" onclick="openModal('modalLacakTiket')" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:text-blue-600 border border-slate-200 hover:bg-slate-50 transition-colors">
                    <i class="bi bi-search"></i>
                    <span>Lacak Tiket</span>
                </button>
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-xs transition-colors">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-medium text-slate-600 hover:text-blue-700 bg-slate-100 hover:bg-slate-200/70 transition-colors">
                        <i class="bi bi-box-arrow-in-right text-blue-600"></i>
                        <span>Login Petugas</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Banner Fresh Nuansa Biru Sesuai Logo -->
    <header class="bg-gradient-to-br from-blue-700 via-blue-800 to-slate-950 text-white py-14 sm:py-20 relative overflow-hidden">
        <!-- Ambient shapes -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-amber-400/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-7 text-center lg:text-left space-y-5">
                    <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/20 px-3.5 py-1.5 rounded-full">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-400 text-slate-950">
                            Layanan Terpadu
                        </span>
                        <span class="text-xs font-medium text-blue-100">Sarana &amp; Prasarana Pembelajaran</span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight">
                        Pusat Pengaduan &amp; Layanan Kendala Fasilitas Sekolah
                    </h1>
                    <p class="text-sm sm:text-base text-blue-100/90 max-w-2xl leading-relaxed mx-auto lg:mx-0">
                        Mengalami kendala pada <strong>komputer lab, mesin bengkel, kelistrikan, atau fasilitas ruang kelas?</strong> Sampaikan laporan kendala Bapak/Ibu Guru dengan mudah, cepat, dan <strong>tanpa perlu login</strong>.
                    </p>

                    <!-- Tombol Aksi Utama yang Ramah -->
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 pt-2">
                        <button type="button" onclick="openModal('modalPengaduan')" class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-2xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-extrabold text-sm shadow-lg shadow-amber-400/20 transition-all hover:-translate-y-0.5">
                            <i class="bi bi-send-fill text-base"></i>
                            <span>Lapor Kendala Sekarang</span>
                        </button>
                        <button type="button" onclick="openModal('modalLacakTiket')" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-sm backdrop-blur-md border border-white/20 transition-all hover:-translate-y-0.5">
                            <i class="bi bi-search text-base"></i>
                            <span>Lacak Status Pengaduan</span>
                        </button>
                    </div>
                </div>

                <!-- Alur Cepat Penanganan Card -->
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 text-slate-800 shadow-2xl border border-white/20 space-y-5">
                        <div class="flex items-center gap-3.5 pb-4 border-b border-slate-100">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <i class="bi bi-clock-history text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900 leading-tight">Alur Cepat Penanganan</h2>
                                <p class="text-xs text-slate-400 mt-0.5">Proses transparan dan terpantau</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-start gap-3.5">
                                <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-700 font-extrabold text-xs flex items-center justify-center border border-blue-200 shrink-0">
                                    1
                                </span>
                                <div>
                                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Tekan Tombol Lapor</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">Isi formulir ringkas kendala ruangan dan nomor WA Anda.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3.5">
                                <span class="w-8 h-8 rounded-full bg-amber-50 text-amber-700 font-extrabold text-xs flex items-center justify-center border border-amber-200 shrink-0">
                                    2
                                </span>
                                <div>
                                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Terima Kode Tiket</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">Sistem otomatis menerbitkan kode unik (misal: <strong class="text-slate-900 font-mono">ADU-2609-XXXX</strong>).</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3.5">
                                <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-700 font-extrabold text-xs flex items-center justify-center border border-emerald-200 shrink-0">
                                    3
                                </span>
                                <div>
                                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Perbaikan Dikerjakan</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">Toolman bengkel atau teknisi Sarpras langsung menindaklanjuti.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Konten Utama: 3 Menu Layanan Interaktif -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
        <div class="text-center max-w-xl mx-auto space-y-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
                Menu Pilihan
            </span>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Akses Layanan Pengaduan &amp; Sarpras</h2>
            <p class="text-xs sm:text-sm text-slate-500">Pilih tombol layanan di bawah ini untuk memulai pelaporan atau mengecek status tiket.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Kartu 1: Buat Pengaduan -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs hover:shadow-lg hover:border-blue-300 transition-all flex flex-col justify-between group">
                <div class="space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <i class="bi bi-tools text-2xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Lapor Kendala Fasilitas</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Laporkan kerusakan komputer lab, mesin bengkel, kelistrikan/lampu, AC/kipas, atau sarana kelas Anda tanpa perlu akun login.
                    </p>
                </div>
                <div class="pt-6">
                    <button type="button" onclick="openModal('modalPengaduan')" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-xs shadow-xs transition-colors">
                        <i class="bi bi-pencil-square"></i>
                        <span>Buka Form Pengaduan</span>
                    </button>
                </div>
            </div>

            <!-- Kartu 2: Lacak Tiket -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs hover:shadow-lg hover:border-blue-300 transition-all flex flex-col justify-between group">
                <div class="space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <i class="bi bi-ticket-detailed text-2xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Lacak Status Pengaduan</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Sudah pernah melapor sebelumnya? Masukkan Kode Tiket Anda atau nomor WhatsApp untuk memantau progres perbaikan oleh teknisi.
                    </p>
                </div>
                <div class="pt-6">
                    <button type="button" onclick="openModal('modalLacakTiket')" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs transition-colors">
                        <i class="bi bi-search"></i>
                        <span>Cek Status Tiket</span>
                    </button>
                </div>
            </div>

            <!-- Kartu 3: Login Petugas -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs hover:shadow-lg hover:border-blue-300 transition-all flex flex-col justify-between group">
                <div class="space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <i class="bi bi-shield-check text-2xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Portal Petugas &amp; Jurusan</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Khusus admin Sarpras Pusat dan kepala bengkel/toolman 5 jurusan untuk mengelola inventaris, aset, peminjaman, dan penanganan aduan.
                    </p>
                </div>
                <div class="pt-6">
                    <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition-colors">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Masuk Akun Petugas</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Komitmen & Kendala Selesai Terkini -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-6 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="bi bi-patch-check-fill text-blue-600"></i>
                    <span>Komitmen Layanan Sarpras</span>
                </h3>
                <div class="space-y-3 text-xs text-slate-600 leading-relaxed">
                    <div class="flex items-start gap-2.5">
                        <i class="bi bi-check-circle-fill text-blue-600 text-sm mt-0.5 shrink-0"></i>
                        <p><strong>Respon Cepat:</strong> Setiap laporan yang masuk akan segera diverifikasi oleh tim Sarpras dan Toolman terkait.</p>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <i class="bi bi-check-circle-fill text-blue-600 text-sm mt-0.5 shrink-0"></i>
                        <p><strong>Transparansi:</strong> Status pengerjaan, nama teknisi yang ditugaskan, dan solusi perbaikan tercatat secara digital.</p>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <i class="bi bi-check-circle-fill text-blue-600 text-sm mt-0.5 shrink-0"></i>
                        <p><strong>Kelancaran KBM:</strong> Prioritas penanganan diberikan pada kendala yang menghentikan jalannya praktikum siswa.</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="bi bi-check2-all text-emerald-600"></i>
                    <span>Kendala Terkini yang Telah Selesai Ditangani</span>
                </h3>
                @if(isset($recentResolved) && $recentResolved->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($recentResolved as $rr)
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/60 flex items-center justify-between text-xs">
                                <div>
                                    <h4 class="font-bold text-slate-900">{{ $rr->judul_kendala }}</h4>
                                    <span class="text-[11px] text-slate-500"><i class="bi bi-geo-alt me-1"></i>{{ $rr->lokasi_ruang }}</span>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Tuntas Selesai
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400">Belum ada kendala baru yang dicatat selesai hari ini.</p>
                @endif
            </div>
        </div>
    </main>

    <!-- MODAL 1: FORMULIR PENGADUAN GURU -->
    <div id="modalPengaduan" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-2xl bg-white rounded-3xl border border-slate-200 shadow-2xl p-6 sm:p-8 text-left transform transition-all my-8 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6 sticky top-0 bg-white z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i class="bi bi-tools text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900">Formulir Lapor Kendala Fasilitas</h2>
                        <p class="text-xs text-slate-400">SMK Dr. Sutomo Temanggung - Tanpa Perlu Login</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('modalPengaduan')" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>

            @if($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-rose-600"></i>
                    <span>Mohon lengkapi seluruh isian wajib yang ditandai bintang merah (*).</span>
                </div>
            @endif

            <form action="{{ route('public.complaint.store') }}" method="POST" enctype="multipart/form-data" id="formComplaintModal" class="space-y-6">
                @csrf

                <!-- Section 1 -->
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-blue-600 mb-3">1. Data Guru / Tenaga Kependidikan</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="nama_pelapor" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nama Lengkap Pelapor <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="nama_pelapor" id="nama_pelapor" 
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border @error('nama_pelapor') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                                   placeholder="Contoh: Bpk. Budi Santoso, S.Pd" 
                                   value="{{ old('nama_pelapor') }}" required>
                            @error('nama_pelapor') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="kontak" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nomor WhatsApp / HP Aktif <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="kontak" id="kontak" 
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border @error('kontak') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                                   placeholder="Contoh: 081234567890" 
                                   value="{{ old('kontak') }}" required>
                            <span class="block text-[11px] text-slate-400 mt-1">Agar teknisi mudah menghubungi di lokasi.</span>
                            @error('kontak') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2 -->
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-blue-600 mb-3">2. Lokasi &amp; Jenis Fasilitas</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="jurusan_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Jurusan / Unit Terkait</label>
                            <select name="jurusan_id" id="jurusan_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                <option value="">-- Sarpras Umum / Bukan Jurusan Tertentu --</option>
                                @foreach($jurusans as $j)
                                    <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>
                                        {{ $j->kode }} - {{ $j->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="kategori" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Kategori Fasilitas <span class="text-rose-500">*</span>
                            </label>
                            <select name="kategori" id="kategori" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required>
                                <option value="komputer_it" {{ old('kategori') == 'komputer_it' ? 'selected' : '' }}>Komputer, Workstation Lab &amp; Jaringan IT</option>
                                <option value="kelistrikan" {{ old('kategori') == 'kelistrikan' ? 'selected' : '' }}>Kelistrikan, MCB &amp; Penerangan Ruang</option>
                                <option value="mesin_peralatan" {{ old('kategori') == 'mesin_peralatan' ? 'selected' : '' }}>Mesin Industri &amp; Peralatan Bengkel</option>
                                <option value="sarana_gedung" {{ old('kategori') == 'sarana_gedung' ? 'selected' : '' }}>Sarana Gedung, Meja, Kursi, &amp; Kipas</option>
                                <option value="lainnya" {{ old('kategori') == 'lainnya' ? 'selected' : '' }}>Lain-lain / Perlengkapan KBM</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="lokasi_ruang" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Lokasi Ruang / Lab / Bengkel Spesifik <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="lokasi_ruang" id="lokasi_ruang" 
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border @error('lokasi_ruang') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                                   placeholder="Contoh: Lab Komputer 1 Meja PC-08, Ruang Teori TITL Lantai 2, Bengkel Bubut Mesin 3..." 
                                   value="{{ old('lokasi_ruang') }}" required>
                            @error('lokasi_ruang') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="item_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Barang / Alat Spesifik (Opsional jika diketahui)</label>
                            <select name="item_id" id="item_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                <option value="">-- Lewati jika kendala bersifat umum / tidak ada di daftar --</option>
                                @foreach($items as $it)
                                    <option value="{{ $it->id }}" {{ old('item_id') == $it->id ? 'selected' : '' }}>
                                        [{{ $it->kode_barang }}] {{ $it->nama_barang }} ({{ $it->lokasi ?? 'Lokasi Sekolah' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 3 -->
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-blue-600 mb-3">3. Rincian Masalah / Gejala Kerusakan</span>
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-8">
                            <label for="judul_kendala" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Ringkasan Masalah / Judul Kendala <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="judul_kendala" id="judul_kendala" 
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border @error('judul_kendala') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                                   placeholder="Contoh: Monitor PC Meja 04 mati total, MCB Bengkel Listrik anjlok..." 
                                   value="{{ old('judul_kendala') }}" required>
                            @error('judul_kendala') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-4">
                            <label for="tingkat_urgensi" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Tingkat Urgensi <span class="text-rose-500">*</span>
                            </label>
                            <select name="tingkat_urgensi" id="tingkat_urgensi" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required>
                                <option value="rendah" {{ old('tingkat_urgensi') == 'rendah' ? 'selected' : '' }}>Rendah (Masih bisa KBM)</option>
                                <option value="sedang" {{ old('tingkat_urgensi', 'sedang') == 'sedang' ? 'selected' : '' }}>Sedang (Perlu dicek)</option>
                                <option value="tinggi_darurat" {{ old('tingkat_urgensi') == 'tinggi_darurat' ? 'selected' : '' }}>Darurat (Menghentikan KBM)</option>
                            </select>
                        </div>

                        <div class="sm:col-span-12">
                            <label for="deskripsi" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Uraian Gejala Kerusakan <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="deskripsi" id="deskripsi" rows="3" 
                                      class="w-full px-3.5 py-2.5 bg-slate-50 border @error('deskripsi') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                                      placeholder="Jelaskan kronologi kendala atau apa yang terjadi saat fasilitas digunakan..." required>{{ old('deskripsi') }}</textarea>
                            @error('deskripsi') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-12">
                            <label for="foto" class="block text-xs font-semibold text-slate-700 mb-1.5">Lampiran Foto Kerusakan (Opsional)</label>
                            <input type="file" name="foto" id="foto" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept="image/*" onchange="validateFileSize(this)">
                            <span class="block text-[11px] text-slate-400 mt-1">Format JPG, PNG, atau WEBP (maksimal 3 MB, otomatis dikompresi).</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-100">
                    <button type="button" onclick="closeModal('modalPengaduan')" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-xs shadow-xs transition-colors">
                        <i class="bi bi-send-check text-sm"></i>
                        <span>Kirim Laporan Sekarang</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: LACAK TIKET PENGADUAN -->
    <div id="modalLacakTiket" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-md bg-white rounded-3xl border border-slate-200 shadow-2xl p-6 sm:p-7 text-left transform transition-all">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="bi bi-search text-sm"></i>
                    </div>
                    <h2 class="text-base font-bold text-slate-900">Lacak Status Pengaduan</h2>
                </div>
                <button type="button" onclick="closeModal('modalLacakTiket')" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>

            <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                Masukkan <strong>Kode Tiket</strong> yang Anda peroleh saat mengirim laporan, atau gunakan <strong>Nomor WhatsApp</strong> yang Anda daftarkan:
            </p>

            <form action="{{ route('public.track') }}" method="GET" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kode Tiket / No. WhatsApp</label>
                    <input type="text" name="ticket" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono uppercase text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Contoh: ADU-2609-XXXX / 081234567890" required>
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition-colors">
                    <i class="bi bi-search"></i>
                    <span>Cek Status Sekarang</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Footer Putih-Biru Rapi -->
    <footer class="bg-white py-8 border-t border-slate-200/80 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 flex flex-col items-center gap-2">
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-6 h-6 object-contain">
                <span class="font-bold text-slate-900">SMK Dr. Sutomo Temanggung</span>
            </div>
            <p>Sistem Informasi Manajemen Inventaris, Aset Bengkel, &amp; Pengaduan Sarana Prasarana.</p>
            <span class="text-[11px] text-slate-400">&copy; {{ date('Y') }} SMK Dr. Sutomo Temanggung. Hak Cipta Dilindungi.</span>
        </div>
    </footer>

    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        function validateFileSize(input) {
            if (input.files && input.files[0]) {
                if (input.files[0].size > 3 * 1024 * 1024) {
                    alert('Ukuran foto melebihi 3 MB! Silakan pilih foto dengan ukuran maksimal 3 MB.');
                    input.value = '';
                }
            }
        }

        // Close on backdrop click & ESC key
        document.addEventListener('DOMContentLoaded', function() {
            ['modalPengaduan', 'modalLacakTiket'].forEach(function(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            closeModal(modalId);
                        }
                    });
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal('modalPengaduan');
                    closeModal('modalLacakTiket');
                }
            });

            @if($errors->any())
                openModal('modalPengaduan');
            @endif

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'lapor') {
                openModal('modalPengaduan');
            } else if (urlParams.get('action') === 'lacak') {
                openModal('modalLacakTiket');
            }
        });
    </script>
</body>
</html>
