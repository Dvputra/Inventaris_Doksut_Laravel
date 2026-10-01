<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'jurusan_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke jurusan (khusus untuk akun jurusan).
     */
    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    /**
     * Cek apakah user adalah admin sarpras pusat.
     */
    public function isSarpras(): bool
    {
        return $this->role === 'sarpras';
    }

    /**
     * Cek apakah user adalah Kepala Sekolah.
     */
    public function isKepalaSekolah(): bool
    {
        return $this->role === 'kepala_sekolah';
    }

    /**
     * Cek apakah user memiliki hak akses manajerial (Sarpras atau Kepala Sekolah).
     */
    public function isSarprasOrKepalaSekolah(): bool
    {
        return in_array($this->role, ['sarpras', 'kepala_sekolah'], true);
    }

    /**
     * Cek apakah user adalah akun jurusan.
     */
    public function isJurusan(): bool
    {
        return $this->role === 'jurusan';
    }
}
