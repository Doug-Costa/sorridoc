<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalFlow extends Model
{
    use HasFactory, \App\Traits\AuditLogTrait;

    protected $fillable = [
        'approval_id',
        'step_name',
        'status',
        'comment',
        'action_type',
        'assigned_to',
        'approved_at',
        'rejection_reason',
        'signature_hash',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function approval(): BelongsTo
    {
        return $this->belongsTo(Approval::class, 'approval_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
