<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory, AuditLogTrait;

    protected $table = 'approvals';

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'category',
        'sensitivity_level',
        'status',
        'hash_sha256',
        'version_number',
        'owner_id',
        'deadline_at',
        'flow_type',
        'assigned_to',
    ];

    protected static function booted()
    {
        static::creating(function ($approval) {
            if ($approval->file_path && file_exists(storage_path('app/private/' . $approval->file_path))) {
                $approval->hash_sha256 = hash_file('sha256', storage_path('app/private/' . $approval->file_path));
            } else {
                // Para acordos sem arquivo, gera hash baseado no conteúdo e tempo para garantir unicidade e integridade
                $approval->hash_sha256 = hash('sha256', $approval->title . $approval->description . microtime());
            }
            $approval->version_number = 1;
        });

        static::created(function ($approval) {
            if ($approval->flow_type === 'Múltipla' && request()->has('multiple_assignees')) {
                $assignees = request()->input('multiple_assignees', []);
                if (is_array($assignees)) {
                    foreach ($assignees as $userId) {
                        ApprovalAssignee::create([
                            'approval_id' => $approval->id,
                            'user_id' => $userId,
                            'status' => 'Pendente',
                        ]);
                    }
                }
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function approvalFlows()
    {
        return $this->hasMany(ApprovalFlow::class, 'approval_id');
    }

    public function assignees()
    {
        return $this->hasMany(ApprovalAssignee::class, 'approval_id');
    }

    public function approvedAssignees()
    {
        return $this->assignees()->where('status', 'Aprovado');
    }

    public function rejectedAssignees()
    {
        return $this->assignees()->where('status', 'Rejeitado');
    }

    public function pendingAssignees()
    {
        return $this->assignees()->where('status', 'Pendente');
    }

    public function isMultipleApproval(): bool
    {
        return $this->flow_type === 'Múltipla';
    }

    public function getApprovalProgress(): array
    {
        if (!$this->isMultipleApproval()) {
            return ['current' => 0, 'total' => 0, 'percentage' => 0];
        }

        $total = $this->assignees()->count();
        $approved = $this->approvedAssignees()->count();
        $rejected = $this->rejectedAssignees()->count();
        
        return [
            'total' => $total,
            'approved' => $approved,
            'rejected' => $rejected,
            'pending' => $total - $approved - $rejected,
            'percentage' => $total > 0 ? round(($approved / $total) * 100, 0) : 0
        ];
    }
}
