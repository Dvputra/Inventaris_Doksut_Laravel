<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'official_report_id',
    'item_id',
    'item_unit_id',
    'tipe_barang',
    'category_id',
    'is_computer',
    'jenis',
    'lokasi',
    'spesifikasi',
    'kode_barang',
    'nama_barang',
    'unit_code',
    'nomor_seri',
    'jumlah',
    'satuan',
    'kondisi_saat_lapor',
    'harga_satuan',
    'subtotal',
    'is_checked',
    'checked_at',
    'checked_by',
    'kondisi_cek',
    'jumlah_cek',
    'catatan_cek',
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
            'jumlah' => 'float',
            'jumlah_cek' => 'float',
            'is_checked' => 'boolean',
            'is_computer' => 'boolean',
            'checked_at' => 'datetime',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}
