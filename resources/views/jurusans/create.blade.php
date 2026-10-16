@extends('layouts.app')

@section('title', 'Tambah Unit Kerja / Jurusan Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('jurusans.index') }}" class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors shadow-xs" title="Kembali ke Daftar">
            <i class="bi bi-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Tambah Unit Kerja / Jurusan Baru</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Daftarkan program keahlian baru atau unit kerja sekolah ke dalam sistem inventaris.</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form action="{{ route('jurusans.store') }}" method="POST">
            @csrf

            <div class="space-y-5">
                <!-- Pilihan Unit Kerja Utama (Induk) atau Sub-Unit / Lab -->
                <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-200/80 space-y-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-900">
                        Struktur &amp; Kedudukan Unit Kerja
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-center gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-blue-300 transition-all">
                            <input type="radio" name="is_sub_unit" value="0" {{ old('parent_id') ? '' : 'checked' }} onchange="toggleParentSelect(false)" class="text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Unit Kerja Utama / Jurusan</span>
                                <span class="text-[11px] text-slate-500">Berdiri sendiri (misal: Jurusan RPL, OTKP, BKK)</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-blue-300 transition-all">
                            <input type="radio" name="is_sub_unit" value="1" {{ old('parent_id') ? 'checked' : '' }} onchange="toggleParentSelect(true)" class="text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Sub-Unit / Lab / Ruangan</span>
                                <span class="text-[11px] text-slate-500">Di bawah naungan jurusan lain (misal: Lab RPL 1)</span>
                            </div>
                        </label>
                    </div>

                    <div id="parentSelectContainer" class="{{ old('parent_id') ? '' : 'hidden' }} pt-2 border-t border-blue-200/60">
                        <label for="parent_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Pilih Unit Kerja Utama (Induk) <span class="text-rose-500">*</span>
                        </label>
                        <select name="parent_id" id="parent_id" 
                                class="w-full px-3.5 py-2.5 bg-white border @error('parent_id') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                            <option value="">-- Pilih Jurusan Induk --</option>
                            @foreach($parentCandidates as $p)
                                <option value="{{ $p->id }}" data-kode="{{ $p->kode }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }} ({{ $p->kode }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1">Barang di sub-unit ini nantinya bisa dipantau langsung oleh akun jurusan induknya.</p>
                        @error('parent_id')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Kode & Nama Unit -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                    <div class="sm:col-span-5">
                        <label for="kode" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Kode / Singkatan <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center rounded-xl shadow-xs overflow-hidden border @error('kode') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror bg-slate-50 focus-within:bg-white focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 transition-all">
                            <span id="parentCodePrefix" class="hidden px-3 py-2.5 bg-blue-100/70 border-r border-blue-200 font-mono font-bold text-sm text-blue-800 select-none shrink-0">
                                RPL-
                            </span>
                            <input type="text" id="kode_input" 
                                   class="w-full px-3.5 py-2.5 bg-transparent border-0 text-sm font-mono uppercase text-slate-800 focus:outline-none" 
                                   placeholder="Contoh: DKV, RPL, BKK" 
                                   value="{{ old('kode') }}" required maxlength="20">
                            <!-- Hidden input yang menyimpan nilai final yang dikirim ke controller -->
                            <input type="hidden" name="kode" id="kode_hidden" value="{{ old('kode') }}">
                        </div>
                        <p id="kodeHelpText" class="text-[11px] text-slate-500 mt-1">Gunakan singkatan huruf kapital (misal: RPL, TKJ).</p>
                        @error('kode')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-7">
                        <label for="nama" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nama Lengkap Unit Kerja / Jurusan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama" id="nama" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border @error('nama') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                               placeholder="Contoh: Desain Komunikasi Visual, Bursa Kerja Khusus" 
                               value="{{ old('nama') }}" required maxlength="255">
                        @error('nama')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Kepala Bengkel / Kepala Unit & NIP -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                    <div class="sm:col-span-8">
                        <label for="kepala_bengkel" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nama Kepala Bengkel / Kepala Unit Kerja
                        </label>
                        <div class="relative rounded-xl">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="bi bi-person-badge text-xs"></i>
                            </div>
                            <input type="text" name="kepala_bengkel" id="kepala_bengkel" 
                                   class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border @error('kepala_bengkel') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                                   placeholder="Contoh: Bpk. Hendra Gunawan, S.Pd / Waka Bidang..." 
                                   value="{{ old('kepala_bengkel') }}">
                        </div>
                        <span class="block text-[11px] text-slate-400 mt-1">Nama ini akan dicantumkan pada dashboard unit serta lembar pengesahan tanda tangan laporan cetak.</span>
                        @error('kepala_bengkel')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-4">
                        <label for="nip" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            NIP / NIY / NPY (Opsional)
                        </label>
                        <div class="relative rounded-xl">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="bi bi-card-text text-xs"></i>
                            </div>
                            <input type="text" name="nip" id="nip" 
                                   class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border @error('nip') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm font-mono text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                                   placeholder="Contoh: 198507..." 
                                   value="{{ old('nip') }}" maxlength="50">
                        </div>
                        <span class="block text-[11px] text-slate-400 mt-1">Tercetak otomatis di dokumen cetak PDF.</span>
                        @error('nip')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Deskripsi / Ruang Lingkup -->
                <div>
                    <label for="deskripsi" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Deskripsi / Ruang Lingkup Fasilitas (Opsional)
                    </label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" 
                              class="w-full px-3.5 py-2.5 bg-slate-50 border @error('deskripsi') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                              placeholder="Keterangan singkat laboratorium, bengkel, ruang lingkup tugas unit kerja...">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Opsi Buat Akun Login Otomatis -->
                <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-100 space-y-4">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" name="create_user_account" id="create_user_account" value="1" 
                               class="mt-1 h-4 w-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500" 
                               {{ old('create_user_account') ? 'checked' : '' }}
                               onchange="toggleAccountFields(this.checked)">
                        <div>
                            <label for="create_user_account" class="text-xs font-bold text-slate-900 cursor-pointer">
                                Buat Akun Pengguna Langsung untuk Unit Ini
                            </label>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Mengaktifkan opsi ini akan langsung membuat akun login dengan role Akun Jurusan/Unit Kerja.
                            </p>
                        </div>
                    </div>

                    <div id="accountFields" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 {{ old('create_user_account') ? '' : 'hidden' }}">
                        <div>
                            <label for="user_email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Alamat Email Login <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" name="user_email" id="user_email" 
                                   class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" 
                                   placeholder="unit@sekolah.sch.id" 
                                   value="{{ old('user_email') }}">
                            @error('user_email')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="user_password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Password Akun <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" name="user_password" id="user_password" 
                                   class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" 
                                   placeholder="Minimal 6 karakter" 
                                   value="{{ old('user_password') }}">
                            @error('user_password')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3 mt-8 pt-5 border-t border-slate-100">
                <a href="{{ route('jurusans.index') }}" class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm transition-colors">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xs transition-colors">
                    <i class="bi bi-save text-sm"></i>
                    <span>Simpan Unit Kerja</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateCodeField() {
        const isSubUnit = document.querySelector('input[name="is_sub_unit"]:checked').value === '1';
        const parentSelect = document.getElementById('parent_id');
        const prefixSpan = document.getElementById('parentCodePrefix');
        const kodeInput = document.getElementById('kode_input');
        const kodeHidden = document.getElementById('kode_hidden');
        const helpText = document.getElementById('kodeHelpText');

        if (isSubUnit) {
            const selectedOpt = parentSelect.options[parentSelect.selectedIndex];
            const parentCode = selectedOpt && selectedOpt.dataset.kode ? selectedOpt.dataset.kode : '';

            if (parentCode) {
                prefixSpan.textContent = parentCode + '-';
                prefixSpan.classList.remove('hidden');
                kodeInput.placeholder = 'Contoh: LAB1, LAB2, 01';
                helpText.innerHTML = `Kode akhir yang tersimpan: <span class="font-mono font-bold text-blue-700">${parentCode}-${kodeInput.value.trim().toUpperCase() || '...'}</span>`;
                kodeHidden.value = kodeInput.value.trim() ? (parentCode + '-' + kodeInput.value.trim().toUpperCase()) : '';
            } else {
                prefixSpan.classList.add('hidden');
                kodeInput.placeholder = 'Pilih jurusan induk di atas terlebih dahulu';
                helpText.textContent = 'Pilih unit kerja induk di atas untuk memunculkan awalan kode.';
                kodeHidden.value = kodeInput.value.trim().toUpperCase();
            }
        } else {
            prefixSpan.classList.add('hidden');
            kodeInput.placeholder = 'Contoh: DKV, RPL, BKK';
            helpText.textContent = 'Gunakan singkatan huruf kapital unik (misal: RPL, TKJ).';
            kodeHidden.value = kodeInput.value.trim().toUpperCase();
        }
    }

    function toggleParentSelect(show) {
        const container = document.getElementById('parentSelectContainer');
        const select = document.getElementById('parent_id');
        if (show) {
            container.classList.remove('hidden');
            select.setAttribute('required', 'required');
        } else {
            container.classList.add('hidden');
            select.removeAttribute('required');
            select.value = '';
        }
        updateCodeField();
    }

    function toggleAccountFields(show) {
        const fields = document.getElementById('accountFields');
        if (show) {
            fields.classList.remove('hidden');
        } else {
            fields.classList.add('hidden');
        }
    }

    document.getElementById('parent_id').addEventListener('change', updateCodeField);
    document.getElementById('kode_input').addEventListener('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9_-]/g, '');
        updateCodeField();
    });

    // Inisialisasi saat pertama kali dimuat
    document.addEventListener('DOMContentLoaded', function() {
        // Jika sebelumnya ada old kode dan merupakan sub-unit, pisahkan prefixnya
        const isSubUnit = document.querySelector('input[name="is_sub_unit"]:checked').value === '1';
        const parentSelect = document.getElementById('parent_id');
        if (isSubUnit && parentSelect.value) {
            const selectedOpt = parentSelect.options[parentSelect.selectedIndex];
            const parentCode = selectedOpt && selectedOpt.dataset.kode ? selectedOpt.dataset.kode : '';
            const rawVal = document.getElementById('kode_input').value;
            if (parentCode && rawVal.startsWith(parentCode + '-')) {
                document.getElementById('kode_input').value = rawVal.substring(parentCode.length + 1);
            }
        }
        updateCodeField();
    });
</script>
@endsection
