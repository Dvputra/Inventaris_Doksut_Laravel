<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemRestockController;
use App\Http\Controllers\ItemUnitController;
use App\Http\Controllers\ItemUsageController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\MaintenanceLogController;
use App\Http\Controllers\OfficialReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\PublicComplaintController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SarprasController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Halaman Awal Publik: Portal Layanan & Pengaduan Kendala Fasilitas Tanpa Perlu Login
Route::get('/', [PublicComplaintController::class, 'index'])->name('welcome');
Route::post('/lapor', [PublicComplaintController::class, 'store'])->name('public.complaint.store');
Route::get('/lacak-status', [PublicComplaintController::class, 'track'])->name('public.track');

// Guest Auth Routes (Login Petugas & Jurusan)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes (Dashboard Petugas Sarpras & Akun Jurusan)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard (Otomatis menampilkan data Sarpras atau Jurusan)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pengaturan Akun & Profil Mandiri
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Manajemen Pengaduan Kendala Fasilitas dari Guru/Tendik (Akun Sarpras & Kepala Sekolah)
    Route::resource('complaints', ComplaintController::class)
        ->only(['index', 'show', 'update', 'destroy'])
        ->middleware('role:sarpras,kepala_sekolah');

    // API Helper generate kode barang
    Route::get('/items/api/generate-code', [ItemController::class, 'generateCode'])->name('items.generate-code');

    // Menu Khusus Sarpras & Fasilitas Sekolah (Inventaris Umum & Stok di Gudang) - Sarpras, Pembantu Sarpras & Kepala Sekolah
    Route::middleware('role:sarpras,pembantu_sarpras,kepala_sekolah')->group(function () {
        Route::get('/sarpras/umum', [SarprasController::class, 'umum'])->name('sarpras.umum');
        Route::get('/sarpras/gudang', [SarprasController::class, 'gudang'])->name('sarpras.gudang');
    });

    // Manajemen Barang Inventaris
    Route::resource('items', ItemController::class);

    // Manajemen Unit Fisik Barang
    Route::post('/items/{item}/units', [ItemUnitController::class, 'store'])->name('items.units.store');
    Route::post('/items/{item}/units/batch', [ItemUnitController::class, 'storeBatch'])->name('items.units.store-batch');
    Route::put('/units/{unit}', [ItemUnitController::class, 'update'])->name('units.update');
    Route::delete('/units/{unit}', [ItemUnitController::class, 'destroy'])->name('units.destroy');

    // Pemeliharaan / Perbaikan Unit (Maintenance Log)
    Route::post('/units/{unit}/maintenance', [MaintenanceLogController::class, 'store'])->name('units.maintenance.store');
    Route::patch('/units/{unit}/maintenance/complete', [MaintenanceLogController::class, 'completeUnit'])->name('units.maintenance.complete');
    Route::patch('/units/{unit}/maintenance/cancel', [MaintenanceLogController::class, 'cancelUnit'])->name('units.maintenance.cancel');
    Route::patch('/maintenance/{log}/status', [MaintenanceLogController::class, 'updateStatus'])->name('maintenance.update-status');
    Route::delete('/maintenance/{log}', [MaintenanceLogController::class, 'destroy'])->name('maintenance.destroy');

    // Pemakaian Bahan Praktik Habis Pakai
    Route::resource('usages', ItemUsageController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    // Re-stok Masuk Bahan Habis Pakai
    Route::post('/items/{item}/restock', [ItemRestockController::class, 'store'])->name('items.restock.store');
    Route::delete('/restocks/{restock}', [ItemRestockController::class, 'destroy'])->name('restocks.destroy');

    // Peminjaman Alat Bengkel
    Route::get('/borrowings', [BorrowingController::class, 'index'])->name('borrowings.index');
    Route::get('/borrowings/create', [BorrowingController::class, 'create'])->name('borrowings.create');
    Route::post('/borrowings', [BorrowingController::class, 'store'])->name('borrowings.store');
    Route::get('/borrowings/{borrowing}/edit', [BorrowingController::class, 'edit'])->name('borrowings.edit');
    Route::put('/borrowings/{borrowing}', [BorrowingController::class, 'update'])->name('borrowings.update');
    Route::delete('/borrowings/{borrowing}', [BorrowingController::class, 'destroy'])->name('borrowings.destroy');
    Route::patch('/borrowings/{borrowing}/return', [BorrowingController::class, 'markAsReturned'])->name('borrowings.return');

    // Usulan Pengadaan Barang (Jurusan -> Sarpras -> Kepala Sekolah)
    Route::get('/procurements', [ProcurementController::class, 'index'])->name('procurements.index');
    Route::get('/procurements/create', [ProcurementController::class, 'create'])->name('procurements.create');
    Route::post('/procurements', [ProcurementController::class, 'store'])->name('procurements.store');
    Route::get('/procurements/{procurement}', [ProcurementController::class, 'show'])->name('procurements.show');
    Route::get('/procurements/{procurement}/edit', [ProcurementController::class, 'edit'])->name('procurements.edit');
    Route::put('/procurements/{procurement}', [ProcurementController::class, 'update'])->name('procurements.update');
    Route::delete('/procurements/{procurement}', [ProcurementController::class, 'destroy'])->name('procurements.destroy');
    Route::get('/procurements/{procurement}/print', [ProcurementController::class, 'print'])->name('procurements.print');
    Route::patch('/procurements/{procurement}/sign-pemohon', [ProcurementController::class, 'signPemohon'])->name('procurements.sign-pemohon');
    Route::patch('/procurements/{procurement}/approve', [ProcurementController::class, 'approve'])->name('procurements.approve')->middleware('role:sarpras');
    Route::patch('/procurements/{procurement}/reject', [ProcurementController::class, 'reject'])->name('procurements.reject')->middleware('role:sarpras');
    Route::patch('/procurements/{procurement}/approve-kepsek', [ProcurementController::class, 'approveKepsek'])->name('procurements.approve-kepsek')->middleware('role:sarpras,kepala_sekolah');
    Route::patch('/procurements/{procurement}/reject-kepsek', [ProcurementController::class, 'rejectKepsek'])->name('procurements.reject-kepsek')->middleware('role:sarpras,kepala_sekolah');

    // Berita Acara Sarpras & Kepala Sekolah (Barang Rusak & Penjualan/Lelang)
    Route::resource('official-reports', OfficialReportController::class)
        ->parameters(['official-reports' => 'officialReport'])
        ->middleware('role:sarpras,pembantu_sarpras,kepala_sekolah');
    Route::get('/official-reports/{officialReport}/print', [OfficialReportController::class, 'print'])
        ->name('official-reports.print')
        ->middleware('role:sarpras,pembantu_sarpras,kepala_sekolah');
    Route::patch('/official-reports/{officialReport}/approve', [OfficialReportController::class, 'approve'])
        ->name('official-reports.approve')
        ->middleware('role:sarpras,kepala_sekolah');
    Route::patch('/official-reports/{officialReport}/reject', [OfficialReportController::class, 'reject'])
        ->name('official-reports.reject')
        ->middleware('role:sarpras,kepala_sekolah');
    Route::patch('/official-reports/{officialReport}/sign-pihak-pertama', [OfficialReportController::class, 'signPihakPertama'])
        ->name('official-reports.sign-pihak-pertama')
        ->middleware('role:sarpras,pembantu_sarpras');

    // Kelola Akun Pengguna (Hanya Admin Sarpras)
    Route::resource('users', UserController::class)->middleware('role:sarpras');

    // Kelola Unit Kerja & Jurusan (Hanya Admin Sarpras)
    Route::resource('jurusans', JurusanController::class)->except(['show'])->middleware('role:sarpras');

    // Update Profil Kepala Unit oleh Akun Unit Kerja Mandiri
    Route::patch('/my-unit/update', [JurusanController::class, 'updateMyUnit'])->name('jurusan.my-unit.update');

    // Pusat Laporan & Cetak Rekapitulasi Resmi
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/items/print', [ReportController::class, 'printItems'])->name('reports.items.print');
    Route::get('/reports/items/export-excel', [ReportController::class, 'exportItemsExcel'])->name('reports.items.export-excel');
    Route::get('/reports/usages/print', [ReportController::class, 'printUsages'])->name('reports.usages.print');
    Route::get('/reports/usages/export-excel', [ReportController::class, 'exportUsagesExcel'])->name('reports.usages.export-excel');
    Route::get('/reports/complaints/print', [ReportController::class, 'printComplaints'])
        ->name('reports.complaints.print')
        ->middleware('role:sarpras,kepala_sekolah');
    Route::get('/reports/complaints/export-excel', [ReportController::class, 'exportComplaintsExcel'])
        ->name('reports.complaints.export-excel')
        ->middleware('role:sarpras,kepala_sekolah');
});

// Fallback route untuk menyajikan file publik jika filesystem host tidak mendukung symlink
Route::get('/storage/{path}', function (string $path) {
    if (! Storage::disk('public')->exists($path)) {
        abort(404);
    }

    return Storage::disk('public')->response($path);
})->where('path', '.*')->name('storage.local');
