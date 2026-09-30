<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'procurement_id',
    'nama_barang',
    'spesifikasi',
    'jumlah',
    'satuan',
    'harga_satuan',
    'perkiraan_biaya',
    'keterangan',
])]
class ProcurementItem extends Model
{
    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'harga_satuan' => 'decimal:2',
            'perkiraan_biaya' => 'decimal:2',
        ];
    }

    public function procurement(): BelongsTo
    {
        return $this->belongsTo(Procurement::class);
    }
}
