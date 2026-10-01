<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Complaint;
use App\Models\Item;
use App\Models\ItemUsage;
use App\Models\Jurusan;
use App\Models\Procurement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman ringkasan / dashboard utama.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isSarprasOrKepalaSekolah()) {
            $totalJurusans = Jurusan::count();
            $totalItems = Item::count();
            $totalUnit = Item::sum('jumlah');
            $totalUsers = User::count();

            $baikCount = Item::where('kondisi', 'baik')->sum('jumlah');
            $rusakRinganCount = Item::where('kondisi', 'rusak_ringan')->sum('jumlah');
            $rusakBeratCount = Item::where('kondisi', 'rusak_berat')->sum('jumlah');

            $peminjamanAktif = Borrowing::where('status', 'dipinjam')->count();
            $pengajuanMenunggu = Procurement::where('status', 'menunggu')->count();
            $pengaduanMenunggu = Complaint::where('status', 'menunggu')->count();

            $totalComputers = Item::where('is_computer', true)->sum('jumlah');
            $totalUsages = ItemUsage::count();
            $lowStockCount = Item::where('jenis', 'bahan')
                ->whereColumn('jumlah', '<=', 'min_stok')
                ->where('min_stok', '>', 0)
                ->count();

            $jurusans = Jurusan::withCount('items')->get();
            $recentBorrowings = Borrowing::with(['item', 'jurusan'])->latest()->take(5)->get();
            $recentProcurements = Procurement::with(['jurusan', 'user'])->latest()->take(5)->get();
            $recentComplaints = Complaint::with(['jurusan'])->latest()->take(5)->get();

            return view('dashboard.sarpras', compact(
                'totalJurusans',
                'totalItems',
                'totalUnit',
                'totalUsers',
                'baikCount',
                'rusakRinganCount',
                'rusakBeratCount',
                'peminjamanAktif',
                'pengajuanMenunggu',
                'pengaduanMenunggu',
                'totalComputers',
                'totalUsages',
                'lowStockCount',
                'jurusans',
                'recentBorrowings',
                'recentProcurements',
                'recentComplaints'
            ));
        }

        // Dashboard untuk akun jurusan
        $jurusanId = $user->jurusan_id;
        $jurusan = $user->jurusan;

        $totalItems = Item::where('jurusan_id', $jurusanId)->count();
        $totalUnit = Item::where('jurusan_id', $jurusanId)->sum('jumlah');

        $baikCount = Item::where('jurusan_id', $jurusanId)->where('kondisi', 'baik')->sum('jumlah');
        $rusakRinganCount = Item::where('jurusan_id', $jurusanId)->where('kondisi', 'rusak_ringan')->sum('jumlah');
        $rusakBeratCount = Item::where('jurusan_id', $jurusanId)->where('kondisi', 'rusak_berat')->sum('jumlah');

        $peminjamanAktif = Borrowing::where('jurusan_id', $jurusanId)->where('status', 'dipinjam')->count();
        $pengajuanCount = Procurement::where('jurusan_id', $jurusanId)->count();
        $pengajuanMenunggu = Procurement::where('jurusan_id', $jurusanId)->where('status', 'menunggu')->count();
        $pengaduanMenunggu = Complaint::where('jurusan_id', $jurusanId)->where('status', 'menunggu')->count();

        $totalComputers = Item::where('jurusan_id', $jurusanId)->where('is_computer', true)->sum('jumlah');
        $totalUsages = ItemUsage::where('jurusan_id', $jurusanId)->count();
        $lowStockCount = Item::where('jurusan_id', $jurusanId)
            ->where('jenis', 'bahan')
            ->whereColumn('jumlah', '<=', 'min_stok')
            ->where('min_stok', '>', 0)
            ->count();

        $recentBorrowings = Borrowing::where('jurusan_id', $jurusanId)->with('item')->latest()->take(5)->get();
        $recentProcurements = Procurement::where('jurusan_id', $jurusanId)->latest()->take(5)->get();
        $recentComplaints = Complaint::where('jurusan_id', $jurusanId)->latest()->take(5)->get();

        return view('dashboard.jurusan', compact(
            'jurusan',
            'totalItems',
            'totalUnit',
            'baikCount',
            'rusakRinganCount',
            'rusakBeratCount',
            'peminjamanAktif',
            'pengajuanCount',
            'pengajuanMenunggu',
            'pengaduanMenunggu',
            'totalComputers',
            'totalUsages',
            'lowStockCount',
            'recentBorrowings',
            'recentProcurements',
            'recentComplaints'
        ));
    }
}
