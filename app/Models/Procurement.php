<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nomor_usulan',
    'jurusan_id',
    'user_id',
    'judul_pengadaan',
    'nama_barang',
    'spesifikasi',
    'jumlah',
    'satuan',
    'perkiraan_biaya',
    'alasan',
    'ttd_pemohon',
    'ttd_pemohon_at',
    'status',
    'catatan_sarpras',
    'tanggal_persetujuan',
    'verified_by',
    'ttd_sarpras',
    'ttd_sarpras_at',
    'status_kepsek',
    'kepsek_by',
    'kepsek_at',
    'catatan_kepsek',
    'ttd_kepsek',
    'ttd_kepsek_at',
])]
class Procurement extends Model
{
    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'perkiraan_biaya' => 'decimal:2',
            'tanggal_persetujuan' => 'date',
            'ttd_pemohon_at' => 'datetime',
            'ttd_sarpras_at' => 'datetime',
            'kepsek_at' => 'datetime',
            'ttd_kepsek_at' => 'datetime',
        ];
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approverKepsek(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kepsek_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProcurementItem::class);
    }

    /**
     * Ringkasan nama barang untuk tampilan tabel & dashboard
     */
    public function getSummaryBarangAttribute(): string
    {
        if ($this->judul_pengadaan) {
            return $this->judul_pengadaan;
        }

        if ($this->items->count() > 1) {
            return $this->items->first()->nama_barang.' (+'.($this->items->count() - 1).' barang lainnya)';
        }

        return $this->nama_barang ?: 'Usulan Pengadaan';
    }
}
