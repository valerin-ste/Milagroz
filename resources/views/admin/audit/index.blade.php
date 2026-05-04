@extends('adminlte::page')

@section('title', 'Auditoría del Sistema')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">
            Auditoría del Sistema
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Registro de trazabilidad y acciones críticas realizadas.</p>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.audit.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Usuario</label>
                    <select name="user_id" class="form-select select2">
                        <option value="">Todos los usuarios</option>
                        @foreach($users as $id => $name)
                            <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Módulo</label>
                    <select name="module" class="form-select">
                        <option value="">Todos</option>
                        @foreach($modules as $mod)
                            @php
                                $tempAudit = new \App\Models\AuditLog(['module' => $mod]);
                            @endphp
                            <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $tempAudit->human_module }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Acción</label>
                    <select name="action" class="form-select">
                        <option value="">Todas</option>
                        @foreach($actions as $act)
                            @php
                                $tempAudit = new \App\Models\AuditLog(['action' => $act]);
                            @endphp
                            <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ $tempAudit->human_action }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Fecha y Hora</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Módulo</th>
                            <th>Registro ID</th>
                            <th>IP</th>
                            <th class="text-end pe-4">Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold">{{ $log->created_at->format('d/m/Y') }}</span>
                                <br>
                                <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                @if($log->user)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2 bg-primary-soft text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px; font-weight: bold;">
                                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $log->user->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">Sistema</span>
                                @endif
                            </td>
                             <td>
                                @php
                                    $badgeClass = match($log->action) {
                                        'create' => 'bg-success',
                                        'update', 'edit' => 'bg-info',
                                        'delete' => 'bg-danger',
                                        'login' => 'bg-primary',
                                        'logout' => 'bg-secondary',
                                        'login_failed' => 'bg-warning text-dark',
                                        default => 'bg-dark'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} rounded-pill px-3">{{ $log->human_action }}</span>
                            </td>
                            <td>
                                <span class="text-uppercase small fw-bold text-muted">{{ $log->human_module }}</span>
                            </td>
                            <td>
                                <code class="text-primary fw-bold">#{{ $log->record_id }}</code>
                            </td>
                            <td>
                                <small class="text-muted">{{ $log->ip_address }}</small>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.audit.show', $log) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-eye me-1"></i> Ver
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <img src="{{ asset('images/no-results.png') }}" alt="Sin resultados" style="width: 120px; opacity: 0.5;">
                                <p class="text-muted mt-3">No se encontraron registros de auditoría.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $logs->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@stop

@push('css')
<style>
    .bg-primary-soft { background-color: rgba(19, 182, 236, 0.1); }
    .table thead th { font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; color: #64748b; border-top: none; }
    .table tbody td { border-color: #f1f5f9; padding-top: 1rem; padding-bottom: 1rem; }
    .form-select, .form-control { border-radius: 8px; border: 1px solid #e2e8f0; }
</style>
@endpush
