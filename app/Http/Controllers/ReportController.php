<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\Item;
use App\Models\ItemUsage;
use App\Models\Jurusan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Halaman pusat pilihan laporan & filter cetak inventaris dengan live preview data.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $jurusans = Jurusan::all();
        $categories = Category::all();
        $tab = $request->input('tab', 'items'); // 'items', 'usages', 'complaints'
        $viewMode = $request->input('view_mode', 'barang'); // 'barang' atau 'unit'

        // Cek apakah pengguna sudah melakukan filter pencarian untuk tab items
        $hasFiltered = $request->has('filter_applied')
            || $request->filled('periode')
            || $request->filled('tgl_mulai')
            || $request->filled('tgl_selesai')
            || $request->filled('category_id')
            || $request->filled('category')
            || $request->filled('kondisi')
            || $request->filled('jenis')
            || ($request->has('is_computer') && $request->is_computer !== null && $request->is_computer !== '')
            || ($user->isSarpras() && $request->filled('jurusan_id'));

        // Data preview untuk Barang / Inventaris
        $previewItems = collect();
        if ($tab === 'items' && $hasFiltered) {
            $itemsQuery = Item::with(['jurusan', 'category', 'units'])->orderBy('kode_barang');
            if ($user->isJurusan()) {
                $itemsQuery->where('jurusan_id', $user->jurusan_id);
            } elseif ($request->filled('jurusan_id')) {
                $itemsQuery->where('jurusan_id', $request->jurusan_id);
            }

            $categoryFilter = $request->input('category_id') ?? $request->input('category');
            $komCategory = Category::where('kode', 'KOM')->first();

            if (! empty($categoryFilter)) {
                $isKomFilter = false;
                if (is_numeric($categoryFilter)) {
                    $catId = (int) $categoryFilter;
                    if ($komCategory && $catId === $komCategory->id) {
                        $isKomFilter = true;
                    } else {
                        $itemsQuery->where('category_id', $catId);
                    }
                } else {
                    $codeOrName = strtoupper(trim((string) $categoryFilter));
                    if ($codeOrName === 'KOM' || str_contains(strtolower((string) $categoryFilter), 'komputer')) {
                        $isKomFilter = true;
                    } else {
                        $itemsQuery->whereHas('category', function ($q) use ($categoryFilter) {
                            $q->where('kode', strtoupper($categoryFilter))
                                ->orWhere('nama', 'like', "%{$categoryFilter}%");
                        });
                    }
                }

                if ($isKomFilter) {
                    $itemsQuery->where(function ($q) use ($komCategory) {
                        if ($komCategory) {
                            $q->where('category_id', $komCategory->id);
                        }
                        $q->orWhere('is_computer', true);
                    });
                }
            }

            if ($request->filled('kondisi')) {
                $itemsQuery->where('kondisi', $request->kondisi);
            }

            if ($request->filled('jenis')) {
                $bhnCategory = Category::where('kode', 'BHN')->first();
                $isBhnCategory = $bhnCategory && ($categoryFilter == $bhnCategory->id || strtoupper((string) $categoryFilter) === 'BHN');

                if ($request->jenis === 'bahan' && ! empty($categoryFilter) && ! $isBhnCategory) {
                    // Abaikan jenis=bahan untuk kategori non-bahan agar PC/alat tidak tersembunyi
                } else {
                    $itemsQuery->where('jenis', $request->jenis);
                }
            }

            if ($request->has('is_computer') && $request->is_computer !== null && $request->is_computer !== '') {
                $itemsQuery->where('is_computer', filter_var($request->is_computer, FILTER_VALIDATE_BOOLEAN));
            }

            $this->applyItemPeriodFilter($itemsQuery, $request);
            $previewItems = $itemsQuery->get();
        }

        // Data preview untuk Pemakaian Bahan
        $usagesQuery = ItemUsage::with(['item', 'jurusan'])->latest('tanggal_pemakaian');
        if ($user->isJurusan()) {
            $usagesQuery->where('jurusan_id', $user->jurusan_id);
        } elseif ($request->filled('jurusan_id') && $tab === 'usages') {
            $usagesQuery->where('jurusan_id', $request->jurusan_id);
        }

        if ($tab === 'usages') {
            if ($request->filled('bulan')) {
                $usagesQuery->whereMonth('tanggal_pemakaian', $request->bulan);
            }
            if ($request->filled('tahun')) {
                $usagesQuery->whereYear('tanggal_pemakaian', $request->tahun);
            }
        }
        $previewUsages = $usagesQuery->get();

        // Data preview untuk Pengaduan (khusus sarpras)
        $previewComplaints = collect();
        if ($user->isSarpras()) {
            $complaintsQuery = Complaint::with(['jurusan', 'item'])->latest();
            if ($request->filled('jurusan_id') && $tab === 'complaints') {
                $complaintsQuery->where('jurusan_id', $request->jurusan_id);
            }
            if ($tab === 'complaints' && $request->filled('status')) {
                $complaintsQuery->where('status', $request->status);
            }
            $previewComplaints = $complaintsQuery->get();
        }

        return view('reports.index', compact('jurusans', 'categories', 'user', 'tab', 'previewItems', 'previewUsages', 'previewComplaints', 'viewMode', 'hasFiltered'));
    }

    /**
     * Halaman cetak laporan inventaris barang (Print-Ready View).
     */
    public function printItems(Request $request): View
    {
        $user = $request->user();
        $query = Item::with(['jurusan', 'category', 'units'])->orderBy('kode_barang');

        if ($user->isJurusan()) {
            $query->where('jurusan_id', $user->jurusan_id);
            $selectedJurusan = $user->jurusan;
        } elseif ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
            $selectedJurusan = Jurusan::find($request->jurusan_id);
        } else {
            $selectedJurusan = null; // Semua Jurusan
        }

        $categoryFilter = $request->input('category_id') ?? $request->input('category');
        $komCategory = Category::where('kode', 'KOM')->first();

        if (! empty($categoryFilter)) {
            $isKomFilter = false;
            if (is_numeric($categoryFilter)) {
                $catId = (int) $categoryFilter;
                if ($komCategory && $catId === $komCategory->id) {
                    $isKomFilter = true;
                } else {
                    $query->where('category_id', $catId);
                }
            } else {
                $codeOrName = strtoupper(trim((string) $categoryFilter));
                if ($codeOrName === 'KOM' || str_contains(strtolower((string) $categoryFilter), 'komputer')) {
                    $isKomFilter = true;
                } else {
                    $query->whereHas('category', function ($q) use ($categoryFilter) {
                        $q->where('kode', strtoupper($categoryFilter))
                            ->orWhere('nama', 'like', "%{$categoryFilter}%");
                    });
                }
            }

            if ($isKomFilter) {
                $query->where(function ($q) use ($komCategory) {
                    if ($komCategory) {
                        $q->where('category_id', $komCategory->id);
                    }
                    $q->orWhere('is_computer', true);
                });
            }
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        if ($request->filled('jenis')) {
            $bhnCategory = Category::where('kode', 'BHN')->first();
            $isBhnCategory = $bhnCategory && ($categoryFilter == $bhnCategory->id || strtoupper((string) $categoryFilter) === 'BHN');

            if ($request->jenis === 'bahan' && ! empty($categoryFilter) && ! $isBhnCategory) {
                // Abaikan jenis=bahan untuk kategori non-bahan agar PC/alat tidak tersembunyi
            } else {
                $query->where('jenis', $request->jenis);
            }
        }

        if ($request->has('is_computer') && $request->is_computer !== null && $request->is_computer !== '') {
            $query->where('is_computer', filter_var($request->is_computer, FILTER_VALIDATE_BOOLEAN));
        }

        $this->applyItemPeriodFilter($query, $request);

        $items = $query->get();
        $viewMode = $request->input('view_mode', 'barang');
        $sarprasUnit = Jurusan::where('kode', 'SAR')->orWhere('nama', 'like', '%Sarpras%')->first();

        return view('reports.print-items', compact('items', 'selectedJurusan', 'user', 'viewMode', 'sarprasUnit'));
    }

    /**
     * Halaman cetak laporan pemakaian bahan praktik (Print-Ready View).
     */
    public function printUsages(Request $request): View
    {
        $user = $request->user();
        $query = ItemUsage::with(['item', 'jurusan'])->latest('tanggal_pemakaian');

        if ($user->isJurusan()) {
            $query->where('jurusan_id', $user->jurusan_id);
            $selectedJurusan = $user->jurusan;
        } elseif ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
            $selectedJurusan = Jurusan::find($request->jurusan_id);
        } else {
            $selectedJurusan = null;
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_pemakaian', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_pemakaian', $request->tahun);
        }

        $usages = $query->get();
        $sarprasUnit = Jurusan::where('kode', 'SAR')->orWhere('nama', 'like', '%Sarpras%')->first();

        return view('reports.print-usages', compact('usages', 'selectedJurusan', 'user', 'sarprasUnit'));
    }

    /**
     * Halaman cetak laporan pengaduan & perbaikan kendala (Print-Ready View).
     */
    public function printComplaints(Request $request): View
    {
        $user = $request->user();
        $query = Complaint::with(['jurusan', 'item'])->latest();

        if ($user->isJurusan()) {
            $query->where('jurusan_id', $user->jurusan_id);
            $selectedJurusan = $user->jurusan;
        } elseif ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
            $selectedJurusan = Jurusan::find($request->jurusan_id);
        } else {
            $selectedJurusan = null;
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $complaints = $query->get();

        return view('reports.print-complaints', compact('complaints', 'selectedJurusan', 'user'));
    }

    /**
     * Export Laporan Inventaris Barang ke format Excel (.csv UTF-8).
     * Mendukung mode ringkasan per barang atau mode detail per unit fisik dengan kondisi individu.
     */
    public function exportItemsExcel(Request $request)
    {
        $user = $request->user();
        $query = Item::with(['jurusan', 'category', 'units'])->orderBy('kode_barang');

        if ($user->isJurusan()) {
            $query->where('jurusan_id', $user->jurusan_id);
            $selectedJurusan = $user->jurusan;
        } elseif ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
            $selectedJurusan = Jurusan::find($request->jurusan_id);
        } else {
            $selectedJurusan = null;
        }

        $categoryFilter = $request->input('category_id') ?? $request->input('category');
        $komCategory = Category::where('kode', 'KOM')->first();

        if (! empty($categoryFilter)) {
            $isKomFilter = false;
            if (is_numeric($categoryFilter)) {
                $catId = (int) $categoryFilter;
                if ($komCategory && $catId === $komCategory->id) {
                    $isKomFilter = true;
                } else {
                    $query->where('category_id', $catId);
                }
            } else {
                $codeOrName = strtoupper(trim((string) $categoryFilter));
                if ($codeOrName === 'KOM' || str_contains(strtolower((string) $categoryFilter), 'komputer')) {
                    $isKomFilter = true;
                } else {
                    $query->whereHas('category', function ($q) use ($categoryFilter) {
                        $q->where('kode', strtoupper($categoryFilter))
                            ->orWhere('nama', 'like', "%{$categoryFilter}%");
                    });
                }
            }

            if ($isKomFilter) {
                $query->where(function ($q) use ($komCategory) {
                    if ($komCategory) {
                        $q->where('category_id', $komCategory->id);
                    }
                    $q->orWhere('is_computer', true);
                });
            }
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        if ($request->filled('jenis')) {
            $bhnCategory = Category::where('kode', 'BHN')->first();
            $isBhnCategory = $bhnCategory && ($categoryFilter == $bhnCategory->id || strtoupper((string) $categoryFilter) === 'BHN');

            if ($request->jenis === 'bahan' && ! empty($categoryFilter) && ! $isBhnCategory) {
                // Abaikan jenis=bahan untuk kategori non-bahan agar PC/alat tidak tersembunyi
            } else {
                $query->where('jenis', $request->jenis);
            }
        }

        if ($request->has('is_computer') && $request->is_computer !== null && $request->is_computer !== '') {
            $query->where('is_computer', filter_var($request->is_computer, FILTER_VALIDATE_BOOLEAN));
        }

        $this->applyItemPeriodFilter($query, $request);

        $items = $query->get();
        $isDetailUnit = $request->input('view_mode') === 'unit' || $request->input('mode') === 'detail' || $request->has('detail');
        $filename = ($isDetailUnit ? 'Laporan_Inventaris_Detail_Unit_' : 'Laporan_Inventaris_Aset_').date('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($items, $isDetailUnit) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens indonesian characters correctly
            fwrite($handle, "\xEF\xBB\xBF");

            if ($isDetailUnit) {
                // Header kolom Excel Mode Detail Fisik Per Unit
                fputcsv($handle, [
                    'No',
                    'Kode Unit Fisik',
                    'Nomor Seri',
                    'Nomor Meja / Stasiun',
                    'Kondisi Unit',
                    'Status Pinjam / Pakai',
                    'Lokasi Penempatan Unit',
                    'Kode Barang Induk',
                    'Nama Barang Induk',
                    'Kategori',
                    'Jurusan / Unit',
                    'Jenis',
                    'Tanggal Masuk Unit',
                    'Spesifikasi Perangkat / PC',
                    'Catatan Unit',
                ]);

                $counter = 1;
                foreach ($items as $item) {
                    $baseSpecs = [];
                    if ($item->is_computer) {
                        if ($item->processor) {
                            $baseSpecs[] = 'Proc: '.$item->processor;
                        }
                        if ($item->ram) {
                            $baseSpecs[] = 'RAM: '.$item->ram;
                        }
                        if ($item->storage) {
                            $baseSpecs[] = 'Disk: '.$item->storage;
                        }
                        if ($item->gpu_vga) {
                            $baseSpecs[] = 'GPU: '.$item->gpu_vga;
                        }
                        if ($item->sistem_operasi) {
                            $baseSpecs[] = 'OS: '.$item->sistem_operasi;
                        }
                    } elseif ($item->spesifikasi) {
                        $baseSpecs[] = $item->spesifikasi;
                    }

                    if ($item->units->count() > 0) {
                        foreach ($item->units as $unit) {
                            // Cek apakah unit memiliki spesifikasi kustom tersendiri
                            $unitSpecs = $baseSpecs;
                            if ($unit->processor || $unit->ram || $unit->storage) {
                                $unitSpecs = [];
                                if ($unit->processor) {
                                    $unitSpecs[] = 'Proc: '.$unit->processor;
                                }
                                if ($unit->ram) {
                                    $unitSpecs[] = 'RAM: '.$unit->ram;
                                }
                                if ($unit->storage) {
                                    $unitSpecs[] = 'Disk: '.$unit->storage;
                                }
                                if ($unit->gpu_vga) {
                                    $unitSpecs[] = 'GPU: '.$unit->gpu_vga;
                                }
                                if ($unit->sistem_operasi) {
                                    $unitSpecs[] = 'OS: '.$unit->sistem_operasi;
                                }
                            }

                            fputcsv($handle, [
                                $counter++,
                                $unit->unit_code,
                                $unit->nomor_seri ?: '-',
                                $unit->nomor_meja ?: '-',
                                ucwords(str_replace('_', ' ', $unit->kondisi ?: $item->kondisi)),
                                ucfirst($unit->status ?: 'tersedia'),
                                $unit->lokasi_penempatan ?: ($item->lokasi ?? '-'),
                                $item->kode_barang,
                                $item->nama_barang,
                                $item->category->nama ?? '-',
                                $item->jurusan->nama ?? 'Sarpras Umum',
                                ucfirst($item->jenis),
                                $unit->tanggal_masuk ? $unit->tanggal_masuk->format('d/m/Y') : '-',
                                implode(' | ', $unitSpecs) ?: '-',
                                $unit->catatan ?: '-',
                            ]);
                        }
                    } else {
                        // Barang bulk / tanpa unit individual
                        fputcsv($handle, [
                            $counter++,
                            $item->kode_barang.' (Bulk)',
                            '-',
                            '-',
                            ucwords(str_replace('_', ' ', $item->kondisi)),
                            'Tersedia ('.$item->jumlah.' '.$item->satuan.')',
                            $item->lokasi ?? '-',
                            $item->kode_barang,
                            $item->nama_barang,
                            $item->category->nama ?? '-',
                            $item->jurusan->nama ?? 'Sarpras Umum',
                            ucfirst($item->jenis),
                            '-',
                            implode(' | ', $baseSpecs) ?: '-',
                            'Pencatatan bulk/akumulasi stok',
                        ]);
                    }
                }
            } else {
                // Header kolom Excel Rekapitulasi Barang Lengkap dengan Rincian Kondisi Per Unit
                fputcsv($handle, [
                    'No',
                    'Kode Barang',
                    'Nama Barang',
                    'Kategori',
                    'Jurusan / Unit',
                    'Lokasi / Ruang',
                    'Tahun / Tgl Masuk',
                    'Jenis',
                    'Total Stok',
                    'Satuan',
                    'Kondisi Umum',
                    'Rincian Kondisi Unit Fisik (Baik / Rusak Ringan / Rusak Berat)',
                    'Daftar Kode Unit & Kondisi Masing-Masing',
                    'Spesifikasi Teknis / Komputer',
                ]);

                foreach ($items as $index => $item) {
                    $specs = [];
                    if ($item->is_computer) {
                        if ($item->processor) {
                            $specs[] = 'Proc: '.$item->processor;
                        }
                        if ($item->ram) {
                            $specs[] = 'RAM: '.$item->ram;
                        }
                        if ($item->storage) {
                            $specs[] = 'Disk: '.$item->storage;
                        }
                        if ($item->gpu_vga) {
                            $specs[] = 'GPU: '.$item->gpu_vga;
                        }
                        if ($item->sistem_operasi) {
                            $specs[] = 'OS: '.$item->sistem_operasi;
                        }
                    } elseif ($item->spesifikasi) {
                        $specs[] = $item->spesifikasi;
                    }

                    // Rincian kondisi unit
                    $baikCount = $item->units->where('kondisi', 'baik')->count();
                    $ringanCount = $item->units->where('kondisi', 'rusak_ringan')->count();
                    $beratCount = $item->units->where('kondisi', 'rusak_berat')->count();

                    if ($item->units->count() > 0) {
                        $kondisiDetailSummary = "Baik: {$baikCount}, Rusak Ringan: {$ringanCount}, Rusak Berat: {$beratCount}";
                        $unitsDetailedText = $item->units->map(function ($u) {
                            $cond = ucwords(str_replace('_', ' ', $u->kondisi ?: 'baik'));
                            $meja = $u->nomor_meja ? " [{$u->nomor_meja}]" : '';

                            return "{$u->unit_code}{$meja} ({$cond} - {$u->status})";
                        })->implode('; ');
                    } else {
                        $kondisiDetailSummary = ucwords(str_replace('_', ' ', $item->kondisi))." ({$item->jumlah} {$item->satuan})";
                        $unitsDetailedText = 'Pencatatan bulk/akumulasi stok';
                    }

                    $tglMasuk = $item->tahun_pengadaan ?: ($item->created_at ? $item->created_at->format('d/m/Y') : '-');

                    fputcsv($handle, [
                        $index + 1,
                        $item->kode_barang,
                        $item->nama_barang,
                        $item->category->nama ?? '-',
                        $item->jurusan->nama ?? 'Sarpras Umum',
                        $item->lokasi ?? '-',
                        $tglMasuk,
                        ucfirst($item->jenis),
                        $item->jumlah,
                        $item->satuan,
                        ucwords(str_replace('_', ' ', $item->kondisi)),
                        $kondisiDetailSummary,
                        $unitsDetailedText,
                        implode(' | ', $specs) ?: '-',
                    ]);
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export Laporan Pemakaian Bahan ke format Excel (.csv UTF-8).
     */
    public function exportUsagesExcel(Request $request)
    {
        $user = $request->user();
        $query = ItemUsage::with(['item', 'jurusan'])->latest('tanggal_pemakaian');

        if ($user->isJurusan()) {
            $query->where('jurusan_id', $user->jurusan_id);
            $selectedJurusan = $user->jurusan;
        } elseif ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
            $selectedJurusan = Jurusan::find($request->jurusan_id);
        } else {
            $selectedJurusan = null;
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_pemakaian', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_pemakaian', $request->tahun);
        }

        $usages = $query->get();
        $filename = 'Laporan_Pemakaian_Bahan_'.date('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($usages) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No',
                'Tanggal Pemakaian',
                'Kode Barang',
                'Nama Bahan',
                'Jurusan / Unit',
                'Guru Pengampu / Penanggung Jawab',
                'Kelas / Kelompok / Nama',
                'Keperluan / Jobsheet / Unit Kerja',
                'Jumlah Dipakai',
                'Satuan',
                'Sisa Stok Sesudah',
                'Catatan Tambahan',
            ]);

            foreach ($usages as $index => $u) {
                fputcsv($handle, [
                    $index + 1,
                    $u->tanggal_pemakaian ? Carbon::parse($u->tanggal_pemakaian)->format('d/m/Y') : '-',
                    $u->item->kode_barang ?? '-',
                    $u->item->nama_barang ?? '-',
                    $u->jurusan->nama ?? 'Sarpras Umum',
                    $u->nama_guru,
                    $u->kelas ?: '-',
                    $u->keperluan_jobsheet ?: '-',
                    $u->jumlah,
                    $u->item->satuan ?? 'unit',
                    $u->stok_sesudah,
                    $u->catatan ?: '-',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export Laporan Pengaduan & Servis Fasilitas ke format Excel (.csv UTF-8).
     */
    public function exportComplaintsExcel(Request $request)
    {
        $user = $request->user();
        $query = Complaint::with(['jurusan', 'item'])->latest();

        if ($user->isJurusan()) {
            $query->where('jurusan_id', $user->jurusan_id);
            $selectedJurusan = $user->jurusan;
        } elseif ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
            $selectedJurusan = Jurusan::find($request->jurusan_id);
        } else {
            $selectedJurusan = null;
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $complaints = $query->get();
        $filename = 'Laporan_Pengaduan_Sarpras_'.date('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($complaints) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No',
                'No. Tiket',
                'Tanggal Lapor',
                'Nama Pelapor',
                'Kontak / WhatsApp',
                'Jurusan / Unit',
                'Lokasi / Ruang',
                'Judul Kendala',
                'Deskripsi Kronologi',
                'Tingkat Urgensi',
                'Status Penanganan',
                'Teknisi Pelaksana',
                'Catatan Penanganan',
            ]);

            foreach ($complaints as $index => $c) {
                fputcsv($handle, [
                    $index + 1,
                    $c->ticket_code,
                    $c->created_at->format('d/m/Y H:i'),
                    $c->nama_pelapor,
                    $c->kontak ?: '-',
                    $c->jurusan->nama ?? 'Sarpras Umum',
                    $c->lokasi_ruang,
                    $c->judul_kendala,
                    $c->deskripsi,
                    ucfirst($c->tingkat_urgensi),
                    ucfirst($c->status),
                    $c->teknisi ?: '-',
                    $c->catatan_penanganan ?: '-',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Terapkan filter rentang waktu pengadaan/pencatatan barang dan unit fisik.
     * Pilihan: 'semua' (default), 'hari_ini', 'minggu_ini', 'bulan_ini', 'kustom', atau rentang tgl_mulai s/d tgl_selesai.
     *
     * @param  Builder  $query
     */
    protected function applyItemPeriodFilter($query, Request $request): void
    {
        $periode = $request->input('periode');
        $tglMulai = $request->input('tgl_mulai');
        $tglSelesai = $request->input('tgl_selesai');

        // Jika ada filter tanggal manual / rentang spesifik
        if (! empty($tglMulai) || ! empty($tglSelesai)) {
            $startDate = $tglMulai ? Carbon::parse($tglMulai)->startOfDay() : null;
            $endDate = $tglSelesai ? Carbon::parse($tglSelesai)->endOfDay() : null;

            $query->where(function ($q) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                        ->orWhereHas('units', function ($uq) use ($startDate, $endDate) {
                            $uq->whereBetween('tanggal_masuk', [$startDate->toDateString(), $endDate->toDateString()]);
                        });
                } elseif ($startDate) {
                    $q->where('created_at', '>=', $startDate)
                        ->orWhereHas('units', function ($uq) use ($startDate) {
                            $uq->whereDate('tanggal_masuk', '>=', $startDate->toDateString());
                        });
                } elseif ($endDate) {
                    $q->where('created_at', '<=', $endDate)
                        ->orWhereHas('units', function ($uq) use ($endDate) {
                            $uq->whereDate('tanggal_masuk', '<=', $endDate->toDateString());
                        });
                }
            });

            return;
        }

        if (! $periode || $periode === 'semua') {
            return;
        }

        $now = Carbon::now();

        if ($periode === 'hari_ini') {
            $today = $now->toDateString();
            $query->where(function ($q) use ($today) {
                $q->whereDate('created_at', $today)
                    ->orWhereHas('units', function ($uq) use ($today) {
                        $uq->whereDate('tanggal_masuk', $today);
                    });
            });
        } elseif ($periode === 'minggu_ini') {
            $startWeek = $now->copy()->startOfWeek()->toDateTimeString();
            $endWeek = $now->copy()->endOfWeek()->toDateTimeString();
            $startWeekDate = $now->copy()->startOfWeek()->toDateString();
            $endWeekDate = $now->copy()->endOfWeek()->toDateString();

            $query->where(function ($q) use ($startWeek, $endWeek, $startWeekDate, $endWeekDate) {
                $q->whereBetween('created_at', [$startWeek, $endWeek])
                    ->orWhereHas('units', function ($uq) use ($startWeekDate, $endWeekDate) {
                        $uq->whereBetween('tanggal_masuk', [$startWeekDate, $endWeekDate]);
                    });
            });
        } elseif ($periode === 'bulan_ini') {
            $month = $now->month;
            $year = $now->year;

            $query->where(function ($q) use ($month, $year) {
                $q->where(function ($sq) use ($month, $year) {
                    $sq->whereMonth('created_at', $month)
                        ->whereYear('created_at', $year);
                })->orWhere(function ($sq) use ($year) {
                    $sq->where('tahun_pengadaan', $year);
                })->orWhereHas('units', function ($uq) use ($month, $year) {
                    $uq->whereMonth('tanggal_masuk', $month)
                        ->whereYear('tanggal_masuk', $year);
                });
            });
        }
    }
}
