@extends('layouts.app')

@section('title', 'Edit Peminjaman Alat')

@section('content')
<div class="mb-6">
    <a href="{{ route('borrowings.index') }}" 
       class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-xl shadow-xs transition-all mb-3">
        <i class="bi bi-arrow-left"></i>
        <span>Kembali ke Riwayat Peminjaman</span>
    </a>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Edit Peminjaman Alat</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Perbarui data sirkulasi peminjaman alat praktikum atau sesuaikan status pengembalian.</p>
        </div>
        <div>
            @if($borrowing->status === 'dipinjam')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                    <i class="bi bi-clock mr-1.5"></i> Sedang Dipinjam
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <i class="bi bi-check2-circle mr-1.5"></i> Sudah Kembali
                </span>
            @endif
        </div>
    </div>
</div>

<div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 sm:p-8">
    <form action="{{ route('borrowings.update', $borrowing) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
            <!-- Pilih Alat -->
            <div class="md:col-span-6">
                <label for="item_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Pilih Alat Bengkel <span class="text-rose-500">*</span>
                </label>
                <select name="item_id" id="item_id" class="hidden" required>
                    <option value="">-- Pilih Alat yang Dipinjam --</option>
                    @foreach($items as $it)
                        <option value="{{ $it->id }}" 
                                data-units="{{ json_encode($it->units) }}"
                                {{ old('item_id', $borrowing->item_id) == $it->id ? 'selected' : '' }}>
                            [{{ $it->kode_barang }}] {{ $it->nama_barang }} (Tersedia: {{ $it->jumlah }} {{ $it->satuan }}) - {{ $it->jurusan->kode }}
                        </option>
                    @endforeach
                </select>
                <div class="searchable-select relative" data-target="item_id" data-placeholder="Cari alat bengkel..." data-empty="-- Pilih Alat yang Dipinjam --" data-onselect="updateUnitsDropdown">
                    <div class="searchable-select-trigger w-full px-3.5 py-2.5 text-xs rounded-xl border @error('item_id') border-rose-300 ring-1 ring-rose-200 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs cursor-pointer flex items-center justify-between gap-2"
                         tabindex="0">
                        <span class="searchable-select-label text-slate-900 truncate flex-1">
                            [{{ $borrowing->item->kode_barang }}] {{ $borrowing->item->nama_barang }}
                        </span>
                        <i class="bi bi-chevron-expand text-slate-400 text-[10px] flex-shrink-0"></i>
                    </div>
                    <div class="searchable-select-dropdown hidden absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg z-50 overflow-hidden">
                        <div class="p-2 border-b border-slate-100">
                            <div class="relative">
                                <i class="bi bi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                                <input type="text" class="searchable-select-search w-full pl-7 pr-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50" placeholder="Cari alat bengkel...">
                            </div>
                        </div>
                        <ul class="searchable-select-options max-h-52 overflow-y-auto py-1"></ul>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Hanya barang jenis "Alat / Mesin" yang dapat dipinjam.</p>
                @error('item_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Pilih Unit Fisik Tertentu (Opsional) -->
            <div class="md:col-span-6" id="unitSelectContainer">
                <label for="item_unit_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Pilih Unit Fisik Tertentu <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <select name="item_unit_id" id="item_unit_id" 
                        class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs">
                    <option value="">-- Pinjam Umum (Tanpa Unit Spesifik) --</option>
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Pilih jika ingin melacak nomor meja atau unit fisik tertentu.</p>
            </div>

            <!-- Status Peminjaman -->
            <div class="md:col-span-4">
                <label for="status" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Status Peminjaman <span class="text-rose-500">*</span>
                </label>
                <select name="status" id="status" 
                        class="w-full px-3.5 py-2.5 text-xs rounded-xl border @error('status') border-rose-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs font-bold" required>
                    <option value="dipinjam" {{ old('status', $borrowing->status) === 'dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                    <option value="kembali" {{ old('status', $borrowing->status) === 'kembali' ? 'selected' : '' }}>Sudah Kembali</option>
                </select>
                @error('status') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-4">
                <label for="jumlah" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Jumlah Dipinjam <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="jumlah" id="jumlah" min="1" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border @error('jumlah') border-rose-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       value="{{ old('jumlah', $borrowing->jumlah) }}" required>
                @error('jumlah') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-4">
                <label for="tanggal_pinjam" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Tanggal Pinjam <span class="text-rose-500">*</span>
                </label>
                <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border @error('tanggal_pinjam') border-rose-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       value="{{ old('tanggal_pinjam', $borrowing->tanggal_pinjam?->format('Y-m-d')) }}" required>
                @error('tanggal_pinjam') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-4">
                <label for="tanggal_kembali" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Tanggal Pengembalian <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <input type="date" name="tanggal_kembali" id="tanggal_kembali" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       value="{{ old('tanggal_kembali', $borrowing->tanggal_kembali?->format('Y-m-d')) }}">
                <span class="block text-[11px] text-slate-400 mt-1">Otomatis terisi jika status "Sudah Kembali".</span>
            </div>

            <div class="md:col-span-4">
                <label for="nama_peminjam" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Nama Peminjam <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_peminjam" id="nama_peminjam" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border @error('nama_peminjam') border-rose-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       placeholder="Nama lengkap siswa / guru peminjam" 
                       value="{{ old('nama_peminjam', $borrowing->nama_peminjam) }}" required>
                @error('nama_peminjam') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-4">
                <label for="kelas_atau_jabatan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Kelas / Jabatan
                </label>
                <input type="text" name="kelas_atau_jabatan" id="kelas_atau_jabatan" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       placeholder="Contoh: XII TKR 2 / Guru Produktif" 
                       value="{{ old('kelas_atau_jabatan', $borrowing->kelas_atau_jabatan) }}">
            </div>

            <div class="md:col-span-4">
                <label for="kontak" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    No. HP / WhatsApp
                </label>
                <input type="text" name="kontak" id="kontak" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       placeholder="08xxxxxxxxxx" 
                       value="{{ old('kontak', $borrowing->kontak) }}">
            </div>

            <div class="md:col-span-12">
                <label for="catatan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Catatan / Keperluan Praktik
                </label>
                <textarea name="catatan" id="catatan" rows="3" 
                          class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                          placeholder="Keterangan tugas praktik, mata pelajaran, atau kondisi alat...">{{ old('catatan', $borrowing->catatan) }}</textarea>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-5 border-t border-slate-100">
            <!-- Tombol Hapus Peminjaman -->
            <button type="button" 
                    onclick="showConfirmDialog({
                        title: 'Hapus Data Peminjaman',
                        message: 'Apakah Anda yakin ingin menghapus data peminjaman alat {{ addslashes($borrowing->item->nama_barang ?? 'alat') }} oleh {{ addslashes($borrowing->nama_peminjam) }}?',
                        type: 'danger',
                        confirmText: 'Ya, Hapus',
                        form: document.getElementById('deleteBorrowingForm')
                    })" 
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl shadow-xs transition-colors self-start sm:self-auto">
                <i class="bi bi-trash text-sm"></i>
                <span>Hapus Peminjaman</span>
            </button>

            <div class="flex items-center gap-3">
                <!-- Batal -->
                <a href="{{ route('borrowings.index') }}" 
                   class="px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-xs transition-all active:scale-95">
                    Batal
                </a>
                <!-- Simpan Perubahan -->
                <button type="submit" 
                        class="px-5 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-200 rounded-xl transition-all active:scale-95 flex items-center gap-2">
                    <i class="bi bi-check-lg text-sm"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </div>
    </form>

    <!-- Hidden Delete Form -->
    <form id="deleteBorrowingForm" action="{{ route('borrowings.destroy', $borrowing) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

<script>
    const currentUnitId = @json(old('item_unit_id', $borrowing->item_unit_id));

    function initSearchableSelects() {
        document.querySelectorAll('.searchable-select').forEach(wrapper => {
            const targetId = wrapper.dataset.target;
            const placeholder = wrapper.dataset.placeholder || 'Cari...';
            const emptyLabel = wrapper.dataset.empty || '-- Pilih --';
            const onSelectFn = wrapper.dataset.onselect;
            const hiddenSelect = document.getElementById(targetId);
            if (!hiddenSelect) return;

            const trigger = wrapper.querySelector('.searchable-select-trigger');
            const label = wrapper.querySelector('.searchable-select-label');
            const dropdown = wrapper.querySelector('.searchable-select-dropdown');
            const searchInput = wrapper.querySelector('.searchable-select-search');
            const optionsList = wrapper.querySelector('.searchable-select-options');
            let activeIndex = -1;

            const options = Array.from(hiddenSelect.options).map((opt, i) => ({
                value: opt.value,
                text: opt.textContent.trim(),
                selected: opt.selected,
                index: i
            }));

            function renderOptions(filterText = '') {
                optionsList.innerHTML = '';
                const query = filterText.toLowerCase();
                const filtered = options.filter(o => !o.value || o.text.toLowerCase().includes(query));

                if (filtered.length === 0) {
                    optionsList.innerHTML = '<li class="px-3 py-2 text-xs text-slate-400 text-center">Tidak ditemukan</li>';
                    return;
                }

                filtered.forEach(opt => {
                    const li = document.createElement('li');
                    li.className = 'px-3 py-2 text-xs cursor-pointer hover:bg-blue-50 text-slate-700 transition-colors flex items-center justify-between';
                    if (opt.value === hiddenSelect.value) {
                        li.classList.add('bg-blue-50', 'text-blue-700', 'font-semibold');
                    }
                    li.textContent = opt.text;
                    li.dataset.idx = opt.index;
                    li.addEventListener('click', () => selectOption(opt.index));
                    optionsList.appendChild(li);
                });
            }

            function selectOption(idx) {
                const opt = options[idx];
                hiddenSelect.value = opt.value;
                label.textContent = opt.text;
                label.classList.remove('text-slate-400');
                label.classList.add('text-slate-900');
                closeDropdown();
                if (onSelectFn && typeof window[onSelectFn] === 'function') {
                    window[onSelectFn]();
                }
            }

            function openDropdown() {
                dropdown.classList.remove('hidden');
                searchInput.value = '';
                renderOptions('');
                setTimeout(() => searchInput.focus(), 10);
            }

            function closeDropdown() {
                dropdown.classList.add('hidden');
                activeIndex = -1;
            }

            function highlightItem(idx) {
                const items = optionsList.querySelectorAll('li[data-idx]');
                items.forEach(li => li.classList.remove('bg-blue-100'));
                if (idx >= 0 && idx < items.length) {
                    items[idx].classList.add('bg-blue-100');
                    items[idx].scrollIntoView({ block: 'nearest' });
                }
            }

            trigger.addEventListener('click', () => {
                dropdown.classList.contains('hidden') ? openDropdown() : closeDropdown();
            });

            trigger.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    openDropdown();
                }
            });

            searchInput.addEventListener('input', () => {
                renderOptions(searchInput.value);
            });

            searchInput.addEventListener('keydown', e => {
                const items = optionsList.querySelectorAll('li[data-idx]');
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeIndex = Math.min(activeIndex + 1, items.length - 1);
                    highlightItem(activeIndex);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeIndex = Math.max(activeIndex - 1, 0);
                    highlightItem(activeIndex);
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (activeIndex >= 0 && items[activeIndex]) {
                        selectOption(parseInt(items[activeIndex].dataset.idx));
                    }
                } else if (e.key === 'Escape') {
                    closeDropdown();
                    trigger.focus();
                }
            });

            searchInput.addEventListener('blur', () => {
                setTimeout(closeDropdown, 150);
            });

            document.addEventListener('click', e => {
                if (!wrapper.contains(e.target)) closeDropdown();
            });

            if (hiddenSelect.value) {
                const sel = options.find(o => o.value === hiddenSelect.value);
                if (sel) {
                    label.textContent = sel.text;
                    label.classList.remove('text-slate-400');
                    label.classList.add('text-slate-900');
                }
            }
        });
    }

    function updateUnitsDropdown() {
        const itemSelect = document.getElementById('item_id');
        const unitSelect = document.getElementById('item_unit_id');
        const selectedOption = itemSelect.options[itemSelect.selectedIndex];

        unitSelect.innerHTML = '<option value="">-- Pinjam Umum (Tanpa Unit Spesifik) --</option>';

        if (selectedOption && selectedOption.value) {
            const rawUnits = selectedOption.getAttribute('data-units');
            if (rawUnits) {
                const units = JSON.parse(rawUnits);
                units.forEach(u => {
                    const opt = document.createElement('option');
                    opt.value = u.id;
                    let label = u.unit_code;
                    if (u.nomor_meja) label += ` (${u.nomor_meja})`;
                    if (u.nomor_seri) label += ` [SN: ${u.nomor_seri}]`;
                    if (currentUnitId && parseInt(currentUnitId) === parseInt(u.id)) {
                        opt.selected = true;
                    }
                    opt.textContent = label;
                    unitSelect.appendChild(opt);
                });
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        initSearchableSelects();
        updateUnitsDropdown();
    });
</script>
@endsection
