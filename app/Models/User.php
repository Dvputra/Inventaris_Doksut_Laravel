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

#[Fillable(['name', 'username', 'email', 'password', 'display_password', 'role', 'jurusan_id'])]
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
            'display_password' => 'encrypted',
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
     * Cek apakah user adalah pembantu sarpras.
     */
    public function isPembantuSarpras(): bool
    {
        return $this->role === 'pembantu_sarpras';
    }

    /**
     * Cek apakah user adalah Kepala Sekolah.
     */
    public function isKepalaSekolah(): bool
    {
        return $this->role === 'kepala_sekolah';
    }

    /**
     * Cek apakah user memiliki hak akses manajerial (Sarpras, Pembantu Sarpras, atau Kepala Sekolah).
     */
    public function isSarprasOrKepalaSekolah(): bool
    {
        return in_array($this->role, ['sarpras', 'pembantu_sarpras', 'kepala_sekolah'], true);
    }

    /**
     * Cek apakah user memiliki hak pengelolaan teknis Sarpras (Sarpras Pusat atau Pembantu Sarpras).
     */
    public function isStaffSarpras(): bool
    {
        return in_array($this->role, ['sarpras', 'pembantu_sarpras'], true);
    }

    /**
     * Cek apakah user adalah akun jurusan / unit kerja.
     */
    public function isJurusan(): bool
    {
        return $this->role === 'jurusan';
    }

    /**
     * Label representasi nama role dalam sistem.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'kepala_sekolah' => 'Kepala Sekolah',
            'sarpras' => 'Sarpras Pusat',
            'pembantu_sarpras' => 'Pembantu Sarpras',
            'jurusan' => 'Jurusan / Unit Kerja',
            default => ucfirst(str_replace('_', ' ', $this->role)),
        };
    }
}
