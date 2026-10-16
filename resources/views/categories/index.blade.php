@extends('layouts.app')

@section('title', 'Kelola Kategori Barang')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 ring-1 ring-blue-500/20">
                    <i class="bi bi-tags text-base"></i>
                </span>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Kelola Kategori Barang</h1>
            </div>
            <p class="text-sm text-slate-500 mt-1">Tambah dan kelola jenis/kategori barang inventaris untuk kemudahan klasifikasi dan penomoran aset otomatis.</p>
        </div>
        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <button type="button" onclick="openCreateModal()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xs transition-colors">
                <i class="bi bi-plus-circle text-sm"></i>
                <span>Tambah Kategori Baru</span>
            </button>
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <i class="bi bi-check-circle-fill text-emerald-600 text-lg shrink-0"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3">
            <i class="bi bi-exclamation-triangle-fill text-rose-600 text-lg shrink-0"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1">
            <div class="font-bold flex items-center gap-2">
                <i class="bi bi-exclamation-circle text-rose-600"></i> Terjadi kesalahan input:
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Stats Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i class="bi bi-tags-fill text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500">Total Kategori</p>
                <h3 class="text-xl font-bold text-slate-900">{{ $categories->count() }} Kategori</h3>
            </div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i class="bi bi-box-seam text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500">Total Aset Terkategori</p>
                <h3 class="text-xl font-bold text-slate-900">{{ $categories->sum('items_count') }} Item</h3>
            </div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <i class="bi bi-shield-check text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500">Status Modul</p>
                <h3 class="text-xl font-bold text-slate-900">Aktif & Siap Digunakan</h3>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5">
        <form action="{{ route('categories.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 items-center">
            <div class="flex-1 w-full relative rounded-xl shadow-xs">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="bi bi-search text-slate-400 text-xs"></i>
                </div>
                <input type="text" name="q" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Cari nama kategori, kode, atau keterangan..." value="{{ request('q') }}">
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs sm:text-sm shadow-xs transition-colors">
                    <i class="bi bi-filter"></i>
                    <span>Cari</span>
                </button>
                @if(request()->filled('q'))
                    <a href="{{ route('categories.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs sm:text-sm transition-colors" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise mr-1"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Categories -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 text-xs font-semibold uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4 w-28">Kode Kategori</th>
                        <th class="py-3 px-4">Nama Kategori</th>
                        <th class="py-3 px-4">Keterangan / Ruang Lingkup</th>
                        <th class="py-3 px-4 text-center w-36">Jumlah Barang</th>
                        <th class="py-3 px-4 text-right w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $index => $c)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 text-center text-slate-400 font-mono">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold font-mono bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $c->kode }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900">{{ $c->nama }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 text-xs leading-relaxed">
                                {{ $c->keterangan ?: '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($c->items_count > 0)
                                    <a href="{{ route('items.index', ['category_id' => $c->id]) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-colors" title="Lihat barang kategori ini">
                                        <i class="bi bi-box-seam"></i>
                                        <span>{{ $c->items_count }} Barang</span>
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-500">
                                        0 Barang
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <button type="button" 
                                            onclick='openEditModal(@json($c))' 
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 text-xs font-medium transition-colors" 
                                            title="Edit Kategori">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>Edit</span>
                                    </button>
                                    @if($c->items_count === 0)
                                        <button type="button" 
                                                onclick='openDeleteModal(@json($c))' 
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 text-xs font-medium transition-colors" 
                                                title="Hapus Kategori">
                                            <i class="bi bi-trash"></i>
                                            <span>Hapus</span>
                                        </button>
                                    @else
                                        <button type="button" 
                                                disabled 
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-300 bg-slate-50 text-xs font-medium cursor-not-allowed" 
                                                title="Kategori ini tidak dapat dihapus karena masih digunakan oleh {{ $c->items_count }} barang inventaris.">
                                            <i class="bi bi-trash"></i>
                                            <span>Hapus</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl">
                                    <i class="bi bi-tags"></i>
                                </div>
                                <p class="font-semibold text-slate-600 text-sm">Tidak ada kategori ditemukan</p>
                                <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci lain atau tambahkan kategori baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards (< md) -->
        <div class="md:hidden divide-y divide-slate-100">
            @forelse($categories as $c)
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold font-mono bg-blue-50 text-blue-700 border border-blue-200 mb-1">
                                {{ $c->kode }}
                            </span>
                            <h4 class="font-bold text-slate-900 text-sm">{{ $c->nama }}</h4>
                        </div>
                        @if($c->items_count > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                {{ $c->items_count }} Barang
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-500">
                                0 Barang
                            </span>
                        @endif
                    </div>
                    @if($c->keterangan)
                        <p class="text-xs text-slate-500">{{ $c->keterangan }}</p>
                    @endif
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" 
                                onclick='openEditModal(@json($c))' 
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold">
                            <i class="bi bi-pencil-square text-blue-600"></i> Edit
                        </button>
                        @if($c->items_count === 0)
                            <button type="button" 
                                    onclick='openDeleteModal(@json($c))' 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-semibold">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        @else
                            <button type="button" 
                                    disabled 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 text-xs font-semibold cursor-not-allowed">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400">
                    <p class="font-semibold text-slate-600 text-sm">Tidak ada kategori ditemukan</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div id="createModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden transition-opacity">
    <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        <div class="px-6 py-5 bg-gradient-to-r from-blue-700 to-blue-800 text-white flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-amber-300">
                    <i class="bi bi-plus-circle text-lg"></i>
                </div>
                <h3 class="font-bold text-base text-white">Tambah Kategori Baru</h3>
            </div>
            <button type="button" onclick="closeCreateModal()" class="text-white/70 hover:text-white transition-colors">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <form action="{{ route('categories.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="create_kode" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Kode Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       id="create_kode" 
                       name="kode" 
                       maxlength="10" 
                       required 
                       placeholder="Contoh: OLR, ELK, LAB, MSK" 
                       value="{{ old('kode') }}"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono font-bold uppercase text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                <p class="text-[11px] text-slate-500 mt-1">Gunakan singkatan 2–5 huruf kapital unik. Kode ini otomatis dipakai pada penomoran kode barang (misal: <span class="font-mono font-semibold text-slate-700">RPL-OLR-001</span>).</p>
            </div>

            <div>
                <label for="create_nama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       id="create_nama" 
                       name="nama" 
                       required 
                       placeholder="Contoh: Alat Olahraga, Elektronik, dll." 
                       value="{{ old('nama') }}"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
            </div>

            <div>
                <label for="create_keterangan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Keterangan / Ruang Lingkup <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                </label>
                <textarea id="create_keterangan" 
                          name="keterangan" 
                          rows="3" 
                          placeholder="Deskripsi singkat jenis atau kelompok barang yang tergolong dalam kategori ini..." 
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold text-sm transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xs transition-colors">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden transition-opacity">
    <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        <div class="px-6 py-5 bg-gradient-to-r from-blue-700 to-blue-800 text-white flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-amber-300">
                    <i class="bi bi-pencil-square text-lg"></i>
                </div>
                <h3 class="font-bold text-base text-white">Edit Kategori Barang</h3>
            </div>
            <button type="button" onclick="closeEditModal()" class="text-white/70 hover:text-white transition-colors">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <form id="editForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="edit_kode" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Kode Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       id="edit_kode" 
                       name="kode" 
                       maxlength="10" 
                       required 
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono font-bold uppercase text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                <p class="text-[11px] text-slate-500 mt-1">Harap diperhatikan: Mengubah kode kategori akan mempengaruhi rumus kode barang baru berikutnya.</p>
            </div>

            <div>
                <label for="edit_nama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       id="edit_nama" 
                       name="nama" 
                       required 
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
            </div>

            <div>
                <label for="edit_keterangan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Keterangan / Ruang Lingkup <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                </label>
                <textarea id="edit_keterangan" 
                          name="keterangan" 
                          rows="3" 
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold text-sm transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xs transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Kategori -->
<div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden transition-opacity">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        <div class="p-6 text-center space-y-4">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl">
                <i class="bi bi-trash-fill"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Hapus Kategori Barang?</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                    Apakah Anda yakin ingin menghapus kategori <span id="delete_nama" class="font-bold text-slate-800"></span> (<span id="delete_kode" class="font-mono font-bold text-slate-800"></span>)? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <form id="deleteForm" method="POST" class="pt-2">
                @csrf
                @method('DELETE')
                <div class="flex items-center justify-center gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold text-sm transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm shadow-xs transition-colors">
                        Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('create_kode').focus(), 50);
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    function openEditModal(category) {
        const form = document.getElementById('editForm');
        form.action = `/categories/${category.id}`;
        document.getElementById('edit_kode').value = category.kode || '';
        document.getElementById('edit_nama').value = category.nama || '';
        document.getElementById('edit_keterangan').value = category.keterangan || '';
        document.getElementById('editModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('edit_nama').focus(), 50);
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function openDeleteModal(category) {
        const form = document.getElementById('deleteForm');
        form.action = `/categories/${category.id}`;
        document.getElementById('delete_nama').textContent = category.nama;
        document.getElementById('delete_kode').textContent = category.kode;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
            closeDeleteModal();
        }
    });

    // Auto uppercase on kode inputs
    document.querySelectorAll('#create_kode, #edit_kode').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9_-]/g, '');
        });
    });
</script>
@endpush
@endsection
