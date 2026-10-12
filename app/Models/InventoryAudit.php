<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'audit_code',
    'judul',
    'tanggal_audit',
    'jurusan_id',
    'auditor_user_id',
    'status',
    'auditor_nama',
    'auditor_nip',
    'auditor_jabatan',
    'ttd_auditor',
    'ttd_auditor_at',
    'auditee_nama',
    'auditee_nip',
    'auditee_jabatan',
    'ttd_auditee',
    'ttd_auditee_at',
    'total_barang_tercatat',
    'total_barang_sesuai',
    'total_barang_rusak',
    'total_barang_selisih',
    'total_barang_hilang',
    'catatan_umum',
])]
class InventoryAudit extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_audit' => 'date',
            'ttd_auditor_at' => 'datetime',
            'ttd_auditee_at' => 'datetime',
        ];
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryAuditItem::class, 'inventory_audit_id');
    }

    /**
     * Helper kalkulasi ulang ringkasan statistik temuan audit.
     */
    public function recalculateSummary(): void
    {
        $this->total_barang_tercatat = $this->items()->count();
        $this->total_barang_sesuai = $this->items()->where('status_verifikasi', 'sesuai')->count();
        $this->total_barang_rusak = $this->items()->where('status_verifikasi', 'rusak')->count();
        $this->total_barang_selisih = $this->items()->where('status_verifikasi', 'selisih')->count();
        $this->total_barang_hilang = $this->items()->where('status_verifikasi', 'hilang')->count();
        $this->save();
    }
}
