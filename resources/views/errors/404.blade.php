<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | SMK Dr. Sutomo Temanggung</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.9); opacity: 0.5; }
            50% { transform: scale(1.05); opacity: 0.2; }
            100% { transform: scale(0.9); opacity: 0.5; }
        }
        .float-anim { animation: float 4s ease-in-out infinite; }
        .pulse-ring { animation: pulse-ring 3s ease-in-out infinite; }
    </style>
</head>
<body class="font-sans bg-slate-50 text-slate-800 min-h-screen flex flex-col">

    {{-- Navbar --}}
    <nav class="bg-white border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8 rounded-lg shadow-sm">
                <div>
                    <span class="text-sm font-extrabold text-brand-900 tracking-tight">SIM Inventaris</span>
                    <span class="hidden sm:inline text-[10px] text-slate-400 font-medium ml-1.5">SMK Dr. Sutomo</span>
                </div>
            </a>
        </div>
    </nav>

    {{-- Content --}}
    <main class="flex-1 flex items-center justify-center px-4 py-12 sm:py-20">
        <div class="text-center max-w-lg">

            {{-- Animated Icon --}}
            <div class="relative inline-block mb-8">
                <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-full bg-blue-50 border border-blue-100 pulse-ring absolute inset-0 m-auto"></div>
                <div class="relative float-anim">
                    <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-3xl bg-gradient-to-br from-blue-500 to-blue-700 shadow-lg shadow-blue-200 flex items-center justify-center">
                        <i class="bi bi-question-lg text-white text-5xl sm:text-6xl"></i>
                    </div>
                </div>
            </div>

            {{-- Error Code --}}
            <h1 class="text-7xl sm:text-8xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-blue-800 tracking-tighter leading-none mb-3">
                404
            </h1>

            {{-- Message --}}
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight mb-2">
                Halaman Tidak Ditemukan
            </h2>
            <p class="text-sm text-slate-500 leading-relaxed mb-8 max-w-sm mx-auto">
                Halaman yang Anda cari mungkin sudah dihapus, dipindahkan, atau alamat URL salah ketik.
            </p>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-200 rounded-xl transition-all active:scale-95">
                    <i class="bi bi-house-door"></i>
                    <span>Kembali ke Beranda</span>
                </a>

                <button onclick="history.back()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-xs transition-all active:scale-95">
                    <i class="bi bi-arrow-left"></i>
                    <span>Halaman Sebelumnya</span>
                </button>
            </div>

            {{-- Helpful Links --}}
            <div class="mt-10 pt-6 border-t border-slate-200/80">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-3">Tautan Cepat</p>
                <div class="flex flex-wrap items-center justify-center gap-2">
                    <a href="{{ url('/') }}"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-blue-600 bg-slate-100 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">
                        <i class="bi bi-megaphone text-[10px]"></i>
                        Pengaduan
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-blue-600 bg-slate-100 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">
                            <i class="bi bi-speedometer2 text-[10px]"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('items.index') }}"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-blue-600 bg-slate-100 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">
                            <i class="bi bi-box-seam text-[10px]"></i>
                            Inventaris
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-blue-600 bg-slate-100 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">
                            <i class="bi bi-box-arrow-in-right text-[10px]"></i>
                            Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="text-center py-5 border-t border-slate-200/60">
        <p class="text-[11px] text-slate-400">&copy; {{ date('Y') }} SMK Dr. Sutomo Temanggung</p>
    </footer>
</body>
</html>
