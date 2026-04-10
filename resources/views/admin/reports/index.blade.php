@extends('adminlte::page')

@section('title', 'Módulo de Reportes — Milagroz')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.65rem; letter-spacing: -0.5px;">
            <i class="fas fa-chart-bar mr-2" style="color: #6366f1; font-size: 1.3rem;"></i>
            Módulo de Reportes
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.875rem;">
            Acceda a información detallada y herramientas de exportación especializadas.
        </p>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-2 pb-5">

    <div class="row g-4">
        
        {{-- Card: Reporte General de Empleados --}}
        <div class="col-md-4">
            <a href="{{ route('admin.reportes.empleados') }}" class="report-launcher-card">
                <div class="report-card-icon" style="background:rgba(99,102,241,0.1); color:#6366f1;">
                    <i class="fas fa-users"></i>
                </div>
                <div class="report-card-body">
                    <h5 class="report-card-title">Directorio de Personal</h5>
                    <p class="report-card-text">Listado completo con filtros avanzados por sede, área y estado.</p>
                    <span class="report-card-link">Generar Reporte <i class="fas fa-arrow-right ml-1"></i></span>
                </div>
            </a>
        </div>

        {{-- Card: Historial Contractual (Próximamente o enlace actual) --}}
        <div class="col-md-4">
            <a href="{{ route('admin.reportes.empleados') }}?vencimientos=1" class="report-launcher-card">
                <div class="report-card-icon" style="background:rgba(16,185,129,0.1); color:#10b981;">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="report-card-body">
                    <h5 class="report-card-title">Seguimiento Contractual</h5>
                    <p class="report-card-text">Control de tipos de contrato, fechas de inicio y vencimientos próximos.</p>
                    <span class="report-card-link text-success">Ver Detalles <i class="fas fa-arrow-right ml-1"></i></span>
                </div>
            </a>
        </div>

        {{-- Card: Salud Ocupacional (SST) --}}
        <div class="col-md-4">
            <a href="{{ route('admin.reportes.empleados') }}" class="report-launcher-card">
                <div class="report-card-icon" style="background:rgba(239,68,68,0.1); color:#ef4444;">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <div class="report-card-body">
                    <h5 class="report-card-title">Salud Ocupacional (SST)</h5>
                    <p class="report-card-text">Reporte detallado de exámenes médicos y registros de seguridad laboral.</p>
                    <span class="report-card-link text-danger">Acceder <i class="fas fa-arrow-right ml-1"></i></span>
                </div>
            </a>
        </div>

    </div>

    {{-- Nota informativa --}}
    <div class="mt-5 p-4 bg-white border-left border-info shadow-sm" style="border-radius: 8px;">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle text-info mr-3 fa-2x"></i>
            <div>
                <h6 class="font-weight-bold mb-1">Análisis de Datos Avanzado</h6>
                <p class="mb-0 text-muted small text-justify">
                    Este módulo está diseñado para la extracción técnica de información. Si busca un resumen visual rápido (gráficas y totales generales), puede consultarlo directamente en el <strong>Panel de Control</strong> principal.
                </p>
            </div>
        </div>
    </div>

</div>

@stop

@section('css')
<style>
    .report-launcher-card {
        display: block;
        background: #fff;
        border-radius: 12px;
        padding: 1.75rem;
        text-decoration: none !important;
        transition: all 0.25s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        border: 1px solid transparent;
        height: 100%;
    }
    .report-launcher-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        border-color: rgba(99,102,241,0.2);
    }
    .report-card-icon {
        width: 50px; height: 50px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        margin-bottom: 1.25rem;
    }
    .report-card-title {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }
    .report-card-text {
        font-size: 0.875rem;
        color: #64748b;
        margin-bottom: 1.5rem;
    }
    .report-card-link {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@stop
