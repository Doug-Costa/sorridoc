<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkerDocument extends Model
{
    use \App\Traits\AuditLogTrait, HasFactory;

    protected static function boot()

    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->uploaded_by && auth()->check()) {
                $model->uploaded_by = auth()->id();
            }
        });
    }


    protected $fillable = [
        'worker_id',
        'uploaded_by',
        'type',
        'title',
        'description',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
        'hash_sha256',
        'issued_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'file_size' => 'integer',
    ];

    protected $appends = ['is_expired'];

    public const TYPES = [
        'ASO' => 'Atestado de Saúde Ocupacional',
        'PCMSO' => 'Programa de PCMSO',
        'PPP' => 'Perfil Profissiográfico Previden.',
        'CAT' => 'Comunicação de Acidente Trab.',
        'LTCAT' => 'Laudo Téc. Cond. Amb. Trabalho',
        'PGR' => 'Programa de Gerenciamento Riscos',
        'Exame' => 'Exame Clínico',
        'Laudo' => 'Laudo Médico',
        'Receita' => 'Receita Médica',
        'Atestado' => 'Atestado Médico',
        'Outro' => 'Outro Documento',
    ];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getIsExpiredAttribute(): bool
    {
        if (! $this->expires_at) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_expired) {
            return 'Vencido';
        }

        return $this->status;
    }

    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
