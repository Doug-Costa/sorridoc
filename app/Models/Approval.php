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
}
