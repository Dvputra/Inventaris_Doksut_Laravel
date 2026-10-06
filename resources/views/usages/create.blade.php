@extends('layouts.app')

@section('title', 'Catat Pemakaian Bahan')

@section('content')
<div class="mb-6">
    <a href="{{ route('usages.index') }}" 
       class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-xl shadow-xs transition-all mb-3">
        <i class="bi bi-arrow-left"></i>
        <span>Kembali ke Log Pemakaian</span>
    </a>
    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Catat Pemakaian Bahan</h2>
    <p class="text-xs sm:text-sm text-slate-500 mt-1">Formulir pemakaian bahan habis pakai / ATK. Stok barang akan otomatis berkurang setelah disimpan.</p>
</div>

<div class="max-w-3xl mx-auto bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 sm:p-8">
    <form action="{{ route('usages.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
            <!-- Pilih Bahan -->
            <div class="md:col-span-12">
                <label for="item_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Pilih Bahan / ATK <span class="text-rose-500">*</span>
                </label>
                <select name="item_id" id="item_id" class="hidden" required>
                    <option value="">-- Pilih Bahan dari Inventaris --</option>
                    @foreach($items as $it)
                        <option value="{{ $it->id }}" 
                                data-stok="{{ $it->jumlah }}" 
                                data-satuan="{{ $it->satuan }}"
                                {{ old('item_id', $selectedItemId) == $it->id ? 'selected' : '' }}>
                            [{{ $it->kode_barang }}] {{ $it->nama_barang }} (Sisa Stok: {{ $it->jumlah }} {{ $it->satuan }})
                        </option>
                    @endforeach
                </select>
                <div class="searchable-select relative" data-target="item_id" data-placeholder="Cari bahan / ATK..." data-empty="-- Pilih Bahan dari Inventaris --" data-onselect="updateStockInfo">
                    <div class="searchable-select-trigger w-full px-3.5 py-2.5 text-xs rounded-xl border @error('item_id') border-rose-300 ring-1 ring-rose-200 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs cursor-pointer flex items-center justify-between gap-2"
                         tabindex="0">
                        <span class="searchable-select-label text-slate-400 truncate flex-1">-- Pilih Bahan dari Inventaris --</span>
                        <i class="bi bi-chevron-expand text-slate-400 text-[10px] flex-shrink-0"></i>
                    </div>
                    <div class="searchable-select-dropdown hidden absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg z-50 overflow-hidden">
                        <div class="p-2 border-b border-slate-100">
                            <div class="relative">
                                <i class="bi bi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                                <input type="text" class="searchable-select-search w-full pl-7 pr-3 py-2 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50" placeholder="Cari bahan / ATK...">
                            </div>
                        </div>
                        <ul class="searchable-select-options max-h-52 overflow-y-auto py-1"></ul>
                    </div>
                </div>
                @error('item_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror

                <div id="stockBadge" class="mt-2.5" style="display: none;">
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl bg-blue-50 text-blue-800 border border-blue-200 shadow-xs">
                        <i class="bi bi-box-seam text-blue-600"></i>
                        <span>Stok Tersedia:</span>
                        <strong id="stockAvailableText" class="text-blue-900">0</strong>
                        <span id="unitText">unit</span>
                    </span>
                </div>
            </div>

            <!-- Jumlah Pemakaian & Satuan -->
            <div class="md:col-span-6">
                <label for="jumlah" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Jumlah yang Dipakai <span class="text-rose-500">*</span>
                </label>
                <div class="flex">
                    <input type="number" name="jumlah" id="jumlah" min="0.01" step="any" 
                           class="flex-1 px-3.5 py-2.5 text-xs rounded-l-xl border @error('jumlah') border-rose-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                           value="{{ old('jumlah', 1) }}" required>
                    <span class="px-3.5 py-2.5 text-xs font-semibold bg-slate-100 text-slate-600 border border-l-0 border-slate-200 rounded-r-xl" id="satuanLabel">
                        Satuan
                    </span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Stok akan langsung dipotong dari total inventaris saat disimpan.</p>
                @error('jumlah') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Tanggal Pemakaian -->
            <div class="md:col-span-6">
                <label for="tanggal_pemakaian" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Tanggal Pemakaian <span class="text-rose-500">*</span>
                </label>
                <input type="date" name="tanggal_pemakaian" id="tanggal_pemakaian" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border @error('tanggal_pemakaian') border-rose-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       value="{{ old('tanggal_pemakaian', date('Y-m-d')) }}" required>
                @error('tanggal_pemakaian') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Guru Pengampu / Penanggung Jawab & Kelas -->
            <div class="md:col-span-6">
                <label for="nama_guru" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Nama Guru Pengampu / Penanggung Jawab <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_guru" id="nama_guru" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border @error('nama_guru') border-rose-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       placeholder="Contoh: Bpk. Budi Santoso, S.Pd / Ibu Ratna (Staf TU)" 
                       value="{{ old('nama_guru') }}" required>
                @error('nama_guru') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-6">
                <label for="kelas" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Kelas / Kelompok Siswa / Nama <span class="text-[10px] font-normal text-slate-400">(Opsional)</span>
                </label>
                <input type="text" name="kelas" id="kelas" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border @error('kelas') border-rose-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       placeholder="Contoh: XI TKR 1 / Kelompok A / Budi Pratama" 
                       value="{{ old('kelas') }}">
                @error('kelas') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Keperluan / Jobsheet / Unit Kerja -->
            <div class="md:col-span-12">
                <label for="keperluan_jobsheet" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Materi / Jobsheet Praktikum / Unit Kerja <span class="text-[10px] font-normal text-slate-400">(Opsional)</span>
                </label>
                <input type="text" name="keperluan_jobsheet" id="keperluan_jobsheet" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border @error('keperluan_jobsheet') border-rose-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       placeholder="Contoh: Praktik Tune Up EFI Mesin Avanza / Pemeliharaan Unit Kerja Tata Usaha" 
                       value="{{ old('keperluan_jobsheet') }}">
                @error('keperluan_jobsheet') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Catatan Tambahan -->
            <div class="md:col-span-12">
                <label for="catatan" class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Tambahan (Opsional)</label>
                <textarea name="catatan" id="catatan" rows="3" 
                          class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                          placeholder="Keterangan kondisi sisa, kelompok praktikan, atau informasi pendukung lainnya...">{{ old('catatan') }}</textarea>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3 pt-5 border-t border-slate-100">
            <!-- Batal (Urgency: Neutral) -->
            <a href="{{ route('usages.index') }}" 
               class="w-full sm:w-auto text-center px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-xs transition-all active:scale-95">
                Batal
            </a>
            <!-- Simpan (Urgency: Primary Action / Blue) -->
            <button type="submit" 
                    class="w-full sm:w-auto px-5 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-200 rounded-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                <i class="bi bi-check2-circle"></i>
                <span>Simpan & Kurangi Stok</span>
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

    function updateStockInfo() {
        const select = document.getElementById('item_id');
        const selectedOption = select.options[select.selectedIndex];
        const stockBadge = document.getElementById('stockBadge');
        const stockAvailableText = document.getElementById('stockAvailableText');
        const unitText = document.getElementById('unitText');
        const satuanLabel = document.getElementById('satuanLabel');
        const inputJumlah = document.getElementById('jumlah');

        if (selectedOption && selectedOption.value) {
            const stock = selectedOption.getAttribute('data-stok');
            const unit = selectedOption.getAttribute('data-satuan');

            stockAvailableText.textContent = stock;
            unitText.textContent = unit;
            satuanLabel.textContent = unit;
            inputJumlah.setAttribute('max', stock);
            stockBadge.style.display = 'block';
        } else {
            stockBadge.style.display = 'none';
            satuanLabel.textContent = 'Satuan';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        initSearchableSelects();
        updateStockInfo();
    });
</script>
@endsection
