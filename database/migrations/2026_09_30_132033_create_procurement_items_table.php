<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('procurements', function (Blueprint $table) {
            $table->string('nomor_usulan')->nullable()->after('id');
            $table->string('judul_pengadaan')->nullable()->after('user_id');
            $table->string('nama_barang')->nullable()->change();
            $table->integer('jumlah')->default(1)->nullable()->change();
            $table->string('satuan', 30)->default('unit')->nullable()->change();
        });

        Schema::create('procurement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_id')->constrained('procurements')->cascadeOnDelete();
            $table->string('nama_barang');
            $table->text('spesifikasi')->nullable();
            $table->integer('jumlah')->default(1);
            $table->string('satuan', 30)->default('unit');
            $table->decimal('harga_satuan', 15, 2)->nullable();
            $table->decimal('perkiraan_biaya', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Backfill existing procurements into procurement_items
        $existing = DB::table('procurements')->get();
        foreach ($existing as $p) {
            $nomor = sprintf('UP-%s-%04d', date('Ym', strtotime($p->created_at ?? now())), $p->id);
            DB::table('procurements')
                ->where('id', $p->id)
                ->update([
                    'nomor_usulan' => $p->nomor_usulan ?: $nomor,
                    'judul_pengadaan' => $p->judul_pengadaan ?: "Usulan Pengadaan {$p->nama_barang}",
                ]);

            if (! empty($p->nama_barang)) {
                DB::table('procurement_items')->insert([
                    'procurement_id' => $p->id,
                    'nama_barang' => $p->nama_barang,
                    'spesifikasi' => $p->spesifikasi,
                    'jumlah' => $p->jumlah ?: 1,
                    'satuan' => $p->satuan ?: 'unit',
                    'perkiraan_biaya' => $p->perkiraan_biaya,
                    'created_at' => $p->created_at ?: now(),
                    'updated_at' => $p->updated_at ?: now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_items');

        Schema::table('procurements', function (Blueprint $table) {
            $table->dropColumn(['nomor_usulan', 'judul_pengadaan']);
        });
    }
};
