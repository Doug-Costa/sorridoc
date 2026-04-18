<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use Illuminate\Support\Str;

class Worker extends Model
{
    use \App\Traits\AuditLogTrait, HasFactory;

    protected $fillable = [
        'company_id',
        'cpf',
        'name',
        'birth_date',
        'role',
        'department',
        'email',
        'phone',
        'gender',
        'access_token',
        'token_expires_at',
        'last_access_at',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'token_expires_at' => 'datetime',
        'last_access_at' => 'datetime',
    ];

    protected $hidden = [
        'access_token',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(WorkerDocument::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class);
    }


    public function generateAccessToken(int $expiresDays = 365): string
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

    public function getMaskedCpfAttribute(): string
    {
        return substr($this->cpf, 0, 3).'.***.***-'.substr($this->cpf, -2);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Ativo');
    }

    public function scopeByCpf($query, string $cpf)
    {
        return $query->where('cpf', $this->formatCpf($cpf));
    }

    public static function formatCpf(string $cpf): string
    {
        return preg_replace('/[^0-9]/', '', $cpf);
    }
}
