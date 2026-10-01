@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto pb-12">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-medium text-slate-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-slate-600 font-semibold">Profil Saya</span>
            </nav>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pengaturan Akun & Profil</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola informasi data diri, detail unit kerja, dan keamanan kata sandi akun Anda.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm shadow-sm animate-fade-in">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0 text-emerald-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="font-medium">{{ session('success') }}</div>
        </div>
    @endif

    <!-- Profile Overview Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col sm:flex-row items-center sm:items-start gap-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-blue-50/60 to-transparent rounded-full -mr-20 -mt-20 pointer-events-none"></div>

        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-bold text-3xl shadow-md ring-4 ring-blue-50 shrink-0">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>

        <div class="flex-1 text-center sm:text-left space-y-2">
            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                @if($user->role === 'kepala_sekolah')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Kepala Sekolah
                    </span>
                @elseif($user->role === 'pembantu_sarpras')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-cyan-50 text-cyan-800 border border-cyan-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>
                        Pembantu Sarpras
                    </span>
                @elseif($user->role === 'sarpras')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        Staff Sarpras
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                        Jurusan / Unit Kerja
                    </span>
                @endif

                @if($user->jurusan)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        {{ $user->jurusan->nama }}
                    </span>
                @endif
            </div>

            <p class="text-sm text-slate-500 flex items-center justify-center sm:justify-start gap-1.5">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                {{ $user->email }}
            </p>

            <div class="pt-2 flex flex-wrap items-center justify-center sm:justify-start gap-4 text-xs text-slate-500">
                <span>Terdaftar sejak: <strong class="text-slate-700 font-semibold">{{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}</strong></span>
                <span>•</span>
                <span>Terakhir diperbarui: <strong class="text-slate-700 font-semibold">{{ $user->updated_at ? $user->updated_at->diffForHumans() : '-' }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Forms Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        <!-- 1. Form Biodata & Profil -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Informasi Profil
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Ubah nama lengkap, alamat email, dan informasi kontak akun Anda.</p>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                        class="w-full text-sm rounded-xl border border-slate-200 px-3.5 py-2.5 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all @error('name') border-rose-400 ring-2 ring-rose-100 @enderror"
                        placeholder="Masukkan nama lengkap">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                        class="w-full text-sm rounded-xl border border-slate-200 px-3.5 py-2.5 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all @error('email') border-rose-400 ring-2 ring-rose-100 @enderror"
                        placeholder="nama@smkdrsutomo-tmg.sch.id">
                    @error('email')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jika akun Jurusan/Unit Kerja, tambahkan input penanggung jawab -->
                @if($user->jurusan)
                    <div class="pt-2 border-t border-slate-100">
                        <label for="kepala_bengkel" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Kepala Unit / Bengkel
                        </label>
                        <input type="text" name="kepala_bengkel" id="kepala_bengkel" value="{{ old('kepala_bengkel', $user->jurusan->kepala_bengkel) }}"
                            class="w-full text-sm rounded-xl border border-slate-200 px-3.5 py-2.5 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                            placeholder="Nama Penanggung Jawab Unit">
                        <p class="text-[11px] text-slate-400 mt-1">Nama penanggung jawab untuk unit {{ $user->jurusan->nama }}.</p>
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Deskripsi / Lokasi Unit
                        </label>
                        <textarea name="deskripsi" id="deskripsi" rows="2"
                            class="w-full text-sm rounded-xl border border-slate-200 px-3.5 py-2.5 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                            placeholder="Keterangan singkat unit...">{{ old('deskripsi', $user->jurusan->deskripsi) }}</textarea>
                    </div>
                @endif

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm transition-all shadow-xs active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. Form Ganti Kata Sandi (Password) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Ganti Kata Sandi
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Pastikan akun Anda menggunakan kata sandi yang aman dan tidak mudah ditebak.</p>
            </div>

            <form action="{{ route('profile.password.update') }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <!-- Kata Sandi Saat Ini -->
                <div>
                    <label for="current_password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="current_password" id="current_password" required autocomplete="current-password"
                        class="w-full text-sm rounded-xl border border-slate-200 px-3.5 py-2.5 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all @error('current_password') border-rose-400 ring-2 ring-rose-100 @enderror"
                        placeholder="••••••••">
                    @error('current_password')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kata Sandi Baru -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Kata Sandi Baru <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password" id="password" required autocomplete="new-password"
                        class="w-full text-sm rounded-xl border border-slate-200 px-3.5 py-2.5 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all @error('password') border-rose-400 ring-2 ring-rose-100 @enderror"
                        placeholder="Minimal 6 karakter">
                    @error('password')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Konfirmasi Kata Sandi Baru -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Ulangi Kata Sandi Baru <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                        class="w-full text-sm rounded-xl border border-slate-200 px-3.5 py-2.5 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all"
                        placeholder="Ketik ulang kata sandi baru">
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-medium text-sm transition-all shadow-xs active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
