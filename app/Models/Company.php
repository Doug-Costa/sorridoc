<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Company extends Model
{
    use \App\Traits\AuditLogTrait, HasFactory;

    protected $fillable = [
        'corporate_name',
        'fantasy_name',
        'cnpj',
        'ie',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'responsible_name',
        'responsible_role',
        'registration_token',
        'token_expires_at',
        'status',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
    ];

    protected $hidden = [
        'registration_token',
    ];

    public function workers(): HasMany
    {
        return $this->hasMany(Worker::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function generateRegistrationToken(int $expiresDays = 30): string
    {
        $token = Str::random(64);
        $this->update([
            'registration_token' => hash('sha256', $token),
            'token_expires_at' => now()->addDays($expiresDays),
        ]);

        return $token;
    }

    public function isTokenValid(string $token): bool
    {
        if (! $this->registration_token || ! $this->token_expires_at) {
            return false;
        }

        return hash_equals($this->registration_token, hash('sha256', $token))
            && $this->token_expires_at->isFuture();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Ativo');
    }
}
