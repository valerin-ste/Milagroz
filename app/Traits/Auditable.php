<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            $model->logAction('create', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $oldValues = $model->getOriginal();
            $newValues = $model->getDirty();
            
            // Only log if something changed
            if (!empty($newValues)) {
                $model->logAction('update', $oldValues, $newValues);
            }
        });

        static::deleted(function ($model) {
            $model->logAction('delete', $model->getAttributes(), null);
        });
    }

    protected function logAction($action, $oldValues, $newValues)
    {
        // Don't log sensitive fields
        $sensitiveFields = ['password', 'remember_token'];
        if ($oldValues) {
            foreach ($sensitiveFields as $field) {
                if (isset($oldValues[$field])) unset($oldValues[$field]);
            }
        }
        if ($newValues) {
            foreach ($sensitiveFields as $field) {
                if (isset($newValues[$field])) unset($newValues[$field]);
            }
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => strtolower(class_basename($this)),
            'record_id' => $this->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
