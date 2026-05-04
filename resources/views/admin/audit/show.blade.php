@extends('adminlte::page')

@section('title', 'Detalle de Auditoría')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">
            Detalle de Auditoría
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Registro ID #{{ $auditLog->id }}</p>
    </div>
    <a href="{{ route('admin.audit.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">
        <i class="fas fa-arrow-left me-2"></i> Volver
    </a>
</div>
@stop

@section('content')
<div class="container-fluid px-2 pb-5">
    <div class="row g-4">
        
        <!-- Metadata -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Información General</h5>
                </div>
                <div class="card-body px-4">
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Usuario</label>
                        <p class="mb-0 fw-bold">{{ $auditLog->user->name ?? 'Sistema' }}</p>
                        <small class="text-muted">{{ $auditLog->user->email ?? '' }}</small>
                    </div>
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Acción</label>
                        <span class="badge bg-primary px-3 rounded-pill">{{ $auditLog->human_action }}</span>
                    </div>
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Módulo y Registro</label>
                        <p class="mb-0 text-uppercase fw-bold text-primary">{{ $auditLog->human_module }} #{{ $auditLog->record_id }}</p>
                    </div>
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Fecha y Hora</label>
                        <p class="mb-0">{{ $auditLog->created_at ? $auditLog->created_at->format('d/m/Y H:i:s') : 'N/A' }}</p>
                    </div>
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Metadatos Técnicos</label>
                        <p class="mb-1 small"><strong>IP:</strong> {{ $auditLog->ip_address }}</p>
                        <p class="small text-muted mb-0" title="{{ $auditLog->user_agent }}">
                            <strong>Dispositivo:</strong> {{ $auditLog->friendly_user_agent }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Valores -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Cambios Realizados</h5>
                </div>
                <div class="card-body px-4">
                    @if($auditLog->action == 'create')
                        <div class="alert alert-success border-0 mb-4" style="border-radius: 10px;">
                            <i class="fas fa-plus-circle me-2"></i> Registro creado con los siguientes valores iniciales.
                        </div>
                    @elseif($auditLog->action == 'delete')
                        <div class="alert alert-danger border-0 mb-4" style="border-radius: 10px;">
                            <i class="fas fa-trash-alt me-2"></i> Registro eliminado. Se muestran los valores que tenía antes de borrar.
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted text-uppercase small mb-3">Valores Anteriores</h6>
                            @if($auditLog->old_values)
                                <div class="bg-light p-3 rounded" style="font-family: monospace; font-size: 13px;">
                                    @foreach($auditLog->old_values as $key => $val)
                                        <div class="mb-1"><strong>{{ $key }}:</strong> {{ is_array($val) ? json_encode($val) : $val }}</div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted italic small">Sin valores anteriores.</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted text-uppercase small mb-3">Valores Nuevos / Actuales</h6>
                            @if($auditLog->new_values)
                                <div class="bg-light p-3 rounded" style="font-family: monospace; font-size: 13px; border-left: 4px solid #13b6ec;">
                                    @foreach($auditLog->new_values as $key => $val)
                                        <div class="mb-1"><strong>{{ $key }}:</strong> {{ is_array($val) ? json_encode($val) : $val }}</div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted italic small">Sin valores nuevos.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@stop
