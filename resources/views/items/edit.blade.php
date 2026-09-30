@extends('layouts.app')

@section('title', 'Ubah Data Barang - ' . $item->nama_barang)

@section('content')
<div class="mb-6">
    <a href="{{ route('items.show', $item) }}" 
       class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-xl shadow-xs transition-all mb-3">
        <i class="bi bi-arrow-left"></i>
        <span>Kembali ke Detail Barang</span>
    </a>
    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Edit Barang: {{ $item->nama_barang }}</h2>
    <p class="text-xs sm:text-sm text-slate-500 mt-1">Perbarui informasi barang inventaris bengkel atau laboratorium.</p>
</div>

<div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 sm:p-8">
    <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- SECTION 1: IDENTITAS & PENGELOMPOKAN -->
        <div class="flex items-center gap-2 text-sm font-bold text-blue-700 pb-3 mb-5 border-b border-slate-100">
            <i class="bi bi-tag text-base"></i>
            <span>1. Identitas & Pengelompokan Barang</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
            <!-- Kode & Nama Barang -->
            <div class="md:col-span-4">
                <label for="kode_barang" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Kode Barang <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="kode_barang" id="kode_barang" 
                       class="w-full font-mono px-3.5 py-2.5 text-xs rounded-xl border @error('kode_barang') border-rose-300 ring-1 ring-rose-200 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       value="{{ old('kode_barang', $item->kode_barang) }}" required>
                @error('kode_barang') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-8">
                <label for="nama_barang" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Nama Barang / Mesin / Alat <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_barang" id="nama_barang" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border @error('nama_barang') border-rose-300 ring-1 ring-rose-200 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       value="{{ old('nama_barang', $item->nama_barang) }}" required>
                @error('nama_barang') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Jurusan & Kategori -->
            @if(Auth::user()->isSarpras())
                <div class="md:col-span-6">
                    <label for="jurusan_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Unit Kepemilikan / Jurusan <span class="text-rose-500">*</span>
                    </label>
                    <select name="jurusan_id" id="jurusan_id" 
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border @error('jurusan_id') border-rose-300 ring-1 ring-rose-200 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                            required onchange="handleJurusanChange()">
                        @foreach($jurusans as $j)
                            <option value="{{ $j->id }}" data-kode="{{ $j->kode }}" {{ old('jurusan_id', $item->jurusan_id) == $j->id ? 'selected' : '' }}>
                                [{{ $j->kode }}] {{ $j->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="md:col-span-6">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jurusan Penempatan</label>
                    <input type="text" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 text-slate-600" value="{{ $item->jurusan->nama }} ({{ $item->jurusan->kode }})" readonly disabled>
                    <input type="hidden" id="jurusan_id" value="{{ $item->jurusan_id }}" data-kode="{{ $item->jurusan->kode }}">
                </div>
            @endif

            <div class="md:col-span-6">
                <label for="category_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Kategori Barang</label>
                <select name="category_id" id="category_id" 
                        class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                        onchange="handleCategoryChange()">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" data-kode="{{ $cat->kode }}" {{ old('category_id', $item->category_id) == $cat->id ? 'selected' : '' }}>
                            [{{ $cat->kode }}] {{ $cat->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- KLASIFIKASI & PERUNTUKAN SARPRAS (GUDANG VS FASILITAS UMUM) -->
        <div id="sarprasPlacementSection" class="p-5 rounded-2xl bg-amber-50/50 border border-amber-200/80 mb-6" style="display: none;">
            <div class="flex flex-wrap items-center justify-between gap-3 pb-3 mb-4 border-b border-amber-200/60">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 text-base">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Klasifikasi & Peruntukan Barang Sarpras</h4>
                        <p class="text-[11px] text-slate-500">Pilih alokasi barang: persediaan logistik gudang atau aset terpasang di fasilitas umum.</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1 text-[11px] font-extrabold px-2.5 py-1 rounded-full bg-amber-400 text-slate-950 shadow-xs">
                    <i class="bi bi-shield-check"></i> Pusat Logistik & Aset Sekolah
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Opsi 1: Stok di Gudang -->
                <label id="cardOptionGudang" class="p-4 rounded-2xl border-2 border-slate-200 bg-white transition-all cursor-pointer hover:border-amber-400 block relative">
                    <div class="flex items-start gap-3">
                        <input class="mt-1 h-4 w-4 text-amber-500 focus:ring-amber-400 border-slate-300" type="radio" name="penempatan_sarpras" id="opt_penempatan_gudang" value="gudang" {{ old('penempatan_sarpras', $penempatanSarpras ?? '') == 'gudang' ? 'checked' : '' }} onchange="applySarprasPlacement('gudang', true)">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold px-2 py-0.5 rounded bg-amber-400 text-slate-950">
                                    <i class="bi bi-archive-fill"></i> STOK GUDANG
                                </span>
                                <span class="font-bold text-xs text-slate-900">Stok Logistik di Gudang</span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed mb-2">
                                Bahan habis pakai pemeliharaan gedung/kelistrikan, suku cadang, perkakas, atau alat cadangan yang disimpan di Gudang Sarpras.
                            </p>
                            <div class="flex flex-wrap gap-1.5">
                                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                    <i class="bi bi-box-arrow-in-right text-amber-500 mr-1"></i>Menu: Stok di Gudang
                                </span>
                                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                    <i class="bi bi-bell text-amber-500 mr-1"></i>Peringatan Min. Stok
                                </span>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- Opsi 2: Inventaris Fasilitas Umum -->
                <label id="cardOptionUmum" class="p-4 rounded-2xl border-2 border-slate-200 bg-white transition-all cursor-pointer hover:border-blue-500 block relative">
                    <div class="flex items-start gap-3">
                        <input class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300" type="radio" name="penempatan_sarpras" id="opt_penempatan_umum" value="umum" {{ old('penempatan_sarpras', $penempatanSarpras ?? '') == 'umum' ? 'checked' : '' }} onchange="applySarprasPlacement('umum', true)">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold px-2 py-0.5 rounded bg-blue-600 text-white">
                                    <i class="bi bi-building"></i> FASILITAS UMUM
                                </span>
                                <span class="font-bold text-xs text-slate-900">Inventaris Fasilitas Umum</span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed mb-2">
                                Aset terpasang/sedang beroperasi di ruangan bersama sekolah (Lab Komputer CBT/Umum, Aula, Ruang Guru, Ruang TU, Rumah Genset, Kelas).
                            </p>
                            <div class="flex flex-wrap gap-1.5">
                                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                    <i class="bi bi-tv text-blue-600 mr-1"></i>Menu: Inventaris Umum
                                </span>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- SECTION 2: KLASIFIKASI & SPESIFIKASI KHUSUS PERANGKAT IT -->
        @php
            $isKomCategory = old('category_id') ? ($categories->firstWhere('id', old('category_id'))?->kode === 'KOM') : ($item->category?->kode === 'KOM');
            $isComputerVal = old('is_computer', $item->is_computer ? 1 : 0);
        @endphp
        <div id="itTypeSection" class="p-5 rounded-2xl bg-blue-50/40 border border-blue-200/80 mb-6" style="display: {{ $isKomCategory ? 'block' : 'none' }};">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-blue-200/60">
                <div class="flex items-center gap-2.5">
                    <i class="bi bi-cpu text-lg text-blue-600"></i>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Klasifikasi Tipe Perangkat IT (Kategori KOM)</h4>
                        <p class="text-[11px] text-slate-500">Pilih apakah barang ini berupa unit komputer atau perangkat IT pendukung lainnya.</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                    <i class="bi bi-display mr-1"></i>Perangkat IT
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Opsi 1: Komputer / PC Lab -->
                <label id="cardOptionIsComputer" class="p-4 rounded-2xl border-2 {{ $isComputerVal == 1 ? 'border-blue-600 bg-white shadow-xs ring-1 ring-blue-500/20' : 'border-slate-200 bg-white/80' }} transition-all cursor-pointer hover:border-blue-400 block relative">
                    <div class="flex items-start gap-3">
                        <input class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300" type="radio" name="is_computer" id="opt_is_computer_yes" value="1" {{ $isComputerVal == 1 ? 'checked' : '' }} onchange="toggleComputerSpec(true)">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold px-2 py-0.5 rounded bg-blue-600 text-white">
                                    <i class="bi bi-display"></i> KOMPUTER / PC LAB
                                </span>
                                <span class="font-bold text-xs text-slate-900">Komputer / PC / Laptop</span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed mb-2">
                                Workstation Lab, PC Client CBT, Server, Laptop, atau All-in-One PC yang memiliki spesifikasi CPU, RAM, & Penyimpanan.
                            </p>
                            <span class="text-[10px] font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200 inline-block">
                                <i class="bi bi-check2-circle mr-1"></i>Spesifikasi diatur per unit fisik di halaman detail
                            </span>
                        </div>
                    </div>
                </label>

                <!-- Opsi 2: Bukan Komputer (Perangkat IT Non-PC) -->
                <label id="cardOptionNotComputer" class="p-4 rounded-2xl border-2 {{ $isComputerVal == 0 ? 'border-amber-500 bg-white shadow-xs ring-1 ring-amber-500/20' : 'border-slate-200 bg-white/80' }} transition-all cursor-pointer hover:border-amber-400 block relative">
                    <div class="flex items-start gap-3">
                        <input class="mt-1 h-4 w-4 text-amber-600 focus:ring-amber-500 border-slate-300" type="radio" name="is_computer" id="opt_is_computer_no" value="0" {{ $isComputerVal == 0 ? 'checked' : '' }} onchange="toggleComputerSpec(false)">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold px-2 py-0.5 rounded bg-amber-400 text-slate-950">
                                    <i class="bi bi-projector"></i> PERANGKAT NON-KOMPUTER
                                </span>
                                <span class="font-bold text-xs text-slate-900">Bukan Komputer</span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed mb-2">
                                Proyektor, Printer, Scanner, Switch/Hub, Router, Access Point, Smart TV, UPS, atau periferal pendukung IT lainnya.
                            </p>
                            <span class="text-[10px] font-semibold text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 inline-block">
                                <i class="bi bi-slash-circle mr-1"></i>Tanpa rincian spesifikasi CPU & RAM
                            </span>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- SECTION 3: RINCIAN STOK & KONDISI -->
        <div class="flex items-center gap-2 text-sm font-bold text-blue-700 pb-3 mb-5 border-b border-slate-100">
            <i class="bi bi-box-seam text-base"></i>
            <span>2. Rincian Stok & Kondisi Fisik</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div>
                <label for="jenis" class="flex items-center gap-1.5 text-xs font-bold text-slate-800 mb-1.5">
                    <span>Jenis Inventaris</span>
                    <span class="text-rose-500">*</span>
                    <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-amber-400 text-slate-950 font-black text-[11px] shadow-2xs cursor-help animate-pulse" title="Peringatan: Pastikan tidak keliru memilih Alat (Aset Tetap) vs Bahan/ATK (Habis Pakai)!">
                        !
                    </span>
                </label>
                <select name="jenis" id="jenis" 
                        class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 bg-amber-50/20 shadow-xs font-semibold text-slate-800" 
                        required onchange="handleJenisChange()">
                    <option value="alat" {{ old('jenis', $item->jenis) == 'alat' ? 'selected' : '' }}>Alat / Mesin (Aset Tetap)</option>
                    <option value="bahan" {{ old('jenis', $item->jenis) == 'bahan' ? 'selected' : '' }}>Bahan / ATK (Habis Pakai)</option>
                </select>
                <!-- Peringatan Visual Tanda Seru -->
                <div id="jenisPeringatanBox" class="mt-1.5 p-2 rounded-xl bg-amber-50/90 border border-amber-200/90 text-[11px] text-amber-900 flex items-start gap-1.5 shadow-2xs">
                    <span class="inline-flex items-center justify-center w-3.5 h-3.5 rounded-full bg-amber-500 text-white font-black text-[9px] shrink-0 mt-0.5">!</span>
                    <p id="jenisPeringatanDesc" class="leading-tight">
                        @if(old('jenis', $item->jenis) === 'bahan')
                            <strong>Bahan / ATK:</strong> Dihitung kuantitas stok habis pakai tanpa pembuatan nomor unit fisik.
                        @else
                            <strong>Alat / Mesin:</strong> Dibuatkan kode unit fisik &amp; QR individual untuk peminjaman/servis.
                        @endif
                    </p>
                </div>
            </div>

            <div>
                <label for="jumlah" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Jumlah Stok <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="jumlah" id="jumlah" min="0" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border @error('jumlah') border-rose-300 @else border-slate-200 @enderror focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       value="{{ old('jumlah', $item->jumlah) }}" required>
            </div>

            <div>
                <label for="satuan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Satuan <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="satuan" id="satuan" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       value="{{ old('satuan', $item->satuan) }}" required>
            </div>

            <div>
                <label for="kondisi" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Kondisi Fisik <span class="text-rose-500">*</span>
                </label>
                <select name="kondisi" id="kondisi" 
                        class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                        required>
                    <option value="baik" {{ old('kondisi', $item->kondisi) == 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="rusak_ringan" {{ old('kondisi', $item->kondisi) == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="rusak_berat" {{ old('kondisi', $item->kondisi) == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
            <div class="md:col-span-4" id="minStokContainer" style="display: {{ $item->jenis === 'bahan' ? 'block' : 'none' }};">
                <label for="min_stok" class="block text-xs font-semibold text-amber-700 mb-1.5 flex items-center gap-1">
                    <i class="bi bi-bell"></i>
                    <span>Batas Minimum Stok (Peringatan)</span>
                </label>
                <input type="number" name="min_stok" id="min_stok" min="0" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-400/20 focus:border-amber-400 bg-amber-50/30 shadow-xs" 
                       value="{{ old('min_stok', $item->min_stok) }}">
            </div>

            <div class="md:col-span-4">
                <label for="lokasi" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Lokasi Ruang / Lab / Bengkel / Gudang
                </label>
                <input type="text" name="lokasi" id="lokasi" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       value="{{ old('lokasi', $item->lokasi) }}">
                <div id="sarprasLocationQuickPills" class="mt-2" style="display: none;">
                    <p class="text-[11px] text-slate-400 mb-1 flex items-center gap-1">
                        <i class="bi bi-lightning-charge-fill text-amber-500"></i>
                        <span>Preset Cepat Lokasi Sarpras:</span>
                    </p>
                    <div class="flex flex-wrap gap-1.5" id="locationChipsContainer"></div>
                </div>
            </div>

            <div class="md:col-span-2">
                <label for="sumber_dana" class="block text-xs font-semibold text-slate-700 mb-1.5">Sumber Dana</label>
                <input type="text" name="sumber_dana" id="sumber_dana" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       value="{{ old('sumber_dana', $item->sumber_dana) }}">
            </div>

            <div class="md:col-span-2">
                <label for="tahun_pengadaan" class="block text-xs font-semibold text-slate-700 mb-1.5">Tahun Pengadaan</label>
                <input type="number" name="tahun_pengadaan" id="tahun_pengadaan" min="1990" max="{{ date('Y') + 1 }}" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs" 
                       value="{{ old('tahun_pengadaan', $item->tahun_pengadaan) }}">
            </div>

            <div class="md:col-span-12">
                <label for="spesifikasi" class="block text-xs font-semibold text-slate-700 mb-1.5">Deskripsi / Spesifikasi Tambahan</label>
                <textarea name="spesifikasi" id="spesifikasi" rows="3" 
                          class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-xs">{{ old('spesifikasi', $item->spesifikasi) }}</textarea>
            </div>

            <!-- Foto Fisik Barang -->
            <div class="md:col-span-12">
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Foto Fisik Barang / Mesin / Alat
                </label>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    @if($item->foto)
                        <div class="w-20 h-20 rounded-xl border border-slate-200 overflow-hidden bg-white shrink-0 relative group shadow-xs">
                            <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama_barang }}" class="w-full h-full object-cover">
                            <a href="{{ asset('storage/' . $item->foto) }}" target="_blank" class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs transition-opacity" title="Lihat Foto Ukuran Penuh">
                                <i class="bi bi-arrows-fullscreen"></i>
                            </a>
                        </div>
                    @endif
                    <div id="fotoPreviewContainer" class="hidden w-20 h-20 rounded-xl border border-slate-200 overflow-hidden bg-white shrink-0 shadow-xs">
                        <img id="fotoPreviewImg" src="" alt="Preview Baru" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 space-y-1.5">
                        <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-white file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                               onchange="previewItemPhoto(this)">
                        <p class="text-[11px] text-slate-400">
                            {{ $item->foto ? 'Pilih file baru jika ingin mengganti foto saat ini (JPG, PNG, WEBP max 3 MB, otomatis dikompresi).' : 'Format: JPG, JPEG, PNG, atau WEBP (maksimal 3 MB, otomatis dikompresi).' }}
                        </p>
                        @if($item->foto)
                            <label class="inline-flex items-center gap-1.5 cursor-pointer mt-1 text-xs text-rose-600 font-semibold hover:text-rose-700">
                                <input type="checkbox" name="hapus_foto" value="1" class="rounded text-rose-600 focus:ring-rose-500">
                                <span>Centang untuk menghapus foto saat ini</span>
                            </label>
                        @endif
                        @error('foto') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Action Buttons -->
        <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3 pt-5 border-t border-slate-100">
            <!-- Batal (Urgency: Neutral) -->
            <a href="{{ route('items.show', $item) }}" 
               class="w-full sm:w-auto text-center px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-xs transition-all active:scale-95">
                Batal
            </a>
            <!-- Simpan (Urgency: Primary Action / Blue) -->
            <button type="submit" 
                    class="w-full sm:w-auto px-5 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-200 rounded-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                <i class="bi bi-save"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>

<script>
    const gudangPresets = [
        'Gudang Sarpras - Rak A (Elektrikal)',
        'Gudang Sarpras - Rak B (Bahan Bangunan)',
        'Gudang Sarpras - Rak C (Plumbing & Pipa)',
        'Gudang Sarpras - Lemari Perkakas',
        'Gudang Sarpras - Cadangan Lab IT',
        'Gudang Sarpras - Material Kebersihan'
    ];

    const umumPresets = [
        'Lab Komputer CBT 1 (Umum)',
        'Lab Komputer CBT 2 (Umum)',
        'Aula Utama Sekolah',
        'Ruang Guru & Piket',
        'Ruang Tata Usaha (TU)',
        'Rumah Genset & Kelistrikan',
        'Ruang UKS',
        'Ruang Kelas X'
    ];

    function setLocationPreset(val) {
        const input = document.getElementById('lokasi');
        if (input) {
            input.value = val;
            input.focus();
        }
    }

    function renderLocationChips(type) {
        const container = document.getElementById('locationChipsContainer');
        if (!container) return;
        container.innerHTML = '';

        const presets = (type === 'gudang') ? gudangPresets : umumPresets;
        presets.forEach(p => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = (type === 'gudang') 
                ? 'inline-flex items-center text-[10px] font-semibold text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 px-2 py-0.5 rounded-full transition-colors' 
                : 'inline-flex items-center text-[10px] font-semibold text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-2 py-0.5 rounded-full transition-colors';
            btn.textContent = p;
            btn.onclick = () => setLocationPreset(p);
            container.appendChild(btn);
        });
    }

    function applySarprasPlacement(type, isUserClick = false) {
        const cardGudang = document.getElementById('cardOptionGudang');
        const cardUmum = document.getElementById('cardOptionUmum');
        const radioGudang = document.getElementById('opt_penempatan_gudang');
        const radioUmum = document.getElementById('opt_penempatan_umum');
        const lokasiInput = document.getElementById('lokasi');
        const minStokContainer = document.getElementById('minStokContainer');

        if (type === 'gudang') {
            if (radioGudang) radioGudang.checked = true;
            if (cardGudang) {
                cardGudang.classList.remove('border-slate-200', 'bg-white');
                cardGudang.classList.add('border-amber-400', 'bg-amber-50/70', 'ring-2', 'ring-amber-400/20');
            }
            if (cardUmum) {
                cardUmum.classList.remove('border-blue-500', 'bg-blue-50/70', 'ring-2', 'ring-blue-400/20');
                cardUmum.classList.add('border-slate-200', 'bg-white');
            }

            renderLocationChips('gudang');

            if (isUserClick && lokasiInput) {
                if (!lokasiInput.value || umumPresets.includes(lokasiInput.value) || !lokasiInput.value.toLowerCase().includes('gudang')) {
                    lokasiInput.value = 'Gudang Sarpras - Rak A (Elektrikal)';
                }
            }

            if (minStokContainer) {
                minStokContainer.style.display = 'block';
            }
        } else {
            if (radioUmum) radioUmum.checked = true;
            if (cardUmum) {
                cardUmum.classList.remove('border-slate-200', 'bg-white');
                cardUmum.classList.add('border-blue-500', 'bg-blue-50/70', 'ring-2', 'ring-blue-400/20');
            }
            if (cardGudang) {
                cardGudang.classList.remove('border-amber-400', 'bg-amber-50/70', 'ring-2', 'ring-amber-400/20');
                cardGudang.classList.add('border-slate-200', 'bg-white');
            }

            renderLocationChips('umum');

            if (isUserClick && lokasiInput) {
                if (!lokasiInput.value || gudangPresets.includes(lokasiInput.value) || lokasiInput.value.toLowerCase().includes('gudang')) {
                    lokasiInput.value = 'Lab Komputer CBT 1 (Umum)';
                }
            }

            const jenisSelect = document.getElementById('jenis');
            if (minStokContainer && jenisSelect && jenisSelect.value !== 'bahan') {
                minStokContainer.style.display = 'none';
            }
        }
    }

    function checkSarprasSelection() {
        const jurusanElem = document.getElementById('jurusan_id');
        let isSarpras = false;

        if (jurusanElem) {
            if (jurusanElem.tagName === 'SELECT') {
                const opt = jurusanElem.options[jurusanElem.selectedIndex];
                const kode = opt ? opt.getAttribute('data-kode') : '';
                if (kode === 'SAR') isSarpras = true;
            } else {
                const kode = jurusanElem.getAttribute('data-kode');
                if (kode === 'SAR') isSarpras = true;
            }
        }

        const placementSection = document.getElementById('sarprasPlacementSection');
        const locationQuickPills = document.getElementById('sarprasLocationQuickPills');

        if (isSarpras) {
            if (placementSection) placementSection.style.display = 'block';
            if (locationQuickPills) locationQuickPills.style.display = 'block';

            const radioGudang = document.getElementById('opt_penempatan_gudang');
            const initialType = (radioGudang && radioGudang.checked) ? 'gudang' : 'umum';
            applySarprasPlacement(initialType, false);
        } else {
            if (placementSection) placementSection.style.display = 'none';
            if (locationQuickPills) locationQuickPills.style.display = 'none';
        }
    }

    function handleJurusanChange() {
        checkSarprasSelection();
    }

    function toggleComputerSpec(isComputer) {
        const cardYes = document.getElementById('cardOptionIsComputer');
        const cardNo = document.getElementById('cardOptionNotComputer');

        if (isComputer) {
            if (cardYes) {
                cardYes.classList.add('border-blue-600', 'ring-1', 'ring-blue-500/20');
                cardYes.classList.remove('border-slate-200');
            }
            if (cardNo) {
                cardNo.classList.remove('border-amber-500', 'ring-1', 'ring-amber-500/20');
                cardNo.classList.add('border-slate-200');
            }
        } else {
            if (cardNo) {
                cardNo.classList.add('border-amber-500', 'ring-1', 'ring-amber-500/20');
                cardNo.classList.remove('border-slate-200');
            }
            if (cardYes) {
                cardYes.classList.remove('border-blue-600', 'ring-1', 'ring-blue-500/20');
                cardYes.classList.add('border-slate-200');
            }
        }
    }

    function handleCategoryChange() {
        const catSelect = document.getElementById('category_id');
        const selectedOption = (catSelect && catSelect.selectedIndex >= 0) ? catSelect.options[catSelect.selectedIndex] : null;
        const catKode = selectedOption ? (selectedOption.getAttribute('data-kode') || '').toUpperCase() : '';
        const itTypeSection = document.getElementById('itTypeSection');
        const radioYes = document.getElementById('opt_is_computer_yes');
        const radioNo = document.getElementById('opt_is_computer_no');

        if (catKode === 'KOM') {
            if (itTypeSection) itTypeSection.style.display = 'block';
            const isComputerChecked = radioYes && radioYes.checked;
            toggleComputerSpec(isComputerChecked);
        } else {
            if (itTypeSection) itTypeSection.style.display = 'none';
            toggleComputerSpec(false);
            if (radioNo) radioNo.checked = true;
        }
    }

    function handleJenisChange() {
        const jenisSelect = document.getElementById('jenis');
        const minStokContainer = document.getElementById('minStokContainer');
        const radioGudang = document.getElementById('opt_penempatan_gudang');
        const isGudangChecked = radioGudang && radioGudang.checked;

        if (jenisSelect.value === 'bahan' || isGudangChecked) {
            minStokContainer.style.display = 'block';
        } else {
            minStokContainer.style.display = 'none';
        }

        const descEl = document.getElementById('jenisPeringatanDesc');
        if (descEl) {
            if (jenisSelect.value === 'bahan') {
                descEl.innerHTML = '<strong>Bahan / ATK:</strong> Dihitung kuantitas stok habis pakai tanpa pembuatan nomor unit fisik.';
            } else {
                descEl.innerHTML = '<strong>Alat / Mesin:</strong> Dibuatkan kode unit fisik &amp; QR individual untuk peminjaman/servis.';
            }
        }
    }

    function previewItemPhoto(input) {
        const container = document.getElementById('fotoPreviewContainer');
        const img = document.getElementById('fotoPreviewImg');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (file.size > 500 * 1024) {
                alert('Ukuran foto melebihi 500 KB! Silakan pilih foto dengan ukuran lebih kecil (maksimal 500 KB).');
                input.value = '';
                container.classList.add('hidden');
                img.src = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                container.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            container.classList.add('hidden');
            img.src = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        checkSarprasSelection();
        handleCategoryChange();
        handleJenisChange();
    });
</script>
@endsection
