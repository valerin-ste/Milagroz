<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role as SystemRole;

class SystemRoleObserver
{
    public function created(SystemRole $role)
    {
        $this->log($role, 'create', null, $role->getAttributes());
    }

    public function updated(SystemRole $role)
    {
        $old = $role->getOriginal();
        $new = $role->getDirty();
        if (!empty($new)) {
            $this->log($role, 'update', $old, $new);
        }
    }

    public function deleted(SystemRole $role)
    {
        $this->log($role, 'delete', $role->getAttributes(), null);
    }

    protected function log($role, $action, $old, $new)
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => 'system_role',
            'record_id' => $role->id,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
