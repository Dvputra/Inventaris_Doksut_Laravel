@extends('layouts.app')

@section('title', 'Edit Akun - ' . $user->name)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors mb-3">
            <i class="bi bi-arrow-left text-sm"></i>
            <span>Kembali ke Kelola Akun</span>
        </a>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 ring-1 ring-blue-500/20">
                <i class="bi bi-person-gear text-base"></i>
            </span>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Edit Akun Pengguna</h1>
        </div>
        <p class="text-sm text-slate-500 mt-1">Perbarui data login atau hak akses untuk <strong>{{ $user->name }}</strong>.</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-5">
                <div class="sm:col-span-6">
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nama Lengkap / Nama Akun <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border @error('name') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-6">
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border @error('email') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-6">
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Kata Sandi Baru (Opsional)
                    </label>
                    <input type="password" name="password" id="password" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border @error('password') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                           placeholder="Kosongkan jika tidak ingin mengubah password">
                    <span class="block text-[11px] text-slate-400 mt-1">Isi hanya jika ingin mereset/mengganti password.</span>
                    @error('password')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-6">
                    <label for="role" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Peran / Hak Akses <span class="text-rose-500">*</span>
                    </label>
                    <select name="role" id="role" class="w-full px-3.5 py-2.5 bg-slate-50 border @error('role') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required onchange="toggleJurusan(this.value)">
                        <option value="jurusan" {{ old('role', $user->role) == 'jurusan' ? 'selected' : '' }}>Akun Jurusan (Program Keahlian)</option>
                        <option value="sarpras" {{ old('role', $user->role) == 'sarpras' ? 'selected' : '' }}>Admin Pusat (Sarpras)</option>
                    </select>
                    @error('role')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jurusan Selection -->
                <div class="sm:col-span-12" id="jurusanWrapper">
                    <label for="jurusan_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Pilih Program Keahlian / Jurusan <span class="text-rose-500">*</span>
                    </label>
                    <select name="jurusan_id" id="jurusan_id" class="w-full px-3.5 py-2.5 bg-slate-50 border @error('jurusan_id') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach($jurusans as $j)
                            <option value="{{ $j->id }}" {{ old('jurusan_id', $user->jurusan_id) == $j->id ? 'selected' : '' }}>
                                {{ $j->kode }} - {{ $j->nama }} (Kepala Bengkel: {{ $j->kepala_bengkel ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('jurusan_id')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3 mt-8 pt-5 border-t border-slate-100">
                <a href="{{ route('users.index') }}" class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm transition-colors">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xs transition-colors">
                    <i class="bi bi-save text-sm"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleJurusan(role) {
        const wrapper = document.getElementById('jurusanWrapper');
        const select = document.getElementById('jurusan_id');
        if (role === 'sarpras') {
            wrapper.style.display = 'none';
            select.required = false;
        } else {
            wrapper.style.display = 'block';
            select.required = true;
        }
    }
    toggleJurusan(document.getElementById('role').value);
</script>
@endpush
@endsection
