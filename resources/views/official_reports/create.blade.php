@extends('layouts.app')

@section('title', 'Buat Berita Acara')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('official-reports.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors mb-2">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Daftar Berita Acara</span>
            </a>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Buat Berita Acara Sarpras</h1>
            <p class="text-sm text-slate-500 mt-0.5">
                Pencatatan formal Berita Acara Serah Terima Barang (BAST), Kerusakan / Afkir, atau Penjualan / Lelang Aset.
            </p>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
            <div class="font-bold flex items-center gap-2 mb-1">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>Terdapat kesalahan pengisian formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('official-reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="reportForm">
        @csrf
        <input type="hidden" name="status_dokumen" id="statusDokumenInput" value="selesai">

        <!-- 1. IDENTITAS DOKUMEN & JENIS -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 space-y-5">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-6 h-6 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center text-xs">1</span>
                <span>Klasifikasi &amp; Nomor Surat Berita Acara</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Jenis Berita Acara <span class="text-rose-500">*</span>
                    </label>
                    <select name="jenis" id="jenisSelect" onchange="if(window.updateFormMode) window.updateFormMode();" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        @if(Auth::user()->isJurusan())
                            <option value="barang_rusak" selected>
                                ⚠️ Berita Acara Kerusakan / Penghapusan Barang (Afkir)
                            </option>
                        @else
                            <option value="serah_terima" {{ old('jenis', 'serah_terima') == 'serah_terima' ? 'selected' : '' }}>
                                📦 Berita Acara Serah Terima Barang ke Jurusan (BAST)
                            </option>
                            <option value="barang_rusak" {{ old('jenis') == 'barang_rusak' ? 'selected' : '' }}>
                                ⚠️ Berita Acara Kerusakan / Penghapusan Barang (Afkir)
                            </option>
                            <option value="penjualan" {{ old('jenis') == 'penjualan' ? 'selected' : '' }}>
                                💰 Berita Acara Penjualan / Lelang Barang Bekas
                            </option>
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nomor Surat / Berita Acara <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nomor_surat" id="nomorSuratInput" value="{{ old('nomor_surat', Auth::user()->isJurusan() ? $suggestedNumberRusak : $suggestedNumberSerahTerima) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 font-mono font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <p class="text-[11px] text-slate-400 mt-1">Format penomoran dapat disesuaikan dengan tata persuratan sekolah.</p>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Judul Berita Acara <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="judul" id="judulInput" value="{{ old('judul', Auth::user()->isJurusan() ? 'Berita Acara Barang Rusak' : 'Berita Acara Serah Terima Barang Inventaris') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Tanggal Berita Acara <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Jurusan / Unit Kerja Penerima / Terkait <span class="text-rose-500" id="jurusanRequiredStar">*</span>
                    </label>
                    <select name="jurusan_id" id="jurusanSelect" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option value="" data-nama="Umum / Pihak Luar" data-kepala="" data-nip="">-- Pilih Unit Kerja / Jurusan --</option>
                        @foreach($jurusans as $j)
                            <option value="{{ $j->id }}" data-nama="{{ $j->nama }}" data-kepala="{{ $j->kepala_bengkel ?? '' }}" data-nip="{{ $j->nip ?? '' }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>
                                [{{ $j->kode }}] {{ $j->nama }}{{ $j->parent_id ? ' (Sub-Unit)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-blue-600 mt-1" id="jurusanInfoHelp">
                        <i class="bi bi-info-circle"></i> Memilih unit kerja akan memuat data inventaris unit tersebut untuk opsi Restock Barang.
                    </p>
                </div>
            </div>
        </div>

        <!-- 2. PIHAK-PIHAK YANG BERTANDATANGAN -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 space-y-5">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-6 h-6 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center text-xs">2</span>
                <span>Pihak yang Terlibat &amp; Menandatangani</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pihak Pertama -->
                <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200 space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-blue-800">
                        <i class="bi bi-person-badge"></i>
                        <span id="labelPihakPertama">Pihak Pertama (Pemberi / Sarpras)</span>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="pihak_pertama_nama" id="pihakPertamaNama" value="{{ old('pihak_pertama_nama', $defaultPihakPertamaNama) }}" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jabatan <span class="text-rose-500">*</span></label>
                        <input type="text" name="pihak_pertama_jabatan" id="pihakPertamaJabatan" value="{{ old('pihak_pertama_jabatan', $defaultPihakPertamaJabatan) }}" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">NIP / NIY / NPY (Opsional)</label>
                        <input type="text" name="pihak_pertama_nip" id="pihakPertamaNip" value="{{ old('pihak_pertama_nip', $defaultPihakPertamaNip) }}" placeholder="Contoh: 1985... atau nomor NIY" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                </div>

                <!-- Pihak Kedua -->
                <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-amber-800">
                            <i class="bi bi-person-check"></i>
                            <span id="labelPihakKedua">Pihak Kedua (Penerima / Kepala Program / Unit)</span>
                        </div>
                    </div>

                    <!-- Pilihan Peran Pihak Kedua khusus Penjualan -->
                    <div id="rolePihakKeduaPenjualanBox" class="hidden p-2.5 bg-amber-50/80 border border-amber-200 rounded-xl space-y-1.5">
                        <span class="block text-xs font-bold text-amber-900">Peran Pihak Kedua dalam Penjualan:</span>
                        <div class="flex items-center gap-4 text-xs font-semibold text-slate-700">
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="pihak_kedua_peran" value="pembeli" checked class="text-blue-600 focus:ring-blue-500 role-pihak-kedua-radio">
                                <span>Pihak Pembeli</span>
                            </label>
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="pihak_kedua_peran" value="saksi" class="text-blue-600 focus:ring-blue-500 role-pihak-kedua-radio">
                                <span>Saksi Luar / Terkait</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="pihak_kedua_nama" id="pihakKeduaNama" value="{{ old('pihak_kedua_nama', $defaultPihakKeduaNama ?? '') }}" required placeholder="Nama Kepala Program / Unit Kerja" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jabatan / Status <span class="text-rose-500">*</span></label>
                        <input type="text" name="pihak_kedua_jabatan" id="pihakKeduaJabatan" value="{{ old('pihak_kedua_jabatan', $defaultPihakKeduaJabatan ?? 'Kepala Program / Unit Kerja') }}" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">NIP / NIY / NPY (Jika Ada)</label>
                        <input type="text" name="pihak_kedua_nip" id="pihakKeduaNip" value="{{ old('pihak_kedua_nip', $defaultPihakKeduaNip ?? '') }}" placeholder="NIP/NIY Kepala Bengkel / Jurusan" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Instansi / Unit</label>
                            <input type="text" name="pihak_kedua_instansi" id="pihakKeduaInstansi" value="{{ old('pihak_kedua_instansi', 'SMK Dr. Sutomo Temanggung') }}" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">No. Kontak / HP</label>
                            <input type="text" name="pihak_kedua_kontak" id="pihakKeduaKontak" value="{{ old('pihak_kedua_kontak') }}" placeholder="08..." class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. RINCIAN BARANG -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center text-xs">3</span>
                        <span>Daftar Rincian Barang</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Pilih barang baru atau ambil dari data inventaris unit kerja untuk penambahan stok (Restock).
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="addItemRowBtn" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3.5 py-2 sm:py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold text-xs transition-colors">
                        <i class="bi bi-plus-circle"></i>
                        <span>Tambah Baris Barang</span>
                    </button>
                </div>
            </div>

            <!-- Kontainer Kartu / Baris Barang Dinamis -->
            <div id="itemsContainer" class="space-y-4">
                <!-- Baris Pertama Default -->
                <div class="item-card p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3 relative transition-all" data-idx="0">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center row-num">1</span>
                            <span class="text-xs font-bold uppercase text-slate-700 item-title">Barang #1</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- Toggle Tipe Barang (Khusus Serah Terima) -->
                            <div class="tipe-barang-toggle flex items-center gap-1 bg-white p-1 rounded-lg border border-slate-200 text-xs">
                                <label class="inline-flex items-center gap-1 px-2 py-0.5 rounded cursor-pointer transition-colors bg-blue-50 text-blue-700 font-bold opt-new">
                                    <input type="radio" name="items[0][tipe_barang]" value="new" checked class="hidden radio-tipe" onchange="toggleItemType(0, 'new')">
                                    <span>Barang Baru</span>
                                </label>
                                <label class="inline-flex items-center gap-1 px-2 py-0.5 rounded cursor-pointer transition-colors text-slate-600 font-medium opt-existing">
                                    <input type="radio" name="items[0][tipe_barang]" value="existing" class="hidden radio-tipe" onchange="toggleItemType(0, 'existing')">
                                    <span>Ambil dari Data Barang Unit (Restock)</span>
                                </label>
                            </div>
                            <button type="button" class="remove-card-btn text-slate-400 hover:text-rose-600 p-1" title="Hapus Baris">
                                <i class="bi bi-trash text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Bagian Pemilihan Barang Existing (Restock) -->
                    <div class="section-existing hidden p-3 rounded-xl bg-amber-50/80 border border-amber-200/80">
                        <label class="block text-xs font-bold text-amber-900 mb-1">
                            Pilih Barang dari Inventaris Unit:
                        </label>
                        <select name="items[0][item_id]" class="existing-item-select w-full px-3 py-2 bg-white border border-amber-300 rounded-lg text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20" onchange="handleExistingItemSelect(0, this)">
                            <option value="">-- Pilih Barang yang Tersedia di Unit --</option>
                        </select>
                        <p class="text-[11px] text-amber-700 mt-1">Stok barang terpilih otomatis bertambah saat berita acara disinkronisasikan ke data barang.</p>
                    </div>

                    <!-- Input Detail Barang -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs sm:text-sm">
                        <div class="sm:col-span-5">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Barang <span class="text-rose-500">*</span></label>
                            <input type="text" name="items[0][nama_barang]" required placeholder="Contoh: Laptop Acer Aspire 5 / Timah Solder" class="inp-nama w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Barang <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <input type="text" name="items[0][kode_barang]" placeholder="Auto / Masukkan kode" class="inp-kode w-full px-3 py-2 bg-white border border-slate-200 rounded-lg font-mono focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div class="sm:col-span-4 section-kategori">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Barang</label>
                            <select name="items[0][category_id]" class="inp-category w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->kode }} - {{ $cat->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah <span class="text-rose-500">*</span></label>
                            <input type="number" name="items[0][jumlah]" value="1" min="0.01" step="any" required class="inp-qty w-full px-3 py-2 bg-white border border-slate-200 rounded-lg font-bold text-center focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Satuan <span class="text-rose-500">*</span></label>
                            <input type="text" name="items[0][satuan]" value="unit" required placeholder="unit, pcs, box" class="inp-satuan w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kondisi</label>
                            <select name="items[0][kondisi_saat_lapor]" class="inp-kondisi w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="baik">Kondisi Baik (Baru / Normal)</option>
                                <option value="rusak_ringan">Rusak Ringan</option>
                                <option value="bekas_layak">Bekas Masih Layak</option>
                                <option value="lengkap">Lengkap &amp; Siap Digunakan</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2 section-jenis">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis</label>
                            <select name="items[0][jenis]" class="inp-jenis w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="alat">Alat (Aset)</option>
                                <option value="bahan">Bahan Habis Pakai</option>
                            </select>
                        </div>
                        <div class="sm:col-span-3 section-lokasi">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Ruang / Penempatan</label>
                            <input type="text" name="items[0][lokasi]" placeholder="Contoh: Lab 1, Bengkel" class="inp-lokasi w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>

                        <!-- Kolom Harga khusus penjualan -->
                        <div class="sm:col-span-3 col-harga hidden">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Satuan (Rp)</label>
                            <input type="number" name="items[0][harga_satuan]" value="0" min="0" step="1000" class="inp-price w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-right font-semibold focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div class="sm:col-span-3 col-subtotal hidden">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Subtotal (Rp)</label>
                            <input type="text" readonly value="Rp 0" class="inp-subtotal w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-right font-bold text-emerald-700">
                        </div>

                        <div class="sm:col-span-12">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Spesifikasi / Keterangan Tambahan</label>
                            <input type="text" name="items[0][keterangan]" placeholder="Spesifikasi teknis, merk, kelengkapan, dll." class="inp-ket w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Tambah Baris Barang di Bawah Tabel -->
            <div class="pt-2">
                <button type="button" id="addItemRowBtnBottom" class="w-full py-3 px-4 rounded-xl border-2 border-dashed border-blue-200 hover:border-blue-400 bg-blue-50/50 hover:bg-blue-50 text-blue-700 font-bold text-xs flex items-center justify-center gap-2 transition-all active:scale-[0.99]">
                    <i class="bi bi-plus-circle-fill text-sm"></i>
                    <span>Tambah Baris Barang Lagi (+ Di Bawah)</span>
                </button>
            </div>

            <!-- Total Nilai Penjualan (Hanya Muncul jika Penjualan) -->
            <div id="totalPenjualanBox" class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 hidden flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Total Hasil Penjualan:</span>
                <span class="text-lg font-extrabold text-emerald-900" id="totalPenjualanDisplay">Rp 0</span>
            </div>
        </div>

        <!-- 4. LATAR BELAKANG, CATATAN & LAMPIRAN -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 space-y-5">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-6 h-6 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center text-xs">4</span>
                <span>Latar Belakang &amp; Lampiran Bukti Fisik</span>
            </h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Latar Belakang / Kronologi / Dasar Berita Acara
                    </label>
                    <textarea name="latar_belakang" id="latarBelakangInput" rows="3" placeholder="Jelaskan alasan dilakukannya serah terima/pemeriksaan aset..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">{{ old('latar_belakang', 'Menyatakan bahwa Pihak Pertama telah menyerahkan barang/aset sarana dan prasarana dalam keadaan baik dan lengkap kepada Pihak Kedua untuk dimanfaatkan serta dipelihara sesuai peruntukannya di unit kerja.') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Upload Bukti Foto Fisik / Nota / Dokumen (Opsional)
                        </label>
                        <input type="file" name="file_lampiran" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-[11px] text-slate-400 mt-1">Format: PDF, JPG, PNG (Maksimal 5MB)</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Catatan Tambahan
                        </label>
                        <input type="text" name="catatan" value="{{ old('catatan') }}" placeholder="Catatan pengiriman / nomor ekspedisi / instruksi penempatan" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi Simpan (Draft vs Final) -->
        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-2">
            <a href="{{ route('official-reports.index') }}" class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm transition-colors">
                Batal
            </a>
            
            <!-- Tombol Simpan Sebagai Draft -->
            <button type="button" onclick="submitAs('draft')" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-sm shadow-xs transition-all">
                <i class="bi bi-file-earmark-text"></i>
                <span>Simpan Sebagai Draft (Belum Final)</span>
            </button>

            <!-- Tombol Simpan & Terbitkan Langsung -->
            <button type="button" onclick="submitAs('selesai')" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition-all">
                <i class="bi bi-check2-circle text-base"></i>
                <span>Terbitkan Berita Acara</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let unitItemsCache = {}; // Cache data barang per jurusan id
let categoriesList = @json($categories);

function submitAs(status) {
    document.getElementById('statusDokumenInput').value = status;
    document.getElementById('reportForm').submit();
}

document.addEventListener('DOMContentLoaded', function () {
    const jenisSelect = document.getElementById('jenisSelect');
    const nomorSuratInput = document.getElementById('nomorSuratInput');
    const judulInput = document.getElementById('judulInput');
    const jurusanSelect = document.getElementById('jurusanSelect');
    const labelPihakPertama = document.getElementById('labelPihakPertama');
    const labelPihakKedua = document.getElementById('labelPihakKedua');
    const pihakKeduaNama = document.getElementById('pihakKeduaNama');
    const pihakKeduaJabatan = document.getElementById('pihakKeduaJabatan');
    const pihakKeduaNip = document.getElementById('pihakKeduaNip');
    const totalPenjualanBox = document.getElementById('totalPenjualanBox');
    const latarBelakangInput = document.getElementById('latarBelakangInput');

    const suggestedSerahTerima = "{{ $suggestedNumberSerahTerima }}";
    const suggestedRusak = "{{ $suggestedNumberRusak }}";
    const suggestedJual = "{{ $suggestedNumberJual }}";
    const rolePihakKeduaPenjualanBox = document.getElementById('rolePihakKeduaPenjualanBox');

    // Fetch items ketika jurusan dipilih
    function fetchItemsForJurusan(jurusanId) {
        if (!jurusanId) return;
        if (unitItemsCache[jurusanId]) {
            populateAllExistingSelects();
            return;
        }

        fetch(`/api/jurusans/${jurusanId}/items`)
            .then(res => res.json())
            .then(data => {
                unitItemsCache[jurusanId] = data;
                populateAllExistingSelects();
            })
            .catch(err => console.error('Error fetching jurusan items:', err));
    }

    function populateAllExistingSelects() {
        const jurusanId = jurusanSelect.value;
        const items = unitItemsCache[jurusanId] || [];

        document.querySelectorAll('.existing-item-select').forEach(select => {
            const currentVal = select.value;
            select.innerHTML = '<option value="">-- Pilih Barang dari Master Unit --</option>';
            items.forEach(it => {
                const opt = document.createElement('option');
                opt.value = it.id;
                opt.textContent = `[${it.kode_barang || 'TANPA KODE'}] ${it.nama_barang} (Stok Saat Ini: ${it.jumlah} ${it.satuan})`;
                opt.dataset.kode = it.kode_barang || '';
                opt.dataset.nama = it.nama_barang || '';
                opt.dataset.satuan = it.satuan || 'unit';
                opt.dataset.kondisi = it.kondisi || 'baik';
                opt.dataset.category = it.category_id || '';
                opt.dataset.jenis = it.jenis || 'alat';
                opt.dataset.lokasi = it.lokasi || '';
                opt.dataset.spesifikasi = it.spesifikasi || '';
                if (currentVal && currentVal == it.id) opt.selected = true;
                select.appendChild(opt);
            });
        });
    }

    window.handleExistingItemSelect = function (idx, select) {
        const opt = select.options[select.selectedIndex];
        const card = select.closest('.item-card');
        if (!card) return;

        if (opt && opt.value) {
            card.querySelector('.inp-nama').value = opt.dataset.nama || '';
            card.querySelector('.inp-kode').value = opt.dataset.kode || '';
            card.querySelector('.inp-satuan').value = opt.dataset.satuan || 'unit';
            if (card.querySelector('.inp-kondisi')) card.querySelector('.inp-kondisi').value = opt.dataset.kondisi || 'baik';
            if (card.querySelector('.inp-category')) card.querySelector('.inp-category').value = opt.dataset.category || '';
            if (card.querySelector('.inp-jenis')) card.querySelector('.inp-jenis').value = opt.dataset.jenis || 'alat';
            if (card.querySelector('.inp-lokasi')) card.querySelector('.inp-lokasi').value = opt.dataset.lokasi || '';
            if (card.querySelector('.inp-ket')) card.querySelector('.inp-ket').value = opt.dataset.spesifikasi || '';
        }
    };

    window.toggleItemType = function (idx, type) {
        const card = document.querySelector(`.item-card[data-idx="${idx}"]`);
        if (!card) return;

        const secExisting = card.querySelector('.section-existing');
        const optNew = card.querySelector('.opt-new');
        const optExisting = card.querySelector('.opt-existing');

        if (type === 'existing') {
            secExisting.classList.remove('hidden');
            optExisting.classList.add('bg-blue-50', 'text-blue-700', 'font-bold');
            optExisting.classList.remove('text-slate-600', 'font-medium');
            optNew.classList.remove('bg-blue-50', 'text-blue-700', 'font-bold');
            optNew.classList.add('text-slate-600', 'font-medium');

            // Cek apakah jurusan sudah dipilih
            if (!jurusanSelect.value) {
                alert('Silakan pilih Jurusan / Unit Kerja Terkait di bagian atas terlebih dahulu untuk mengambil data barangnya.');
                jurusanSelect.focus();
            } else {
                fetchItemsForJurusan(jurusanSelect.value);
            }
        } else {
            secExisting.classList.add('hidden');
            optNew.classList.add('bg-blue-50', 'text-blue-700', 'font-bold');
            optNew.classList.remove('text-slate-600', 'font-medium');
            optExisting.classList.remove('bg-blue-50', 'text-blue-700', 'font-bold');
            optExisting.classList.add('text-slate-600', 'font-medium');
            card.querySelector('.existing-item-select').value = '';
        }
    };

    function updateFormMode() {
        const mode = jenisSelect.value;
        const isSerahTerima = (mode === 'serah_terima');
        const isJual = (mode === 'penjualan');

        // Tampilkan toggle tipe barang hanya jika serah terima
        document.querySelectorAll('.tipe-barang-toggle').forEach(el => {
            if (isSerahTerima) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        });

        if (isSerahTerima) {
            if (nomorSuratInput.value === suggestedRusak || nomorSuratInput.value === suggestedJual || !nomorSuratInput.value) {
                nomorSuratInput.value = suggestedSerahTerima;
            }
            judulInput.value = 'Berita Acara Serah Terima Barang Inventaris';
            labelPihakPertama.textContent = 'Pihak Pertama (Pihak yang Menyerahkan / Sarpras)';
            labelPihakKedua.textContent = 'Pihak Kedua (Pihak yang Menerima / Kepala Program / Unit)';
            pihakKeduaNama.placeholder = 'Nama Kepala Program / Unit Kerja';
            
            if (rolePihakKeduaPenjualanBox) rolePihakKeduaPenjualanBox.classList.add('hidden');
            totalPenjualanBox.classList.add('hidden');
            document.querySelectorAll('.col-harga').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.col-subtotal').forEach(el => el.classList.add('hidden'));

            if (!latarBelakangInput.value || latarBelakangInput.value.includes('rusak berat') || latarBelakangInput.value.includes('pelepasan aset')) {
                latarBelakangInput.value = 'Menyatakan bahwa Pihak Pertama telah menyerahkan barang/aset sarana dan prasarana dalam keadaan baik dan lengkap kepada Pihak Kedua untuk dimanfaatkan serta dipelihara sesuai peruntukannya di unit kerja.';
            }

            syncJurusanToPihakKedua();
        } else if (isJual) {
            if (nomorSuratInput.value === suggestedRusak || nomorSuratInput.value === suggestedSerahTerima) {
                nomorSuratInput.value = suggestedJual;
            }
            judulInput.value = 'Berita Acara Penjualan / Pelepasan Aset Barang Bekas';
            labelPihakPertama.textContent = 'Pihak Pertama (Penyelenggara / Sarpras)';
            if (rolePihakKeduaPenjualanBox) rolePihakKeduaPenjualanBox.classList.remove('hidden');
            totalPenjualanBox.classList.remove('hidden');
            document.querySelectorAll('.col-harga').forEach(el => el.classList.remove('hidden'));
            document.querySelectorAll('.col-subtotal').forEach(el => el.classList.remove('hidden'));
            
            if (latarBelakangInput.value.includes('rusak berat dan dinilai') || latarBelakangInput.value.includes('Pihak Pertama telah menyerahkan')) {
                latarBelakangInput.value = 'Berdasarkan keputusan pelepasan aset inventaris yang telah habis masa pakai/scrap dan disetujui untuk dijual/dilelang guna optimalisasi ruang gudang dan kas sekolah.';
            }
        } else {
            // barang_rusak
            if (nomorSuratInput.value === suggestedJual || nomorSuratInput.value === suggestedSerahTerima) {
                nomorSuratInput.value = suggestedRusak;
            }
            judulInput.value = 'Berita Acara Barang Rusak';
            labelPihakPertama.textContent = 'Pihak Pertama (Penyelenggara / Sarpras)';
            labelPihakKedua.textContent = 'Pihak Kedua (Saksi / Kepala Bengkel / Laboratorium)';
            pihakKeduaNama.placeholder = 'Nama Kepala Bengkel / Unit Kerja';
            if (rolePihakKeduaPenjualanBox) rolePihakKeduaPenjualanBox.classList.add('hidden');
            totalPenjualanBox.classList.add('hidden');
            document.querySelectorAll('.col-harga').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.col-subtotal').forEach(el => el.classList.add('hidden'));

            if (latarBelakangInput.value.includes('dijual/dilelang') || latarBelakangInput.value.includes('Pihak Pertama telah menyerahkan')) {
                latarBelakangInput.value = 'Menyatakan bahwa dengan mempertimbangkan kondisi fisik aset sarana dan prasarana yang ada pada lingkungan sekolah, bersama ini telah dilakukan pemeriksaan fisik bersama terhadap barang-barang inventaris yang telah rusak berat dan tidak dapat dipergunakan kembali.';
            }
        }
        calculateTotal();
    }

    function syncJurusanToPihakKedua() {
        if (!jurusanSelect) return;
        const selectedOpt = jurusanSelect.options[jurusanSelect.selectedIndex];
        if (selectedOpt && selectedOpt.value) {
            const kepala = selectedOpt.dataset.kepala;
            const nip = selectedOpt.dataset.nip;
            const namaJurusan = selectedOpt.dataset.nama;

            if (kepala && (!pihakKeduaNama.value || jenisSelect.value === 'serah_terima')) {
                pihakKeduaNama.value = kepala;
            }
            if (namaJurusan && (!pihakKeduaJabatan.value || jenisSelect.value === 'serah_terima' || pihakKeduaJabatan.value === 'Kepala Program / Unit Kerja')) {
                pihakKeduaJabatan.value = 'Kepala ' + namaJurusan;
            }
            if (nip && (!pihakKeduaNip.value || jenisSelect.value === 'serah_terima')) {
                pihakKeduaNip.value = nip;
            }

            fetchItemsForJurusan(selectedOpt.value);
        }
    }

    jurusanSelect.addEventListener('change', function () {
        syncJurusanToPihakKedua();
    });

    window.updateFormMode = updateFormMode;
    jenisSelect.addEventListener('change', updateFormMode);

    // Dynamic Row Add
    let rowIdx = 1;
    const addItemRowBtn = document.getElementById('addItemRowBtn');
    const addItemRowBtnBottom = document.getElementById('addItemRowBtnBottom');
    const itemsContainer = document.getElementById('itemsContainer');

    function createCardHtml(idx) {
        const isJual = jenisSelect.value === 'penjualan';
        const isSerahTerima = jenisSelect.value === 'serah_terima';

        let catOptions = '<option value="">-- Pilih Kategori --</option>';
        categoriesList.forEach(c => {
            catOptions += `<option value="${c.id}">${c.kode} - ${c.nama}</option>`;
        });

        return `
            <div class="item-card p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3 relative transition-all" data-idx="${idx}">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center row-num">${itemsContainer.children.length + 1}</span>
                        <span class="text-xs font-bold uppercase text-slate-700 item-title">Barang #${itemsContainer.children.length + 1}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="tipe-barang-toggle flex items-center gap-1 bg-white p-1 rounded-lg border border-slate-200 text-xs ${isSerahTerima ? '' : 'hidden'}">
                            <label class="inline-flex items-center gap-1 px-2 py-0.5 rounded cursor-pointer transition-colors bg-blue-50 text-blue-700 font-bold opt-new">
                                <input type="radio" name="items[${idx}][tipe_barang]" value="new" checked class="hidden radio-tipe" onchange="toggleItemType(${idx}, 'new')">
                                <span>Barang Baru</span>
                            </label>
                            <label class="inline-flex items-center gap-1 px-2 py-0.5 rounded cursor-pointer transition-colors text-slate-600 font-medium opt-existing">
                                <input type="radio" name="items[${idx}][tipe_barang]" value="existing" class="hidden radio-tipe" onchange="toggleItemType(${idx}, 'existing')">
                                <span>Ambil dari Data Barang Unit (Restock)</span>
                            </label>
                        </div>
                        <button type="button" class="remove-card-btn text-slate-400 hover:text-rose-600 p-1" title="Hapus Baris">
                            <i class="bi bi-trash text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="section-existing hidden p-3 rounded-xl bg-amber-50/80 border border-amber-200/80">
                    <label class="block text-xs font-bold text-amber-900 mb-1">
                        Pilih Barang dari Inventaris Unit:
                    </label>
                    <select name="items[${idx}][item_id]" class="existing-item-select w-full px-3 py-2 bg-white border border-amber-300 rounded-lg text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20" onchange="handleExistingItemSelect(${idx}, this)">
                        <option value="">-- Pilih Barang yang Tersedia di Unit --</option>
                    </select>
                    <p class="text-[11px] text-amber-700 mt-1">Stok barang terpilih otomatis bertambah saat berita acara disinkronisasikan ke data barang.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs sm:text-sm">
                    <div class="sm:col-span-5">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Barang <span class="text-rose-500">*</span></label>
                        <input type="text" name="items[${idx}][nama_barang]" required placeholder="Nama Barang" class="inp-nama w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Barang <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <input type="text" name="items[${idx}][kode_barang]" placeholder="Kode Barang" class="inp-kode w-full px-3 py-2 bg-white border border-slate-200 rounded-lg font-mono focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div class="sm:col-span-4 section-kategori">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Barang</label>
                        <select name="items[${idx}][category_id]" class="inp-category w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                            ${catOptions}
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah <span class="text-rose-500">*</span></label>
                        <input type="number" name="items[${idx}][jumlah]" value="1" min="0.01" step="any" required class="inp-qty w-full px-3 py-2 bg-white border border-slate-200 rounded-lg font-bold text-center focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Satuan <span class="text-rose-500">*</span></label>
                        <input type="text" name="items[${idx}][satuan]" value="unit" required placeholder="unit, pcs, box" class="inp-satuan w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kondisi</label>
                        <select name="items[${idx}][kondisi_saat_lapor]" class="inp-kondisi w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="baik">Kondisi Baik (Baru / Normal)</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="bekas_layak">Bekas Masih Layak</option>
                            <option value="lengkap">Lengkap &amp; Siap Digunakan</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2 section-jenis">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis</label>
                        <select name="items[${idx}][jenis]" class="inp-jenis w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="alat">Alat (Aset)</option>
                            <option value="bahan">Bahan Habis Pakai</option>
                        </select>
                    </div>
                    <div class="sm:col-span-3 section-lokasi">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Ruang / Penempatan</label>
                        <input type="text" name="items[${idx}][lokasi]" placeholder="Contoh: Lab 1, Bengkel" class="inp-lokasi w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>

                    <div class="sm:col-span-3 col-harga ${isJual ? '' : 'hidden'}">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Satuan (Rp)</label>
                        <input type="number" name="items[${idx}][harga_satuan]" value="0" min="0" step="1000" class="inp-price w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-right font-semibold focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div class="sm:col-span-3 col-subtotal ${isJual ? '' : 'hidden'}">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Subtotal (Rp)</label>
                        <input type="text" readonly value="Rp 0" class="inp-subtotal w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-right font-bold text-emerald-700">
                    </div>

                    <div class="sm:col-span-12">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Spesifikasi / Keterangan Tambahan</label>
                        <input type="text" name="items[${idx}][keterangan]" placeholder="Spesifikasi teknis, merk, kelengkapan, dll." class="inp-ket w-full px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>
            </div>
        `;
    }

    function addRow() {
        const div = document.createElement('div');
        div.innerHTML = createCardHtml(rowIdx);
        const card = div.firstElementChild;
        itemsContainer.appendChild(card);
        rowIdx++;
        attachCardEvents();
        populateAllExistingSelects();
        card.querySelector('.inp-nama').focus();
    }

    if (addItemRowBtn) addItemRowBtn.addEventListener('click', addRow);
    if (addItemRowBtnBottom) addItemRowBtnBottom.addEventListener('click', addRow);

    function attachCardEvents() {
        document.querySelectorAll('.remove-card-btn').forEach(btn => {
            btn.onclick = function () {
                if (itemsContainer.children.length > 1) {
                    btn.closest('.item-card').remove();
                    updateRowNumbers();
                    calculateTotal();
                } else {
                    alert('Minimal harus menyertakan 1 barang pada berita acara.');
                }
            };
        });

        document.querySelectorAll('.inp-qty, .inp-price').forEach(inp => {
            inp.oninput = calculateTotal;
        });
    }

    function updateRowNumbers() {
        document.querySelectorAll('.item-card').forEach((card, idx) => {
            card.querySelector('.row-num').textContent = idx + 1;
            card.querySelector('.item-title').textContent = 'Barang #' + (idx + 1);
        });
    }

    function calculateTotal() {
        if (jenisSelect.value !== 'penjualan') return;
        let total = 0;
        document.querySelectorAll('.item-card').forEach(card => {
            const qty = parseFloat(card.querySelector('.inp-qty')?.value || 0);
            const price = parseFloat(card.querySelector('.inp-price')?.value || 0);
            const subtotal = qty * price;
            const subtotalInput = card.querySelector('.inp-subtotal');
            if (subtotalInput) {
                subtotalInput.value = 'Rp ' + subtotal.toLocaleString('id-ID');
            }
            total += subtotal;
        });
        document.getElementById('totalPenjualanDisplay').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    attachCardEvents();
    updateFormMode();

    if (jurusanSelect.value) {
        fetchItemsForJurusan(jurusanSelect.value);
    }
});
</script>
@endpush
@endsection
