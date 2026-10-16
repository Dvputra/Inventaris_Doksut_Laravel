<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kode', 'nama', 'parent_id', 'kepala_bengkel', 'nip', 'deskripsi'])]
class Jurusan extends Model
{
    /**
     * Unit Kerja Utama / Induk (jika ini adalah sub-unit / lab).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'parent_id');
    }

    /**
     * Seluruh Sub-Unit / Lab / Bengkel di bawah unit kerja ini.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Jurusan::class, 'parent_id')->orderBy('nama');
    }

    /**
     * Cek apakah unit ini adalah sub-unit (memiliki induk).
     */
    public function isSubUnit(): bool
    {
        return ! is_null($this->parent_id);
    }

    /**
     * Cek apakah unit ini memiliki sub-unit di bawahnya.
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    public function procurements(): HasMany
    {
        return $this->hasMany(Procurement::class);
    }
}
