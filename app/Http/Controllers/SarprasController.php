<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemUsage;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SarprasController extends Controller
{
    /**
     * Halaman Inventaris Fasilitas Umum Sekolah (Lab CBT, TU, Aula, Ruang Guru, Genset, Kelas).
     */
    public function umum(Request $request): View
    {
        $sarJurusan = Jurusan::where('kode', 'SAR')->first();
        $sarJurusanId = $sarJurusan ? $sarJurusan->id : null;

        $query = Item::with(['category', 'units'])
            ->where('jurusan_id', $sarJurusanId)
            ->where(function ($q) {
                // Fasilitas umum: bukan yang berlokasi murni di gudang atau tipe alat terpasang
                $q->where('lokasi', 'not like', '%Gudang%')
                    ->orWhereNull('lokasi');
            })
            ->latest();

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

        if ($request->has('is_computer') && $request->is_computer !== '') {
            $query->where('is_computer', (bool) $request->is_computer);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%")
                    ->orWhere('processor', 'like', "%{$search}%")
                    ->orWhere('spesifikasi', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($cq) use ($search) {
                        $cq->where('kode', 'like', "%{$search}%")
                            ->orWhere('nama', 'like', "%{$search}%");
                    });
            });
        }

        $items = $query->paginate(10)->withQueryString();
        $categories = Category::all();

        // Ringkasan statistik fasilitas umum
        $totalItems = Item::where('jurusan_id', $sarJurusanId)
            ->where(function ($q) {
                $q->where('lokasi', 'not like', '%Gudang%')
                    ->orWhereNull('lokasi');
            })->count();

        $totalComputers = Item::where('jurusan_id', $sarJurusanId)
            ->where('is_computer', true)
            ->sum('jumlah');

        $totalBaik = Item::where('jurusan_id', $sarJurusanId)
            ->where('kondisi', 'baik')
            ->where(function ($q) {
                $q->where('lokasi', 'not like', '%Gudang%')
                    ->orWhereNull('lokasi');
            })->sum('jumlah');

        return view('sarpras.umum', compact(
            'items',
            'categories',
            'sarJurusan',
            'totalItems',
            'totalComputers',
            'totalBaik'
        ));
    }

    /**
     * Halaman Stok & Logistik di Gudang Sarpras (Buffer stock, bahan pemeliharaan, cadangan, perkakas).
     */
    public function gudang(Request $request): View
    {
        $sarJurusan = Jurusan::where('kode', 'SAR')->first();
        $sarJurusanId = $sarJurusan ? $sarJurusan->id : null;

        $query = Item::with(['category'])
            ->where('jurusan_id', $sarJurusanId)
            ->where(function ($q) {
                // Barang di gudang: lokasi mengandung kata Gudang atau bertipe bahan habis pakai
                $q->where('lokasi', 'like', '%Gudang%')
                    ->orWhere('jenis', 'bahan');
            })
            ->latest();

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('kritis') && $request->kritis == '1') {
            $query->whereColumn('jumlah', '<=', 'min_stok')->where('min_stok', '>', 0);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        $items = $query->paginate(10)->withQueryString();
        $categories = Category::all();

        // Ringkasan Gudang
        $totalItemGudang = Item::where('jurusan_id', $sarJurusanId)
            ->where(function ($q) {
                $q->where('lokasi', 'like', '%Gudang%')
                    ->orWhere('jenis', 'bahan');
            })->count();

        $totalStokFisik = Item::where('jurusan_id', $sarJurusanId)
            ->where(function ($q) {
                $q->where('lokasi', 'like', '%Gudang%')
                    ->orWhere('jenis', 'bahan');
            })->sum('jumlah');

        $kritisCount = Item::where('jurusan_id', $sarJurusanId)
            ->where('jenis', 'bahan')
            ->whereColumn('jumlah', '<=', 'min_stok')
            ->where('min_stok', '>', 0)
            ->count();

        $recentUsages = ItemUsage::with(['item'])
            ->where('jurusan_id', $sarJurusanId)
            ->latest('tanggal_pemakaian')
            ->take(5)
            ->get();

        return view('sarpras.gudang', compact(
            'items',
            'categories',
            'sarJurusan',
            'totalItemGudang',
            'totalStokFisik',
            'kritisCount',
            'recentUsages'
        ));
    }
}
