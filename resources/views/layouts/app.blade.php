<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Inventaris') - SMK Dr. Sutomo Temanggung</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons (Proportional Glyphs) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind CSS CDN with Custom Theme Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#0f172a',
                        },
                        accent: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col text-slate-800 bg-slate-50 antialiased selection:bg-brand-500 selection:text-white">

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="sidebarBackdrop" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs hidden transition-opacity lg:hidden"></div>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="fixed top-0 bottom-0 left-0 z-50 w-64 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-300 flex flex-col transition-transform duration-300 -translate-x-full lg:translate-x-0 border-r border-slate-800/80 shadow-2xl">
        <!-- Logo & Brand Header -->
        <div class="px-5 py-4 flex items-center justify-between border-b border-amber-400/20 bg-slate-950/60">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-white p-1 flex items-center justify-center shadow-md ring-2 ring-amber-400/30 group-hover:scale-105 transition-transform shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SMK Dr. Sutomo" class="w-8 h-8 object-contain">
                </div>
                <div class="leading-tight overflow-hidden">
                    <h1 class="text-sm font-extrabold text-white tracking-wide truncate">SMK Dr. SUTOMO</h1>
                    <span class="inline-block mt-0.5 text-[10px] font-bold uppercase tracking-wider bg-amber-400 text-slate-950 px-2 py-0.5 rounded-full shadow-xs">
                        TEMANGGUNG
                    </span>
                </div>
            </a>
            <!-- Mobile Close Button -->
            <button id="sidebarCloseBtn" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none" aria-label="Tutup menu">
                <i class="bi bi-x-lg text-base"></i>
            </button>
        </div>

        <!-- Scrollable Navigation Items -->
        <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
            <!-- Nav Group: Menu Utama -->
            <div>
                <div class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Menu Utama
                </div>
                <nav class="space-y-1">
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-700 to-blue-600 text-white shadow-md shadow-blue-900/30 border-l-4 border-amber-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <i class="bi bi-speedometer2 text-base {{ request()->routeIs('dashboard') ? 'text-amber-300' : 'text-slate-400' }} shrink-0"></i>
                        <span>Dashboard</span>
                    </a>
                </nav>
            </div>

            <!-- Nav Group: Khusus Sarpras & Kepala Sekolah -->
            @if(Auth::user()->isSarprasOrKepalaSekolah())
                <div>
                    <div class="px-3 mb-2 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <span>Sarpras & Sekolah</span>
                        <span class="text-[9px] font-extrabold bg-amber-400/90 text-slate-950 px-1.5 py-0.5 rounded shadow-xs">
                            @if(Auth::user()->isKepalaSekolah())
                                KS
                            @elseif(Auth::user()->isPembantuSarpras())
                                PS
                            @else
                                SAR
                            @endif
                        </span>
                    </div>
                    <nav class="space-y-1">
                        <a href="{{ route('sarpras.umum') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('sarpras.umum') ? 'bg-gradient-to-r from-blue-700 to-blue-600 text-white shadow-md shadow-blue-900/30 border-l-4 border-amber-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="bi bi-building-check text-base text-amber-400 shrink-0"></i>
                            <span>Inventaris Umum</span>
                        </a>
                        <a href="{{ route('sarpras.gudang') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('sarpras.gudang') ? 'bg-gradient-to-r from-blue-700 to-blue-600 text-white shadow-md shadow-blue-900/30 border-l-4 border-amber-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="bi bi-archive-fill text-base text-amber-400 shrink-0"></i>
                            <span>Stok di Gudang</span>
                        </a>
                        <a href="{{ route('official-reports.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('official-reports.*') ? 'bg-gradient-to-r from-blue-700 to-blue-600 text-white shadow-md shadow-blue-900/30 border-l-4 border-amber-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="bi bi-file-earmark-ruled-fill text-base text-amber-400 shrink-0"></i>
                            <span>Berita Acara</span>
                        </a>
                    </nav>
                </div>
            @endif

            <!-- Nav Group: Inventaris & Aset -->
            <div>
                <div class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Inventaris & Aset
                </div>
                <nav class="space-y-1">
                    <a href="{{ route('items.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('items.*') ? 'bg-gradient-to-r from-blue-700 to-blue-600 text-white shadow-md shadow-blue-900/30 border-l-4 border-amber-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <i class="bi bi-boxes text-base {{ request()->routeIs('items.*') ? 'text-amber-300' : 'text-slate-400' }} shrink-0"></i>
                        <span>Data Barang</span>
                    </a>
                    <a href="{{ route('borrowings.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('borrowings.*') ? 'bg-gradient-to-r from-blue-700 to-blue-600 text-white shadow-md shadow-blue-900/30 border-l-4 border-amber-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <i class="bi bi-arrow-left-right text-base {{ request()->routeIs('borrowings.*') ? 'text-amber-300' : 'text-slate-400' }} shrink-0"></i>
                        <span>Peminjaman Alat</span>
                    </a>
                    <a href="{{ route('usages.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('usages.*') ? 'bg-gradient-to-r from-blue-700 to-blue-600 text-white shadow-md shadow-blue-900/30 border-l-4 border-amber-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <i class="bi bi-droplet-half text-base {{ request()->routeIs('usages.*') ? 'text-amber-300' : 'text-slate-400' }} shrink-0"></i>
                        <span>Pemakaian Bahan</span>
                    </a>
                    @if(Auth::user()->isPembantuSarpras())
                        <a href="{{ route('reports.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-blue-700 to-blue-600 text-white shadow-md shadow-blue-900/30 border-l-4 border-amber-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="bi bi-printer text-base {{ request()->routeIs('reports.*') ? 'text-amber-300' : 'text-slate-400' }} shrink-0"></i>
                            <span>Laporan & Cetak</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Nav Group: Pengadaan & Layanan (Hanya untuk Sarpras, Kepala Sekolah, dan Jurusan; Pembantu Sarpras Tidak Ada) -->
            @if(!Auth::user()->isPembantuSarpras())
                <div>
                    <div class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Pengadaan & Layanan
                    </div>
                    <nav class="space-y-1">
                        <a href="{{ route('procurements.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('procurements.*') ? 'bg-gradient-to-r from-blue-700 to-blue-600 text-white shadow-md shadow-blue-900/30 border-l-4 border-amber-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="bi bi-clipboard2-check text-base {{ request()->routeIs('procurements.*') ? 'text-amber-300' : 'text-slate-400' }} shrink-0"></i>
                            <span>Usulan Pengadaan</span>
                        </a>
                        @if(Auth::user()->isJurusan())
                            <a href="{{ route('official-reports.index') }}" 
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('official-reports.*') ? 'bg-gradient-to-r from-blue-700 to-blue-600 text-white shadow-md shadow-blue-900/30 border-l-4 border-amber-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <i class="bi bi-file-earmark-ruled-fill text-base {{ request()->routeIs('official-reports.*') ? 'text-amber-300' : 'text-slate-400' }} shrink-0"></i>
                                <span>Berita Acara</span>
                            </a>
                        @endif
                        @if(Auth::user()->isSarpras())
                            <a href="{{ route('complaints.index') }}" 
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('complaints.*') ? 'bg-gradient-to-r from-blue-700 to-blue-600 text-white shadow-md shadow-blue-900/30 border-l-4 border-amber-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <i class="bi bi-exclamation-octagon text-base {{ request()->routeIs('complaints.*') ? 'text-amber-300' : 'text-rose-400' }} shrink-0"></i>
                                <span>Pengaduan Guru</span>
                            </a>
                        @endif
                        <a href="{{ route('reports.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-blue-700 to-blue-600 text-white shadow-md shadow-blue-900/30 border-l-4 border-amber-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="bi bi-printer text-base {{ request()->routeIs('reports.*') ? 'text-amber-300' : 'text-slate-400' }} shrink-0"></i>
                            <span>Laporan & Cetak</span>
                        </a>
                    </nav>
                </div>
            @endif

            <!-- Nav Group: Pengaturan (Sarpras Only) -->
            @if(Auth::user()->isSarpras())
                <div>
                    <div class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Pengaturan Sistem
                    </div>
                    <nav class="space-y-1">
                        <a href="{{ route('users.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-blue-700 to-blue-600 text-white shadow-md shadow-blue-900/30 border-l-4 border-amber-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="bi bi-people text-base {{ request()->routeIs('users.*') ? 'text-amber-300' : 'text-slate-400' }} shrink-0"></i>
                            <span>Kelola Akun</span>
                        </a>
                        <a href="{{ route('jurusans.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('jurusans.*') ? 'bg-gradient-to-r from-blue-700 to-blue-600 text-white shadow-md shadow-blue-900/30 border-l-4 border-amber-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="bi bi-building-gear text-base {{ request()->routeIs('jurusans.*') ? 'text-amber-300' : 'text-slate-400' }} shrink-0"></i>
                            <span>Unit Kerja & Jurusan</span>
                        </a>
                    </nav>
                </div>
            @endif
        </div>

        <!-- User Profile Pill in Sidebar Footer -->
        <div class="p-3 border-t border-slate-800/90 bg-slate-950/70">
            <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-2 py-2 rounded-xl bg-slate-900/80 hover:bg-slate-800/90 border border-slate-800 transition-all group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-300 to-amber-500 text-slate-950 font-bold flex items-center justify-center shadow-xs shrink-0 ring-1 ring-amber-400/40 text-sm group-hover:scale-105 transition-transform">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-white truncate group-hover:text-amber-300 transition-colors">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-slate-400 truncate">
                        @if(Auth::user()->isKepalaSekolah())
                            Kepala Sekolah
                        @elseif(Auth::user()->isPembantuSarpras())
                            Pembantu Sarpras
                        @elseif(Auth::user()->isSarpras())
                            Sarpras Pusat
                        @else
                            Unit: {{ Auth::user()->jurusan->kode ?? 'Jurusan' }}
                        @endif
                    </p>
                </div>
                <i class="bi bi-gear text-slate-400 group-hover:text-amber-300 text-sm shrink-0 transition-colors"></i>
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="lg:pl-64 flex flex-col min-h-screen">
        <!-- Top Navbar -->
        <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-t-2 border-amber-400 border-b border-slate-200/90 shadow-xs">
            <div class="flex items-center justify-between px-3 sm:px-6 py-2.5 sm:py-3 gap-2">
                <!-- Left: Mobile Toggle & Unit Identifier -->
                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                    <button id="sidebarToggleBtn" class="lg:hidden p-2 -ml-1 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors focus:outline-none shrink-0" aria-label="Buka menu navigasi">
                        <i class="bi bi-list text-xl"></i>
                    </button>
                    
                    <div class="flex items-center gap-2 min-w-0">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-6 h-6 sm:w-7 sm:h-7 object-contain lg:hidden shrink-0">
                        
                        <!-- Role Badge -->
                        @if(Auth::user()->isKepalaSekolah())
                            <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-300 shadow-xs truncate">
                                <i class="bi bi-mortarboard-fill text-amber-600 text-xs sm:text-sm shrink-0"></i>
                                <span class="truncate">KEPALA SEKOLAH</span>
                            </span>
                        @elseif(Auth::user()->isPembantuSarpras())
                            <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-cyan-50 text-cyan-800 border border-cyan-300 shadow-xs truncate">
                                <i class="bi bi-person-badge-fill text-cyan-600 text-xs sm:text-sm shrink-0"></i>
                                <span class="truncate">PEMBANTU SARPRAS</span>
                            </span>
                        @elseif(Auth::user()->isSarpras())
                            <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200/80 shadow-xs truncate">
                                <i class="bi bi-shield-fill-check text-amber-500 text-xs sm:text-sm shrink-0"></i>
                                <span class="truncate hidden xs:inline sm:inline">ADMIN PUSAT (SARPRAS)</span>
                                <span class="truncate inline xs:hidden sm:hidden">SARPRAS</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 shadow-xs truncate">
                                <i class="bi bi-building text-amber-500 text-xs sm:text-sm shrink-0"></i>
                                <span class="truncate">{{ Auth::user()->jurusan->nama ?? 'Unit Kerja' }}</span>
                            </span>
                        @endif

                        <!-- Portal Publik Button (Netral Secondary) -->
                        <a href="{{ route('welcome') }}" target="_blank" 
                           class="hidden md:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200/80 border border-slate-200 hover:text-slate-900 transition-all shadow-xs shrink-0" 
                           title="Buka Portal Pengaduan Publik">
                            <i class="bi bi-box-arrow-up-right text-amber-500 text-xs"></i>
                            <span>Portal Publik</span>
                        </a>
                    </div>
                </div>

                <!-- Right: Account Info & Keluar (Urgency: Danger/Red) -->
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <a href="{{ route('profile.show') }}" class="hidden sm:flex items-center gap-2 p-1.5 -mr-1 rounded-xl hover:bg-slate-100 transition-colors group text-right" title="Buka Profil Akun">
                        <div>
                            <span class="block text-xs font-semibold text-slate-800 group-hover:text-blue-600 transition-colors leading-tight">{{ Auth::user()->name }}</span>
                            <span class="block text-[11px] text-slate-500 leading-tight">{{ Auth::user()->email }}</span>
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-slate-100 group-hover:bg-blue-50 text-slate-600 group-hover:text-blue-600 flex items-center justify-center text-xs font-bold transition-colors">
                            <i class="bi bi-person-gear text-sm"></i>
                        </div>
                    </a>

                    <!-- Tombol Keluar (Warna Urgensi: Red/Rose) -->
                    <button type="button" id="openLogoutModalBtn" 
                            class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 hover:text-rose-700 border border-rose-200 rounded-xl transition-all shadow-xs active:scale-95"
                            title="Keluar dari sistem">
                        <i class="bi bi-box-arrow-right text-sm"></i>
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Body Content Area -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
            <!-- Flash Message: Success (Emerald Green) -->
            @if(session('success'))
                <div class="mb-5 flex items-center justify-between p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 shadow-xs animate-fade-in" role="alert">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <i class="bi bi-check-circle-fill text-base"></i>
                        </div>
                        <p class="text-sm font-medium leading-relaxed">{{ session('success') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1.5 rounded-lg" aria-label="Tutup notifikasi">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>
            @endif

            <!-- Flash Message: Error (Rose Red) -->
            @if(session('error'))
                <div class="mb-5 flex items-center justify-between p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 shadow-xs animate-fade-in" role="alert">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                            <i class="bi bi-exclamation-triangle-fill text-base"></i>
                        </div>
                        <p class="text-sm font-medium leading-relaxed">{{ session('error') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 p-1.5 rounded-lg" aria-label="Tutup notifikasi">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>
            @endif

            <!-- Validation Errors Alert -->
            @if($errors->any())
                <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 shadow-xs" role="alert">
                    <div class="flex items-center gap-2 mb-2 font-semibold text-sm text-rose-800">
                        <i class="bi bi-exclamation-circle-fill text-base text-rose-600"></i>
                        <span>Terdapat kesalahan pengisian data:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-xs text-rose-700 pl-4">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Page Content Slot -->
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="mt-auto py-4 px-6 bg-white border-t border-slate-200 text-center text-xs text-slate-500">
            <div class="flex flex-wrap items-center justify-center gap-2">
                <span>&copy; {{ date('Y') }} <strong class="text-slate-700">SMK Dr. Sutomo Temanggung</strong></span>
                <span class="inline-block text-[10px] font-bold uppercase tracking-wider bg-amber-400 text-slate-950 px-1.5 py-0.5 rounded shadow-xs">DOKSUT</span>
                <span>— Sistem Informasi Inventaris & Sarpras Terpadu.</span>
            </div>
        </footer>
    </div>

    <!-- Modal Konfirmasi Keluar (Themed Tailwind Modal) -->
    <div id="logoutConfirmModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity" role="dialog" aria-modal="true" aria-labelledby="logoutModalTitle">
        <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200 transform transition-all">
            <!-- Accent Top Stripe (Red-Amber Urgency) -->
            <div class="h-1.5 bg-gradient-to-r from-rose-600 via-amber-500 to-amber-400"></div>

            <div class="p-6 text-center">
                <!-- Icon with urgency ring -->
                <div class="mx-auto mb-4 w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center shadow-xs">
                    <i class="bi bi-box-arrow-right text-2xl"></i>
                </div>

                <h3 class="text-lg font-bold text-slate-900 mb-1" id="logoutModalTitle">Konfirmasi Keluar</h3>
                <p class="text-xs text-slate-500 mb-5 leading-relaxed">
                    Apakah Anda yakin ingin mengakhiri sesi dan keluar dari sistem <strong class="text-slate-700">SMK Dr. Sutomo Temanggung</strong>?
                </p>

                <!-- User Info Preview Box -->
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center gap-3 text-left mb-6">
                    <div class="w-10 h-10 rounded-xl @if(Auth::user()->isKepalaSekolah()) bg-amber-100 text-amber-800 ring-1 ring-amber-300 @elseif(Auth::user()->isPembantuSarpras()) bg-cyan-100 text-cyan-800 ring-1 ring-cyan-300 @elseif(Auth::user()->isSarpras()) bg-blue-100 text-blue-700 ring-1 ring-blue-300 @else bg-indigo-100 text-indigo-700 ring-1 ring-indigo-300 @endif font-bold flex items-center justify-center text-sm shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                        <span class="inline-block mt-0.5 text-[10px] font-semibold px-2 py-0.5 rounded-full @if(Auth::user()->isKepalaSekolah()) bg-amber-100 text-amber-900 @elseif(Auth::user()->isPembantuSarpras()) bg-cyan-100 text-cyan-800 @elseif(Auth::user()->isSarpras()) bg-blue-100 text-blue-800 @else bg-indigo-100 text-indigo-800 @endif">
                            @if(Auth::user()->isKepalaSekolah())
                                Kepala Sekolah
                            @elseif(Auth::user()->isPembantuSarpras())
                                Pembantu Sarpras
                            @elseif(Auth::user()->isSarpras())
                                Sarpras Pusat
                            @else
                                Jurusan: {{ Auth::user()->jurusan->kode ?? 'Jurusan' }}
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Action Buttons: Neutral (Cancel) vs Danger (Keluar) -->
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" id="closeLogoutModalBtn" 
                            class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition-all active:scale-95">
                        Batal
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" 
                                class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-200 transition-all flex items-center justify-center gap-2 active:scale-95">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Ya, Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Universal Themed Verification / Confirmation Modal -->
    <div id="globalConfirmModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity" role="dialog" aria-modal="true" aria-labelledby="globalConfirmTitle">
        <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200 transform transition-all">
            <!-- Accent Top Stripe -->
            <div id="globalConfirmStripe" class="h-1.5 bg-gradient-to-r from-rose-600 via-amber-500 to-amber-400"></div>

            <div class="p-6 text-center">
                <!-- Close Button in corner -->
                <button type="button" onclick="closeConfirmDialog()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors" aria-label="Tutup">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>

                <!-- Icon with urgency ring -->
                <div id="globalConfirmIconContainer" class="mx-auto mb-4 w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center shadow-xs">
                    <i id="globalConfirmIcon" class="bi bi-trash3-fill text-2xl"></i>
                </div>

                <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" id="globalConfirmTitle">Konfirmasi Tindakan</h3>
                <p class="text-xs sm:text-sm text-slate-500 mb-4 leading-relaxed" id="globalConfirmMessage">
                    Apakah Anda yakin ingin melanjutkan tindakan ini?
                </p>

                <!-- Optional Preview / Detail Box -->
                <div id="globalConfirmDetail" class="hidden p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-left mb-5 text-xs text-slate-700"></div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="button" id="globalConfirmCancelBtn" onclick="closeConfirmDialog()" 
                            class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition-all active:scale-95">
                        Batal
                    </button>
                    <button type="button" id="globalConfirmSubmitBtn" 
                            class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-200 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                        <i id="globalConfirmBtnIcon" class="bi bi-check-lg"></i>
                        <span id="globalConfirmBtnText">Ya, Lanjutkan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Universal Themed Alert / Notification Modal -->
    <div id="globalAlertModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity" role="dialog" aria-modal="true" aria-labelledby="globalAlertTitle">
        <div class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200 transform transition-all">
            <!-- Accent Top Stripe -->
            <div id="globalAlertStripe" class="h-1.5 bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600"></div>

            <div class="p-6 text-center">
                <!-- Close Button in corner -->
                <button type="button" onclick="closeAlertDialog()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors" aria-label="Tutup">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>

                <!-- Icon with urgency ring -->
                <div id="globalAlertIconContainer" class="mx-auto mb-4 w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center shadow-xs">
                    <i id="globalAlertIcon" class="bi bi-exclamation-circle-fill text-2xl"></i>
                </div>

                <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" id="globalAlertTitle">Pemberitahuan</h3>
                <p class="text-xs sm:text-sm text-slate-600 mb-5 leading-relaxed" id="globalAlertMessage">
                    Pesan pemberitahuan sistem.
                </p>

                <!-- Action Button -->
                <div>
                    <button type="button" id="globalAlertOkBtn" onclick="closeAlertDialog()" 
                            class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-slate-950 bg-amber-400 hover:bg-amber-500 shadow-md shadow-amber-200 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                        <i class="bi bi-check2"></i>
                        <span id="globalAlertBtnText">Mengerti</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Vanilla JS Logic for Sidebar & Modals -->
    <script>
        // Global Modal Helpers
        window.openModal = function(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        };

        window.closeModal = function(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        };

        let activeConfirmCallback = null;

        window.showConfirmDialog = function(options) {
            const modal = document.getElementById('globalConfirmModal');
            if (!modal) return;

            const titleEl = document.getElementById('globalConfirmTitle');
            const msgEl = document.getElementById('globalConfirmMessage');
            const detailEl = document.getElementById('globalConfirmDetail');
            const iconContainer = document.getElementById('globalConfirmIconContainer');
            const iconEl = document.getElementById('globalConfirmIcon');
            const stripeEl = document.getElementById('globalConfirmStripe');
            const submitBtn = document.getElementById('globalConfirmSubmitBtn');
            const btnText = document.getElementById('globalConfirmBtnText');
            const btnIcon = document.getElementById('globalConfirmBtnIcon');
            const cancelBtn = document.getElementById('globalConfirmCancelBtn');

            const type = options.type || 'danger';
            const title = options.title || (type === 'danger' ? 'Konfirmasi Penghapusan' : 'Konfirmasi Tindakan');
            const message = options.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
            const detail = options.detail || '';
            const confirmText = options.confirmText || (type === 'danger' ? 'Ya, Hapus' : (type === 'success' ? 'Ya, Lanjutkan' : 'Konfirmasi'));
            const cancelText = options.cancelText || 'Batal';

            titleEl.textContent = title;
            msgEl.innerHTML = message;

            if (detail) {
                detailEl.innerHTML = detail;
                detailEl.classList.remove('hidden');
            } else {
                detailEl.classList.add('hidden');
                detailEl.innerHTML = '';
            }

            btnText.textContent = confirmText;
            cancelBtn.textContent = cancelText;

            // Reset classes
            iconContainer.className = 'mx-auto mb-4 w-14 h-14 rounded-2xl flex items-center justify-center shadow-xs';
            submitBtn.className = 'w-full py-2.5 px-4 rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-1.5 active:scale-95 text-white';

            if (type === 'danger') {
                stripeEl.className = 'h-1.5 bg-gradient-to-r from-rose-600 via-rose-500 to-amber-400';
                iconContainer.classList.add('bg-rose-50', 'text-rose-600', 'border', 'border-rose-200');
                iconEl.className = options.icon || 'bi bi-trash3-fill text-2xl';
                submitBtn.classList.add('bg-rose-600', 'hover:bg-rose-700', 'shadow-md', 'shadow-rose-200');
                btnIcon.className = 'bi bi-trash3';
            } else if (type === 'success') {
                stripeEl.className = 'h-1.5 bg-gradient-to-r from-emerald-600 via-teal-500 to-teal-400';
                iconContainer.classList.add('bg-emerald-50', 'text-emerald-600', 'border', 'border-emerald-200');
                iconEl.className = options.icon || 'bi bi-check2-circle text-2xl';
                submitBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-700', 'shadow-md', 'shadow-emerald-200');
                btnIcon.className = 'bi bi-check-lg';
            } else if (type === 'warning') {
                stripeEl.className = 'h-1.5 bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-400';
                iconContainer.classList.add('bg-amber-50', 'text-amber-600', 'border', 'border-amber-200');
                iconEl.className = options.icon || 'bi bi-exclamation-triangle-fill text-2xl';
                submitBtn.classList.add('bg-amber-500', 'hover:bg-amber-600', 'text-slate-950', 'shadow-md', 'shadow-amber-200');
                btnIcon.className = 'bi bi-check-lg';
            } else {
                stripeEl.className = 'h-1.5 bg-gradient-to-r from-blue-600 via-indigo-500 to-blue-400';
                iconContainer.classList.add('bg-blue-50', 'text-blue-600', 'border', 'border-blue-200');
                iconEl.className = options.icon || 'bi bi-info-circle-fill text-2xl';
                submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700', 'shadow-md', 'shadow-blue-200');
                btnIcon.className = 'bi bi-check-lg';
            }

            activeConfirmCallback = function() {
                closeConfirmDialog();
                if (typeof options.onConfirm === 'function') {
                    options.onConfirm();
                } else if (options.form && typeof options.form.submit === 'function') {
                    options.form.submit();
                }
            };

            submitBtn.onclick = activeConfirmCallback;

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };

        window.closeConfirmDialog = function() {
            const modal = document.getElementById('globalConfirmModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
            activeConfirmCallback = null;
        };

        let activeAlertCallback = null;

        window.showAlertDialog = function(options) {
            const modal = document.getElementById('globalAlertModal');
            if (!modal) {
                window._nativeAlert ? window._nativeAlert(typeof options === 'string' ? options : options.message) : console.log(options);
                return;
            }

            const titleEl = document.getElementById('globalAlertTitle');
            const msgEl = document.getElementById('globalAlertMessage');
            const iconContainer = document.getElementById('globalAlertIconContainer');
            const iconEl = document.getElementById('globalAlertIcon');
            const stripeEl = document.getElementById('globalAlertStripe');
            const okBtn = document.getElementById('globalAlertOkBtn');
            const btnText = document.getElementById('globalAlertBtnText');

            const opts = typeof options === 'string' ? { message: options } : (options || {});
            const type = opts.type || 'warning';
            const title = opts.title || (type === 'danger' ? 'Terjadi Kesalahan' : (type === 'success' ? 'Berhasil' : 'Pemberitahuan'));
            const message = opts.message || '';
            const btnLabel = opts.btnText || 'Mengerti';

            titleEl.textContent = title;
            msgEl.innerHTML = message;
            btnText.textContent = btnLabel;

            // Reset classes
            iconContainer.className = 'mx-auto mb-4 w-14 h-14 rounded-2xl flex items-center justify-center shadow-xs';
            okBtn.className = 'w-full py-2.5 px-4 rounded-xl text-xs font-bold shadow-md transition-all flex items-center justify-center gap-1.5 active:scale-95';

            if (type === 'danger') {
                stripeEl.className = 'h-1.5 bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500';
                iconContainer.classList.add('bg-rose-50', 'text-rose-600', 'border', 'border-rose-200');
                iconEl.className = opts.icon || 'bi bi-x-circle-fill text-2xl';
                okBtn.classList.add('bg-rose-600', 'hover:bg-rose-700', 'text-white', 'shadow-rose-200');
            } else if (type === 'success') {
                stripeEl.className = 'h-1.5 bg-gradient-to-r from-emerald-600 via-teal-500 to-teal-400';
                iconContainer.classList.add('bg-emerald-50', 'text-emerald-600', 'border', 'border-emerald-200');
                iconEl.className = opts.icon || 'bi bi-check-circle-fill text-2xl';
                okBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-700', 'text-white', 'shadow-emerald-200');
            } else if (type === 'info') {
                stripeEl.className = 'h-1.5 bg-gradient-to-r from-blue-600 via-indigo-500 to-blue-400';
                iconContainer.classList.add('bg-blue-50', 'text-blue-600', 'border', 'border-blue-200');
                iconEl.className = opts.icon || 'bi bi-info-circle-fill text-2xl';
                okBtn.classList.add('bg-blue-600', 'hover:bg-blue-700', 'text-white', 'shadow-blue-200');
            } else {
                stripeEl.className = 'h-1.5 bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600';
                iconContainer.classList.add('bg-amber-50', 'text-amber-600', 'border', 'border-amber-200');
                iconEl.className = opts.icon || 'bi bi-exclamation-triangle-fill text-2xl';
                okBtn.classList.add('bg-amber-400', 'hover:bg-amber-500', 'text-slate-950', 'shadow-amber-200');
            }

            activeAlertCallback = function() {
                closeAlertDialog();
                if (typeof opts.onOk === 'function') {
                    opts.onOk();
                }
            };

            okBtn.onclick = activeAlertCallback;
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };

        window.closeAlertDialog = function() {
            const modal = document.getElementById('globalAlertModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
            activeAlertCallback = null;
        };

        // Override standard window.alert to automatically use our stylish modal
        window._nativeAlert = window.alert;
        window.alert = function(msg) {
            window.showAlertDialog({
                title: 'Perhatian',
                message: msg,
                type: 'warning'
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Drawer Toggle for Mobile
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const closeBtn = document.getElementById('sidebarCloseBtn');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
            }

            if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);

            // Logout Modal Logic
            const logoutModal = document.getElementById('logoutConfirmModal');
            const openLogoutBtn = document.getElementById('openLogoutModalBtn');
            const closeLogoutBtn = document.getElementById('closeLogoutModalBtn');

            function openLogout() {
                logoutModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeLogout() {
                logoutModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            if (openLogoutBtn) openLogoutBtn.addEventListener('click', openLogout);
            if (closeLogoutBtn) closeLogoutBtn.addEventListener('click', closeLogout);

            if (logoutModal) {
                logoutModal.addEventListener('click', function(e) {
                    if (e.target === logoutModal) closeLogout();
                });
            }

            // Global Confirm Modal backdrop click
            const confirmModal = document.getElementById('globalConfirmModal');
            if (confirmModal) {
                confirmModal.addEventListener('click', function(e) {
                    if (e.target === confirmModal) closeConfirmDialog();
                });
            }

            // Delegated form submission with data-confirm
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (form && form.dataset && form.dataset.confirm && !form.dataset.confirmed) {
                    e.preventDefault();
                    window.showConfirmDialog({
                        title: form.dataset.confirmTitle || 'Konfirmasi Tindakan',
                        message: form.dataset.confirm,
                        detail: form.dataset.confirmDetail || '',
                        type: form.dataset.confirmType || 'danger',
                        confirmText: form.dataset.confirmBtn || (form.dataset.confirmType === 'success' ? 'Ya, Lanjutkan' : 'Ya, Hapus'),
                        icon: form.dataset.confirmIcon || '',
                        onConfirm: function() {
                            form.dataset.confirmed = 'true';
                            form.submit();
                        }
                    });
                }
            });

            // Global Alert Modal backdrop click
            const alertModal = document.getElementById('globalAlertModal');
            if (alertModal) {
                alertModal.addEventListener('click', function(e) {
                    if (e.target === alertModal) closeAlertDialog();
                });
            }

            // Keyboard accessibility: Escape closes any open modal
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (alertModal && !alertModal.classList.contains('hidden')) closeAlertDialog();
                    if (confirmModal && !confirmModal.classList.contains('hidden')) closeConfirmDialog();
                    if (logoutModal && !logoutModal.classList.contains('hidden')) closeLogout();
                    if (sidebar && !sidebar.classList.contains('-translate-x-full') && window.innerWidth < 1024) closeSidebar();
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
