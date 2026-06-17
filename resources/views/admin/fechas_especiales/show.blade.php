@extends('adminlte::page')

@section('title', 'Detalle de Fecha Especial')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">
            Detalle de Fecha Especial
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">
            Visualización completa de la fecha especial registrada.
        </p>
    </div>

    <a href="{{ route('admin.fechas_especiales.index') }}" class="btn btn-light border px-4">
        <i class="fas fa-arrow-left me-2"></i> Volver al listado
    </a>
</div>
@stop

@section('content')
<div class="container-fluid px-2">
    <div class="row">
        <div class="col-lg-7 mx-auto">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="fw-bold text-orange">
                            <i class="fas fa-calendar-star me-2"></i>
                            Registro de Evento
                        </h5>
                        <span class="badge {{ $fechaEspecial->estado == 1 ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} rounded-pill px-3 py-2 fw-bold">
                            {{ $fechaEspecial->estado == 1 ? 'ACTIVO' : 'INACTIVO' }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="mb-4 pb-3 border-bottom">
                        <label class="text-muted small fw-bold text-uppercase d-block mb-2">Empleado</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px; font-size: 1.2rem;">
                                {{ strtoupper(substr($fechaEspecial->empleado->persona->nombres, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 1.1rem;">{{ $fechaEspecial->empleado->persona->nombres }} {{ $fechaEspecial->empleado->persona->apellidos }}</div>
                                <div class="text-muted small">Cédula: {{ $fechaEspecial->empleado->persona->numero_documento }} &bull; Cargo: {{ $fechaEspecial->empleado->cargo }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Tipo de Evento</label>
                            <span class="badge bg-soft-info text-info rounded-pill px-3 py-1 fw-bold">{{ $fechaEspecial->tipo }}</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Fecha Programada</label>
                            <div class="fw-bold text-dark"><i class="far fa-calendar-alt me-1 text-muted"></i> {{ $fechaEspecial->fecha->format('d/m/Y') }}</div>
                        </div>
                    </div>

                    @if($fechaEspecial->mensaje)
                        <div class="mb-4">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Mensaje / Descripción</label>
                            <div class="p-3 bg-light rounded-3 text-dark" style="border-left: 4px solid #ff6a00; font-style: italic;">
                                "{{ $fechaEspecial->mensaje }}"
                            </div>
                        </div>
                    @endif

                    @if($fechaEspecial->archivo)
                        <div class="p-3 rounded-3 border" style="background-color: #f8fafc;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                    <div>
                                        <div class="fw-bold text-dark small">Documento de Soporte</div>
                                        <div class="text-muted small">
                                            <i class="fas fa-file me-1"></i>

                                            @if($fechaEspecial->archivo)
                                                @php
                                                    $nombre = basename($fechaEspecial->archivo);
                                                    $pos = strpos($nombre, '_');
                                                    $nombreLimpio = $pos !== false ? substr($nombre, $pos + 1) : $nombre;
                                                @endphp

                                                {{ $nombreLimpio }}
                                            @else
                                                <span class="text-muted">Sin archivo</span>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ asset('storage/' . $fechaEspecial->archivo) }}" target="_blank" class="btn btn-white btn-sm border shadow-sm px-3">
                                        <i class="fas fa-eye me-1"></i> Ver
                                    </a>
                                    <a href="{{ asset('storage/' . $fechaEspecial->archivo) }}" download class="btn btn-primary-modern btn-sm px-3 text-white">
                                        <i class="fas fa-download me-1"></i> Descargar
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="card-footer bg-light border-0 text-end px-4 py-3">
                    <a href="{{ route('admin.fechas_especiales.edit', $fechaEspecial->id) }}" class="btn btn-orange text-white fw-bold px-4">
                        <i class="fas fa-pen me-2"></i> Editar Registro
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
.text-orange { color: #ff6a00 !important; }
.btn-orange { background-color: #ff6a00; border: none; }
.btn-orange:hover { background-color: #e65c00; }
.btn-primary-modern { background-color: #6366f1; border: none; }
.btn-primary-modern:hover { background-color: #4f46e5; }
.bg-soft-success { background-color: rgba(16, 185, 129, 0.1); }
.bg-soft-danger { background-color: rgba(239, 68, 68, 0.1); }
.bg-soft-info { background-color: rgba(6, 182, 212, 0.1); }
.bg-soft-primary { background-color: rgba(99, 102, 241, 0.1); }
.text-success { color: #10b981 !important; }
.text-danger { color: #ef4444 !important; }
.text-info { color: #06b6d4 !important; }
.text-primary { color: #6366f1 !important; }
.rounded-4 { border-radius: 1rem !important; }
.btn-white { background: white; }
</style>
@endsection
