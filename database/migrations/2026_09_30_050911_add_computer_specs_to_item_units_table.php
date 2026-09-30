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
        Schema::table('item_units', function (Blueprint $table) {
            $table->string('processor')->nullable()->after('nomor_meja');
            $table->string('ram')->nullable()->after('processor');
            $table->string('storage')->nullable()->after('ram');
            $table->string('gpu_vga')->nullable()->after('storage');
            $table->string('monitor')->nullable()->after('gpu_vga');
            $table->string('sistem_operasi')->nullable()->after('monitor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_units', function (Blueprint $table) {
            $table->dropColumn([
                'processor',
                'ram',
                'storage',
                'gpu_vga',
                'monitor',
                'sistem_operasi',
            ]);
        });
    }
};
