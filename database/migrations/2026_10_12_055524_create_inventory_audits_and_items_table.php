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
        Schema::create('inventory_audits', function (Blueprint $table) {
            $table->id();
            $table->string('audit_code', 50)->unique();
            $table->string('judul', 255);
            $table->date('tanggal_audit');
            $table->foreignId('jurusan_id')->constrained('jurusans')->cascadeOnDelete();
            $table->foreignId('auditor_user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Status audit: draft (sedang berlangsung), selesai (selesai verifikasi & siap ttd), final (lengkap kedua ttd)
            $table->enum('status', ['draft', 'selesai', 'final'])->default('draft');
            
            // Identitas Auditor (Sarpras)
            $table->string('auditor_nama', 150);
            $table->string('auditor_nip', 50)->nullable();
            $table->string('auditor_jabatan', 100)->default('Petugas Sarpras');
            $table->string('ttd_auditor', 255)->nullable();
            $table->timestamp('ttd_auditor_at')->nullable();

            // Identitas Auditee (Kepala Bengkel / Unit Kerja)
            $table->string('auditee_nama', 150)->nullable();
            $table->string('auditee_nip', 50)->nullable();
            $table->string('auditee_jabatan', 100)->nullable();
            $table->string('ttd_auditee', 255)->nullable();
            $table->timestamp('ttd_auditee_at')->nullable();

            // Ringkasan statistik otomatis
            $table->integer('total_barang_tercatat')->default(0);
            $table->integer('total_barang_sesuai')->default(0);
            $table->integer('total_barang_rusak')->default(0);
            $table->integer('total_barang_selisih')->default(0);
            $table->integer('total_barang_hilang')->default(0);

            $table->text('catatan_umum')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_audit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_audit_id')->constrained('inventory_audits')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            
            $table->string('kode_barang', 50);
            $table->string('nama_barang', 255);
            $table->string('kategori_nama', 100)->nullable();
            $table->string('satuan', 30)->default('unit');
            $table->string('lokasi_sistem', 255)->nullable();

            // Kuantitas & Kondisi di Sistem saat ditarik
            $table->decimal('jumlah_sistem', 12, 2)->default(0);
            $table->string('kondisi_sistem', 30)->default('baik');

            // Kuantitas & Kondisi Hasil Verifikasi Fisik
            $table->decimal('jumlah_fisik', 12, 2)->default(0);
            $table->enum('kondisi_fisik', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            
            // Status verifikasi: belum_dicek, sesuai, rusak, selisih, hilang
            $table->enum('status_verifikasi', ['belum_dicek', 'sesuai', 'rusak', 'selisih', 'hilang'])->default('belum_dicek');
            
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_audit_items');
        Schema::dropIfExists('inventory_audits');
    }
};
