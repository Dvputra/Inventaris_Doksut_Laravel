<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nomor_surat',
    'jenis',
    'judul',
    'tanggal',
    'user_id',
    'jurusan_id',
    'pihak_pertama_nama',
    'pihak_pertama_jabatan',
    'pihak_pertama_nip',
    'pihak_kedua_nama',
    'pihak_kedua_jabatan',
    'pihak_kedua_peran',
    'pihak_kedua_nip',
    'pihak_kedua_instansi',
    'pihak_kedua_kontak',
    'mengetahui_nama',
    'mengetahui_jabatan',
    'mengetahui_nip',
    'latar_belakang',
    'total_nominal',
    'status_dokumen',
    'status_approval',
    'approved_by',
    'approved_at',
    'catatan_approval',
    'ttd_pihak_pertama',
    'ttd_pihak_pertama_at',
    'ttd_pihak_kedua',
    'ttd_pihak_kedua_at',
    'ttd_mengetahui',
    'ttd_mengetahui_at',
    'file_lampiran',
    'catatan',
])]
class OfficialReport extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'total_nominal' => 'decimal:2',
            'approved_at' => 'datetime',
            'ttd_pihak_pertama_at' => 'datetime',
            'ttd_pihak_kedua_at' => 'datetime',
            'ttd_mengetahui_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OfficialReportItem::class);
    }

    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis) {
            'serah_terima' => 'Berita Acara Serah Terima Barang',
            'barang_rusak' => 'Berita Acara Barang Rusak / Afkir',
            'penjualan' => 'Berita Acara Penjualan / Lelang Barang',
            default => ucfirst(str_replace('_', ' ', $this->jenis)),
        };
    }
}
