@extends('adminlte::page')

@section('title', 'Detalle - Planta Personal SENA')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="font-size:1.75rem;">Detalle de Registro</h2>
        <p class="text-muted mb-0">Información completa del registro de Planta Personal SENA.</p>
    </div>
    <a href="{{ route('admin.planta_personal_sena.index') }}" class="btn btn-light border px-4 shadow-sm" style="border-radius:10px;">
        <i class="fas fa-arrow-left me-2"></i> Volver
    </a>
</div>
@stop

@section('content')
<div class="row justify-content-center pb-5">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm" style="border-radius:20px;">

            <div class="card-header bg-white border-0 pt-4 pb-0 px-4 px-md-5 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-users text-purple me-2"></i> Planta Personal SENA
                </h5>
                @if($registro->estado == 1)
                    <span class="badge bg-success px-3 py-2 rounded-pill">Activo</span>
                @else
                    <span class="badge bg-secondary px-3 py-2 rounded-pill">Inactivo</span>
                @endif
            </div>

            <div class="card-body p-4 p-md-5">

                {{-- EMPLEADO --}}
                <div class="d-flex align-items-center mb-5 p-3 rounded"
                     style="background-color:#f8fafc; border:1px solid #e2e8f0;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width:60px; height:60px; background-color:rgba(255,106,0,.1); color:#ff6a00; flex-shrink:0;">
                        <span class="fw-bold" style="font-size:1.5rem;">
                            {{ strtoupper(substr($registro->empleado->persona->nombres ?? 'U', 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            {{ $registro->empleado->persona->nombres ?? '' }}
                            {{ $registro->empleado->persona->apellidos ?? '' }}
                        </h5>
                        <p class="text-muted mb-0" style="font-size:.9rem;">
                            <i class="far fa-id-card me-1"></i>
                            {{ $registro->empleado->persona->numero_documento ?? 'N/A' }}
                            &nbsp;|&nbsp;
                            <i class="fas fa-briefcase me-1"></i>
                            {{ $registro->empleado->cargo ?? 'Sin Cargo' }}
                        </p>
                    </div>
                </div>

                {{-- FECHA --}}
                <div class="mb-4">
                    <label class="text-muted small fw-bold text-uppercase d-block mb-1" style="letter-spacing:.5px;">
                        Fecha de Reporte
                    </label>
                    <div class="fs-5 text-dark fw-medium">
                        {{ \Carbon\Carbon::parse($registro->fecha_reporte)->format('d \d\e F, Y') }}
                    </div>
                </div>

                {{-- OBSERVACIONES --}}
                <div class="mb-4">
                    <label class="text-muted small fw-bold text-uppercase d-block mb-1" style="letter-spacing:.5px;">
                        Observaciones
                    </label>
                    @if($registro->observaciones)
                        <div class="p-3 rounded" style="background-color:#f8fafc; border:1px solid #e2e8f0;">
                            <p class="mb-0 text-dark">{{ $registro->observaciones }}</p>
                        </div>
                    @else
                        <p class="text-muted fst-italic mb-0">Sin observaciones registradas.</p>
                    @endif
                </div>

                {{-- FECHAS SISTEMA --}}
                <div class="row g-3 mt-2 text-muted" style="font-size:.83rem;">
                    <div class="col-md-6">
                        <i class="far fa-clock me-1"></i>
                        Creado: {{ $registro->created_at ? $registro->created_at->format('d/m/Y H:i') : '—' }}
                    </div>
                    <div class="col-md-6">
                        <i class="fas fa-pen me-1"></i>
                        Actualizado: {{ $registro->updated_at ? $registro->updated_at->format('d/m/Y H:i') : '—' }}
                    </div>
                </div>

                @if($registro->estado == 1)
                <div class="mt-5 text-end">
                    <a href="{{ route('admin.planta_personal_sena.edit', $registro->id) }}"
                       class="btn btn-orange px-4 py-2" style="border-radius:10px;">
                        <i class="fas fa-pen me-2"></i> Editar Registro
                    </a>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
.text-purple { color:#7c3aed !important; }
.btn-orange { background-color:#ff6a00; border:none; color:#fff; transition:all .3s; }
.btn-orange:hover { background-color:#e65c00; color:#fff; transform:translateY(-2px); box-shadow:0 5px 15px rgba(255,106,0,.3); }
</style>
@endsection
