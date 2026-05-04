<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-auditoria');
    }

    public function index(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Filtros
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->module) {
            $query->where('module', $request->module);
        }
        if ($request->action) {
            $query->where('action', $request->action);
        }
        if ($request->fecha_desde) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->fecha_hasta) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $logs = $query->paginate(20);
        $users = User::orderBy('name')->pluck('name', 'id');
        $modules = AuditLog::distinct()->pluck('module');
        $actions = AuditLog::distinct()->pluck('action');

        return view('admin.audit.index', compact('logs', 'users', 'modules', 'actions'));
    }

    public function show(AuditLog $audit)
    {
        $audit->load('user');
        return view('admin.audit.show', ['auditLog' => $audit]);
    }
}
