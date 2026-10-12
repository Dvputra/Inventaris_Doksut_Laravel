<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'inventory_audit_id',
    'item_id',
    'kode_barang',
    'nama_barang',
    'kategori_nama',
    'satuan',
    'lokasi_sistem',
    'jumlah_sistem',
    'kondisi_sistem',
    'jumlah_fisik',
    'kondisi_fisik',
    'status_verifikasi',
    'catatan',
])]
class InventoryAuditItem extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jumlah_sistem' => 'float',
            'jumlah_fisik' => 'float',
        ];
    }

    public function audit(): BelongsTo
    {
        return $this->belongsTo(InventoryAudit::class, 'inventory_audit_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
