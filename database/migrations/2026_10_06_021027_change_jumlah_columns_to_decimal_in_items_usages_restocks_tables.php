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
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('jumlah', 10, 2)->default(1)->change();
            $table->decimal('min_stok', 10, 2)->default(0)->change();
        });

        Schema::table('item_usages', function (Blueprint $table) {
            $table->decimal('jumlah', 10, 2)->change();
            $table->decimal('stok_sebelum', 10, 2)->nullable()->change();
            $table->decimal('stok_sesudah', 10, 2)->nullable()->change();
        });

        Schema::table('item_restocks', function (Blueprint $table) {
            $table->decimal('jumlah', 10, 2)->change();
            $table->decimal('stok_sebelum', 10, 2)->nullable()->change();
            $table->decimal('stok_sesudah', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_restocks', function (Blueprint $table) {
            $table->integer('jumlah')->change();
            $table->integer('stok_sebelum')->nullable()->change();
            $table->integer('stok_sesudah')->nullable()->change();
        });

        Schema::table('item_usages', function (Blueprint $table) {
            $table->integer('jumlah')->change();
            $table->integer('stok_sebelum')->nullable()->change();
            $table->integer('stok_sesudah')->nullable()->change();
        });

        Schema::table('items', function (Blueprint $table) {
            $table->integer('jumlah')->default(1)->change();
            $table->integer('min_stok')->default(0)->change();
        });
    }
};
