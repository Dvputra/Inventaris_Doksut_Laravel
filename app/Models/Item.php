<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'kode_barang',
    'nama_barang',
    'jurusan_id',
    'category_id',
    'jumlah',
    'satuan',
    'kondisi',
    'lokasi',
    'jenis',
    'sumber_dana',
    'tahun_pengadaan',
    'spesifikasi',
    'foto',
    'is_computer',
    'processor',
    'ram',
    'storage',
    'gpu_vga',
    'monitor',
    'sistem_operasi',
    'min_stok',
])]
class Item extends Model
{
    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_computer' => 'boolean',
            'jumlah' => 'float',
            'min_stok' => 'float',
        ];
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(ItemUnit::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(ItemUsage::class);
    }

    public function restocks(): HasMany
    {
        return $this->hasMany(ItemRestock::class);
    }
}
