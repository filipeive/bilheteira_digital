<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    const ROLES = [
        'super_admin' => 'Super Admin',
        'admin'       => 'Administrador',
        'organizer'   => 'Organizador',
        'operator'    => 'Operador',
        'scanner'     => 'Scanner',
        'seller'      => 'Vendedor',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'is_active' => 'boolean',
        ];
    }

    // ── Role checks (existing) ──────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    public function isOrganizer(): bool
    {
        return $this->role === 'organizer';
    }

    // ── Role checks (new) ───────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isScanner(): bool
    {
        return $this->role === 'scanner';
    }

    public function isSeller(): bool
    {
        return $this->role === 'seller';
    }

    public function canAccessAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'organizer']);
    }

    public function canValidate(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'operator', 'scanner']);
    }

    public function canSell(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'organizer', 'seller']);
    }

    // ── Relations ────────────────────────────────────────────

    public function scannedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'scanned_by');
    }

    // ── Accessors ────────────────────────────────────────────

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=D4A017&color=0D0B07&length=2';
    }
}
