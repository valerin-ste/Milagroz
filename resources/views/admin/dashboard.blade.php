@extends('adminlte::page')

@section('title', 'Panel de Control — Milagroz')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-2 mb-1 px-1">
    <div>
        <h2 class="fw-bold mb-0" style="color:var(--text-main); font-size:1.6rem; letter-spacing:-0.5px;">
            Panel de Control
        </h2>
        <p class="text-muted mb-0" style="font-size:0.875rem;">
            <i class="fas fa-calendar-alt mr-1"></i>
            {{ \Carbon\Carbon::now()->translatedFormat('l, d \d\e F \d\e Y') }}
        </p>
    </div>
    <div class="d-flex" style="gap:0.5rem;">
        @if($totalAlertasCriticas > 0)
        <span class="btn btn-sm" style="background:#fef2f2; color:#b91c1c; border:1px solid rgba(239,68,68,0.3); font-weight:600; font-size:0.78rem; cursor:default;">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ $totalAlertasCriticas }} alertas críticas
        </span>
        @endif
        <a href="{{ route('admin.empleados.create') }}" class="btn btn-sm btn-orange">
            <i class="fas fa-plus mr-1"></i> Nuevo Empleado
        </a>
        <a href="{{ route('admin.solicitudes.create') }}" class="btn btn-sm btn-light-custom border">
            <i class="fas fa-file-alt mr-1"></i> Nueva Solicitud
        </a>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-2 pb-5">

{{-- ══════════════════════════════════════════════════════════════
     BLOQUE DE ALERTAS
══════════════════════════════════════════════════════════════ --}}
@php
    $hayAlertas = $alertas['contratosVencidos']->count() > 0
               || $alertas['sstVencidos']->count() > 0
               || $alertas['contratosCriticos']->count() > 0
               || $alertas['sstCriticos']->count() > 0
               || $alertas['contratosPreventivos']->count() > 0
               || $alertas['solicitudesPendientesDetalle']->count() > 0;
@endphp

@if($hayAlertas)
<div class="row mb-2">
    <div class="col-12">
        <div style="display:flex; flex-direction:column; gap:0.6rem;">

            {{-- 🔴 CRÍTICAS: Vencidos hoy o ya expirados --}}
            @if($alertas['contratosVencidos']->count() > 0 || $alertas['sstVencidos']->count() > 0)
            <div class="dash-alert dash-alert-critical d-flex align-items-start" style="gap:1rem;">
                <div class="dash-alert-icon" style="background:rgba(239,68,68,0.15); color:#b91c1c; flex-shrink:0;">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="dash-alert-title">Documentos Vencidos — Acción Inmediata Requerida</div>
                    <div style="display:flex; flex-wrap:wrap; gap:0.4rem; margin-top:0.4rem;">
                        @foreach($alertas['contratosVencidos']->take(3) as $c)
                        <span class="dash-alert-tag" style="background:#fef2f2; color:#b91c1c; border-color:rgba(239,68,68,0.3);">
                            <i class="fas fa-file-contract mr-1"></i>
                            {{ $c->empleado->persona->nombres ?? '—' }} {{ $c->empleado->persona->apellidos ?? '' }}
                            — vence {{ \Carbon\Carbon::parse($c->fecha_fin)->format('d/m/Y') }}
                        </span>
                        @endforeach
                        @foreach($alertas['sstVencidos']->take(2) as $s)
                        <span class="dash-alert-tag" style="background:#fef2f2; color:#b91c1c; border-color:rgba(239,68,68,0.3);">
                            <i class="fas fa-heartbeat mr-1"></i>
                            {{ $s->empleado->persona->nombres ?? '—' }} — SST {{ \Carbon\Carbon::parse($s->fecha)->format('d/m/Y') }}
                        </span>
                        @endforeach
                        @if(($alertas['contratosVencidos']->count() + $alertas['sstVencidos']->count()) > 5)
                        <span class="dash-alert-tag" style="background:#f1f5f9; color:#64748b; border-color:#e2e8f0;">
                            +{{ ($alertas['contratosVencidos']->count() + $alertas['sstVencidos']->count()) - 5 }} más
                        </span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('admin.etapa_contractual.index') }}"
                   class="btn btn-sm" style="background:#fef2f2; color:#b91c1c; border:1px solid rgba(239,68,68,0.3); white-space:nowrap; font-weight:600; font-size:0.78rem; flex-shrink:0;">
                    Ver <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            @endif

            {{-- 🟠 PREVENTIVAS: 8 días --}}
            @if($alertas['contratosCriticos']->count() > 0 || $alertas['sstCriticos']->count() > 0)
            <div class="dash-alert dash-alert-warning d-flex align-items-start" style="gap:1rem;">
                <div class="dash-alert-icon" style="background:rgba(249,115,22,0.12); color:#c2410c; flex-shrink:0;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="dash-alert-title">Próximos Vencimientos — Menos de 8 días</div>
                    <div style="display:flex; flex-wrap:wrap; gap:0.4rem; margin-top:0.4rem;">
                        @foreach($alertas['contratosCriticos']->take(3) as $c)
                        <span class="dash-alert-tag" style="background:rgba(249,115,22,0.08); color:#c2410c; border-color:rgba(249,115,22,0.3);">
                            <i class="fas fa-file-contract mr-1"></i>
                            {{ $c->empleado->persona->nombres ?? '—' }}
                            — {{ \Carbon\Carbon::parse($c->fecha_fin)->format('d/m/Y') }}
                            ({{ \Carbon\Carbon::parse($c->fecha_fin)->diffInDays(now()) }}d)
                        </span>
                        @endforeach
                        @foreach($alertas['sstCriticos']->take(2) as $s)
                        <span class="dash-alert-tag" style="background:rgba(249,115,22,0.08); color:#c2410c; border-color:rgba(249,115,22,0.3);">
                            <i class="fas fa-heartbeat mr-1"></i>
                            {{ $s->empleado->persona->nombres ?? '—' }} — SST
                        </span>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('admin.etapa_contractual.index') }}?estado=1"
                   class="btn btn-sm" style="background:rgba(249,115,22,0.1); color:#c2410c; border:1px solid rgba(249,115,22,0.3); white-space:nowrap; font-weight:600; font-size:0.78rem; flex-shrink:0;">
                    Ver <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            @endif

            {{-- 🟡 INFORMATIVAS: 30 días --}}
            @if($alertas['contratosPreventivos']->count() > 0)
            <div class="dash-alert dash-alert-info d-flex align-items-start" style="gap:1rem;">
                <div class="dash-alert-icon" style="background:rgba(234,179,8,0.12); color:#854d0e; flex-shrink:0;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="dash-alert-title">
                        {{ $alertas['contratosPreventivos']->count() }} contrato(s) vencen en los próximos 30 días
                    </div>
                    <p class="mb-0 mt-1" style="font-size:0.8rem; color:#92400e;">
                        Planifique las renovaciones con anticipación para evitar interrupciones laborales.
                    </p>
                </div>
            </div>
            @endif

            {{-- 🔵 SOLICITUDES PENDIENTES --}}
            @if($alertas['solicitudesPendientesDetalle']->count() > 0)
            <div class="dash-alert dash-alert-blue d-flex align-items-start" style="gap:1rem;">
                <div class="dash-alert-icon" style="background:rgba(19,182,236,0.12); color:#0369a1; flex-shrink:0;">
                    <i class="fas fa-inbox"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="dash-alert-title">
                        {{ $kpi['solPendientes'] }} solicitud(es) pendiente(s) de gestión
                    </div>
                    <div style="display:flex; flex-wrap:wrap; gap:0.4rem; margin-top:0.4rem;">
                        @foreach($alertas['solicitudesPendientesDetalle']->take(4) as $sol)
                        <span class="dash-alert-tag" style="background:rgba(19,182,236,0.08); color:#0369a1; border-color:rgba(19,182,236,0.2);">
                            {{ $sol->empleado->persona->nombres ?? '—' }} — {{ ucfirst($sol->tipo) }}
                        </span>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('admin.solicitudes.index') }}?estado=pendiente"
                   class="btn btn-sm" style="background:rgba(19,182,236,0.1); color:#0369a1; border:1px solid rgba(19,182,236,0.25); white-space:nowrap; font-weight:600; font-size:0.78rem; flex-shrink:0;">
                    Gestionar <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            @endif

        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════
     KPIs
══════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Total Empleados --}}
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card" style="border-left: 4px solid #0ea5e9;">
            <div class="kpi-icon" style="background:rgba(14,165,233,0.1); color:#0ea5e9;">
                <i class="fas fa-users"></i>
            </div>
            <div class="kpi-body">
                <span class="kpi-label">Total Empleados</span>
                <div class="kpi-value">{{ $kpi['totalEmpleados'] }}</div>
                <div class="kpi-sub">
                    <span class="kpi-active">{{ $kpi['activos'] }} activos</span>
                    <span class="kpi-inactive">{{ $kpi['inactivos'] }} inactivos</span>
                </div>
            </div>
            @if($kpi['empVariacion'] != 0)
            <div class="kpi-trend {{ $kpi['empVariacion'] > 0 ? 'trend-up' : 'trend-down' }}">
                <i class="fas fa-arrow-{{ $kpi['empVariacion'] > 0 ? 'up' : 'down' }}"></i>
                {{ abs($kpi['empVariacion']) }}%
            </div>
            @endif
        </div>
    </div>

    {{-- Empleados Activos --}}
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card" style="border-left: 4px solid #10b981;">
            <div class="kpi-icon" style="background:rgba(16,185,129,0.1); color:#10b981;">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="kpi-body">
                <span class="kpi-label">Empleados Activos</span>
                <div class="kpi-value">{{ $kpi['activos'] }}</div>
                <div class="kpi-sub">
                    @php $pctActivos = $kpi['totalEmpleados'] > 0 ? round(($kpi['activos'] / $kpi['totalEmpleados']) * 100) : 0; @endphp
                    <div class="kpi-bar-wrap">
                        <div class="kpi-bar-fill" style="width:{{ $pctActivos }}%; background:#10b981;"></div>
                    </div>
                    <span style="font-size:0.72rem; color:#10b981; font-weight:600;">{{ $pctActivos }}% de plantilla</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Solicitudes --}}
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card" style="border-left: 4px solid #f59e0b;">
            <div class="kpi-icon" style="background:rgba(245,158,11,0.1); color:#f59e0b;">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="kpi-body">
                <span class="kpi-label">Solicitudes</span>
                <div class="kpi-value">{{ $kpi['totalSolicitudes'] }}</div>
                <div class="kpi-sub">
                    @if($kpi['solPendientes'] > 0)
                        <span style="color:#f59e0b; font-weight:600; font-size:0.76rem;">
                            <i class="fas fa-clock mr-1"></i>{{ $kpi['solPendientes'] }} sin gestionar
                        </span>
                    @else
                        <span style="color:#10b981; font-size:0.76rem;">
                            <i class="fas fa-check-circle mr-1"></i>Todas gestionadas
                        </span>
                    @endif
                </div>
            </div>
            @if($kpi['solMesAnterior'] > 0)
            @php $solVar = round((($kpi['solMesActual'] - $kpi['solMesAnterior']) / $kpi['solMesAnterior']) * 100); @endphp
            <div class="kpi-trend {{ $solVar >= 0 ? 'trend-up' : 'trend-down' }}">
                <i class="fas fa-arrow-{{ $solVar >= 0 ? 'up' : 'down' }}"></i>
                {{ abs($solVar) }}%
            </div>
            @endif
        </div>
    </div>

    {{-- Comunicaciones --}}
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card" style="border-left: 4px solid #6366f1;">
            <div class="kpi-icon" style="background:rgba(99,102,241,0.1); color:#6366f1;">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div class="kpi-body">
                <span class="kpi-label">Comunicados</span>
                <div class="kpi-value">{{ $kpi['totalComunicaciones'] }}</div>
                <div class="kpi-sub">
                    <span style="font-size:0.76rem; color:var(--text-muted);">
                        {{ $kpi['comMesActual'] }} este mes
                        &nbsp;·&nbsp; {{ $kpi['totalFormaciones'] }} formaciones
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>


{{-- ══════════════════════════════════════════════════════════════
     GRÁFICAS
══════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Solicitudes por mes --}}
    <div class="col-lg-5">
        <div class="card border-0 h-100">
            <div class="card-header" style="padding:1.1rem 1.5rem;">
                <h6 class="fw-bold mb-0">Solicitudes Mensuales</h6>
                <small class="text-muted">Últimos 6 meses</small>
            </div>
            <div class="card-body" style="height:260px;">
                <canvas id="solicitudChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Empleados --}}
    <div class="col-lg-4">
        <div class="card border-0 h-100">
            <div class="card-header" style="padding:1.1rem 1.5rem;">
                <h6 class="fw-bold mb-0">Ingresos de Personal</h6>
                <small class="text-muted">Nuevos empleados</small>
            </div>
            <div class="card-body" style="height:260px;">
                <canvas id="empChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Áreas --}}
    <div class="col-lg-3">
        <div class="card border-0 h-100">
            <div class="card-header" style="padding:1.1rem 1.5rem;">
                <h6 class="fw-bold mb-0">Distribución por Área</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="height:240px;">
                <canvas id="areaChart"></canvas>
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════
     TABLA ÚLTIMOS EMPLEADOS + ACTIVIDAD
══════════════════════════════════════════════════════════════ --}}
<div class="row g-3">
    {{-- Nuevos registros --}}
    <div class="col-lg-8">
        <div class="card border-0">
            <div class="card-header d-flex align-items-center justify-content-between" style="padding:1.1rem 1.5rem; border-bottom:1px solid var(--border-color);">
                <div>
                    <h6 class="fw-bold mb-0" style="font-size:0.95rem;">Últimos Empleados Registrados</h6>
                    <p class="mb-0 text-muted" style="font-size:0.78rem;">Los 6 más recientes en el sistema</p>
                </div>
                <a href="{{ route('admin.empleados.index') }}"
                   class="btn btn-sm btn-light-custom border" style="font-size:0.78rem;">
                    Ver todos <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="padding-left:1.5rem;">Empleado</th>
                                <th>Área / Cargo</th>
                                <th>Sede</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center" style="padding-right:1.5rem;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimosEmpleados as $emp)
                            <tr>
                                <td style="padding-left:1.5rem;">
                                    <div class="d-flex align-items-center" style="gap:0.75rem;">
                                        <div class="cell-avatar">
                                            {{ strtoupper(substr($emp->persona->nombres ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="cell-primary">{{ $emp->persona->nombres ?? '' }} {{ $emp->persona->apellidos ?? '' }}</div>
                                            <div class="cell-secondary"><i class="fas fa-id-card mr-1"></i>{{ $emp->persona->numero_documento ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="cell-primary">{{ $emp->area->nombre ?? 'Sin área' }}</div>
                                    <div class="cell-secondary">{{ $emp->cargo }}</div>
                                </td>
                                <td>
                                    <span style="font-size:0.8rem; color:#475569;">
                                        <i class="fas fa-hospital-alt mr-1 text-muted"></i>
                                        {{ $emp->sede->nombre ?? 'Sin sede' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($emp->estado == 1)
                                        <span class="badge-soft-success">Activo</span>
                                    @else
                                        <span class="badge-soft-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center" style="padding-right:1.5rem;">
                                    <div class="action-container">
                                        <a href="{{ route('admin.empleados.show', $emp) }}"
                                           class="btn-table-action"
                                           data-toggle="tooltip" title="Ver perfil">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($emp->estado == 1)
                                        <a href="{{ route('admin.empleados.edit', $emp) }}"
                                           class="btn-table-action"
                                           data-toggle="tooltip" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Actividad reciente --}}
    <div class="col-lg-4">
        <div class="card border-0">
            <div class="card-header" style="padding:1.1rem 1.5rem; border-bottom:1px solid var(--border-color);">
                <h6 class="fw-bold mb-0" style="font-size:0.95rem;">
                    <i class="fas fa-history mr-2" style="color:#6366f1;"></i>Actividad Reciente
                </h6>
            </div>
            <div class="card-body" style="padding:0.5rem 0; max-height:380px; overflow-y:auto;">
                @php
                    $hoyStr = \Carbon\Carbon::today()->format('Y-m-d');
                    $ayerStr = \Carbon\Carbon::yesterday()->format('Y-m-d');
                    $currentGroup = null;
                    $groups = ['today' => 'Hoy', 'yesterday' => 'Ayer', 'week' => 'Esta semana'];
                @endphp
                @foreach($actividad as $item)
                @php
                    $itemDate = $item['fecha']->format('Y-m-d');
                    $group = $itemDate === $hoyStr ? 'today' : ($itemDate === $ayerStr ? 'yesterday' : 'week');
                @endphp
                @if($group !== $currentGroup)
                    @php $currentGroup = $group; @endphp
                    <div style="padding:0.5rem 1.25rem 0.2rem; font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#94a3b8;">
                        {{ $groups[$group] ?? $group }}
                    </div>
                @endif
                <a href="{{ $item['link'] ?? '#' }}" class="activity-row text-decoration-none" style="display:flex; align-items:center; gap:0.85rem; padding:0.7rem 1.25rem; transition:background 0.15s; border-bottom:1px solid #f8fafc;">
                    <div style="width:34px; height:34px; border-radius:9px; background:{{ $item['bg'] }}; color:{{ $item['color'] }}; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:0.8rem;">
                        <i class="{{ $item['icono'] }}"></i>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:0.82rem; font-weight:600; color:var(--text-main); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $item['titulo'] }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $item['descripcion'] }}</div>
                    </div>
                    <div style="font-size:0.7rem; color:#cbd5e1; white-space:nowrap;">{{ $item['fecha']->diffForHumans() }}</div>
                </a>
                @endforeach
                @if($actividad->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-history fa-2x mb-2 d-block" style="opacity:0.2;"></i>
                    <small>Sin actividad reciente</small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

</div>{{-- /container --}}
@stop

@section('css')
<style>
/* ── DASH ALERT CARDS ─────────────────────────────── */
.dash-alert {
    padding: 0.9rem 1.1rem;
    border-radius: var(--radius-lg);
    border: 1px solid;
    animation: fadeIn 0.3s ease;
}
.dash-alert-critical { background: #fff5f5; border-color: rgba(239,68,68,0.25); }
.dash-alert-warning  { background: #fff7ed; border-color: rgba(249,115,22,0.25); }
.dash-alert-info     { background: #fffbeb; border-color: rgba(234,179,8,0.25); }
.dash-alert-blue     { background: #f0f9ff; border-color: rgba(14,165,233,0.2); }

.dash-alert-icon {
    width: 38px; height: 38px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
}
.dash-alert-title {
    font-weight: 700; font-size: 0.875rem; color: var(--text-main);
}
.dash-alert-tag {
    display: inline-flex; align-items: center;
    padding: 0.25rem 0.6rem; border-radius: 6px;
    font-size: 0.775rem; font-weight: 500;
    border: 1px solid;
}

/* ── KPI CARDS ────────────────────────────────────── */
.kpi-card {
    background: #fff;
    border-radius: var(--radius-xl);
    padding: 1.1rem 1.25rem;
    display: flex;
    align-items: flex-start;
    gap: 0.85rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    position: relative;
    overflow: hidden;
    transition: box-shadow 0.2s;
    height: 100%;
}
.kpi-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.kpi-icon {
    width: 46px; height: 46px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.kpi-body { flex: 1; min-width: 0; }
.kpi-label {
    font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.6px; color: #94a3b8; display: block;
}
.kpi-value {
    font-size: 1.9rem; font-weight: 700; color: var(--text-main);
    line-height: 1.1; margin: 0.15rem 0 0.35rem;
}
.kpi-sub { display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap; }
.kpi-active  { font-size: 0.72rem; font-weight: 600; color: #10b981; }
.kpi-inactive{ font-size: 0.72rem; font-weight: 600; color: #ef4444; }
.kpi-trend {
    position: absolute; top: 0.85rem; right: 0.9rem;
    font-size: 0.7rem; font-weight: 700;
    padding: 0.2rem 0.5rem; border-radius: 6px;
}
.trend-up   { background: #f0fdf4; color: #15803d; }
.trend-down { background: #fef2f2; color: #b91c1c; }
.kpi-bar-wrap { height: 4px; background: #f1f5f9; border-radius: 99px; overflow: hidden; width: 60px; flex-shrink: 0; }
.kpi-bar-fill { height: 100%; border-radius: 99px; }

/* ── ACTIVITY ROW ─────────────────────────────────── */
.activity-row:hover { background: #f8fafc !important; }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const gridColor = '#f1f5f9';

    // ─────────────────────────────
    // 🔹 SOLICITUDES
    // ─────────────────────────────
    const solicitudCanvas = document.getElementById('solicitudChart');

    if (solicitudCanvas) {

        if (window.solicitudChartInstance) {
            window.solicitudChartInstance.destroy();
        }

        window.solicitudChartInstance = new Chart(solicitudCanvas, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartMesesLabels) !!},
                datasets: [{
                    label: 'Solicitudes',
                    data: {!! json_encode($chartMesesData) !!},
                    backgroundColor: 'rgba(245,158,11,0.3)',
                    borderColor: '#f59e0b',
                    borderWidth: 2,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // ─────────────────────────────
    // 🔹 EMPLEADOS
    // ─────────────────────────────
    const empCanvas = document.getElementById('empChart');

    if (empCanvas) {

        if (window.empChartInstance) {
            window.empChartInstance.destroy();
        }

        window.empChartInstance = new Chart(empCanvas, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartEmpLabels) !!},
                datasets: [{
                    label: 'Empleados',
                    data: {!! json_encode($chartEmpData) !!},
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14,165,233,0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // ─────────────────────────────
    // 🔹 ÁREAS
    // ─────────────────────────────
    const areaCanvas = document.getElementById('areaChart');

    if (areaCanvas) {

        if (window.areaChartInstance) {
            window.areaChartInstance.destroy();
        }

        window.areaChartInstance = new Chart(areaCanvas, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartAreasLabels) !!},
                datasets: [{
                    data: {!! json_encode($chartAreasData) !!},
                    backgroundColor: [
                        '#0ea5e9',
                        '#10b981',
                        '#f59e0b',
                        '#6366f1',
                        '#ef4444'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

});
</script>
@stop

    // 🔹 ÁREAS
    const areaCanvas = document.getElementById('areaChart');
    if (areaCanvas) {
        if (window.areaChartInstance) {
            window.areaChartInstance.destroy();
        }

        window.areaChartInstance = new Chart(areaCanvas, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartAreasLabels) !!},
                datasets: [{
                    data: {!! json_encode($chartAreasData) !!},
                    backgroundColor: ['#0ea5e9','#10b981','#f59e0b','#6366f1','#ef4444']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

});
</script>
@stop