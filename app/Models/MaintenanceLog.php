<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'item_unit_id',
    'jurusan_id',
    'user_id',
    'tanggal',
    'gejala_kerusakan',
    'tindakan_perbaikan',
    'biaya',
    'teknisi_pelaksana',
    'status',
])]
class MaintenanceLog extends Model
{
    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'biaya' => 'decimal:2',
        ];
    }

    public function itemUnit(): BelongsTo
    {
        return $this->belongsTo(ItemUnit::class);
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
