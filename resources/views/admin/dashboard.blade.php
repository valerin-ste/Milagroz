@extends('adminlte::page')

@section('title', 'Panel de Control - Milagroz')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-soft-slate">Panel de Control</h1>
            <p class="text-muted small">Gestión integral de Talento Humano</p>
        </div>
        <div class="col-sm-6 text-right">
            <div class="btn-group">
                <a href="{{ route('admin.empleados.create') }}" class="btn btn-light-custom btn-sm shadow-xs border">
                    <i class="fas fa-plus mr-1 text-soft-blue"></i> Nuevo Empleado
                </a>
                <a href="{{ route('admin.solicitudes.create') }}" class="btn btn-light-custom btn-sm shadow-xs border ml-2">
                    <i class="fas fa-file-alt mr-1 text-soft-green"></i> Nueva Solicitud
                </a>
            </div>
        </div>
    </div>
@stop

@section('content')
    <!-- =========================================================== -->
    <!-- 🔹 SECCIÓN DE ALERTAS REFINADAS (SOFT COLORS) -->
    <!-- =========================================================== -->
    <div class="row">
        <div class="col-md-12">
            @if(count($alertas['contratos_por_vencer']) > 0)
                <div class="alert bg-soft-red alert-dismissible shadow-xs mb-3 border-0">
                    <button type="button" class="close text-soft-red" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Contratos por vencer</h5>
                    Hay <span class="font-weight-bold">{{ count($alertas['contratos_por_vencer']) }}</span> contratos que requieren renovación en los próximos 30 días. 
                    <a href="{{ route('admin.etapa_contractual.index') }}" class="text-soft-red font-weight-bold ml-2 text-underline">Revisar</a>
                </div>
            @endif

            @if($alertas['solicitudes_pendientes'] > 0)
                <div class="alert bg-soft-yellow alert-dismissible shadow-xs mb-3 border-0">
                    <button type="button" class="close text-soft-yellow" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-bell"></i> Solicitudes en espera</h5>
                    Tienes <span class="font-weight-bold">{{ $alertas['solicitudes_pendientes'] }}</span> solicitudes pendientes de aprobación.
                    <a href="{{ route('admin.solicitudes.index') }}" class="text-soft-yellow font-weight-bold ml-2 text-underline">Gestionar</a>
                </div>
            @endif
        </div>
    </div>

    <!-- =========================================================== -->
    <!-- 🔹 KPIs MODERNOS (SOFT STYLES) -->
    <!-- =========================================================== -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="card bg-white shadow-xs border-0 mb-4 overflow-hidden">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <span class="text-muted small text-uppercase font-weight-bold">Total Empleados</span>
                            <h3 class="font-weight-bold mb-0 mt-1">{{ $kpi['total_empleados'] }}</h3>
                        </div>
                        <div class="col-4 text-right">
                            <div class="icon-shape bg-soft-blue rounded-circle shadow-none d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-users text-soft-blue"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card bg-white shadow-xs border-0 mb-4 overflow-hidden">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <span class="text-muted small text-uppercase font-weight-bold">Activos</span>
                            <h3 class="font-weight-bold mb-0 mt-1">{{ $kpi['empleados_activos'] }}</h3>
                        </div>
                        <div class="col-4 text-right">
                            <div class="icon-shape bg-soft-green rounded-circle shadow-none d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-user-check text-soft-green"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card bg-white shadow-xs border-0 mb-4 overflow-hidden">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <span class="text-muted small text-uppercase font-weight-bold">Solicitudes</span>
                            <h3 class="font-weight-bold mb-0 mt-1">{{ $kpi['total_solicitudes'] }}</h3>
                        </div>
                        <div class="col-4 text-right">
                            <div class="icon-shape bg-soft-yellow rounded-circle shadow-none d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-file-invoice text-soft-yellow"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card bg-white shadow-xs border-0 mb-4 overflow-hidden">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <span class="text-muted small text-uppercase font-weight-bold">Comunicados</span>
                            <h3 class="font-weight-bold mb-0 mt-1">{{ $kpi['total_comunicaciones'] }}</h3>
                        </div>
                        <div class="col-4 text-right">
                            <div class="icon-shape bg-soft-red rounded-circle shadow-none d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-bullhorn text-soft-red"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================== -->
    <!-- 🔹 GRÁFICAS CON PALETA REFINADA -->
    <!-- =========================================================== -->
    <div class="row">
        <div class="col-md-7">
            <div class="card bg-white shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h3 class="card-title font-weight-bold text-soft-slate">
                        <i class="fas fa-chart-bar mr-2 text-soft-blue"></i> Solicitudes Mensuales
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="solicitudChart" style="min-height: 280px; height: 280px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card bg-white shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h3 class="card-title font-weight-bold text-soft-slate">
                        <i class="fas fa-chart-pie mr-2 text-soft-green"></i> Distribución por Área
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="areaChart" style="min-height: 280px; height: 280px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- =========================================================== -->
        <!-- 🔹 ÚLTIMOS EMPLEADOS -->
        <!-- =========================================================== -->
        <div class="col-md-8">
            <div class="card bg-white shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h3 class="card-title font-weight-bold text-soft-slate">Nuevos Registros</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.empleados.index') }}" class="btn btn-tool text-soft-blue font-weight-bold">Ver todos</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr class="bg-light">
                                    <th class="border-0 text-muted small text-uppercase">Nombre</th>
                                    <th class="border-0 text-muted small text-uppercase">Cargo</th>
                                    <th class="border-0 text-muted small text-uppercase">Área</th>
                                    <th class="border-0 text-muted small text-uppercase text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ultimosEmpleados as $emp)
                                    <tr>
                                        <td class="font-weight-500">{{ $emp->persona->nombres }} {{ $emp->persona->apellidos }}</td>
                                        <td class="text-muted">{{ $emp->cargo }}</td>
                                        <td><span class="badge bg-soft-blue px-2 py-1">{{ $emp->area->nombre }}</span></td>
                                        <td class="text-center">
                                            @if($emp->estado == 1)
                                                <span class="text-soft-green"><i class="fas fa-check-circle"></i></span>
                                            @else
                                                <span class="text-muted"><i class="fas fa-times-circle"></i></span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================================================== -->
        <!-- 🔹 TIMELINE REFINADO -->
        <!-- =========================================================== -->
        <div class="col-md-4">
            <div class="card bg-white shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h3 class="card-title font-weight-bold text-soft-slate">Actividad Reciente</h3>
                </div>
                <div class="card-body py-2">
                    <div class="timeline timeline-inverse" id="timeline-soft">
                        @foreach($actividad as $item)
                        <div>
                            <i class="{{ $item['icono'] }} {{ str_replace('bg-', 'bg-soft-', $item['color']) }}"></i>
                            <div class="timeline-item shadow-none border-0 bg-light-soft mb-2">
                                <span class="time text-muted small"><i class="far fa-clock"></i> {{ $item['fecha']->diffForHumans() }}</span>
                                <h3 class="timeline-header border-0 font-weight-bold" style="font-size: 0.85rem; color: var(--soft-slate-text);">{{ $item['titulo'] }}</h3>
                                <div class="timeline-body pt-0 pb-2 text-muted" style="font-size: 0.8rem;">
                                    {{ $item['descripcion'] }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        body { background-color: #f8fafc !important; }
        .content-wrapper { background-color: #f8fafc !important; }
        .text-underline { text-decoration: underline !important; }
        .font-weight-500 { font-weight: 500; }
        .bg-light-soft { background-color: #f8fafc; border-radius: 8px; margin-left: 55px; }
        #timeline-soft.timeline > div > .timeline-item { box-shadow: none; background: #f8fafc; margin-left: 50px; margin-right: 0; }
        #timeline-soft.timeline:before { left: 25px; background: #e2e8f0; }
        #timeline-soft.timeline > div > i { left: 10px; width: 30px; height: 30px; line-height: 30px; }
        .icon-shape i { font-size: 1.25rem; }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(function () {
            // Configuración común
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#64748b';

            // --- GRÁFICA DE SOLICITUDES (BAR) ---
            var ctxSol = document.getElementById('solicitudChart').getContext('2d');
            new Chart(ctxSol, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartMesesLabels) !!},
                    datasets: [{
                        label: 'Solicitudes',
                        backgroundColor: '#bae6fd',
                        hoverBackgroundColor: '#0ea5e9',
                        data: {!! json_encode($chartMesesData) !!},
                        borderRadius: 6,
                        borderWidth: 0
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: '#f1f5f9', drawBorder: false },
                            ticks: { stepSize: 1 }
                        },
                        x: { grid: { display: false }, drawBorder: false }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // --- GRÁFICA DE ÁREAS (DONUT) ---
            var ctxArea = document.getElementById('areaChart').getContext('2d');
            new Chart(ctxArea, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartAreasLabels) !!},
                    datasets: [{
                        data: {!! json_encode($chartAreasData) !!},
                        backgroundColor: ['#e0f2fe', '#dcfce7', '#fef3c7', '#fee2e2', '#f1f5f9'],
                        hoverBackgroundColor: ['#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#cbd5e1'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    cutout: '75%',
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                    }
                }
            });
        });
    </script>
@stop