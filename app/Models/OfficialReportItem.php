<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'official_report_id',
    'item_id',
    'item_unit_id',
    'kode_barang',
    'nama_barang',
    'unit_code',
    'nomor_seri',
    'jumlah',
    'satuan',
    'kondisi_saat_lapor',
    'harga_satuan',
    'subtotal',
    'keterangan',
])]
class OfficialReportItem extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'harga_satuan' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'jumlah' => 'integer',
        ];
    }

    public function officialReport(): BelongsTo
    {
        return $this->belongsTo(OfficialReport::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function itemUnit(): BelongsTo
    {
        return $this->belongsTo(ItemUnit::class);
    }
}
