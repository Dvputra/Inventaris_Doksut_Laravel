<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIM Inventaris SMK Dr. Sutomo Temanggung</title>
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
<body class="font-sans bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 min-h-screen flex items-center justify-center p-4 sm:p-6 text-slate-800">

<div class="w-full max-w-4xl space-y-4">
    <!-- Top Action: Kembali ke Halaman Publik -->
    <div>
        <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold text-white/90 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 shadow-xs transition-all hover:-translate-x-0.5">
            <i class="bi bi-arrow-left text-sm"></i>
            <span>Kembali ke Halaman Publik</span>
        </a>
    </div>

    <!-- Main Login Card -->
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden grid grid-cols-1 lg:grid-cols-12">
        <!-- Left Side: Brand Banner (Hidden on Mobile) -->
        <div class="hidden lg:flex lg:col-span-5 bg-gradient-to-br from-blue-700 via-blue-800 to-blue-950 p-8 text-white flex-col justify-between relative overflow-hidden">
            <!-- Decorative circle -->
            <div class="absolute -top-16 -right-16 w-52 h-52 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -left-16 w-52 h-52 bg-amber-400/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-white p-2 flex items-center justify-center shadow-md ring-2 ring-amber-400/40">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo SMK Dr. Sutomo" class="w-9 h-9 object-contain">
                    </div>
                    <div>
                        <h2 class="font-black text-sm tracking-wider leading-none text-white">SMK Dr. SUTOMO</h2>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-400 text-slate-950">
                            TEMANGGUNG
                        </span>
                    </div>
                </div>

                <h3 class="text-xl font-extrabold text-white leading-snug mb-3">
                    SIM Inventaris &amp; Sarpras Sekolah
                </h3>
                <p class="text-xs text-blue-100/80 leading-relaxed mb-6">
                    Sistem informasi manajemen aset dan sarana prasarana terpadu SMK Dr. Sutomo Temanggung untuk monitoring bengkel kejuruan dan Sarpras pusat.
                </p>

                <div class="space-y-2.5 text-xs text-blue-100/90">
                    <div class="flex items-center gap-2.5">
                        <i class="bi bi-check-circle-fill text-amber-400 text-sm"></i>
                        <span>5 Konsentrasi Keahlian (TKR, TITL, TKI, TP, TKP)</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <i class="bi bi-check-circle-fill text-amber-400 text-sm"></i>
                        <span>Pencatatan Peminjaman Alat Bengkel</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <i class="bi bi-check-circle-fill text-amber-400 text-sm"></i>
                        <span>Alur Transparan Usulan Pengadaan Sarpras</span>
                    </div>
                </div>
            </div>

            <div class="relative z-10 pt-6 border-t border-white/10 text-[11px] text-blue-200/60">
                &copy; {{ date('Y') }} SMK Dr. Sutomo Temanggung.
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="lg:col-span-7 p-6 sm:p-10 flex flex-col justify-center">
            <!-- Mobile Brand Header -->
            <div class="flex items-center gap-3 mb-6 lg:hidden">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-10 h-10 object-contain">
                <div>
                    <h2 class="font-black text-sm text-slate-900 leading-none">SMK Dr. SUTOMO</h2>
                    <span class="text-[10px] font-semibold text-slate-500">TEMANGGUNG</span>
                </div>
            </div>

            <div class="mb-6">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60 mb-2">
                    SIM-INVENTARIS DOKSUT
                </span>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Masuk ke Akun Anda</h1>
                <p class="text-xs text-slate-500 mt-1">Silakan masukkan email dan kata sandi Anda.</p>
            </div>

            @if(session('success'))
                <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-check-circle-fill text-emerald-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-rose-600"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Email</label>
                    <div class="relative rounded-xl shadow-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-envelope text-sm"></i>
                        </div>
                        <input type="email" name="email" id="email" 
                               class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border @error('email') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                               placeholder="nama@sekolah.sch.id" 
                               value="{{ old('email', 'sarpras@sekolah.sch.id') }}" required autofocus>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">Kata Sandi</label>
                    <div class="relative rounded-xl shadow-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-lock text-sm"></i>
                        </div>
                        <input type="password" name="password" id="password" 
                               class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border @error('password') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                               placeholder="Masukkan password" value="password" required>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500">
                        <span class="text-xs text-slate-600">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md transition-colors">
                    <span>Masuk ke Dashboard</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <!-- Quick Demo Login Switcher -->
            <div class="mt-6 pt-5 border-t border-slate-100">
                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2.5">
                    ⚡ Akun Demo Siap Pakai (Klik untuk pilih):
                </span>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    <button type="button" onclick="fillForm('sarpras@sekolah.sch.id')" class="px-2.5 py-2 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-200/80 text-left transition-colors">
                        <div class="flex items-center gap-1.5 text-amber-800 font-bold text-xs">
                            <i class="bi bi-shield-lock-fill text-amber-600"></i>
                            <span>Sarpras</span>
                        </div>
                        <span class="block text-[10px] text-amber-600">Pusat</span>
                    </button>

                    <button type="button" onclick="fillForm('tkr@sekolah.sch.id')" class="px-2.5 py-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-left transition-colors">
                        <div class="flex items-center gap-1.5 text-slate-800 font-bold text-xs">
                            <i class="bi bi-truck text-blue-600"></i>
                            <span>TKR</span>
                        </div>
                        <span class="block text-[10px] text-slate-500">Otomotif</span>
                    </button>

                    <button type="button" onclick="fillForm('titl@sekolah.sch.id')" class="px-2.5 py-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-left transition-colors">
                        <div class="flex items-center gap-1.5 text-slate-800 font-bold text-xs">
                            <i class="bi bi-lightning-charge-fill text-amber-500"></i>
                            <span>TITL</span>
                        </div>
                        <span class="block text-[10px] text-slate-500">Listrik</span>
                    </button>

                    <button type="button" onclick="fillForm('tki@sekolah.sch.id')" class="px-2.5 py-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-left transition-colors">
                        <div class="flex items-center gap-1.5 text-slate-800 font-bold text-xs">
                            <i class="bi bi-eyedropper text-emerald-600"></i>
                            <span>TKI</span>
                        </div>
                        <span class="block text-[10px] text-slate-500">Kimia</span>
                    </button>

                    <button type="button" onclick="fillForm('tp@sekolah.sch.id')" class="px-2.5 py-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-left transition-colors">
                        <div class="flex items-center gap-1.5 text-slate-800 font-bold text-xs">
                            <i class="bi bi-gear-fill text-cyan-600"></i>
                            <span>TP</span>
                        </div>
                        <span class="block text-[10px] text-slate-500">Mesin</span>
                    </button>

                    <button type="button" onclick="fillForm('tkp@sekolah.sch.id')" class="px-2.5 py-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-left transition-colors">
                        <div class="flex items-center gap-1.5 text-slate-800 font-bold text-xs">
                            <i class="bi bi-buildings-fill text-slate-600"></i>
                            <span>TKP</span>
                        </div>
                        <span class="block text-[10px] text-slate-500">Konstruksi</span>
                    </button>
                </div>
                <span class="block text-[11px] text-slate-400 mt-2 text-center">
                    *Kata sandi semua akun demo adalah: <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-700 font-mono">password</code>
                </span>
            </div>
        </div>
    </div>
</div>

<script>
    function fillForm(email) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = 'password';
    }
</script>
</body>
</html>
