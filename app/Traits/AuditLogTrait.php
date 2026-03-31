<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait AuditLogTrait
{
    public static function bootAuditLogTrait()
    {
        static::created(function ($model) {
            self::logAction($model, 'Created');
        });

        static::updated(function ($model) {
            self::logAction($model, 'Updated');
        });

        static::deleted(function ($model) {
            self::logAction($model, 'Deleted');
        });
    }

    protected static function logAction($model, $action)
    {
        $payload = clone $model;
        if ($action === 'Updated') {
            $payload = [
                'old' => $model->getOriginal(),
                'new' => $model->getChanges(),
            ];
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action . ' ' . class_basename($model),
            'description' => "Record {$model->id} was {$action}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'payload' => $payload,
        ]);
    }
}
