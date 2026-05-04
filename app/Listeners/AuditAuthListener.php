<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Http\Request;

class AuditAuthListener
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(object $event): void
    {
        $action = '';
        $user_id = null;
        $new_values = null;

        if ($event instanceof Login) {
            $action = 'login';
            $user_id = $event->user->id;
        } elseif ($event instanceof Logout) {
            $action = 'logout';
            $user_id = $event->user->id;
        } elseif ($event instanceof PasswordReset) {
            $action = 'password_reset';
            $user_id = $event->user->id;
        } elseif ($event instanceof Failed) {
            $action = 'login_failed';
            $user_id = $event->user ? $event->user->id : null;
            $new_values = [
                'email_attempted' => $event->credentials['email'] ?? 'unknown',
                'reason' => $event->user ? ($event->user->estado == 0 ? 'account_inactive' : 'wrong_password') : 'user_not_found'
            ];
        }

        if ($action) {
            AuditLog::create([
                'user_id' => $user_id,
                'action' => $action,
                'module' => 'auth',
                'record_id' => $user_id,
                'new_values' => $new_values,
                'ip_address' => $this->request->ip(),
                'user_agent' => $this->request->userAgent(),
                'created_at' => now(),
            ]);
        }
    }
}
