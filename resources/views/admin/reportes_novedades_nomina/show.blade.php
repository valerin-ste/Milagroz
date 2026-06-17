@extends('adminlte::page')

@section('title', 'Detalle Novedad Nómina')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">Detalle de Registro</h2>
        <p class="text-muted mb-0">Información completa de la novedad reportada.</p>
    </div>
    <a href="{{ route('admin.reportes-novedades-nomina.index') }}" class="btn btn-light border px-4 shadow-sm" style="border-radius: 10px;">
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
                    <i class="fas fa-file-invoice-dollar text-success me-2"></i> Novedad
                </h4>
                @if($reporte->estado == 1)
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
                            {{ strtoupper(substr($reporte->empleado->persona->nombres ?? 'U', 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            {{ $reporte->empleado->persona->nombres ?? '' }} {{ $reporte->empleado->persona->apellidos ?? '' }}
                        </h5>
                        <p class="text-muted mb-0">
                            <i class="far fa-id-card me-1"></i> {{ $reporte->empleado->persona->numero_documento ?? 'N/A' }} | 
                            <i class="fas fa-briefcase mx-1"></i> {{ $reporte->empleado->cargo ?? 'Sin Cargo' }}
                        </p>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    {{-- TIPO NOVEDAD --}}
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Tipo de Novedad</label>
                        <div class="fs-5 text-dark fw-medium">{{ $reporte->tipo_novedad }}</div>
                    </div>

                    {{-- FECHA --}}
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Fecha de la Novedad</label>
                        <div class="fs-5 text-dark fw-medium">{{ \Carbon\Carbon::parse($reporte->fecha)->format('d \d\e F, Y') }}</div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    {{-- CANTIDAD --}}
                    <div class="col-md-6">
                        <div class="p-3 rounded text-center" style="background-color: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.2);">
                            <label class="text-primary small fw-bold text-uppercase d-block mb-1" style="letter-spacing: 0.5px;">Cantidad</label>
                            <span class="fs-3 fw-bold text-primary">{{ $reporte->cantidad }}</span>
                        </div>
                    </div>

                    {{-- ARCHIVO --}}
                    <div class="col-md-6">
                        <div class="p-3 rounded text-center h-100 d-flex flex-column justify-content-center" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-2" style="letter-spacing: 0.5px;">Archivo de Soporte</label>
                            @if($reporte->archivo)
                                <div>
                                    <a href="{{ route('admin.reportes-novedades-nomina.archivo.download', $reporte->id) }}" class="btn btn-outline-primary btn-sm mb-2 w-100" style="border-radius: 8px;">
                                        <i class="fas fa-download me-1"></i> Descargar
                                    </a>
                                    <a href="{{ route('admin.reportes-novedades-nomina.archivo.view', $reporte->id) }}" target="_blank" class="btn btn-primary btn-sm w-100" style="border-radius: 8px;">
                                        <i class="fas fa-eye me-1"></i> Ver Online
                                    </a>
                                </div>
                            @else
                                <span class="text-muted"><i class="fas fa-times-circle"></i> No adjunto</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- OBSERVACIONES --}}
                @if($reporte->observaciones)
                <div class="mt-4">
                    <label class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Observaciones</label>
                    <div class="p-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                        <p class="mb-0 text-dark">{{ $reporte->observaciones }}</p>
                    </div>
                </div>
                @endif

                <div class="mt-5 text-end">
                    @if($reporte->estado == 1)
                        <a href="{{ route('admin.reportes-novedades-nomina.edit', $reporte->id) }}" class="btn btn-orange px-4 py-2" style="border-radius: 10px;">
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
