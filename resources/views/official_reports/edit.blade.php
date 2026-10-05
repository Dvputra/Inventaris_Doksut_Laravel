@extends('layouts.app')

@section('title', 'Edit Berita Acara - ' . $officialReport->nomor_surat)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('official-reports.show', $officialReport) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors mb-2">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Detail Berita Acara</span>
            </a>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Edit Berita Acara</h1>
            <p class="text-sm text-slate-500 mt-0.5">
                Perbarui data arsip berita acara <span class="font-semibold text-slate-700">[{{ $officialReport->nomor_surat }}]</span>.
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

    <form action="{{ route('official-reports.update', $officialReport) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="reportForm">
        @csrf
        @method('PUT')

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
                        <option value="serah_terima" {{ old('jenis', $officialReport->jenis) == 'serah_terima' ? 'selected' : '' }}>
                            📦 Berita Acara Serah Terima Barang ke Jurusan (BAST)
                        </option>
                        <option value="barang_rusak" {{ old('jenis', $officialReport->jenis) == 'barang_rusak' ? 'selected' : '' }}>
                            ⚠️ Berita Acara Kerusakan / Penghapusan Barang (Afkir)
                        </option>
                        <option value="penjualan" {{ old('jenis', $officialReport->jenis) == 'penjualan' ? 'selected' : '' }}>
                            💰 Berita Acara Penjualan / Lelang Barang Bekas
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nomor Surat / Berita Acara <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nomor_surat" id="nomorSuratInput" value="{{ old('nomor_surat', $officialReport->nomor_surat) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 font-mono font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <p class="text-[11px] text-slate-400 mt-1">Format penomoran dapat disesuaikan dengan tata persuratan sekolah.</p>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Judul Berita Acara <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="judul" id="judulInput" value="{{ old('judul', $officialReport->judul) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Tanggal Berita Acara <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $officialReport->tanggal->format('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Jurusan / Unit Kerja Terkait <span class="text-rose-500" id="jurusanRequiredStar">*</span>
                    </label>
                    <select name="jurusan_id" id="jurusanSelect" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option value="" data-nama="Umum / Pihak Luar" data-kepala="" data-nip="">-- Umum (Pihak Luar / Sarpras) --</option>
                        @foreach($jurusans as $j)
                            <option value="{{ $j->id }}" data-nama="{{ $j->nama }}" data-kepala="{{ $j->kepala_bengkel ?? '' }}" data-nip="{{ $j->nip ?? '' }}" {{ old('jurusan_id', $officialReport->jurusan_id) == $j->id ? 'selected' : '' }}>
                                {{ $j->kode }} - {{ $j->nama }}
                            </option>
                        @endforeach
                    </select>
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
                        <input type="text" name="pihak_pertama_nama" id="pihakPertamaNama" value="{{ old('pihak_pertama_nama', $officialReport->pihak_pertama_nama) }}" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jabatan <span class="text-rose-500">*</span></label>
                        <input type="text" name="pihak_pertama_jabatan" id="pihakPertamaJabatan" value="{{ old('pihak_pertama_jabatan', $officialReport->pihak_pertama_jabatan) }}" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">NIP / NIY / NPY (Opsional)</label>
                        <input type="text" name="pihak_pertama_nip" id="pihakPertamaNip" value="{{ old('pihak_pertama_nip', $officialReport->pihak_pertama_nip) }}" placeholder="Contoh: 1985... atau nomor NIY" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
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

                    <!-- Pilihan Peran Pihak Kedua khusus Penjualan (Pembeli vs Saksi) -->
                    <div id="rolePihakKeduaPenjualanBox" class="hidden p-2.5 bg-amber-50/80 border border-amber-200 rounded-xl space-y-1.5">
                        <span class="block text-xs font-bold text-amber-900">Peran Pihak Kedua dalam Penjualan:</span>
                        <div class="flex items-center gap-4 text-xs font-semibold text-slate-700">
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="pihak_kedua_peran" value="pembeli" {{ old('pihak_kedua_peran', $officialReport->pihak_kedua_peran ?? 'pembeli') === 'pembeli' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500 role-pihak-kedua-radio">
                                <span>Pihak Pembeli</span>
                            </label>
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="pihak_kedua_peran" value="saksi" {{ old('pihak_kedua_peran', $officialReport->pihak_kedua_peran) === 'saksi' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500 role-pihak-kedua-radio">
                                <span>Saksi Luar / Terkait</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="pihak_kedua_nama" id="pihakKeduaNama" value="{{ old('pihak_kedua_nama', $officialReport->pihak_kedua_nama) }}" required placeholder="Nama Kepala Program / Unit Kerja" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jabatan / Status <span class="text-rose-500">*</span></label>
                        <input type="text" name="pihak_kedua_jabatan" id="pihakKeduaJabatan" value="{{ old('pihak_kedua_jabatan', $officialReport->pihak_kedua_jabatan) }}" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">NIP / NIY / NPY (Jika Ada)</label>
                        <input type="text" name="pihak_kedua_nip" id="pihakKeduaNip" value="{{ old('pihak_kedua_nip', $officialReport->pihak_kedua_nip) }}" placeholder="NIP/NIY Kepala Bengkel / Jurusan" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Instansi / Unit</label>
                            <input type="text" name="pihak_kedua_instansi" id="pihakKeduaInstansi" value="{{ old('pihak_kedua_instansi', $officialReport->pihak_kedua_instansi) }}" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">No. Kontak / HP</label>
                            <input type="text" name="pihak_kedua_kontak" id="pihakKeduaKontak" value="{{ old('pihak_kedua_kontak', $officialReport->pihak_kedua_kontak) }}" placeholder="08..." class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mengetahui Kepala Sekolah -->
            <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-2">
                    <i class="bi bi-shield-check"></i>
                    <span>Pejabat yang Mengetahui / Mengesahkan</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Pejabat</label>
                        <input type="text" name="mengetahui_nama" value="{{ old('mengetahui_nama', $officialReport->mengetahui_nama) }}" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jabatan</label>
                        <input type="text" name="mengetahui_jabatan" value="{{ old('mengetahui_jabatan', $officialReport->mengetahui_jabatan) }}" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">NIP / NIY / NPY (Jika Ada)</label>
                        <input type="text" name="mengetahui_nip" value="{{ old('mengetahui_nip', $officialReport->mengetahui_nip) }}" placeholder="-" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
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
                    <p class="text-[11px] text-slate-400 mt-0.5 sm:hidden flex items-center gap-1">
                        <i class="bi bi-arrows-expand text-slate-400"></i> Geser tabel ke samping untuk melihat & mengisi semua kolom
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="addItemRowBtn" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3.5 py-2 sm:py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold text-xs transition-colors">
                        <i class="bi bi-plus-circle"></i>
                        <span>Tambah Baris Barang</span>
                    </button>
                </div>
            </div>

            <!-- Tabel Dinamis Barang -->
            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="w-full text-left border-collapse text-xs sm:text-sm" id="itemsTable">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200 text-xs">
                            <th class="py-2.5 px-3 w-10 text-center">No</th>
                            <th class="py-2.5 px-3 min-w-[200px]">Nama Barang / Identitas Aset</th>
                            <th class="py-2.5 px-3 w-28">Kode / Unit</th>
                            <th class="py-2.5 px-3 w-20 text-center">Jumlah</th>
                            <th class="py-2.5 px-3 w-24">Satuan</th>
                            <th class="py-2.5 px-3 w-32" id="headerKondisi">Kondisi</th>
                            <th class="py-2.5 px-3 w-36 {{ $officialReport->jenis === 'penjualan' ? '' : 'hidden' }}" id="headerHarga">Harga Satuan (Rp)</th>
                            <th class="py-2.5 px-3 w-36 {{ $officialReport->jenis === 'penjualan' ? '' : 'hidden' }}" id="headerSubtotal">Subtotal Biaya (Rp)</th>
                            <th class="py-2.5 px-3 min-w-[150px]">Keterangan</th>
                            <th class="py-2.5 px-2 w-10 text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody" class="divide-y divide-slate-100">
                        @php
                            $formItems = old('items', $officialReport->items->toArray());
                            if (empty($formItems)) {
                                $formItems = [
                                    ['nama_barang' => '', 'kode_barang' => '', 'jumlah' => 1, 'satuan' => 'unit', 'kondisi_saat_lapor' => 'rusak_berat', 'harga_satuan' => 0, 'keterangan' => '']
                                ];
                            }
                        @endphp
                        @foreach($formItems as $idx => $it)
                            <tr class="item-row hover:bg-slate-50/50">
                                <td class="py-2.5 px-3 text-center text-slate-400 font-medium row-num">{{ $loop->iteration }}</td>
                                <td class="py-2 px-3">
                                    <input type="text" name="items[{{ $idx }}][nama_barang]" value="{{ $it['nama_barang'] ?? '' }}" required placeholder="Contoh: Mesin Bubut Krisbow" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" name="items[{{ $idx }}][kode_barang]" value="{{ $it['kode_barang'] ?? '' }}" placeholder="TP-MSN-001" class="w-full px-2 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <input type="number" name="items[{{ $idx }}][jumlah]" value="{{ $it['jumlah'] ?? 1 }}" min="1" required class="w-full px-2 py-1.5 text-center bg-white border border-slate-200 rounded-lg text-xs sm:text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-blue-500 item-qty">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" name="items[{{ $idx }}][satuan]" value="{{ $it['satuan'] ?? 'unit' }}" required class="w-full px-2 py-1.5 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </td>
                                <td class="py-2 px-3 col-kondisi">
                                    <select name="items[{{ $idx }}][kondisi_saat_lapor]" class="w-full px-2 py-1.5 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 item-kondisi-select" data-selected="{{ $it['kondisi_saat_lapor'] ?? '' }}">
                                        <!-- Diisi via JS updateFormMode -->
                                    </select>
                                </td>
                                <td class="py-2 px-3 col-harga {{ $officialReport->jenis === 'penjualan' ? '' : 'hidden' }}">
                                    <input type="number" name="items[{{ $idx }}][harga_satuan]" value="{{ (int)($it['harga_satuan'] ?? 0) }}" min="0" step="1000" class="w-full px-2 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-right focus:outline-none focus:ring-1 focus:ring-blue-500 item-price">
                                </td>
                                <td class="py-2 px-3 col-subtotal {{ $officialReport->jenis === 'penjualan' ? '' : 'hidden' }}">
                                    <input type="text" readonly class="w-full px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-right text-slate-700 item-subtotal" value="Rp {{ number_format(((int)($it['jumlah'] ?? 1)) * ((float)($it['harga_satuan'] ?? 0)), 0, ',', '.') }}">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" name="items[{{ $idx }}][keterangan]" value="{{ $it['keterangan'] ?? '' }}" placeholder="Keterangan kondisi/alasan" class="w-full px-2 py-1.5 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </td>
                                <td class="py-2 px-2 text-center">
                                    <button type="button" class="remove-row-btn text-slate-300 hover:text-rose-500 transition-colors p-1" title="Hapus Baris">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Total Nilai Penjualan (Hanya Muncul jika Penjualan) -->
            <div id="totalPenjualanBox" class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 {{ $officialReport->jenis === 'penjualan' ? 'flex' : 'hidden' }} items-center justify-between">
                <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Total Hasil Penjualan:</span>
                <span class="text-lg font-extrabold text-emerald-900" id="totalPenjualanDisplay">Rp {{ number_format($officialReport->total_nominal, 0, ',', '.') }}</span>
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
                    <textarea name="latar_belakang" id="latarBelakangInput" rows="3" placeholder="Jelaskan alasan dilakukannya pemeriksaan/penghapusan atau dasar penjualan aset..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">{{ old('latar_belakang', $officialReport->latar_belakang) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Upload Bukti Foto Fisik / Nota / Dokumen (Opsional)
                        </label>
                        @if($officialReport->file_lampiran)
                            <div class="mb-2 flex items-center gap-2 text-xs text-slate-600 bg-slate-50 p-2 rounded-lg border border-slate-200">
                                <i class="bi bi-file-earmark-check text-blue-600"></i>
                                <span class="truncate">File tersimpan: <a href="{{ Storage::url($officialReport->file_lampiran) }}" target="_blank" class="text-blue-600 font-semibold underline">Lihat Lampiran Saat Ini</a></span>
                            </div>
                        @endif
                        <input type="file" name="file_lampiran" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-[11px] text-slate-400 mt-1">Biarkan kosong jika tidak ingin mengubah lampiran. Format: PDF, JPG, PNG (Maksimal 5MB)</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Catatan Tambahan
                        </label>
                        <input type="text" name="catatan" value="{{ old('catatan', $officialReport->catatan) }}" placeholder="Contoh: Dana penjualan disetorkan ke Kas Sarpras / Komite" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-2">
            <a href="{{ route('official-reports.show', $officialReport) }}" class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm transition-colors">
                Batal
            </a>
            <button type="submit" id="btnSubmitReport" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition-all">
                <i class="bi bi-check2-circle text-base"></i>
                <span>Simpan Perubahan Berita Acara</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
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
    const headerHarga = document.getElementById('headerHarga');
    const headerSubtotal = document.getElementById('headerSubtotal');
    const totalPenjualanBox = document.getElementById('totalPenjualanBox');
    const latarBelakangInput = document.getElementById('latarBelakangInput');

    const rolePihakKeduaPenjualanBox = document.getElementById('rolePihakKeduaPenjualanBox');
    const rolePihakKeduaRadios = document.querySelectorAll('.role-pihak-kedua-radio');

    function getSelectedPihakKeduaPeran() {
        let peran = 'pembeli';
        rolePihakKeduaRadios.forEach(r => {
            if (r.checked) peran = r.value;
        });
        return peran;
    }

    function applyPeranPihakKeduaPenjualan() {
        if (jenisSelect.value !== 'penjualan') return;
        const peran = getSelectedPihakKeduaPeran();
        if (peran === 'pembeli') {
            labelPihakKedua.textContent = 'Pihak Kedua (Pihak Pembeli / Penerima Aset)';
            pihakKeduaNama.placeholder = 'Nama Pembeli / Rekanan';
        } else {
            labelPihakKedua.textContent = 'Pihak Kedua (Saksi Penjualan / Luar)';
            pihakKeduaNama.placeholder = 'Nama Saksi / Perwakilan';
        }
    }

    rolePihakKeduaRadios.forEach(radio => {
        radio.addEventListener('change', applyPeranPihakKeduaPenjualan);
    });

    function getKondisiOptions(mode, selectedValue = '') {
        if (mode === 'serah_terima') {
            return `
                <option value="baik" ${selectedValue === 'baik' ? 'selected' : ''}>Kondisi Baik (Baru / Normal)</option>
                <option value="rusak_ringan" ${selectedValue === 'rusak_ringan' ? 'selected' : ''}>Rusak Ringan (Perlu Servis)</option>
                <option value="bekas_layak" ${selectedValue === 'bekas_layak' ? 'selected' : ''}>Bekas Masih Layak Pakai</option>
                <option value="lengkap" ${selectedValue === 'lengkap' ? 'selected' : ''}>Lengkap &amp; Siap Digunakan</option>
            `;
        } else if (mode === 'penjualan') {
            return `
                <option value="bekas_layak" ${selectedValue === 'bekas_layak' ? 'selected' : ''}>Bekas Layak Jual</option>
                <option value="rusak_berat" ${selectedValue === 'rusak_berat' ? 'selected' : ''}>Rusak Berat / Scrap</option>
                <option value="rusak_total" ${selectedValue === 'rusak_total' ? 'selected' : ''}>Rusak Total / Afkir</option>
            `;
        } else {
            return `
                <option value="rusak_berat" ${selectedValue === 'rusak_berat' ? 'selected' : ''}>Rusak Berat</option>
                <option value="rusak_total" ${selectedValue === 'rusak_total' ? 'selected' : ''}>Rusak Total / Afkir</option>
                <option value="hilang" ${selectedValue === 'hilang' ? 'selected' : ''}>Hilang</option>
                <option value="bekas_layak" ${selectedValue === 'bekas_layak' ? 'selected' : ''}>Bekas Masih Layak</option>
            `;
        }
    }

    function syncKondisiDropdowns() {
        const mode = jenisSelect.value;
        document.querySelectorAll('.item-kondisi-select').forEach(select => {
            const currentVal = select.dataset.selected || select.value;
            select.innerHTML = getKondisiOptions(mode, currentVal);
            select.removeAttribute('data-selected');
        });
    }

    function updateFormMode() {
        const mode = jenisSelect.value;
        syncKondisiDropdowns();

        if (mode === 'serah_terima') {
            labelPihakPertama.textContent = 'Pihak Pertama (Pihak yang Menyerahkan / Sarpras)';
            labelPihakKedua.textContent = 'Pihak Kedua (Pihak yang Menerima / Kepala Program / Unit)';
            pihakKeduaNama.placeholder = 'Nama Kepala Program / Unit Kerja';
            
            if (rolePihakKeduaPenjualanBox) rolePihakKeduaPenjualanBox.classList.add('hidden');
            headerHarga.classList.add('hidden');
            if (headerSubtotal) headerSubtotal.classList.add('hidden');
            totalPenjualanBox.classList.add('hidden');
            totalPenjualanBox.classList.remove('flex');
            document.querySelectorAll('.col-harga').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.col-subtotal').forEach(el => el.classList.add('hidden'));
        } else if (mode === 'penjualan') {
            labelPihakPertama.textContent = 'Pihak Pertama (Penyelenggara / Sarpras)';
            if (rolePihakKeduaPenjualanBox) rolePihakKeduaPenjualanBox.classList.remove('hidden');
            applyPeranPihakKeduaPenjualan();

            headerHarga.classList.remove('hidden');
            if (headerSubtotal) headerSubtotal.classList.remove('hidden');
            totalPenjualanBox.classList.remove('hidden');
            totalPenjualanBox.classList.add('flex');
            document.querySelectorAll('.col-harga').forEach(el => el.classList.remove('hidden'));
            document.querySelectorAll('.col-subtotal').forEach(el => el.classList.remove('hidden'));
        } else {
            // barang_rusak
            labelPihakPertama.textContent = 'Pihak Pertama (Penyelenggara / Sarpras)';
            labelPihakKedua.textContent = 'Pihak Kedua (Saksi / Kepala Bengkel / Laboratorium)';
            pihakKeduaNama.placeholder = 'Nama Kepala Bengkel / Unit Kerja';
            if (rolePihakKeduaPenjualanBox) rolePihakKeduaPenjualanBox.classList.add('hidden');
            headerHarga.classList.add('hidden');
            if (headerSubtotal) headerSubtotal.classList.add('hidden');
            totalPenjualanBox.classList.add('hidden');
            totalPenjualanBox.classList.remove('flex');
            document.querySelectorAll('.col-harga').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.col-subtotal').forEach(el => el.classList.add('hidden'));
        }
        calculateTotal();
    }

    window.updateFormMode = updateFormMode;
    jenisSelect.addEventListener('change', updateFormMode);

    // Dynamic Row Add
    let rowIdx = {{ count($formItems) }};
    const addItemRowBtn = document.getElementById('addItemRowBtn');
    const tableBody = document.getElementById('itemsTableBody');

    addItemRowBtn.addEventListener('click', function () {
        const isJual = jenisSelect.value === 'penjualan';
        const kondisiOpts = getKondisiOptions(jenisSelect.value);
        const tr = document.createElement('tr');
        tr.className = 'item-row hover:bg-slate-50/50';
        tr.innerHTML = `
            <td class="py-2.5 px-3 text-center text-slate-400 font-medium row-num">${tableBody.children.length + 1}</td>
            <td class="py-2 px-3">
                <input type="text" name="items[${rowIdx}][nama_barang]" required placeholder="Nama Barang" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
            </td>
            <td class="py-2 px-3">
                <input type="text" name="items[${rowIdx}][kode_barang]" placeholder="Kode/Unit" class="w-full px-2 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono focus:outline-none focus:ring-1 focus:ring-blue-500">
            </td>
            <td class="py-2 px-3 text-center">
                <input type="number" name="items[${rowIdx}][jumlah]" value="1" min="1" required class="w-full px-2 py-1.5 text-center bg-white border border-slate-200 rounded-lg text-xs sm:text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-blue-500 item-qty">
            </td>
            <td class="py-2 px-3">
                <input type="text" name="items[${rowIdx}][satuan]" value="unit" required class="w-full px-2 py-1.5 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
            </td>
            <td class="py-2 px-3 col-kondisi">
                <select name="items[${rowIdx}][kondisi_saat_lapor]" class="w-full px-2 py-1.5 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 item-kondisi-select">
                    ${kondisiOpts}
                </select>
            </td>
            <td class="py-2 px-3 col-harga ${isJual ? '' : 'hidden'}">
                <input type="number" name="items[${rowIdx}][harga_satuan]" value="0" min="0" step="1000" class="w-full px-2 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-right focus:outline-none focus:ring-1 focus:ring-blue-500 item-price">
            </td>
            <td class="py-2 px-3 col-subtotal ${isJual ? '' : 'hidden'}">
                <input type="text" readonly class="w-full px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-right text-slate-700 item-subtotal" value="Rp 0">
            </td>
            <td class="py-2 px-3">
                <input type="text" name="items[${rowIdx}][keterangan]" placeholder="Keterangan" class="w-full px-2 py-1.5 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
            </td>
            <td class="py-2 px-2 text-center">
                <button type="button" class="remove-row-btn text-slate-300 hover:text-rose-500 transition-colors p-1" title="Hapus Baris">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(tr);
        rowIdx++;
        attachRowEvents();
    });

    function attachRowEvents() {
        document.querySelectorAll('.remove-row-btn').forEach(btn => {
            btn.onclick = function () {
                if (tableBody.children.length > 1) {
                    btn.closest('tr').remove();
                    updateRowNumbers();
                    calculateTotal();
                } else {
                    alert('Minimal harus menyertakan 1 barang pada berita acara.');
                }
            };
        });

        document.querySelectorAll('.item-qty, .item-price').forEach(inp => {
            inp.oninput = calculateTotal;
        });
    }

    function updateRowNumbers() {
        document.querySelectorAll('.row-num').forEach((cell, idx) => {
            cell.textContent = idx + 1;
        });
    }

    function calculateTotal() {
        if (jenisSelect.value !== 'penjualan') return;
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty')?.value || 0);
            const price = parseFloat(row.querySelector('.item-price')?.value || 0);
            const subtotal = qty * price;
            const subtotalInput = row.querySelector('.item-subtotal');
            if (subtotalInput) {
                subtotalInput.value = 'Rp ' + subtotal.toLocaleString('id-ID');
            }
            total += subtotal;
        });
        document.getElementById('totalPenjualanDisplay').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    attachRowEvents();
    updateFormMode();
});
</script>
@endpush
@endsection
