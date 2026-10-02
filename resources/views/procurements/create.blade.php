@extends('layouts.app')

@section('title', 'Buat Usulan Pengadaan Barang')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <a href="{{ route('procurements.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors mb-3">
            <i class="bi bi-arrow-left text-sm"></i>
            <span>Kembali ke Daftar Usulan</span>
        </a>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 ring-1 ring-amber-500/20">
                <i class="bi bi-cart-plus text-base"></i>
            </span>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Formulir Usulan Pengadaan Barang &amp; Bahan</h1>
        </div>
        <p class="text-sm text-slate-500 mt-1">Ajukan permohonan pengadaan inventaris atau bahan praktik multi-barang ke bagian Sarana &amp; Prasarana.</p>
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
        <form action="{{ route('procurements.store') }}" method="POST" id="procurementForm">
            @csrf

            <!-- Section 1: Data Pokok Usulan -->
            <div class="border-b border-slate-100 pb-6 mb-6">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">1</span>
                    Informasi Pokok Usulan
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-5">
                    <!-- Jurusan Pemohon -->
                    @if(Auth::user()->isSarprasOrKepalaSekolah() || !Auth::user()->jurusan_id)
                        <div class="sm:col-span-6">
                            <label for="jurusan_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Jurusan / Unit Pemohon <span class="text-rose-500">*</span>
                            </label>
                            <select name="jurusan_id" id="jurusan_id" class="w-full px-3.5 py-2.5 bg-slate-50 border @error('jurusan_id') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required>
                                <option value="">-- Pilih Jurusan / Unit --</option>
                                @foreach($jurusans as $j)
                                    <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>
                                        {{ $j->kode }} - {{ $j->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="sm:col-span-6">
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jurusan / Unit Pemohon</label>
                            <input type="text" class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm text-slate-700 font-medium cursor-not-allowed" value="{{ Auth::user()->jurusan ? (Auth::user()->jurusan->nama . ' (' . Auth::user()->jurusan->kode . ')') : '-' }}" readonly disabled>
                            <span class="block text-[11px] text-slate-400 mt-1">Usulan akan dicatat resmi atas nama unit bengkel Anda.</span>
                        </div>
                    @endif

                    <!-- Judul Pengadaan -->
                    <div class="sm:col-span-6">
                        <label for="judul_pengadaan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Judul / Nama Agenda Usulan
                        </label>
                        <input type="text" name="judul_pengadaan" id="judul_pengadaan" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border @error('judul_pengadaan') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                               placeholder="Contoh: Pengadaan Alat Praktik Mesin & Perkakas TKR Semester Gasal" 
                               value="{{ old('judul_pengadaan') }}">
                        <span class="block text-[11px] text-slate-400 mt-1">Opsional: Jika dikosongkan, otomatis menggunakan nama barang utama.</span>
                    </div>

                    <!-- Alasan Kebutuhan -->
                    <div class="sm:col-span-12">
                        <label for="alasan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Alasan / Urgensi &amp; Justifikasi Kebutuhan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="alasan" id="alasan" rows="3" 
                                  class="w-full px-3.5 py-2.5 bg-slate-50 border @error('alasan') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                                  placeholder="Jelaskan kebutuhan operasional/praktik, urgensi (persiapan UKK, penggantian alat rusak, penambahan kuota siswa)..." required>{{ old('alasan') }}</textarea>
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
                        <p class="text-xs text-slate-500 mt-0.5">Anda dapat menambahkan beberapa barang sekaligus dalam satu surat usulan pengadaan.</p>
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
                        <span>Total Barang: <strong id="totalItemCount" class="text-slate-900 font-bold">1</strong> jenis barang</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-slate-500 font-medium">Total Estimasi Anggaran:</span>
                        <span class="text-base font-bold text-slate-900 bg-amber-100/70 text-amber-900 px-3 py-1 rounded-lg border border-amber-200" id="totalBiayaDisplay">
                            Rp 0
                        </span>
                    </div>
                </div>
            </div>

            <!-- Section 3: Tanda Tangan Digital Pemohon / Kepala Bengkel -->
            <div class="border-t border-slate-100 pt-6 mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-6 h-6 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">3</span>
                        Tanda Tangan Pemohon / Pengusul
                    </h2>
                    <span class="text-xs text-slate-500">Legalitas Pengusulan</span>
                </div>
                <p class="text-xs text-slate-500 mb-3">
                    Goreskan tanda tangan digital Kepala Program / Pemohon Unit Kerja di bawah ini menggunakan mouse atau layar sentuh HP:
                </p>

                <div class="max-w-md">
                    <input type="hidden" name="signature_data" id="pemohonSignatureData">
                    <div class="border-2 border-dashed border-slate-300 hover:border-blue-400 rounded-2xl overflow-hidden bg-slate-50 touch-none flex justify-center p-1 transition-colors">
                        <canvas id="pemohonCanvas" width="400" height="160" class="cursor-crosshair bg-white rounded-xl shadow-inner w-full max-w-[400px]"></canvas>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <button type="button" onclick="clearPemohonCanvas()" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold flex items-center gap-1.5 transition-colors">
                            <i class="bi bi-eraser text-xs"></i>
                            <span>Bersihkan TTD</span>
                        </button>
                        <span class="text-[11px] text-slate-400 italic">* TTD akan tercetak otomatis di surat dinas</span>
                    </div>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3 pt-5 border-t border-slate-100">
                <a href="{{ route('procurements.index') }}" class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm transition-colors">
                    Batal
                </a>
                <button type="submit" id="btnSubmitUsulan" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-sm shadow-xs transition-colors">
                    <i class="bi bi-send text-sm"></i>
                    <span>Kirim Usulan ke Sarpras</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('itemsContainer');
    const btnTambah = document.getElementById('btnTambahBarang');
    const totalItemCount = document.getElementById('totalItemCount');
    const totalBiayaDisplay = document.getElementById('totalBiayaDisplay');

    // Data lama dari session jika ada validation error
    const oldItems = @json(old('items', []));

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

            // Jika harga diisi dan subtotal kosong atau sedang diketik
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

    // Inisialisasi data lama jika ada, atau baris kosong pertama
    if (oldItems && oldItems.length > 0) {
        oldItems.forEach(item => createRow(item));
    } else {
        createRow();
    }

    // Inisialisasi Canvas TTD Pemohon
    initPemohonCanvas();
});

let pemohonDrawn = false;
function initPemohonCanvas() {
    const canvas = document.getElementById('pemohonCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    ctx.strokeStyle = '#0f172a';
    ctx.lineWidth = 2.5;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';

    let isDrawing = false;

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        if (e.touches && e.touches[0]) {
            return {
                x: (e.touches[0].clientX - rect.left) * scaleX,
                y: (e.touches[0].clientY - rect.top) * scaleY
            };
        }
        return {
            x: (e.clientX - rect.left) * scaleX,
            y: (e.clientY - rect.top) * scaleY
        };
    }

    function start(e) {
        e.preventDefault();
        isDrawing = true;
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
        pemohonDrawn = true;
    }

    function move(e) {
        if (!isDrawing) return;
        e.preventDefault();
        const pos = getPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
    }

    function stop(e) {
        if (isDrawing) {
            ctx.closePath();
            isDrawing = false;
        }
    }

    canvas.addEventListener('mousedown', start);
    canvas.addEventListener('mousemove', move);
    window.addEventListener('mouseup', stop);

    canvas.addEventListener('touchstart', start, { passive: false });
    canvas.addEventListener('touchmove', move, { passive: false });
    window.addEventListener('touchend', stop);

    const form = document.getElementById('procurementForm');
    if (form) {
        form.addEventListener('submit', function() {
            if (pemohonDrawn) {
                const dataUrl = canvas.toDataURL('image/png');
                document.getElementById('pemohonSignatureData').value = dataUrl;
            }
        });
    }
}

function clearPemohonCanvas() {
    const canvas = document.getElementById('pemohonCanvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        pemohonDrawn = false;
        const sigInput = document.getElementById('pemohonSignatureData');
        if (sigInput) sigInput.value = '';
    }
}
</script>
@endsection
