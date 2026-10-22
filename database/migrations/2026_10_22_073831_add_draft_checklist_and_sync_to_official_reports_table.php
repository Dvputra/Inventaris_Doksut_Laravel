<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('official_reports', function (Blueprint $table) {
            // is_locked: apakah dokumen dikunci (true) atau terbuka untuk revisi (false)
            $table->boolean('is_locked')->default(false)->after('status_dokumen');
            $table->timestamp('locked_at')->nullable()->after('is_locked');
            $table->foreignId('locked_by')->nullable()->after('locked_at')->constrained('users')->nullOnDelete();

            // is_synced: apakah barang sudah disinkronisasikan ke master barang (items)
            $table->boolean('is_synced')->default(false)->after('locked_by');
            $table->timestamp('synced_at')->nullable()->after('is_synced');
            $table->foreignId('synced_by')->nullable()->after('synced_at')->constrained('users')->nullOnDelete();
        });

        Schema::table('official_report_items', function (Blueprint $table) {
            // tipe_barang: 'existing' (restock barang lama) atau 'new' (barang baru)
            $table->string('tipe_barang')->default('new')->after('item_unit_id');
            $table->foreignId('category_id')->nullable()->after('tipe_barang')->constrained('categories')->nullOnDelete();
            $table->string('jenis')->default('alat')->after('category_id'); // alat atau bahan
            $table->string('lokasi')->nullable()->after('jenis');
            $table->text('spesifikasi')->nullable()->after('lokasi');

            // Status checklist fisik oleh unit kerja
            $table->boolean('is_checked')->default(false)->after('subtotal');
            $table->timestamp('checked_at')->nullable()->after('is_checked');
            $table->foreignId('checked_by')->nullable()->after('checked_at')->constrained('users')->nullOnDelete();
            $table->string('kondisi_cek')->nullable()->after('checked_by'); // baik, rusak_ringan, dll
            $table->decimal('jumlah_cek', 10, 2)->nullable()->after('kondisi_cek');
            $table->text('catatan_cek')->nullable()->after('jumlah_cek');

            // Ubah tipe kolom jumlah jadi decimal agar presisi untuk bahan desimal (misal 2.5 kg)
            $table->decimal('jumlah', 10, 2)->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('official_report_items', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['checked_by']);
            $table->dropColumn([
                'tipe_barang',
                'category_id',
                'jenis',
                'lokasi',
                'spesifikasi',
                'is_checked',
                'checked_at',
                'checked_by',
                'kondisi_cek',
                'jumlah_cek',
                'catatan_cek',
            ]);
        });

        Schema::table('official_reports', function (Blueprint $table) {
            $table->dropForeign(['locked_by']);
            $table->dropForeign(['synced_by']);
            $table->dropColumn([
                'is_locked',
                'locked_at',
                'locked_by',
                'is_synced',
                'synced_at',
                'synced_by',
            ]);
        });
    }
};
