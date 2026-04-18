<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLES = [
        'Super Admin' => 'Super Admin',
        'Advogado' => 'Advogado',
        'Diretor' => 'Diretor',
        'Operacional' => 'Operacional',
        'Gestor RH' => 'Gestor RH',
        'Empresa' => 'Empresa',
        'Funcionario' => 'Funcionario',
    ];


    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'unit',
        '2fa_secret',
        'pin_code',
        'company_id',
        'worker_id',
        'can_manage_portal',
        'access_token',

        'token_expires_at',
        'last_access_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'pin_code',
        'access_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'pin_code' => 'hashed',
            'token_expires_at' => 'datetime',
            'last_access_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }


    public function isSuperAdmin(): bool
    {
        return $this->role === 'Super Admin';
    }

    public function hasPortalManagementAccess(): bool
    {
        return $this->isSuperAdmin() || $this->can_manage_portal;
    }


    public function isGestorRH(): bool
    {
        return $this->role === 'Gestor RH';
    }

    public function canAccessCompany(Company $company): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->isGestorRH() && $this->company_id === $company->id) {
            return true;
        }

        return false;
    }

    public function generateAccessToken(int $expiresDays = 30): string
    {
        $token = Str::random(64);
        $this->update([
            'access_token' => hash('sha256', $token),
            'token_expires_at' => now()->addDays($expiresDays),
        ]);

        return $token;
    }

    public function isTokenValid(string $token): bool
    {
        if (! $this->access_token || ! $this->token_expires_at) {
            return false;
        }

        return hash_equals($this->access_token, hash('sha256', $token))
            && $this->token_expires_at->isFuture();
    }

    public function recordAccess(): void
    {
        $this->update(['last_access_at' => now()]);
    }

    /**
     * Scope a query to only include internal operational users.
     */
    public function scopeInternal(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereNotIn('role', ['Empresa', 'Funcionario']);
    }
}
