@extends('layouts.app')

@section('title', 'Catat Peminjaman Alat')

@section('content')
<div class="mb-6">
    @if(isset($selectedItem))
        <a href="{{ route('items.show', $selectedItem) }}" 
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-xl shadow-xs transition-all mb-3">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Detail Barang ({{ $selectedItem->nama_barang }})</span>
        </a>
    @else
        <a href="{{ route('borrowings.index') }}" 
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-xl shadow-xs transition-all mb-3">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Riwayat Peminjaman</span>
        </a>
    @endif
    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Catat Peminjaman Alat Bengkel</h2>
    <p class="text-xs sm:text-sm text-slate-500 mt-1">Formulir peminjaman alat praktikum untuk siswa, guru, atau teknisi.</p>
</div>

@if(isset($selectedItem))
    <div class="mb-5 p-4 rounded-2xl bg-blue-50/80 border border-blue-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
        <div class="flex items-start sm:items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg flex-shrink-0 shadow-sm shadow-blue-200">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-bold text-blue-900">{{ $selectedItem->nama_barang }}</span>
                    <span class="text-[11px] font-mono font-semibold px-2 py-0.5 rounded bg-blue-100 text-blue-800">{{ $selectedItem->kode_barang }}</span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white text-slate-700 border border-blue-200">{{ $selectedItem->jurusan->kode ?? 'Umum' }}</span>
                </div>
                <p class="text-xs text-blue-700 mt-0.5">
                    Alat ini otomatis terintegrasi dari halaman barang. Stok total: <strong>{{ $selectedItem->jumlah }} {{ $selectedItem->satuan }}</strong> ({{ $selectedItem->units->where('status', 'tersedia')->count() }} unit fisik tersedia).
                </p>
            </div>
        </div>
        <a href="{{ route('borrowings.create') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-700 bg-white hover:bg-blue-100/60 border border-blue-200 px-3 py-1.5 rounded-xl transition-colors shadow-xs flex-shrink-0">
            <i class="bi bi-arrow-repeat"></i>
            <span>Pilih Alat Lain</span>
        </a>
    </div>
@endif

<div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 sm:p-8">
    <form action="{{ route('borrowings.store') }}" method="POST">
        @csrf

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
                                {{ (string) old('item_id', $selectedItemId ?? '') === (string) $it->id ? 'selected' : '' }}>
                            [{{ $it->kode_barang }}] {{ $it->nama_barang }} (Tersedia: {{ $it->jumlah }} {{ $it->satuan }}) - {{ $it->jurusan->kode ?? 'Umum' }}
                        </option>
                    @endforeach
                </select>
                <div class="searchable-select relative" data-target="item_id" data-placeholder="Cari alat bengkel..." data-empty="-- Pilih Alat yang Dipinjam --" data-onselect="updateUnitsDropdown">
                    <div class="searchable-select-trigger w-full px-3.5 py-2.5 text-xs rounded-xl border @error('item_id') border-rose-300 ring-1 ring-rose-200 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs cursor-pointer flex items-center justify-between gap-2"
                         tabindex="0">
                        <span class="searchable-select-label text-slate-400 truncate flex-1">-- Pilih Alat yang Dipinjam --</span>
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
                        data-selected="{{ old('item_unit_id', $selectedUnitId ?? '') }}"
                        class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs">
                    <option value="">-- Pinjam Umum (Tanpa Unit Spesifik) --</option>
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Pilih jika ingin melacak nomor meja atau unit fisik tertentu.</p>
            </div>

            <div class="md:col-span-3">
                <label for="jumlah" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Jumlah Dipinjam <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="jumlah" id="jumlah" min="1" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border @error('jumlah') border-rose-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       value="{{ old('jumlah', 1) }}" required>
                <p id="unitQuantityHint" class="text-[11px] text-amber-600 mt-1 hidden">
                    <i class="bi bi-info-circle me-1"></i>Otomatis 1 unit karena Anda memilih unit fisik spesifik.
                </p>
                @error('jumlah') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-3">
                <label for="tanggal_pinjam" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Tanggal Pinjam <span class="text-rose-500">*</span>
                </label>
                <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border @error('tanggal_pinjam') border-rose-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required>
                @error('tanggal_pinjam') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-6">
                <label for="nama_peminjam" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Nama Peminjam <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_peminjam" id="nama_peminjam" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border @error('nama_peminjam') border-rose-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       placeholder="Nama lengkap siswa / guru peminjam" 
                       value="{{ old('nama_peminjam') }}" required>
                @error('nama_peminjam') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-6">
                <label for="kelas_atau_jabatan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Kelas / Jabatan
                </label>
                <input type="text" name="kelas_atau_jabatan" id="kelas_atau_jabatan" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       placeholder="Contoh: XII TKR 2 / Guru Produktif" 
                       value="{{ old('kelas_atau_jabatan') }}">
            </div>

            <div class="md:col-span-6">
                <label for="kontak" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    No. HP / WhatsApp
                </label>
                <input type="text" name="kontak" id="kontak" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       placeholder="08xxxxxxxxxx" 
                       value="{{ old('kontak') }}">
            </div>

            <div class="md:col-span-12">
                <label for="catatan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Catatan / Keperluan Praktik
                </label>
                <textarea name="catatan" id="catatan" rows="3" 
                          class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                          placeholder="Keterangan tugas praktik, mata pelajaran, atau kondisi alat sebelum dipinjam...">{{ old('catatan') }}</textarea>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3 pt-5 border-t border-slate-100">
            <!-- Batal (Urgency: Neutral) -->
            <a href="{{ route('borrowings.index') }}" 
               class="w-full sm:w-auto text-center px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-xs transition-all active:scale-95">
                Batal
            </a>
            <!-- Simpan (Urgency: Primary Action / Blue) -->
            <button type="submit" 
                    class="w-full sm:w-auto px-5 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-200 rounded-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                <i class="bi bi-save"></i>
                <span>Simpan Peminjaman</span>
            </button>
        </div>
    </form>
</div>

<script>
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

            const options = [];
            hiddenSelect.querySelectorAll('option').forEach(opt => {
                if (opt.value === '') return;
                options.push({
                    value: opt.value,
                    text: opt.textContent.trim(),
                    el: opt
                });
            });

            function renderOptions(filter) {
                const query = (filter || '').toLowerCase();
                optionsList.innerHTML = '';
                activeIndex = -1;
                let count = 0;

                options.forEach((opt, idx) => {
                    if (query && !opt.text.toLowerCase().includes(query)) return;
                    const li = document.createElement('li');
                    li.className = 'px-3.5 py-2 text-xs cursor-pointer hover:bg-blue-50 hover:text-blue-700 transition-colors truncate';
                    li.textContent = opt.text;
                    li.dataset.idx = idx;
                    if (hiddenSelect.value === opt.value) {
                        li.classList.add('bg-blue-50', 'text-blue-700', 'font-semibold');
                    }
                    li.addEventListener('mousedown', e => {
                        e.preventDefault();
                        selectOption(idx);
                    });
                    optionsList.appendChild(li);
                    count++;
                });

                if (count === 0) {
                    const li = document.createElement('li');
                    li.className = 'px-3.5 py-2.5 text-xs text-slate-400 italic';
                    li.textContent = 'Tidak ditemukan';
                    optionsList.appendChild(li);
                }
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

            // Set initial label if a value is pre-selected
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
        if (!itemSelect || !unitSelect) return;

        const selectedOption = itemSelect.options[itemSelect.selectedIndex];
        const preselectedUnit = unitSelect.dataset.selected || '';

        unitSelect.innerHTML = '<option value="">-- Pinjam Umum (Tanpa Unit Spesifik) --</option>';

        if (selectedOption && selectedOption.value) {
            const rawUnits = selectedOption.getAttribute('data-units');
            if (rawUnits) {
                try {
                    const units = JSON.parse(rawUnits);
                    units.forEach(u => {
                        const opt = document.createElement('option');
                        opt.value = u.id;
                        let label = u.unit_code;
                        if (u.nomor_meja) label += ` (${u.nomor_meja})`;
                        if (u.nomor_seri) label += ` [SN: ${u.nomor_seri}]`;
                        opt.textContent = label;
                        if (preselectedUnit && String(u.id) === String(preselectedUnit)) {
                            opt.selected = true;
                        }
                        unitSelect.appendChild(opt);
                    });
                } catch (e) {
                    console.error('Error parsing units', e);
                }
            }
        }
        checkUnitQuantityLock();
    }

    function checkUnitQuantityLock() {
        const unitSelect = document.getElementById('item_unit_id');
        const jumlahInput = document.getElementById('jumlah');
        const unitHint = document.getElementById('unitQuantityHint');
        if (!unitSelect || !jumlahInput) return;

        if (unitSelect.value) {
            jumlahInput.value = 1;
            jumlahInput.setAttribute('readonly', 'true');
            jumlahInput.classList.add('bg-slate-100', 'cursor-not-allowed');
            if (unitHint) unitHint.classList.remove('hidden');
        } else {
            jumlahInput.removeAttribute('readonly');
            jumlahInput.classList.remove('bg-slate-100', 'cursor-not-allowed');
            if (unitHint) unitHint.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        initSearchableSelects();
        updateUnitsDropdown();

        const unitSelect = document.getElementById('item_unit_id');
        if (unitSelect) {
            unitSelect.addEventListener('change', checkUnitQuantityLock);
        }
    });
</script>
@endsection
