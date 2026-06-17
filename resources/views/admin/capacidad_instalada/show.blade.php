@extends('adminlte::page')

@section('title', 'Detalle Capacidad Instalada')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">Detalle de Registro</h2>
        <p class="text-muted mb-0">Información completa de la capacidad instalada y utilizada.</p>
    </div>
    <a href="{{ route('admin.capacidad_instalada.index') }}" class="btn btn-light border px-4 shadow-sm" style="border-radius: 10px;">
        <i class="fas fa-arrow-left me-2"></i> Volver
    </a>
</div>
@stop

@section('content')
<div class="row justify-content-center pb-5">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4 px-md-5 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-chart-pie text-orange me-2"></i> Capacidad
                </h4>
                @if($capacidad->estado == 1)
                    <span class="badge bg-success px-3 py-2 rounded-pill" style="font-size: 0.9rem;">Activo</span>
                @else
                    <span class="badge bg-secondary px-3 py-2 rounded-pill" style="font-size: 0.9rem;">Inactivo</span>
                @endif
            </div>

            <div class="card-body p-4 p-md-5">
                
                {{-- EMPLEADO INFO --}}
                <div class="d-flex align-items-center mb-5 p-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width: 60px; height: 60px; background-color: rgba(255,106,0,0.1); color: #ff6a00;">
                        <span class="fw-bold" style="font-size: 1.5rem;">
                            {{ strtoupper(substr($capacidad->empleado->persona->nombres ?? 'U', 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            {{ $capacidad->empleado->persona->nombres ?? '' }} {{ $capacidad->empleado->persona->apellidos ?? '' }}
                        </h5>
                        <p class="text-muted mb-0">
                            <i class="far fa-id-card me-1"></i> {{ $capacidad->empleado->persona->numero_documento ?? 'N/A' }} | 
                            <i class="fas fa-briefcase mx-1"></i> {{ $capacidad->empleado->cargo ?? 'Sin Cargo' }}
                        </p>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    {{-- PROCESO --}}
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Proceso</label>
                        <div class="fs-5 text-dark fw-medium">{{ $capacidad->proceso ?? 'N/A' }}</div>
                    </div>

                    {{-- FECHA --}}
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Fecha de Registro</label>
                        <div class="fs-5 text-dark fw-medium">{{ \Carbon\Carbon::parse($capacidad->fecha)->format('d \d\e F, Y') }}</div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    {{-- DISPONIBLE --}}
                    <div class="col-md-6">
                        <div class="p-3 rounded text-center" style="background-color: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.2);">
                            <label class="text-primary small fw-bold text-uppercase d-block mb-1" style="letter-spacing: 0.5px;">Capacidad Disponible</label>
                            <span class="fs-3 fw-bold text-primary">{{ $capacidad->capacidad_disponible ?? 0 }}</span>
                        </div>
                    </div>

                    {{-- UTILIZADA --}}
                    <div class="col-md-6">
                        <div class="p-3 rounded text-center" style="background-color: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.2);">
                            <label class="text-success small fw-bold text-uppercase d-block mb-1" style="letter-spacing: 0.5px;">Capacidad Utilizada</label>
                            <span class="fs-3 fw-bold text-success">{{ $capacidad->capacidad_utilizada ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                {{-- OBSERVACIONES --}}
                @if($capacidad->observaciones)
                <div class="mt-5">
                    <label class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Observaciones</label>
                    <div class="p-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                        <p class="mb-0 text-dark">{{ $capacidad->observaciones }}</p>
                    </div>
                </div>
                @endif

                <div class="mt-5 text-end">
                    @if($capacidad->estado == 1)
                        <a href="{{ route('admin.capacidad_instalada.edit', $capacidad->id) }}" class="btn btn-orange px-4 py-2" style="border-radius: 10px;">
                            <i class="fas fa-pen me-2"></i> Editar Registro
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
.text-orange { color: #ff6a00 !important; }
.btn-orange {
    background-color: #ff6a00;
    border: none;
    color: #fff;
    transition: all 0.3s ease;
}
.btn-orange:hover {
    background-color: #e65c00;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255,106,0,0.3);
}
</style>
@endsection
