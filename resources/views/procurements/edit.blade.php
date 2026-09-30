@extends('layouts.app')

@section('title', 'Edit Usulan Pengadaan Barang')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <a href="{{ route('procurements.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors mb-3">
            <i class="bi bi-arrow-left text-sm"></i>
            <span>Kembali ke Daftar Usulan</span>
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 ring-1 ring-blue-500/20">
                    <i class="bi bi-pencil-square text-base"></i>
                </span>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Edit Usulan Pengadaan</h1>
                    <span class="text-xs font-mono font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                        {{ $procurement->nomor_usulan ?? 'UP-' . $procurement->id }}
                    </span>
                </div>
            </div>
            <div>
                @if($procurement->status === 'menunggu')
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                        <i class="bi bi-hourglass-split"></i> Status: Menunggu Verifikasi
                    </span>
                @elseif($procurement->status === 'disetujui')
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <i class="bi bi-check-circle-fill"></i> Status: Disetujui
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                        <i class="bi bi-x-circle-fill"></i> Status: Ditolak
                    </span>
                @endif
            </div>
        </div>
        <p class="text-sm text-slate-500 mt-1">Perbarui rincian usulan pengadaan barang atau bahan praktik.</p>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            <div class="font-bold mb-1 flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-rose-500"></i>
                <span>Terdapat kesalahan pada input formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form action="{{ route('procurements.update', $procurement) }}" method="POST" id="procurementForm">
            @csrf
            @method('PUT')

            <!-- Section 1: Data Pokok Usulan -->
            <div class="border-b border-slate-100 pb-6 mb-6">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">1</span>
                    Informasi Pokok Usulan
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-5">
                    <!-- Jurusan Pemohon -->
                    @if(Auth::user()->isSarpras())
                        <div class="sm:col-span-6">
                            <label for="jurusan_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Jurusan / Unit Pemohon <span class="text-rose-500">*</span>
                            </label>
                            <select name="jurusan_id" id="jurusan_id" class="w-full px-3.5 py-2.5 bg-slate-50 border @error('jurusan_id') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required>
                                <option value="">-- Pilih Jurusan / Unit --</option>
                                @foreach($jurusans as $j)
                                    <option value="{{ $j->id }}" {{ old('jurusan_id', $procurement->jurusan_id) == $j->id ? 'selected' : '' }}>
                                        {{ $j->kode }} - {{ $j->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="sm:col-span-6">
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jurusan / Unit Pemohon</label>
                            <input type="text" class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm text-slate-700 font-medium cursor-not-allowed" value="{{ $procurement->jurusan->nama }} ({{ $procurement->jurusan->kode }})" readonly disabled>
                            <span class="block text-[11px] text-slate-400 mt-1">Usulan tercatat atas nama unit bengkel Anda.</span>
                        </div>
                    @endif

                    <!-- Judul Pengadaan -->
                    <div class="sm:col-span-6">
                        <label for="judul_pengadaan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Judul / Nama Agenda Usulan
                        </label>
                        <input type="text" name="judul_pengadaan" id="judul_pengadaan" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border @error('judul_pengadaan') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                               placeholder="Contoh: Pengadaan Alat Praktik Mesin & Perkakas TKR" 
                               value="{{ old('judul_pengadaan', $procurement->judul_pengadaan) }}">
                    </div>

                    <!-- Alasan Kebutuhan -->
                    <div class="sm:col-span-12">
                        <label for="alasan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Alasan / Urgensi &amp; Justifikasi Kebutuhan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="alasan" id="alasan" rows="3" 
                                  class="w-full px-3.5 py-2.5 bg-slate-50 border @error('alasan') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                                  placeholder="Jelaskan kebutuhan operasional/praktik..." required>{{ old('alasan', $procurement->alasan) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Daftar Rincian Barang yang Diusulkan (Multi-Item) -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-6 h-6 rounded-md bg-amber-50 text-amber-600 flex items-center justify-center text-xs font-bold">2</span>
                            Daftar Rincian Barang / Bahan yang Diusulkan
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Kelola daftar item barang yang diajukan dalam surat usulan ini.</p>
                    </div>
                    <button type="button" id="btnTambahBarang" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold text-xs border border-blue-200/80 transition-colors">
                        <i class="bi bi-plus-circle-fill text-sm"></i>
                        <span>Tambah Barang Lagi</span>
                    </button>
                </div>

                <div class="block sm:hidden text-[11px] text-slate-500 mb-1.5 flex items-center gap-1">
                    <i class="bi bi-arrows-expand text-blue-600"></i>
                    <span>Geser tabel ke samping untuk mengisi spesifikasi & harga</span>
                </div>
                <div class="overflow-x-auto border border-slate-200 rounded-xl bg-slate-50/50">
                    <table class="w-full text-left border-collapse text-xs" id="itemsTable">
                        <thead>
                            <tr class="bg-slate-100 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                                <th class="py-3 px-3 w-10 text-center">No</th>
                                <th class="py-3 px-3 min-w-[220px]">Nama Barang / Alat <span class="text-rose-500">*</span></th>
                                <th class="py-3 px-3 min-w-[180px]">Spesifikasi / Merk</th>
                                <th class="py-3 px-3 w-24">Jumlah <span class="text-rose-500">*</span></th>
                                <th class="py-3 px-3 w-28">Satuan <span class="text-rose-500">*</span></th>
                                <th class="py-3 px-3 min-w-[140px]">Est. Harga Satuan (Rp)</th>
                                <th class="py-3 px-3 min-w-[140px]">Subtotal Biaya (Rp)</th>
                                <th class="py-3 px-3 min-w-[140px]">Keterangan</th>
                                <th class="py-3 px-2 w-12 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="itemsContainer" class="divide-y divide-slate-200/70 bg-white">
                            <!-- Baris item dinamis akan dimuat di sini -->
                        </tbody>
                    </table>
                </div>

                <!-- Summary Bar -->
                <div class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-slate-600 text-xs">
                        <i class="bi bi-info-circle text-blue-600"></i>
                        <span>Total Barang: <strong id="totalItemCount" class="text-slate-900 font-bold">0</strong> jenis barang</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-slate-500 font-medium">Total Estimasi Anggaran:</span>
                        <span class="text-base font-bold text-slate-900 bg-amber-100/70 text-amber-900 px-3 py-1 rounded-lg border border-amber-200" id="totalBiayaDisplay">
                            Rp 0
                        </span>
                    </div>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-5 border-t border-slate-100">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                    <a href="{{ route('procurements.print', $procurement) }}" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm transition-colors">
                        <i class="bi bi-printer text-slate-500"></i>
                        <span>Cetak Usulan</span>
                    </a>
                    @if(Auth::user()->isSarpras() || $procurement->status !== 'disetujui')
                        <button type="button" 
                                onclick="showConfirmDialog({
                                    title: 'Hapus Usulan Pengadaan',
                                    message: 'Apakah Anda yakin ingin menghapus usulan pengadaan [{{ $procurement->nomor_usulan ?? 'UP-'.$procurement->id }}] {{ addslashes($procurement->summary_barang) }}?',
                                    type: 'danger',
                                    confirmText: 'Ya, Hapus Usulan',
                                    form: document.getElementById('deleteProcurementForm')
                                })" 
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-sm transition-colors">
                            <i class="bi bi-trash text-sm"></i>
                            <span>Hapus Usulan</span>
                        </button>
                    @endif
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
                    <a href="{{ route('procurements.index') }}" class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xs transition-colors">
                        <i class="bi bi-check-lg text-base"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </div>
        </form>

        <form id="deleteProcurementForm" action="{{ route('procurements.destroy', $procurement) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('itemsContainer');
    const btnTambah = document.getElementById('btnTambahBarang');
    const totalItemCount = document.getElementById('totalItemCount');
    const totalBiayaDisplay = document.getElementById('totalBiayaDisplay');

    // Data dari session jika ada validation error, atau data model yang ada
    const oldItems = @json(old('items', []));
    const modelItems = @json($procurement->items);

    let itemIndex = 0;

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
    }

    function calculateTotals() {
        const rows = container.querySelectorAll('.item-row');
        totalItemCount.textContent = rows.length;

        let totalAnggaran = 0;
        rows.forEach((row, idx) => {
            const noCell = row.querySelector('.row-number');
            if (noCell) noCell.textContent = idx + 1;

            const jumlahInput = row.querySelector('.input-jumlah');
            const hargaInput = row.querySelector('.input-harga');
            const subtotalInput = row.querySelector('.input-subtotal');

            const jumlah = parseFloat(jumlahInput ? jumlahInput.value : 0) || 0;
            const harga = parseFloat(hargaInput ? hargaInput.value : 0) || 0;

            let subtotal = parseFloat(subtotalInput ? subtotalInput.value : 0) || 0;
            if (harga > 0 && (!subtotalInput.dataset.manual || subtotalInput.dataset.manual === 'false')) {
                subtotal = jumlah * harga;
                if (subtotalInput) subtotalInput.value = subtotal > 0 ? subtotal : '';
            }

            totalAnggaran += subtotal;
        });

        totalBiayaDisplay.textContent = formatRupiah(totalAnggaran);
    }

    function createRow(data = {}) {
        const tr = document.createElement('tr');
        tr.className = 'item-row hover:bg-slate-50/50 transition-colors';
        const curIdx = itemIndex++;

        tr.innerHTML = `
            <td class="py-2.5 px-3 text-center font-bold text-slate-400 row-number">
                1
            </td>
            <td class="py-2.5 px-3">
                <input type="text" name="items[${curIdx}][nama_barang]" 
                       class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Misal: Tang Kombinasi 8 inch" 
                       value="${data.nama_barang || ''}" required>
            </td>
            <td class="py-2.5 px-3">
                <input type="text" name="items[${curIdx}][spesifikasi]" 
                       class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Merk Tekiro / Vanadium Steel" 
                       value="${data.spesifikasi || ''}">
            </td>
            <td class="py-2.5 px-3">
                <input type="number" name="items[${curIdx}][jumlah]" min="1" 
                       class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 input-jumlah font-bold text-center" 
                       value="${data.jumlah || 1}" required>
            </td>
            <td class="py-2.5 px-3">
                <input type="text" name="items[${curIdx}][satuan]" 
                       class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="unit/set/pcs" 
                       value="${data.satuan || 'unit'}" required>
            </td>
            <td class="py-2.5 px-3">
                <input type="number" name="items[${curIdx}][harga_satuan]" min="0" step="500" 
                       class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 input-harga" 
                       placeholder="Est. Harga" 
                       value="${data.harga_satuan || ''}">
            </td>
            <td class="py-2.5 px-3">
                <input type="number" name="items[${curIdx}][perkiraan_biaya]" min="0" step="500" 
                       class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 input-subtotal font-bold" 
                       placeholder="Subtotal" 
                       value="${data.perkiraan_biaya || ''}">
            </td>
            <td class="py-2.5 px-3">
                <input type="text" name="items[${curIdx}][keterangan]" 
                       class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Keterangan..." 
                       value="${data.keterangan || ''}">
            </td>
            <td class="py-2.5 px-2 text-center">
                <button type="button" class="btn-hapus-row inline-flex items-center justify-center w-7 h-7 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus Barang">
                    <i class="bi bi-trash text-sm"></i>
                </button>
            </td>
        `;

        const btnHapus = tr.querySelector('.btn-hapus-row');
        btnHapus.addEventListener('click', function() {
            if (container.querySelectorAll('.item-row').length <= 1) {
                alert('Minimal satu barang harus ada dalam usulan pengadaan.');
                return;
            }
            tr.remove();
            calculateTotals();
        });

        const inputJumlah = tr.querySelector('.input-jumlah');
        const inputHarga = tr.querySelector('.input-harga');
        const inputSubtotal = tr.querySelector('.input-subtotal');

        inputJumlah.addEventListener('input', function() {
            inputSubtotal.dataset.manual = 'false';
            calculateTotals();
        });

        inputHarga.addEventListener('input', function() {
            inputSubtotal.dataset.manual = 'false';
            calculateTotals();
        });

        inputSubtotal.addEventListener('input', function() {
            inputSubtotal.dataset.manual = 'true';
            calculateTotals();
        });

        container.appendChild(tr);
        calculateTotals();
    }

    btnTambah.addEventListener('click', function() {
        createRow();
    });

    // Populate rows
    if (oldItems && oldItems.length > 0) {
        oldItems.forEach(item => createRow(item));
    } else if (modelItems && modelItems.length > 0) {
        modelItems.forEach(item => createRow(item));
    } else {
        // Fallback jika belum ada di procurement_items tapi ada di procurement parent
        createRow({
            nama_barang: @json($procurement->nama_barang),
            spesifikasi: @json($procurement->spesifikasi),
            jumlah: @json($procurement->jumlah),
            satuan: @json($procurement->satuan),
            perkiraan_biaya: @json($procurement->perkiraan_biaya),
        });
    }
});
</script>
@endsection
