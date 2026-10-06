<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'item_id',
    'jurusan_id',
    'user_id',
    'jumlah',
    'satuan',
    'tanggal_pemakaian',
    'nama_guru',
    'kelas',
    'keperluan_jobsheet',
    'stok_sebelum',
    'stok_sesudah',
    'catatan',
])]
class ItemUsage extends Model
{
    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'tanggal_pemakaian' => 'date',
            'jumlah' => 'float',
            'stok_sebelum' => 'float',
            'stok_sesudah' => 'float',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
