<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'ticket_code',
    'nama_pelapor',
    'kontak',
    'jurusan_id',
    'lokasi_ruang',
    'kategori',
    'item_id',
    'judul_kendala',
    'deskripsi',
    'foto',
    'tingkat_urgensi',
    'status',
    'tindak_lanjut',
    'teknisi_penanganan',
    'tanggal_selesai',
])]
class Complaint extends Model
{
    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'tanggal_selesai' => 'datetime',
        ];
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
