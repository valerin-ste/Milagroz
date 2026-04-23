@extends('adminlte::page')

@section('title', 'Panel de Control — Milagroz')

@section('content_header')
<div class="dash-welcome-header d-flex justify-content-between align-items-center mt-3 mb-4 px-1">
    <div>
        <h1 class="fw-bold mb-1 header-title">
            ¡Hola, {{ explode(' ', auth()->user()->name)[0] ?? 'Admin' }}! 👋
        </h1>
        <p class="header-subtitle text-muted mb-0">
            <i class="far fa-calendar-check me-1"></i>
            {{ \Carbon\Carbon::now()->translatedFormat('l, d \d\e F') }} — Resumen del estado actual.
        </p>
    </div>
    <div class="header-actions d-flex gap-2">
        @if($totalAlertasCriticas > 0)
        <div class="critical-indicator shadow-sm">
            <span class="pulse"></span>
            <i class="fas fa-exclamation-triangle me-1"></i> {{ $totalAlertasCriticas }} Alertas
        </div>
        @endif
        <a href="{{ route('admin.empleados.create') }}" class="btn btn-primary-modern shadow-sm">
            <i class="fas fa-user-plus me-1"></i> Nuevo Empleado
        </a>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-3 pb-5">

    {{-- ══════════════════════════════════════════════════════════════
         ALERTAS PRIOTIZADAS (GRID DINÁMICO)
         ══════════════════════════════════════════════════════════════ --}}
    @php
        $hayAlertas = $alertas['alertasSST']->count() > 0
                   || $alertas['alertasContratos']->count() > 0
                   || $alertas['alertasFormaciones']->count() > 0;
    @endphp

    @if($hayAlertas)
    <div class="row mb-5 g-4">
        {{-- 🔴 SST VENCIDOS --}}
        <div class="col-lg-4">
            <div class="alert-group-card h-100">
                <div class="group-header sst">
                    <div class="icon-box"><i class="fas fa-heartbeat"></i></div>
                    <div class="title-box">
                        <h6 class="fw-bold mb-0">SST Alertas</h6>
                        <small>Documentos y exámenes</small>
                    </div>
                </div>
                <div class="group-body">
                    @forelse($alertas['alertasSST'] as $item)
                        <a href="{{ $item->link }}" class="alert-list-item {{ $item->critico ? 'is-critical' : '' }}">
                            <div class="alert-marker"></div>
                            <div class="alert-content">
                                <span class="emp-name">{{ $item->empleado }}</span>
                                <span class="alert-desc">{{ $item->tipo }} <span class="alert-date">({{ $item->fecha }})</span></span>
                            </div>
                            <i class="fas fa-chevron-right alert-arrow"></i>
                        </a>
                    @empty
                        <div class="empty-alerts">
                            <i class="fas fa-check-circle me-2"></i> Sin alertas de SST
                        </div>
                    @endforelse
                </div>
                <div class="group-footer">
                    <a href="{{ route('admin.seguridad_salud_trabajo.index') }}" class="btn-view-all">
                        Ver todos los SST <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- 🟡 CONTRATOS POR VENCER --}}
        <div class="col-lg-4">
            <div class="alert-group-card h-100">
                <div class="group-header contracts">
                    <div class="icon-box"><i class="fas fa-file-contract"></i></div>
                    <div class="title-box">
                        <h6 class="fw-bold mb-0">Contratos</h6>
                        <small>Vencimientos próximos</small>
                    </div>
                </div>
                <div class="group-body">
                    @forelse($alertas['alertasContratos'] as $item)
                        <a href="{{ $item->link }}" class="alert-list-item {{ $item->critico ? 'is-critical' : '' }}">
                            <div class="alert-marker"></div>
                            <div class="alert-content">
                                <span class="emp-name">{{ $item->empleado }}</span>
                                <span class="alert-desc">{{ $item->tipo }} <span class="alert-date">({{ $item->fecha }})</span></span>
                            </div>
                            <i class="fas fa-chevron-right alert-arrow"></i>
                        </a>
                    @empty
                        <div class="empty-alerts">
                            <i class="fas fa-check-circle me-2"></i> Sin contratos por vencer
                        </div>
                    @endforelse
                </div>
                <div class="group-footer">
                    <a href="{{ route('admin.etapa_contractual.index') }}" class="btn-view-all">
                        Ver todos los Contratos <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- 🟠 FORMACIONES POR VENCER --}}
        <div class="col-lg-4">
            <div class="alert-group-card h-100">
                <div class="group-header training">
                    <div class="icon-box"><i class="fas fa-graduation-cap"></i></div>
                    <div class="title-box">
                        <h6 class="fw-bold mb-0">Formaciones</h6>
                        <small>Cursos y capacitaciones</small>
                    </div>
                </div>
                <div class="group-body">
                    @forelse($alertas['alertasFormaciones'] as $item)
                        <a href="{{ $item->link }}" class="alert-list-item {{ $item->critico ? 'is-critical' : '' }}">
                            <div class="alert-marker"></div>
                            <div class="alert-content">
                                <span class="emp-name">{{ $item->empleado }}</span>
                                <span class="alert-desc">{{ $item->tipo }} <span class="alert-date">({{ $item->fecha }})</span></span>
                            </div>
                            <i class="fas fa-chevron-right alert-arrow"></i>
                        </a>
                    @empty
                        <div class="empty-alerts">
                            <i class="fas fa-check-circle me-2"></i> Sin formaciones próximas
                        </div>
                    @endforelse
                </div>
                <div class="group-footer">
                    <a href="{{ route('admin.formaciones.index') }}" class="btn-view-all">
                        Ver todas las Formaciones <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
         KPI CARDS (GLASSMORPHISM)
         ══════════════════════════════════════════════════════════════ --}}
    <div class="row g-4 mb-5">
        {{-- Total Empleados --}}
        <div class="col-xl-3 col-md-6">
            <div class="glass-kpi">
                <div class="kpi-icon-wrap bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="kpi-content">
                    <span class="label">Total Personal</span>
                    <h2 class="value">{{ number_format($kpi['totalEmpleados']) }}</h2>
                    <div class="sub-info">
                        <span class="text-success fw-bold"><i class="fas fa-check-circle pe-1"></i>{{ $kpi['activos'] }} Activos</span>
                    </div>
                </div>
                @if($kpi['empVariacion'] != 0)
                <div class="trend-badge {{ $kpi['empVariacion'] > 0 ? 'up' : 'down' }}">
                    <i class="fas fa-caret-{{ $kpi['empVariacion'] > 0 ? 'up' : 'down' }}"></i> {{ abs($kpi['empVariacion']) }}%
                </div>
                @endif
            </div>
        </div>

        {{-- Solicitudes Pendientes --}}
        <div class="col-xl-3 col-md-6">
            <div class="glass-kpi">
                <div class="kpi-icon-wrap bg-warning">
                    <i class="fas fa-file-signature"></i>
                </div>
                <div class="kpi-content">
                    <span class="label">Solicitudes</span>
                    <h2 class="value">{{ $kpi['totalSolicitudes'] }}</h2>
                    <div class="sub-info">
                        @if($kpi['solPendientes'] > 0)
                            <span class="text-warning fw-bold pulse-text"><i class="fas fa-exclamation-circle pe-1"></i>{{ $kpi['solPendientes'] }} pendientes</span>
                        @else
                            <span class="text-success"><i class="fas fa-check-double pe-1"></i>Al día</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Comunicaciones --}}
        <div class="col-xl-3 col-md-6">
            <div class="glass-kpi">
                <div class="kpi-icon-wrap bg-info">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div class="kpi-content">
                    <span class="label">Comunicación</span>
                    <h2 class="value">{{ $kpi['totalComunicaciones'] }}</h2>
                    <div class="sub-info">
                        <span class="text-muted">{{ $kpi['comMesActual'] }} este mes</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rotación/Empresa --}}
        <div class="col-xl-3 col-md-6">
            <div class="glass-kpi">
                <div class="kpi-icon-wrap bg-indigo">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="kpi-content">
                    <span class="label">Capacidad Humana</span>
                    <h2 class="value">{{ $kpi['totalFormaciones'] }}</h2>
                    <div class="sub-info">
                        <span class="text-muted">Eventos de formación</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         DATAVIZ SECTION (CHARTS)
         ══════════════════════════════════════════════════════════════ --}}
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="glass-panel h-100">
                <div class="panel-header">
                    <div>
                        <h6 class="fw-bold mb-0">Demanda de Gestión</h6>
                        <small class="text-muted">Solicitudes vs Ingresos de Personal</small>
                    </div>
                    <div class="chart-legend">
                        <span class="dot bg-warning"></span> Solicitudes
                        <span class="dot bg-primary ms-3"></span> Ingresos
                    </div>
                </div>
                <div class="panel-body pt-3">
                    <div style="height: 300px;">
                        <canvas id="mainComboChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="glass-panel h-100">
                <div class="panel-header">
                    <h6 class="fw-bold mb-0">Personal por Área</h6>
                </div>
                <div class="panel-body d-flex align-items-center justify-content-center">
                    <div style="height: 280px; width: 100%;">
                        <canvas id="areaDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         RECENT ACTIVITY & TABLES
         ══════════════════════════════════════════════════════════════ --}}
    <div class="row g-4">
        {{-- Tabla de Empleados --}}
        <div class="col-lg-7">
            <div class="glass-panel p-0 overflow-hidden">
                <div class="panel-header border-bottom px-4 py-3 bg-white-50">
                    <h6 class="fw-bold mb-0 text-dark">Últimas Incorporaciones</h6>
                    <a href="{{ route('admin.empleados.index') }}" class="btn btn-link btn-sm text-primary text-decoration-none fw-bold">Ver todos</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover modern-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Colaborador</th>
                                <th>Resumen</th>
                                <th class="text-center">Estado</th>
                                <th class="text-end pe-4">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimosEmpleados as $emp)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm bg-soft-primary text-primary fw-bold">
                                            {{ strtoupper(substr($emp->persona->nombres ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $emp->persona->short_name ?? ($emp->persona->nombres . ' ' . $emp->persona->apellidos) }}</div>
                                            <small class="text-muted">{{ $emp->persona->numero_documento }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-truncate" style="max-width: 150px;">{{ $emp->cargo }}</div>
                                    <small class="text-muted">{{ $emp->area->nombre ?? 'N/A' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $emp->estado == 1 ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} rounded-pill px-3">
                                        {{ $emp->estado == 1 ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.empleados.show', $emp) }}" class="btn btn-icon-soft">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Timeline de Actividad --}}
        <div class="col-lg-5">
            <div class="glass-panel h-100">
                <div class="panel-header mb-4">
                    <h6 class="fw-bold mb-0">Movimientos Recientes</h6>
                </div>
                <div style="max-height: 480px; overflow-y: auto; padding-right: 15px; margin-right: -10px;">
                    <div class="modern-timeline">
                        @foreach($actividad as $item)
                        <div class="timeline-item">
                            <div class="timeline-icon" style="background: {{ $item['bg'] }}; color: {{ $item['color'] }}">
                                <i class="{{ $item['icono'] }}"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold text-dark">{{ $item['titulo'] }}</span>
                                    <small class="text-muted">{{ $item['fecha']->diffForHumans() }}</small>
                                </div>
                                <p class="text-muted small mb-0">{{ $item['descripcion'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@stop

@section('css')
<style>
:root {
    --primary-modern: #4f46e5;
    --primary-hover: #4338ca;
    --glass-bg: rgba(255, 255, 255, 0.7);
    --glass-border: rgba(255, 255, 255, 0.5);
    --radius-xl: 1.25rem;
    --shadow-soft: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
    --text-main: #1e293b;
    --bg-soft-primary: rgba(79, 70, 229, 0.1);
    --bg-soft-success: rgba(16, 185, 129, 0.1);
    --bg-soft-warning: rgba(245, 158, 11, 0.1);
    --bg-soft-danger: rgba(239, 68, 68, 0.1);
}

body {
    background-color: #f8fafc !important;
    color: var(--text-main);
}

.header-title { color: var(--text-main); font-size: 1.85rem; letter-spacing: -1px; }
.header-subtitle { font-size: 0.95rem; }

.btn-primary-modern {
    background: var(--primary-modern);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 0.6rem 1.4rem;
    font-weight: 600;
    transition: all 0.2s;
}
.btn-primary-modern:hover {
    background: var(--primary-hover);
    transform: translateY(-2px);
    color: white;
}

.critical-indicator {
    background: #fff;
    border: 1px solid #fee2e2;
    color: #ef4444;
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    position: relative;
}

.pulse {
    width: 8px; height: 8px; background: #ef4444; border-radius: 50%;
    margin-right: 8px; animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}

/* Glass Panels */
.glass-panel {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-soft);
    padding: 1.5rem;
    transition: all 0.3s;
}

.border-start-danger { border-left: 5px solid #ef4444 !important; }

.modern-tag {
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    width: fit-content;
    max-width: 100%;
}
.tag-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.tag-warning { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
.tag-warning-soft { background: #fffbeb; color: #92400e; border: 1px solid #fef3c7; }
.tag-info { background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }

/* KPI CARDS */
.glass-kpi {
    background: white;
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    box-shadow: var(--shadow-soft);
    position: relative;
    border: 1px solid #f1f5f9;
}

.kpi-icon-wrap {
    width: 54px; height: 54px; border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; color: white;
}

.kpi-content .label { font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
.kpi-content .value { font-size: 2rem; font-weight: 800; margin: 0.2rem 0; letter-spacing: -1px; color: var(--text-main); }
.kpi-content .sub-info { font-size: 0.8rem; }

.trend-badge {
    position: absolute; top: 1.5rem; right: 1.5rem;
    padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 700;
}
.trend-badge.up { background: #f0fdf4; color: #16a34a; }
.trend-badge.down { background: #fef2f2; color: #dc2626; }

/* Tables */
.modern-table thead th {
    background: #f8fafc;
    border: none;
    color: #64748b;
    text-transform: uppercase;
    font-size: 0.72rem;
    letter-spacing: 0.5px;
    padding: 1rem 0.75rem;
}
.modern-table tbody td { border-bottom: 1px solid #f1f5f9; padding: 1.1rem 0.75rem; }
.avatar-sm { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; }
.bg-soft-primary { background: #eef2ff; color: #4f46e5; }
.bg-soft-success { background: #ecfdf5; color: #059669; }
.bg-soft-danger { background: #fef2f2; color: #dc2626; }

.btn-icon-soft {
    width: 32px; height: 32px; border-radius: 8px;
    background: #f1f5f9; color: #64748b; border: none;
    display: inline-flex; align-items: center; justify-content: center;
}
.btn-icon-soft:hover { background: var(--bg-soft-primary); color: var(--primary-modern); }

/* Timeline */
.modern-timeline { position: relative; padding-left: 1.5rem; }
.modern-timeline::before {
    content: ''; position: absolute; left: 0; top: 0; bottom: 0;
    width: 2px; background: #e2e8f0; border-radius: 1px;
}
.timeline-item { position: relative; padding-bottom: 2rem; }
.timeline-icon {
    position: absolute; left: 0; top: 0;
    width: 38px; height: 38px; border-radius: 50%;
    transform: translateX(-50%);
    display: flex; align-items: center; justify-content: center;
    border: 4px solid white; font-size: 0.85rem;
    box-shadow: 0 4px 10px -1px rgba(0,0,0,0.1);
    z-index: 10;
    overflow: hidden; /* Para avatares */
}
.timeline-icon i { flex-shrink: 0; }
.timeline-icon img { width: 100%; height: 100%; object-fit: cover; }
.timeline-content { padding-left: 1.5rem; }

/* Charts */
.panel-header { display: flex; justify-content: space-between; align-items: flex-start; }
.chart-legend { font-size: 0.75rem; font-weight: 600; color: #64748b; }
.dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 4px; }
.bg-primary { background-color: var(--primary-modern) !important; }
.bg-warning { background-color: #f59e0b !important; }
.bg-info { background-color: #0ea5e9 !important; }
.bg-indigo { background-color: #6366f1 !important; }

.bg-soft-warning { background-color: rgba(245, 158, 11, 0.1); }
.pulse-text { animation: pulseOpacity 2s infinite; }
@keyframes pulseOpacity { 0% { opacity: 0.6; } 50% { opacity: 1; } 100% { opacity: 0.6; } }

/* 🚨 NUEVOS ESTILOS: ALERTAS DASHBOARD 🚨 */
.alert-group-card {
    background: white;
    border-radius: 20px;
    border: 1px solid #f1f5f9;
    box-shadow: var(--shadow-soft);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.alert-group-card:hover { 
    transform: translateY(-8px); 
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.group-header {
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    color: white;
}
.group-header.sst { background: linear-gradient(135deg, #ef4444, #f87171); }
.group-header.contracts { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
.group-header.training { background: linear-gradient(135deg, #f97316, #fb923c); }

.icon-box {
    width: 44px; height: 44px; background: rgba(255,255,255,0.25);
    border-radius: 14px; display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; backdrop-filter: blur(4px);
}

.group-body { padding: 0.75rem 0; flex-grow: 1; }

.alert-list-item {
    display: flex; align-items: center; padding: 0.9rem 1.25rem;
    text-decoration: none !important; color: inherit; transition: all 0.2s;
    border-bottom: 1px solid #f8fafc;
}
.alert-list-item:hover { background: #f1f5f9; }
.alert-list-item:last-child { border-bottom: none; }

.alert-marker { width: 4px; height: 32px; border-radius: 10px; background: #e2e8f0; margin-right: 1rem; flex-shrink: 0; }
.sst .alert-marker { background: #fee2e2; }
.contracts .alert-marker { background: #fef3c7; }
.training .alert-marker { background: #ffedd5; }

.is-critical .alert-marker { background: #ef4444 !important; }
.is-critical .emp-name { color: #b91c1c; }

.alert-content { flex-grow: 1; display: flex; flex-direction: column; overflow: hidden; }
.emp-name { font-weight: 700; font-size: 0.92rem; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.alert-desc { font-size: 0.78rem; color: #64748b; margin-top: 1px; }
.alert-date { font-weight: 600; color: #94a3b8; }
.is-critical .alert-date { color: #ef4444; }

.alert-arrow { font-size: 0.75rem; color: #cbd5e1; transition: transform 0.2s; }
.alert-list-item:hover .alert-arrow { transform: translateX(3px); color: var(--primary-modern); }

.group-footer { padding: 1rem 1.25rem; border-top: 1px solid #f1f5f9; background: #fcfdfe; }
.btn-view-all { 
    font-size: 0.8rem; font-weight: 700; color: #4f46e5; 
    text-decoration: none !important; display: flex; align-items: center; justify-content: center;
    transition: all 0.2s;
}
.btn-view-all:hover { color: #4338ca; transform: scale(1.02); }

.empty-alerts { 
    padding: 3rem 1.25rem; text-align: center; color: #94a3b8; 
    font-size: 0.85rem; display: flex; flex-direction: column; align-items: center; gap: 10px;
}
.empty-alerts i { font-size: 1.5rem; opacity: 0.5; }

</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const primaryColor = '#4f46e5';
    const warningColor = '#f59e0b';
    const gridColor = 'rgba(226, 232, 240, 0.4)';

    // ─────────────────────────────
    // 🔹 COMBO CHART (SOLICITUDES + INGRESOS)
    // ─────────────────────────────
    const mainCtx = document.getElementById('mainComboChart');
    if (mainCtx) {
        const mesesLabels = {!! json_encode($chartMesesLabels ?? []) !!};
        const mesesData = {!! json_encode($chartMesesData ?? []) !!};
        const empData = {!! json_encode($chartEmpData ?? []) !!};

        if (mesesLabels.length === 0) {
            console.warn('Dashboard: No hay datos para el gráfico de demanda.');
        }

        new Chart(mainCtx, {
            type: 'bar',
            data: {
                labels: mesesLabels,
                datasets: [
                    {
                        label: 'Solicitudes',
                        data: mesesData,
                        backgroundColor: 'rgba(245, 158, 11, 0.7)',
                        borderRadius: 6,
                        order: 2,
                        yAxisID: 'y' 
                    },
                    {
                        label: 'Ingresos',
                        data: empData,
                        type: 'line',
                        borderColor: primaryColor,
                        backgroundColor: 'transparent',
                        borderWidth: 3,
                        pointBackgroundColor: primaryColor,
                        tension: 0.4,
                        order: 1,
                        yAxisID: 'y' 
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: { 
                        grid: { display: false }, 
                        border: { display: false } 
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        border: { display: false },
                        ticks: { 
                            stepSize: 1,
                            callback: function(value) { if (value % 1 === 0) return value; }
                        }
                    }
                }
            }
        });
    }

    // ─────────────────────────────
    // 🔹 AREA DISTRIBUTION (DOUGHNUT)
    // ─────────────────────────────
    const areaCtx = document.getElementById('areaDistributionChart');
    if (areaCtx) {
        const areaLabels = {!! json_encode($chartAreasLabels ?? []) !!};
        const areaData = {!! json_encode($chartAreasData ?? []) !!};

        if (areaLabels.length === 0) {
            console.warn('Dashboard: No hay datos para el gráfico de áreas.');
        }

        new Chart(areaCtx, {
            type: 'doughnut',
            data: {
                labels: areaLabels,
                datasets: [{
                    data: areaData,
                    backgroundColor: [
                        '#4f46e5', '#f59e0b', '#0ea5e9', '#10b981', '#6366f1', '#f43f5e'
                    ],
                    borderWidth: 4,
                    borderColor: '#ffffff',
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 11, weight: '600' }
                        }
                    },
                    // Mostrar mensaje central si no hay datos
                    beforeDraw: function(chart) {
                        if (chart.data.datasets[0].data.every(item => item === 0)) {
                            let ctx = chart.ctx;
                            let width = chart.width;
                            let height = chart.height;
                            ctx.restore();
                            let fontSize = (height / 200).toFixed(2);
                            ctx.font = fontSize + "em sans-serif";
                            ctx.textBaseline = "middle";
                            let text = "Sin datos disponibles";
                            let textX = Math.round((width - ctx.measureText(text).width) / 2);
                            let textY = height / 2;
                            ctx.fillText(text, textX, textY);
                            ctx.save();
                        }
                    }
                }
            }
        });
    }

});
</script>
@stop