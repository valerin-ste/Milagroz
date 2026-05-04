<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false; // We use created_at manually via DB default

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'record_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Parse User Agent to a human-friendly version
     */
    public function getFriendlyUserAgentAttribute()
    {
        $ua = $this->user_agent;
        if (empty($ua)) return 'N/A';

        // Detect OS
        $os = 'Otro';
        if (preg_match('/windows|win32/i', $ua)) $os = 'Windows';
        elseif (preg_match('/macintosh|mac os x/i', $ua)) $os = 'Mac';
        elseif (preg_match('/linux/i', $ua)) $os = 'Linux';
        elseif (preg_match('/iphone|ipad|ipod/i', $ua)) $os = 'iOS';
        elseif (preg_match('/android/i', $ua)) $os = 'Android';

        // Detect Browser
        $browser = 'Otro';
        if (preg_match('/chrome/i', $ua) && !preg_match('/edge/i', $ua) && !preg_match('/opr|opera/i', $ua)) $browser = 'Chrome';
        elseif (preg_match('/safari/i', $ua) && !preg_match('/chrome/i', $ua)) $browser = 'Safari';
        elseif (preg_match('/firefox/i', $ua)) $browser = 'Firefox';
        elseif (preg_match('/edge/i', $ua)) $browser = 'Edge';
        elseif (preg_match('/opr|opera/i', $ua)) $browser = 'Opera';
        elseif (preg_match('/msie|trident/i', $ua)) $browser = 'Internet Explorer';

        return "{$browser} - {$os}";
    }

    /**
     * Get a human-readable version of the action
     */
    public function getHumanActionAttribute()
    {
        $actions = [
            'create'          => 'Creó registro',
            'update'          => 'Editó registro',
            'edit'            => 'Editó registro',
            'delete'          => 'Eliminó registro',
            'login'           => 'Inició sesión',
            'logout'          => 'Cerró sesión',
            'login_failed'    => 'Intento fallido de acceso',
            'assign_role'     => 'Asignó rol',
            'revoke_role'     => 'Removió rol',
            'password_reset'  => 'Restableció contraseña',
            'activate'        => 'Activó registro',
            'deactivate'      => 'Desactivó registro',
            'toggle_status'   => 'Cambió estado',
            'file_upload'     => 'Subió archivo',
            'file_delete'     => 'Eliminó archivo',
            'system_update'   => 'Actualización de sistema',
        ];

        return $actions[strtolower($this->action)] ?? ucfirst(str_replace('_', ' ', $this->action));
    }

    /**
     * Get a human-readable version of the module
     */
    public function getHumanModuleAttribute()
    {
        $modules = [
            'users'           => 'Usuarios del Sistema',
            'system_roles'    => 'Roles del Sistema',
            'empleados'       => 'Gestión de Empleados',
            'auth'            => 'Autenticación',
            'roles'           => 'Perfiles de Cargo',
            'sedes'           => 'Sedes',
            'areas'           => 'Áreas',
            'audit'           => 'Auditoría',
            'formaciones'     => 'Formaciones',
            'contratos'       => 'Contratos',
            'sst'             => 'Seguridad y Salud',
            'comunicaciones'  => 'Comunicaciones',
            'solicitudes'     => 'Solicitudes',
            'evaluaciones'    => 'Evaluaciones',
        ];

        return $modules[strtolower($this->module)] ?? ucfirst(str_replace('_', ' ', $this->module));
    }
}
