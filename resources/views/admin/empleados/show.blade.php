@extends('adminlte::page')

@section('title', 'Perfil – ' . ($empleado->persona->nombres ?? 'Empleado'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-3 px-2">
    <div>
        <h2 class="fw-bold mb-1 head-title-modern">
            <i class="fas fa-id-badge me-2 text-primary-modern"></i>
            Perfil del Empleado
        </h2>
        <p class="text-muted mb-0 small-text">
            Gestión integral del expediente digital y documentos asociados
        </p>
    </div>
    <div class="d-flex gap-2">
        @if($empleado->estado == 1)
        <a href="{{ route('admin.empleados.edit', $empleado) }}" class="btn btn-primary-modern shadow-sm">
            <i class="fas fa-pen me-1"></i> Editar Perfil
        </a>
        @endif
        <a href="{{ route('admin.empleados.index') }}" class="btn btn-outline-modern shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>
@stop

@section('content')
@php
    $fullname     = $empleado->persona->full_name;
    $initial      = strtoupper(substr($empleado->persona->nombres ?? 'X', 0, 1));
    $diasEmpresa  = \Carbon\Carbon::parse($empleado->fecha_ingreso)->diffInDays(now());
    $nContratos   = $empleado->etapaContractuales->count();
    $nSST         = $empleado->seguridadSaludTrabajo->count();
    $nEvals       = $empleado->evaluacionesDesempeno->count();
    $nFormaciones = $empleado->formaciones->count();
    $contratoActivo = $empleado->etapaContractuales->where('estado', 1)->sortByDesc('fecha_fin')->first();

    // Agrupar evaluaciones por año
    $evaluacionesPorAño = $empleado->evaluacionesDesempeno->sortByDesc('fecha')->groupBy(fn($ev) => \Carbon\Carbon::parse($ev->fecha)->format('Y'));

    $mesesNombre = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
        7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];

    $groupByYearAndMonth = function($collection, $dateField) {
        return $collection->sortByDesc($dateField)->groupBy(function($item) use ($dateField) {
            return \Carbon\Carbon::parse($item->{$dateField})->format('Y');
        })->map(function($yearItems) use ($dateField) {
            return $yearItems->groupBy(function($item) use ($dateField) {
                return (int)\Carbon\Carbon::parse($item->{$dateField})->format('n');
            })->sortKeysDesc();
        });
    };

    $productividadesPorAnioMes = $groupByYearAndMonth($empleado->productividades->where('estado', 1), 'fecha');
    $capacidadPorAnioMes = $groupByYearAndMonth($empleado->capacidadInstaladas, 'fecha');
    $nominaPorAnioMes = $groupByYearAndMonth($empleado->reportesNovedadesNomina, 'fecha');
    $formacionesPorAnioMes = $groupByYearAndMonth($empleado->formaciones, 'fecha_inicio');
    $calidadPorAnioMes = $groupByYearAndMonth($empleado->calidadDocumentos->where('estado', 1), 'fecha_emision');
    $senaPorAnioMes = $groupByYearAndMonth($empleado->plantaPersonalSena, 'fecha_reporte');
    $dotacionesPorAnioMes = $groupByYearAndMonth($empleado->dotaciones, 'fecha');
    $certificacionesPorAnioMes = $groupByYearAndMonth($empleado->certificaciones, 'fecha_expedicion');
    $fechasEspecialesPorAnioMes = $groupByYearAndMonth($empleado->fechasEspeciales->where('estado', 1), 'fecha');
@endphp

<div class="container-fluid px-2 pb-5">

    {{-- ══════════════════════════════════════════════════════
         HERO BANNER (GLASSMORPHISM)
    ══════════════════════════════════════════════════════ --}}
    <div class="glass-profile-hero mb-4">
        <div class="row align-items-center">
            <div class="col-md-auto text-center mb-3 mb-md-0">
                <div class="profile-avatar-xl shadow-lg">{{ $initial }}</div>
            </div>
            <div class="col-md px-md-4">
                <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                    <h1 class="fullname-display">{{ $fullname }}</h1>
                    <span class="badge-status {{ $empleado->estado == 1 ? 'active' : 'inactive' }}">
                        <i class="fas fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i>
                        {{ $empleado->estado == 1 ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <div class="profile-details text-white-50">
                    <span class="me-3"><i class="fas fa-briefcase me-1"></i> {{ $empleado->cargo }}</span>
                    <span><i class="fas fa-map-marker-alt me-1"></i> {{ $empleado->sede->nombre ?? 'Sin sede' }}</span>
                </div>
                @if($contratoActivo)
                    @php $badge = $contratoActivo->getStatusBadge($contratoActivo->fecha_fin); @endphp
                    <div class="mt-3 active-contract-info">
                        <span class="text-white-50 small me-2">Contrato Principal:</span>
                        <span class="badge {{ $badge['class'] }} rounded-pill px-3">
                            <i class="{{ $badge['icon'] }} me-1"></i> {{ $badge['label'] }}
                            @if($contratoActivo->fecha_fin)
                                &mdash; Expira: {{ \Carbon\Carbon::parse($contratoActivo->fecha_fin)->format('d/m/Y') }}
                            @endif
                        </span>
                    </div>
                @endif
            </div>
            <div class="col-lg-4 mt-4 mt-lg-0 text-center text-lg-end pr-md-4">
                <div class="d-flex justify-content-center justify-content-lg-end gap-3">
                    <div class="mini-stat">
                        <div class="val">{{ number_format($diasEmpresa) }}</div>
                        <div class="lbl">Días</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- ─── SIDEBAR: INFO CARDS ─────────────────────────────── --}}
        <div class="col-lg-3">
            {{-- Datos Personales --}}
            <div class="glass-card mb-4">
                <div class="card-title-bar">
                    <i class="fas fa-user-circle me-2"></i> Datos Personales
                </div>
                <div class="card-body-content">
                    <div class="card-info-item">
                        <div class="icon"><i class="fas fa-id-card"></i></div>
                        <div class="cont">
                            <label>Documento</label>
                            <span>{{ $empleado->persona->tipo_documento ?? '' }} {{ $empleado->persona->numero_documento ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="card-info-item">
                        <div class="icon"><i class="fas fa-phone"></i></div>
                        <div class="cont">
                            <label>Teléfono</label>
                            <span>{{ $empleado->persona->telefono ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="card-info-item">
                        <div class="icon"><i class="fas fa-envelope"></i></div>
                        <div class="cont">
                            <label>Email Corporativo</label>
                            <span class="text-truncate" style="max-width: 100%;">{{ $empleado->persona->correo ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="card-info-item">
                        <div class="icon"><i class="fas fa-birthday-cake"></i></div>
                        <div class="cont">
                            <label>Cumpleaños</label>
                            <span>{{ $empleado->persona->fecha_nacimiento ? \Carbon\Carbon::parse($empleado->persona->fecha_nacimiento)->translatedFormat('d \d\e F') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Datos Laborales --}}
            <div class="glass-card">
                <div class="card-title-bar orange">
                    <i class="fas fa-briefcase me-2"></i> Datos Laborales
                </div>
                <div class="card-body-content">
                    <div class="card-info-item">
                        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                        <div class="cont">
                            <label>Fecha de Ingreso</label>
                            <span>{{ \Carbon\Carbon::parse($empleado->fecha_ingreso)->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="card-info-item">
                        <div class="icon"><i class="fas fa-user-tag"></i></div>
                        <div class="cont">
                            <label>Rol de Sistema</label>
                            <span>{{ $empleado->rol->nombre ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="card-info-item">
                        <div class="icon"><i class="fas fa-id-badge"></i></div>
                        <div class="cont">
                            <label>Cargo Actual</label>
                            <span>{{ $empleado->cargo }}</span>
                        </div>
                    </div>
                    <div class="card-info-item">
                        <div class="icon"><i class="fas fa-building"></i></div>
                        <div class="cont">
                            <label>Sede</label>
                            <span>
                                {{ $empleado->sede->nombre ?? 'Sin sede' }} - 
                                {{ $empleado->sede->ciudad ?? ''}}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── MAIN CONTENT: TABS SYSTEM ───────────────────────── --}}
        <div class="col-lg-9">
            <div class="main-tabs-card">
                <nav>
                    <div class="nav nav-tabs profile-nav-tabs px-3 border-0 flex-nowrap"
                        id="emp-tabs"
                        role="tablist"
                        style="overflow-x: auto; overflow-y: hidden; white-space: nowrap;">
                        <a class="nav-link active" data-toggle="tab" href="#tab-precontractual" role="tab">
                            <i class="fas fa-file-contract me-2"></i> Precontractual
                        </a>
                        <a class="nav-link" data-toggle="tab" href="#tab-contractual" role="tab">
                            <i class="fas fa-handshake me-2"></i> Contractual
                        </a>
                        <a class="nav-link" data-toggle="tab" href="#tab-sst" role="tab">
                            <i class="fas fa-heartbeat me-2"></i> SST
                        </a>
                        <a class="nav-link" data-toggle="tab" href="#tab-evaluaciones" role="tab">
                            <i class="fas fa-chart-line me-2"></i> Evaluaciones
                        </a>
                        <a class="nav-link" data-toggle="tab" href="#tab-formacion" role="tab">
                            <i class="fas fa-graduation-cap me-2"></i> Formación
                        </a>
                        <a class="nav-link" data-toggle="tab" href="#tab-certificaciones" role="tab">
                            <i class="fas fa-certificate me-2"></i> Certificaciones
                        </a>
                        <a class="nav-link" data-toggle="tab" href="#tab-comunicaciones" role="tab">
                            <i class="fas fa-bullhorn me-2"></i> Comunicados
                        </a>
                        <a class="nav-link" data-toggle="tab" href="#tab-solicitudes" role="tab">
                            <i class="fas fa-tasks me-2"></i> Solicitudes
                        </a>
                        <a class="nav-link" data-toggle="tab" href="#tab-dotacion" role="tab">
                            <i class="fas fa-tshirt me-2"></i> Dotación
                        </a>
                        <a class="nav-link" data-toggle="tab" href="#tab-fechas" role="tab">
                            <i class="fas fa-calendar-alt me-2"></i> Fechas Especiales
                        </a>
                        <a class="nav-link" data-toggle="tab" href="#tab-capacidad" role="tab">
                            <i class="fas fa-industry me-2"></i> Capacidad Instalada
                        </a>

                        <a class="nav-link" data-toggle="tab" href="#tab-nomina" role="tab">
                            <i class="fas fa-file-invoice-dollar me-2"></i> Novedades Nómina
                        </a>
    
                        <a class="nav-link" data-toggle="tab" href="#tab-sena" role="tab">
                            <i class="fas fa-users me-2"></i> Planta Personal SENA
                        </a>
                        <a class="nav-link" data-toggle="tab" href="#tab-productividad" role="tab">
                            <i class="fas fa-chart-line me-2"></i> Productividad
                        </a>
                        <a class="nav-link" data-toggle="tab" href="#tab-calidad" role="tab">
                            <i class="fas fa-check-circle me-2"></i> Calidad
                    </div>
                </nav>  

                <div class="tab-content main-tabs-body p-4" id="nav-tabContent">

                    {{-- ■ TAB: PRECONTRACTUAL ■ --}}
                    <div class="tab-pane fade show active" id="tab-precontractual" role="tabpanel">
                        <div class="tab-section-header mb-4">
                            <h5 class="fw-bold mb-1">Documentación Precontractual</h5>
                            <p class="text-muted small mb-0">Requisitos y soportes previos a la vinculación oficial.</p>
                        </div>
                        @forelse($empleado->etapaPrecontractuales as $ep)    
                            <div class="modern-doc-block mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="doc-date"><i class="far fa-calendar-alt me-2 text-primary"></i> Registrado el {{ \Carbon\Carbon::parse($ep->fecha_registro)->format('d/m/Y') }}</div>
                                    <span class="badge {{ $ep->estado == 1 ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} rounded-pill px-3">
                                        {{ $ep->estado == 1 ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                                @foreach($ep->documentos as $doc)
                                    <div class="file-item-pill">
                                        <div class="file-info">
                                            <i class="fas fa-file-invoice text-danger me-3"></i>
                                            <span class="file-name">{{ $doc->nombre_original }}</span>
                                        </div>
                                        <div class="file-btn-group">
                                            <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" class="file-btn view" title="Ver archivo"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('admin.documentos.download', $doc->id) }}" class="file-btn download" title="Descargar"><i class="fas fa-download"></i></a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @empty
                            <div class="empty-tab-state">
                                <i class="fas fa-folder-open mb-3"></i>
                                <p>No hay registros precontractuales para este empleado.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- ■ TAB: CONTRACTUAL ■ --}}
                    <div class="tab-pane fade" id="tab-contractual" role="tabpanel">
                        <div class="tab-section-header mb-4">
                            <h5 class="fw-bold mb-1">Historial Contractual</h5>
                            <p class="text-muted small mb-0">Contratos, renovaciones y anexos laborales.</p>
                        </div>
                        @php
                            $normalContracts = $empleado->etapaContractuales->filter(function($c) {
                                return !in_array($c->tipo_contrato, ['Antecedentes Judiciales', 'Rethus - Aplica', 'Rethus - No aplica', 'Vacunas', 'Otros']);
                            })->sortByDesc('fecha_inicio');

                            $antecedentes = $empleado->etapaContractuales->filter(function($c) {
                                return $c->tipo_contrato === 'Antecedentes Judiciales';
                            })->sortByDesc('fecha_inicio')->groupBy(fn($i) => \Carbon\Carbon::parse($i->fecha_inicio)->format('Y'));

                            $rethus = $empleado->etapaContractuales->filter(function($c) {
                                return str_starts_with($c->tipo_contrato, 'Rethus');
                            })->sortByDesc('fecha_inicio')->groupBy(fn($i) => \Carbon\Carbon::parse($i->fecha_inicio)->format('Y'));

                            $vacunas = $empleado->etapaContractuales->filter(function($c) {
                                return $c->tipo_contrato === 'Vacunas';
                            })->sortByDesc('fecha_inicio');

                            $otrosDocs = $empleado->etapaContractuales->filter(function($c) {
                                return $c->tipo_contrato === 'Otros';
                            })->sortByDesc('fecha_inicio');

                            $totalOtrosCount = $antecedentes->flatten()->count() + $rethus->flatten()->count() + $vacunas->count() + $otrosDocs->count();
                        @endphp

                        @forelse($normalContracts as $c)
                            @php $badge = $c->getStatusBadge($c->fecha_fin); @endphp
                            <div class="modern-doc-block mb-3">
                                <div class="row align-items-center">
                                    <div class="col-md">
                                        <h6 class="fw-bold text-primary mb-1">{{ $c->tipo_contrato }}</h6>
                                        <div class="text-muted small">
                                            <i class="far fa-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($c->fecha_inicio)->format('d/m/Y') }}
                                            &mdash;
                                            {{ $c->fecha_fin ? \Carbon\Carbon::parse($c->fecha_fin)->format('d/m/Y') : 'Indefinido' }}
                                            @if($c->salario && $c->salario > 0)
                                                <i class="fas fa-coins me-1"></i> $ {{ number_format($c->salario) }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-auto text-end mt-2 mt-md-0">
                                        <span class="badge {{ $c->estado == 1 ? $badge['class'] : 'bg-soft-danger text-danger' }} rounded-pill px-3">
                                            {{ $c->estado == 1 ? $badge['label'] : 'Inactivo' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    @foreach($c->documentos as $doc)
                                        <div class="file-item-pill">
                                            <div class="file-info">
                                                <i class="fas fa-file-contract text-success me-3"></i>
                                                <span class="file-name">{{ $doc->nombre_original }}</span>
                                            </div>
                                            <div class="file-btn-group">
                                                <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" class="file-btn view"><i class="fas fa-eye"></i></a>
                                                <a href="{{ route('admin.documentos.download', $doc->id) }}" class="file-btn download"><i class="fas fa-download"></i></a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="empty-tab-state mb-4"><i class="fas fa-handshake mb-3"></i><p>No se encontraron contratos registrados.</p></div>
                        @endforelse

                        <hr class="my-4" style="border-color: #e2e8f0;">

                        <div class="tab-section-header mb-4">
                            <h5 class="fw-bold mb-1">Documentos Adicionales</h5>
                            <p class="text-muted small mb-0">Otros documentos relacionados a la etapa contractual.</p>
                        </div>

                        {{-- CARPETA: ANTECEDENTES JUDICIALES --}}
                        <div class="folder-container mb-3">
                            <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-antecedentes">
                                <div class="folder-info">
                                    <i class="fas fa-folder folder-icon text-warning"></i>
                                    <span class="folder-title">Antecedentes Judiciales</span>
                                    <span class="folder-count">{{ $antecedentes->flatten()->count() }} registros</span>
                                </div>
                                <i class="fas fa-chevron-down folder-arrow"></i>
                            </div>
                            <div id="folder-antecedentes" class="collapse folder-content">
                                @forelse($antecedentes as $year => $yearItems)      
                                    <div class="folder-container mb-2" style="border-style: dashed;">
                                        <div class="folder-header collapsed py-2" data-toggle="collapse" data-target="#folder-antecedentes-{{ $year }}">
                                                    <div class="folder-info">
                                                        <i class="fas fa-folder-open folder-icon small text-muted"></i>
                                                        <span class="folder-title" style="font-size: 0.9rem;">Año {{ $year }}</span>
                                                    </div>
                                                    <i class="fas fa-chevron-down folder-arrow small"></i>
                                                </div>
                                                <div id="folder-antecedentes-{{ $year }}" class="collapse folder-content py-2">
                                                    @foreach($yearItems as $item)
                                                        <div class="modern-doc-block border-0 bg-light mb-2 p-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($item->fecha_inicio)->format('d/m/Y') }}</small>
                                                            </div>
                                                            @foreach($item->documentos as $doc)
                                                                <div class="file-item-pill py-2">
                                                                    <div class="file-info"><i class="fas fa-file-pdf text-danger me-2"></i><span class="file-name" style="font-size: 0.8rem;">{{ $doc->nombre_original }}</span></div>
                                                                    <div class="file-btn-group">
                                                                        <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" class="file-btn view btn-sm"><i class="fas fa-eye"></i></a>
                                                                        <a href="{{ route('admin.documentos.download', $doc->id) }}" class="file-btn download btn-sm"><i class="fas fa-download"></i></a>
                                                                     </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-3 text-muted small italic">
                                                <i class="fas fa-info-circle me-1"></i> No hay documentos de Antecedentes Judiciales.
                                            </div>
                                        @endforelse
                            </div>
                        </div>

                        {{-- CARPETA: RETHUS --}}
                        <div class="folder-container mb-3">
                            <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-rethus">
                                <div class="folder-info">
                                    <i class="fas fa-folder folder-icon text-warning"></i>
                                    <span class="folder-title">Rethus</span>
                                    <span class="folder-count">{{ $rethus->flatten()->count() }} registros</span>
                                </div>
                                <i class="fas fa-chevron-down folder-arrow"></i>
                            </div>
                            <div id="folder-rethus" class="collapse folder-content">
                                @forelse($rethus as $year => $yearItems)
                                    <div class="folder-container mb-2" style="border-style: dashed;">
                                        <div class="folder-header collapsed py-2" data-toggle="collapse" data-target="#folder-rethus-{{ $year }}">
                                                    <div class="folder-info">
                                                        <i class="fas fa-folder-open folder-icon small text-muted"></i>
                                                        <span class="folder-title" style="font-size: 0.9rem;">Año {{ $year }}</span>
                                                    </div>
                                                    <i class="fas fa-chevron-down folder-arrow small"></i>
                                                </div>
                                                <div id="folder-rethus-{{ $year }}" class="collapse folder-content py-2">
                                                    @foreach($yearItems as $item)
                                                        <div class="modern-doc-block border-0 bg-light mb-2 p-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($item->fecha_inicio)->format('d/m/Y') }}</small>
                                                                @if($item->tipo_contrato == 'Rethus - Aplica')
                                                                    <span class="badge bg-soft-success text-success rounded-pill px-2 small">
                                                                        ✅ Aplica
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-soft-danger text-danger rounded-pill px-2 small">
                                                                        ❌ No aplica
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            @foreach($item->documentos as $doc)
                                                                <div class="file-item-pill py-2 mt-2">
                                                                    <div class="file-info"><i class="fas fa-file-pdf text-danger me-2"></i><span class="file-name" style="font-size: 0.8rem;">{{ $doc->nombre_original }}</span></div>
                                                                    <div class="file-btn-group">
                                                                        <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" class="file-btn view btn-sm"><i class="fas fa-eye"></i></a>
                                                                        <a href="{{ route('admin.documentos.download', $doc->id) }}" class="file-btn download btn-sm"><i class="fas fa-download"></i></a>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-3 text-muted small italic">
                                                <i class="fas fa-info-circle me-1"></i> No hay registros de Rethus.
                                            </div>
                                        @endforelse
                            </div>
                        </div>

                        {{-- CARPETA: VACUNAS --}}
                        <div class="folder-container mb-3">
                            <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-vacunas">
                                <div class="folder-info">
                                    <i class="fas fa-folder folder-icon text-warning"></i>
                                    <span class="folder-title">Vacunas</span>
                                    <span class="folder-count">{{ $vacunas->count() }} documentos</span>
                                </div>
                                <i class="fas fa-chevron-down folder-arrow"></i>
                            </div>
                            <div id="folder-vacunas" class="collapse folder-content">
                                @forelse($vacunas as $item)
                                            <div class="modern-doc-block border-0 bg-light mb-2 p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($item->fecha_inicio)->format('d/m/Y') }}</small>
                                                </div>
                                                @foreach($item->documentos as $doc)
                                                    <div class="file-item-pill py-2">
                                                        <div class="file-info"><i class="fas fa-file-pdf text-danger me-2"></i><span class="file-name" style="font-size: 0.8rem;">{{ $doc->nombre_original }}</span></div>
                                                        <div class="file-btn-group">
                                                            <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" class="file-btn view btn-sm"><i class="fas fa-eye"></i></a>
                                                            <a href="{{ route('admin.documentos.download', $doc->id) }}" class="file-btn download btn-sm"><i class="fas fa-download"></i></a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @empty
                                            <div class="text-center py-3 text-muted small italic">
                                                <i class="fas fa-info-circle me-1"></i> No hay documentos de Vacunas.
                                            </div>
                                        @endforelse
                            </div>
                        </div>

                        {{-- CARPETA: OTROS DOCUMENTOS --}}
                        <div class="folder-container mb-3">
                            <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-otros-docs">
                                <div class="folder-info">
                                    <i class="fas fa-folder folder-icon text-warning"></i>
                                    <span class="folder-title">Otros</span>
                                    <span class="folder-count">{{ $otrosDocs->count() }} documentos</span>
                                </div>
                                <i class="fas fa-chevron-down folder-arrow"></i>
                            </div>
                            <div id="folder-otros-docs" class="collapse folder-content">
                                @forelse($otrosDocs as $item)
                                            <div class="modern-doc-block border-0 bg-light mb-2 p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($item->fecha_inicio)->format('d/m/Y') }}</small>
                                                </div>
                                                @foreach($item->documentos as $doc)
                                                    <div class="file-item-pill py-2">
                                                        <div class="file-info"><i class="fas fa-file-pdf text-danger me-2"></i><span class="file-name" style="font-size: 0.8rem;">{{ $doc->nombre_original }}</span></div>
                                                        <div class="file-btn-group">
                                                            <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" class="file-btn view btn-sm"><i class="fas fa-eye"></i></a>
                                                            <a href="{{ route('admin.documentos.download', $doc->id) }}" class="file-btn download btn-sm"><i class="fas fa-download"></i></a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @empty
                                            <div class="text-center py-3 text-muted small italic">
                                                <i class="fas fa-info-circle me-1"></i> No hay otros documentos registrados.
                                            </div>
                                        @endforelse
                            </div>
                        </div>

                    </div>

                    {{-- ■ TAB: Seguridad y Salud en el Trabajo■ --}}
                    <div class="tab-pane fade" id="tab-sst" role="tabpanel">
                        <div class="tab-section-header mb-4">
                            <h5 class="fw-bold mb-1">Seguridad y Salud en el Trabajo</h5>
                            <p class="text-muted small mb-0">Exámenes médicos, EPPs y documentos preventivos.</p>
                        </div>
                        @php
                            $predefinedTypes = ['Ingresos', 'Periódicos', 'ARL', 'Retiros'];

                            $actualGroups = $empleado->seguridadSaludTrabajo
                                ->sortByDesc('fecha')
                                ->groupBy(function($item) {
                                    $tipo = strtolower($item->tipo_documento);

                                    if (str_contains($tipo, 'ingreso')) return 'Ingresos';
                                    if (str_contains($tipo, 'periódic') || str_contains($tipo, 'periodic')) return 'Periódicos';
                                    if (str_contains($tipo, 'retiro')) return 'Retiros';
                                    if (str_contains($tipo, 'arl')) return 'ARL';

                                    return 'Otros';
                                });

                            $sstGroups = collect();

                            foreach ($predefinedTypes as $type) {
                                $sstGroups->put($type, $actualGroups->get($type, collect()));
                            }
                        @endphp


                        @foreach($sstGroups as $groupName => $items)
                            @php
                                $groupIcon = match($groupName) {
                                    'Ingresos'   => ['icon' => 'fas fa-folder', 'color' => 'text-warning'],
                                    'Periódicos' => ['icon' => 'fas fa-folder', 'color' => 'text-warning'],
                                    'ARL'        => ['icon' => 'fas fa-folder', 'color' => 'text-warning'],
                                    'Retiros'    => ['icon' => 'fas fa-folder', 'color' => 'text-warning'],
                                    default      => ['icon' => 'fas fa-folder', 'color' => 'text-warning']
                                };

                                $hasYears = in_array($groupName, ['ARL', 'Periódicos']);

                                $yearGroups = $hasYears
                                    ? $items->groupBy(fn($i) => \Carbon\Carbon::parse($i->fecha)->format('Y'))
                                    : collect(['all' => $items]);
                            @endphp

                            <div class="folder-container mb-3">
                                <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-sst-{{ Str::slug($groupName) }}">
                                    <div class="folder-info">
                                        <i class="{{ $groupIcon['icon'] }} folder-icon {{ $groupIcon['color'] }}"></i>
                                        <span class="folder-title">{{ $groupName }}</span>
                                        <span class="folder-count">{{ $items->count() }} registros</span>
                                    </div>
                                    <i class="fas fa-chevron-down folder-arrow"></i>
                                </div>
                                <div id="folder-sst-{{ Str::slug($groupName) }}" class="collapse folder-content">
                                    @forelse($yearGroups as $year => $yearItems)
                                        @if($hasYears && $year !== 'all')
                                            <div class="folder-container mb-2" style="border-style: dashed;">
                                                <div class="folder-header collapsed py-2" data-toggle="collapse" data-target="#folder-sst-{{ Str::slug($groupName) }}-{{ $year }}">
                                                    <div class="folder-info">
                                                        <i class="fas fa-folder-open folder-icon small text-muted"></i>
                                                        <span class="folder-title" style="font-size: 0.9rem;">Año {{ $year }}</span>
                                                    </div>
                                                    <i class="fas fa-chevron-down folder-arrow small"></i>
                                                </div>
                                                <div id="folder-sst-{{ Str::slug($groupName) }}-{{ $year }}" class="collapse folder-content py-2">
                                                    @foreach($yearItems as $sst)
                                                        @php $sstBadge = $sst->getStatusBadge($sst->fecha); @endphp
                                                        <div class="modern-doc-block border-0 bg-light mb-2 p-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <small class="text-muted"><i class="far fa-calend
                                                                ar-alt me-1"></i> {{ \Carbon\Carbon::parse($sst->fecha)->format('d/m/Y') }}</small>
                                                                <span class="badge {{ $sst->estado == 1 ? $sstBadge['class'] : 'bg-soft-danger text-danger' }} rounded-pill px-2 small">
                                                                    {{ $sst->estado == 1 ? $sstBadge['label'] : 'Inactivo' }}
                                                                </span>
                                                            </div>
                                                            @foreach($sst->documentos as $doc)
                                                                <div class="file-item-pill py-2">
                                                                    <div class="file-info"><i class="fas fa-file-pdf text-danger me-2"></i><span class="file-name" style="font-size: 0.8rem;">{{ $doc->nombre_original }}</span></div>
                                                                    <div class="file-btn-group">
                                                                        <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" class="file-btn view btn-sm"><i class="fas fa-eye"></i></a>
                                                                        <a href="{{ route('admin.documentos.download', $doc->id) }}" class="file-btn download btn-sm"><i class="fas fa-download"></i></a>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                       
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            @foreach($yearItems as $sst)
                                                @php $sstBadge = $sst->getStatusBadge($sst->fecha); @endphp
                                                <div class="modern-doc-block border-0 bg-light mb-2 p-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($sst->fecha)->format('d/m/Y') }}</small>
                                                        <span class="badge {{ $sst->estado == 1 ? $sstBadge['class'] : 'bg-soft-danger text-danger' }} rounded-pill px-2 small">
                                                            {{ $sst->estado == 1 ? $sstBadge['label'] : 'Inactivo' }}
                                                        </span>
                                                    </div>
                                                    @foreach($sst->documentos as $doc)
                                                        <div class="file-item-pill py-2">
                                                            <div class="file-info"><i class="fas fa-file-pdf text-danger me-2"></i><span class="file-name" style="font-size: 0.8rem;">{{ $doc->nombre_original }}</span></div>
                                                            <div class="file-btn-group">
                                                                <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" class="file-btn view btn-sm"><i class="fas fa-eye"></i></a>
                                                                <a href="{{ route('admin.documentos.download', $doc->id) }}" class="file-btn download btn-sm"><i class="fas fa-download"></i></a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        @endif
                                    @empty
                                        <div class="text-center py-3 text-muted small italic">
                                            <i class="fas fa-info-circle me-1"></i> No hay documentos disponibles en esta categoría.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- ■ TAB: EVALUACIONES ■ --}}
                    <div class="tab-pane fade" id="tab-evaluaciones" role="tabpanel">
                        <div class="tab-section-header mb-4">
                            <h5 class="fw-bold mb-1">Evaluaciones de Desempeño</h5>
                            <p class="text-muted small mb-0">Expediente histórico de evaluaciones y rendimiento laboral.</p>
                        </div>

                        @forelse($evaluacionesPorAño as $year => $evaluaciones)
                            <div class="folder-container mb-3">
                                <div class="folder-header {{ $loop->first ? '' : 'collapsed' }}" data-toggle="collapse" data-target="#folder-eval-{{ $year }}">
                                    <div class="folder-info">
                                        <i class="fas fa-folder folder-icon text-warning"></i>
                                        <span class="folder-title">Año {{ $year }}</span>
                                        <span class="folder-count">{{ $evaluaciones->count() }} documentos</span>
                                    </div>
                                    <i class="fas fa-chevron-down folder-arrow"></i>
                                </div>
                                <div id="folder-eval-{{ $year }}" class="collapse {{ $loop->first ? 'show' : '' }} folder-content">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle border-0 mb-0">
                                            <thead>
                                                <tr class="text-muted small text-uppercase fw-bold" style="background: #f8fafc; border-bottom: 2px solid #f1f5f9;">
                                                    <th class="border-0 px-3 py-3">Nombre del Documento</th>
                                                    <th class="border-0 px-3 py-3">Fecha de Creación</th>
                                                    <th class="border-0 px-3 py-3">Estado</th>
                                                    <th class="border-0 px-3 py-3 text-end">Opciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($evaluaciones as $ev)
                                                    @php
                                                        $statusMap = [
                                                            1 => ['lbl' => 'Pendiente', 'cls' => 'bg-soft-warning text-warning'],
                                                            2 => ['lbl' => 'En proceso', 'cls' => 'bg-soft-primary text-primary'],
                                                            3 => ['lbl' => 'Finalizada', 'cls' => 'bg-soft-success text-success']
                                                        ];
                                                        $st = $statusMap[$ev->estado] ?? ['lbl' => 'Registrada', 'cls' => 'bg-light text-muted'];
                                                    @endphp
                                                    <tr class="eval-row-hover">
                                                        <td class="px-3 py-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="eval-icon-circle me-3">
                                                                    <i class="fas fa-file-alt text-primary-modern"></i>
                                                                </div>
                                                                <div>
                                                                    <span class="fw-bold d-block text-dark">{{ $ev->observaciones ? Str::limit($ev->observaciones, 50) : 'Evaluación de Desempeño' }}</span>
                                                                    <small class="text-muted">ID: #EVAL-{{ str_pad($ev->id, 4, '0', STR_PAD_LEFT) }}</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-3 py-3">
                                                            <span class="text-muted small fw-bold"><i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($ev->fecha)->format('d/m/Y') }}</span>
                                                        </td>
                                                        <td class="px-3 py-3">
                                                            <span class="badge {{ $st['cls'] }} rounded-pill px-3 py-2 small fw-bold">
                                                                {{ $st['lbl'] }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-3 text-end">
                                                            <div class="d-flex justify-content-end gap-2">
                                                                @if($ev->documentos->count() > 0)
                                                                    <a href="{{ route('admin.documentos.view', $ev->documentos->first()->id) }}" target="_blank" class="file-btn view btn-sm" title="Visualizar">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    <a href="{{ route('admin.documentos.download', $ev->documentos->first()->id) }}" class="file-btn download btn-sm" title="Descargar">
                                                                        <i class="fas fa-download"></i>
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted small italic">Sin adjunto</span>
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
                        @empty
                            <div class="empty-tab-state">
                                <div class="empty-icon-box mb-4">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <h6 class="fw-bold">No hay evaluaciones registradas</h6>
                                <p class="text-muted">Este empleado no cuenta con registros de evaluación en su expediente.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- ■ TAB: FORMACIÓN ■ --}}
                    <div class="tab-pane fade" id="tab-formacion" role="tabpanel">
                        <div class="tab-section-header mb-4">
                            <h5 class="fw-bold mb-1">Registro de Formación Académica</h5>
                            <p class="text-muted small mb-0">Expediente digital de capacitaciones y certificaciones.</p>
                        </div>

                        @forelse($formacionesPorAnioMes as $year => $months)
                            <div class="folder-container mb-3">
                                <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-form-{{ $year }}">
                                    <div class="folder-info">
                                        <i class="fas fa-folder folder-icon text-warning"></i>
                                        <span class="folder-title">Año {{ $year }}</span>
                                        <span class="folder-count">
                                            {{ $months->flatten(1)->count() }} registros
                                        </span>
                                    </div>
                                    <i class="fas fa-chevron-down folder-arrow"></i>
                                </div>
                                <div id="folder-form-{{ $year }}" class="collapse folder-content">
                                    @foreach($months as $monthNum => $items)
                                        <div class="folder-container mb-2" style="border-style: dashed;">
                                            <div class="folder-header collapsed py-2" data-toggle="collapse" data-target="#folder-form-{{ $year }}-{{ $monthNum }}">
                                                <div class="folder-info">
                                                    <i class="fas fa-folder-open folder-icon small text-muted"></i>
                                                    <span class="folder-title" style="font-size: 0.9rem;">{{ $mesesNombre[$monthNum] }}</span>
                                                    <span class="folder-count" style="font-size: 0.75rem; padding: 2px 8px;">
                                                        {{ $items->count() }} registros
                                                    </span>
                                                </div>
                                                <i class="fas fa-chevron-down folder-arrow small"></i>
                                            </div>
                                            <div id="folder-form-{{ $year }}-{{ $monthNum }}" class="collapse folder-content py-2">
                                                @foreach($items as $f)
                                                    <div class="modern-doc-block">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <h6 class="fw-bold text-dark mb-0"><i class="fas fa-graduation-cap me-2 text-muted"></i>{{ $f->nombre_curso }}</h6>
                                                            @if($f->vence == 1)
                                                                @if($f->fecha_fin)
                                                                    @php $badge = $f->getStatusBadge($f->fecha_fin); @endphp
                                                                    <span class="badge {{ $badge['class'] }} rounded-pill px-3">
                                                                        <i class="fas fa-clock me-1"></i> {{ $badge['label'] }}
                                                                    </span>
                                                                @endif
                                                            @else
                                                                <span class="badge bg-soft-success text-success rounded-pill px-3">Permanente</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-muted small mb-3">
                                                            <i class="far fa-calendar-alt me-1"></i> 
                                                            {{ $f->fecha_inicio ? \Carbon\Carbon::parse($f->fecha_inicio)->format('d/m/Y') : 'N/A' }}
                                                            @if($f->vence == 1 && $f->fecha_fin)
                                                                <span class="mx-2 text-silver">|</span>
                                                                <span class="fw-bold text-danger">Vence: {{ \Carbon\Carbon::parse($f->fecha_fin)->format('d/m/Y') }}</span>
                                                            @endif
                                                        </div>
                                                        @foreach($f->documentos as $doc)
                                                            <div class="file-item-pill">
                                                                <div class="file-info">
                                                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                                                    <span class="file-name">{{ $doc->nombre_original }}</span>
                                                                </div>
                                                                <div class="file-btn-group">
                                                                    <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" class="file-btn view" title="Ver"><i class="fas fa-eye"></i></a>
                                                                    <a href="{{ route('admin.documentos.download', $doc->id) }}" class="file-btn download" title="Descargar"><i class="fas fa-download"></i></a>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="empty-tab-state text-center py-4">
                                <div class="empty-icon-box mb-3 mx-auto">
                                    <i class="fas fa-graduation-cap text-muted fa-2x"></i>
                                </div>
                                <p class="text-muted">No hay registros de formación académica para este empleado.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- ■ TAB: CERTIFICACIONES ■ --}}
                    <div class="tab-pane fade" id="tab-certificaciones" role="tabpanel">
                        <div class="tab-section-header mb-4">
                            <h5 class="fw-bold mb-1">Certificaciones Oficiales</h5>
                            <p class="text-muted small mb-0">Certificados de idoneidad, licencias y documentos legales de capacitación.</p>
                        </div>

                        @forelse($certificacionesPorAnioMes as $year => $months)
                            <div class="folder-container mb-3">
                                <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-cert-{{ $year }}">
                                    <div class="folder-info">
                                        <i class="fas fa-folder folder-icon text-warning"></i>
                                        <span class="folder-title">Año {{ $year }}</span>
                                        <span class="folder-count">
                                            {{ $months->flatten(1)->count() }} registros
                                        </span>
                                    </div>
                                    <i class="fas fa-chevron-down folder-arrow"></i>
                                </div>
                                <div id="folder-cert-{{ $year }}" class="collapse folder-content">
                                    @foreach($months as $monthNum => $items)
                                        <div class="folder-container mb-2" style="border-style: dashed;">
                                            <div class="folder-header collapsed py-2" data-toggle="collapse" data-target="#folder-cert-{{ $year }}-{{ $monthNum }}">
                                                <div class="folder-info">
                                                    <i class="fas fa-folder-open folder-icon small text-muted"></i>
                                                    <span class="folder-title" style="font-size: 0.9rem;">{{ $mesesNombre[$monthNum] }}</span>
                                                    <span class="folder-count" style="font-size: 0.75rem; padding: 2px 8px;">
                                                        {{ $items->count() }} registros
                                                    </span>
                                                </div>
                                                <i class="fas fa-chevron-down folder-arrow small"></i>
                                            </div>
                                            <div id="folder-cert-{{ $year }}-{{ $monthNum }}" class="collapse folder-content py-2">
                                                @foreach($items as $cert)
                                                    @php $badge = $cert->getStatusBadge($cert->fecha_vencimiento); @endphp
                                                    <div class="modern-doc-block mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                            <div>
                                                                <h6 class="fw-bold text-dark mb-1">
                                                                    <i class="fas fa-certificate me-2 text-primary"></i>{{ $cert->nombre_certificacion }}
                                                                </h6>
                                                                <div class="text-muted small">
                                                                    <i class="fas fa-university me-1"></i> {{ $cert->institucion }}
                                                                    @if($cert->codigo_certificado)
                                                                        <span class="mx-2">|</span>
                                                                        <i class="fas fa-hashtag me-1"></i> {{ $cert->codigo_certificado }}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <span class="badge {{ $cert->estado == 1 ? $badge['class'] : 'bg-soft-danger text-danger' }} rounded-pill px-3">
                                                                <i class="{{ $badge['icon'] }} me-1"></i> {{ $cert->estado == 1 ? $badge['label'] : 'Inactivo' }}
                                                            </span>
                                                        </div>
                                                        
                                                        <div class="row align-items-center">
                                                            <div class="col-md">
                                                                <div class="text-muted small">
                                                                    <span class="me-3">
                                                                        <i class="far fa-calendar-alt me-1"></i> 
                                                                        <strong>Expedición:</strong> {{ $cert->fecha_expedicion->format('d/m/Y') }}
                                                                    </span>
                                                                    <span>
                                                                        <i class="fas fa-hourglass-end me-1"></i> 
                                                                        <strong>Vencimiento:</strong> {{ $cert->fecha_vencimiento ? $cert->fecha_vencimiento->format('d/m/Y') : 'Permanente' }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-auto text-end mt-3 mt-md-0">
                                                                @if($cert->archivo)
                                                                    <div class="file-btn-group">
                                                                        <a href="{{ asset('storage/' . $cert->archivo) }}" target="_blank" class="file-btn view" title="Ver Certificado">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                        <a href="{{ asset('storage/' . $cert->archivo) }}" download class="file-btn download" title="Descargar">
                                                                            <i class="fas fa-download"></i>
                                                                        </a>
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted small italic">Sin soporte digital</span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        @if($cert->observaciones)
                                                            <div class="mt-3 p-2 bg-light rounded border-start border-4 border-primary" style="font-size: 0.85rem;">
                                                                <i class="fas fa-info-circle me-1 text-primary"></i> <strong>Nota:</strong> {{ $cert->observaciones }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="empty-tab-state">
                                <i class="fas fa-certificate mb-3"></i>
                                <p>No se han registrado certificaciones para este empleado.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- ■ TAB: COMUNICACIONES ■ --}}
                    <div class="tab-pane fade" id="tab-comunicaciones" role="tabpanel">
                        <div class="tab-section-header mb-4">
                            <h5 class="fw-bold mb-1">Comunicaciones Internas</h5>
                            <p class="text-muted small mb-0">Mensajes y notificaciones oficiales emitidas.</p>
                        </div>

                        @php
                            $comGroups = $empleado->comunicaciones->sortByDesc('fecha')->groupBy(fn($comp) => \Carbon\Carbon::parse($comp->fecha)->format('Y'));
                        @endphp

                        @forelse($comGroups as $year => $items)
                            <div class="folder-container">
                                <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-com-{{ $year }}">
                                    <div class="folder-info">
                                        <i class="fas fa-folder folder-icon" style="color: #4f46e5;"></i>
                                        <span class="folder-title">Año {{ $year }}</span>
                                        <span class="folder-count">{{ $items->count() }} documentos</span>
                                    </div>
                                    <i class="fas fa-chevron-down folder-arrow"></i>
                                </div>
                                <div id="folder-com-{{ $year }}" class="collapse folder-content">
                                    @foreach($items as $com)
                                        <div class="modern-doc-block">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="fw-bold mb-1 text-dark">{{ $com->asunto }}</h6>
                                                    <p class="text-muted small mb-0">{{ Str::limit($com->mensaje, 150) }}</p>
                                                </div>
                                                <small class="text-muted text-nowrap"><i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($com->fecha)->format('d/m/Y') }}</small>
                                            </div>
                                            @foreach($com->documentos as $doc)
                                            <div class="file-item-pill mt-3">
                                                    <div class="file-info"><i class="fas fa-paperclip text-info me-3"></i><span class="file-name">{{ $doc->nombre_original }}</span></div>
                                                    <div class="file-btn-group">
                                                        <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" class="file-btn view" title="Ver adjunto"><i class="fas fa-eye"></i></a>
                                                        <a href="{{ route('admin.documentos.download', $doc->id) }}" class="file-btn download" title="Descargar"><i class="fas fa-download"></i></a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="empty-tab-state"><i class="fas fa-bullhorn mb-3"></i><p>No se han emitido comunicaciones para este empleado.</p></div>
                        @endforelse
                    </div>

                    {{-- ■ TAB: SOLICITUDES ■ --}}
                    <div class="tab-pane fade" id="tab-solicitudes" role="tabpanel">
                        <div class="tab-section-header mb-4">
                            <h5 class="fw-bold mb-1">Solicitudes y Requerimientos</h5>
                            <p class="text-muted small mb-0">Estado de vacaciones, permisos y solicitudes.</p>
                        </div>
                        @php
                            $tiposSolicitud = ['Vacaciones', 'Solicitud', 'Ausentismo', 'Otro'];
                            
                            $solicitudesAgrupadas = collect($tiposSolicitud)->mapWithKeys(function($tipo) {
                                return [$tipo => collect()];
                            });
                            
                            foreach($empleado->solicitudes->sortByDesc('fecha') as $sol) {
                                $tipoCarpeta = in_array(ucfirst($sol->tipo), ['Vacaciones', 'Solicitud', 'Ausentismo']) ? ucfirst($sol->tipo) : 'Otro';
                                $solicitudesAgrupadas[$tipoCarpeta]->push($sol);
                            }
                        @endphp
                        
                        @foreach($solicitudesAgrupadas as $tipo => $solicitudes)
                            <div class="folder-container mb-3">
                                <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-sol-{{ Str::slug($tipo) }}">
                                    <div class="folder-info">
                                        <i class="fas fa-folder folder-icon text-warning"></i>
                                        <span class="folder-title">{{ $tipo }}</span>
                                        @php
                                            $totalFiles = $solicitudes->sum(function($s) { return $s->documentos->count(); });
                                        @endphp
                                        <span class="folder-count">{{ $totalFiles }} archivos</span>
                                    </div>
                                    <i class="fas fa-chevron-down folder-arrow"></i>
                                </div>
                                <div id="folder-sol-{{ Str::slug($tipo) }}" class="collapse folder-content">
                                    @if($solicitudes->count() > 0)
                                        @php $hasAnyFile = false; @endphp
                                        @foreach($solicitudes as $sol)
                                            @if($sol->documentos->count() > 0)
                                                @php $hasAnyFile = true; @endphp
                                                <div class="modern-doc-block border-0 bg-light mb-2 p-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <div>
                                                            <span class="type-pill me-2">{{ strtoupper($sol->tipo) }}</span>
                                                            <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($sol->fecha)->format('d/m/Y') }}</small>
                                                        </div>
                                                        @php
                                                            $statusConf = [
                                                                'aprobado'  => ['cls'=>'bg-soft-success text-success', 'icon'=>'fa-check-circle'],
                                                                'rechazado' => ['cls'=>'bg-soft-danger text-danger', 'icon'=>'fa-times-circle'],
                                                                'pendiente' => ['cls'=>'bg-soft-warning text-warning', 'icon'=>'fa-clock']
                                                            ][strtolower($sol->estado)] ?? ['cls'=>'bg-soft-secondary text-secondary', 'icon'=>'fa-question-circle'];
                                                        @endphp
                                                        <span class="badge {{ $statusConf['cls'] }} rounded-pill px-3">
                                                            <i class="fas {{ $statusConf['icon'] }} me-1"></i> {{ ucfirst($sol->estado) }}
                                                        </span>
                                                    </div>
                                                    @foreach($sol->documentos as $doc)
                                                        <div class="file-item-pill py-2 mt-2">
                                                            <div class="file-info"><i class="fas fa-file-pdf text-danger me-2"></i><span class="file-name" style="font-size: 0.8rem;">{{ $doc->nombre_original }}</span></div>
                                                            <div class="file-btn-group">
                                                                <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" class="file-btn view btn-sm"><i class="fas fa-eye"></i></a>
                                                                <a href="{{ route('admin.documentos.download', $doc->id) }}" class="file-btn download btn-sm"><i class="fas fa-download"></i></a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endforeach
                                        
                                        @if(!$hasAnyFile)
                                            <div class="text-center py-3 text-muted small italic">
                                                <i class="fas fa-info-circle me-1"></i> No hay archivos registrados en esta carpeta.
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-3 text-muted small italic">
                                            <i class="fas fa-info-circle me-1"></i> No hay archivos registrados en esta carpeta.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- ■ TAB: DOTACIÓN ■ --}}
                    <div class="tab-pane fade" id="tab-dotacion" role="tabpanel">
                        <div class="tab-section-header mb-4">
                            <h5 class="fw-bold mb-1">Registro de Dotación</h5>
                            <p class="text-muted small mb-0">Historial de uniformes, calzado y equipos entregados.</p>
                        </div>

                        @forelse($dotacionesPorAnioMes as $year => $months)
                            <div class="folder-container mb-3">
                                <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-dot-{{ $year }}">
                                    <div class="folder-info">
                                        <i class="fas fa-folder folder-icon text-warning"></i>
                                        <span class="folder-title">Año {{ $year }}</span>
                                        <span class="folder-count">
                                            {{ $months->flatten(1)->count() }} registros
                                        </span>
                                    </div>
                                    <i class="fas fa-chevron-down folder-arrow"></i>
                                </div>
                                <div id="folder-dot-{{ $year }}" class="collapse folder-content">
                                    @foreach($months as $monthNum => $items)
                                        <div class="folder-container mb-2" style="border-style: dashed;">
                                            <div class="folder-header collapsed py-2" data-toggle="collapse" data-target="#folder-dot-{{ $year }}-{{ $monthNum }}">
                                                <div class="folder-info">
                                                    <i class="fas fa-folder-open folder-icon small text-muted"></i>
                                                    <span class="folder-title" style="font-size: 0.9rem;">{{ $mesesNombre[$monthNum] }}</span>
                                                    <span class="folder-count" style="font-size: 0.75rem; padding: 2px 8px;">
                                                        {{ $items->count() }} registros
                                                    </span>
                                                </div>
                                                <i class="fas fa-chevron-down folder-arrow small"></i>
                                            </div>
                                            <div id="folder-dot-{{ $year }}-{{ $monthNum }}" class="collapse folder-content py-2">
                                                @foreach($items as $dot)
                                                    <div class="modern-doc-block mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                            <div>
                                                                <h6 class="fw-bold text-dark mb-1">
                                                                    <i class="fas fa-tshirt me-2 text-warning"></i>{{ $dot->tipo_dotacion }}
                                                                </h6>
                                                                <div class="text-muted small">
                                                                    <span class="me-3"><strong>Talla:</strong> {{ $dot->talla }}</span>
                                                                    <span><strong>Cantidad:</strong> {{ $dot->cantidad }} unds.</span>
                                                                </div>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="text-muted small mb-1">
                                                                    <i class="far fa-calendar-alt me-1"></i> {{ $dot->fecha->format('d/m/Y') }}
                                                                </div>
                                                                <span class="badge {{ $dot->estado == 1 ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} rounded-pill px-3">
                                                                    {{ $dot->estado == 1 ? 'Activo' : 'Inactivo' }}
                                                                </span>
                                                            </div>
                                                        </div>

                                                        @if($dot->observaciones)
                                                            <p class="text-muted small mb-3 italic">"{{ $dot->observaciones }}"</p>
                                                        @endif

                                                        @foreach($dot->documentos as $doc)
                                                            <div class="file-item-pill">
                                                                <div class="file-info">
                                                                    <i class="fas fa-file-invoice text-danger me-3"></i>
                                                                    <span class="file-name">{{ $doc->nombre_original }}</span>
                                                                </div>
                                                                <div class="file-btn-group">
                                                                    <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" class="file-btn view" title="Ver"><i class="fas fa-eye"></i></a>
                                                                    <a href="{{ route('admin.documentos.download', $doc->id) }}" class="file-btn download" title="Descargar"><i class="fas fa-download"></i></a>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="empty-tab-state">
                                <div class="empty-icon-box mb-4">
                                    <i class="fas fa-tshirt"></i>
                                </div>
                                <h6 class="fw-bold">Sin registros de dotación</h6>
                                <p class="text-muted">No se han registrado entregas de dotación para este empleado.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- ■ TAB: FECHAS ESPECIALES ■ --}}
                    <div class="tab-pane fade" id="tab-fechas" role="tabpanel">
                        <div class="tab-section-header mb-4">
                            <h5 class="fw-bold mb-1">Fechas Especiales</h5>
                            <p class="text-muted small mb-0">Permisos, eventos familiares y fechas importantes.</p>
                        </div>

                        @forelse($fechasEspecialesPorAnioMes as $year => $months)
                            <div class="folder-container mb-3">
                                <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-fechas-{{ $year }}">
                                    <div class="folder-info">
                                        <i class="fas fa-folder folder-icon text-warning"></i>
                                        <span class="folder-title">Año {{ $year }}</span>
                                        <span class="folder-count">
                                            {{ $months->flatten(1)->count() }} registros
                                        </span>
                                    </div>
                                    <i class="fas fa-chevron-down folder-arrow"></i>
                                </div>
                                <div id="folder-fechas-{{ $year }}" class="collapse folder-content">
                                    @foreach($months as $monthNum => $items)
                                        <div class="folder-container mb-2" style="border-style: dashed;">
                                            <div class="folder-header collapsed py-2" data-toggle="collapse" data-target="#folder-fechas-{{ $year }}-{{ $monthNum }}">
                                                <div class="folder-info">
                                                    <i class="fas fa-folder-open folder-icon small text-muted"></i>
                                                    <span class="folder-title" style="font-size: 0.9rem;">{{ $mesesNombre[$monthNum] }}</span>
                                                    <span class="folder-count" style="font-size: 0.75rem; padding: 2px 8px;">
                                                        {{ $items->count() }} registros
                                                    </span>
                                                </div>
                                                <i class="fas fa-chevron-down folder-arrow small"></i>
                                            </div>
                                            <div id="folder-fechas-{{ $year }}-{{ $monthNum }}" class="collapse folder-content py-2">
                                                @foreach($items as $fecha)
                                                    <div class="modern-doc-block mb-3">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="fw-bold mb-1">
                                                                    🎉 {{ $fecha->tipo }}
                                                                </h6>
                                                                <small class="text-muted d-block mb-2">
                                                                    <i class="far fa-calendar-alt me-1"></i>
                                                                    {{ \Carbon\Carbon::parse($fecha->fecha)->format('d/m/Y') }}
                                                                </small>
                                                            </div>
                                                            <div class="text-end">
                                                                <span class="badge rounded-pill bg-info mb-2">
                                                                    Evento Especial
                                                                </span>

                                                                @if($fecha->archivo)
                                                                    <div class="file-btn-group justify-content-end mt-2">
                                                                        <a href="{{ asset('storage/' . $fecha->archivo) }}"
                                                                        target="_blank"
                                                                        class="file-btn view"
                                                                        title="Ver archivo">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                        <a href="{{ asset('storage/' . $fecha->archivo) }}"
                                                                        download
                                                                        class="file-btn download"
                                                                        title="Descargar archivo">
                                                                            <i class="fas fa-download"></i>
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="empty-tab-state">
                                <i class="fas fa-calendar-star mb-3"></i>
                                <p>No hay fechas especiales registradas para este empleado.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- ■ TAB: PRODUCTIVIDAD ■ --}}
                    <div class="tab-pane fade" id="tab-productividad" role="tabpanel">
                        <div class="tab-section-header mb-4">
                            <h5 class="fw-bold mb-1">Productividad</h5>
                            <p class="text-muted small mb-0">Seguimientos, observaciones y actividades.</p>
                        </div>

                        @forelse($productividadesPorAnioMes as $year => $months)
                            <div class="folder-container mb-3">
                                <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-prod-{{ $year }}">
                                    <div class="folder-info">
                                        <i class="fas fa-folder folder-icon text-warning"></i>
                                        <span class="folder-title">Año {{ $year }}</span>
                                        <span class="folder-count">
                                            {{ $months->flatten(1)->count() }} registros
                                        </span>
                                    </div>
                                    <i class="fas fa-chevron-down folder-arrow"></i>
                                </div>
                                <div id="folder-prod-{{ $year }}" class="collapse folder-content">
                                    @foreach($months as $monthNum => $items)
                                        <div class="folder-container mb-2" style="border-style: dashed;">
                                            <div class="folder-header collapsed py-2" data-toggle="collapse" data-target="#folder-prod-{{ $year }}-{{ $monthNum }}">
                                                <div class="folder-info">
                                                    <i class="fas fa-folder-open folder-icon small text-muted"></i>
                                                    <span class="folder-title" style="font-size: 0.9rem;">{{ $mesesNombre[$monthNum] }}</span>
                                                    <span class="folder-count" style="font-size: 0.75rem; padding: 2px 8px;">
                                                        {{ $items->count() }} registros
                                                    </span>
                                                </div>
                                                <i class="fas fa-chevron-down folder-arrow small"></i>
                                            </div>
                                            <div id="folder-prod-{{ $year }}-{{ $monthNum }}" class="collapse folder-content py-2">
                                                @foreach($items as $prod)
                                                    <div class="modern-doc-block mb-3">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <h6 class="fw-bold mb-1">{{ $prod->titulo }}</h6>
                                                                <div class="text-muted small mb-2">
                                                                    <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($prod->fecha)->format('d/m/Y') }}
                                                                    @if($prod->tipo)
                                                                        <span class="ms-2"><i class="fas fa-tag me-1"></i> {{ $prod->tipo }}</span>
                                                                    @endif
                                                                </div>
                                                                <p class="mb-2 text-dark small">{{ $prod->descripcion }}</p>
                                                            </div>
                                                            <div class="text-end">
                                                                <span class="badge bg-soft-success text-success border border-success rounded-pill px-3 py-1" style="font-size: 0.75rem;">Activo</span>
                                                            </div>
                                                        </div>
                                                        
                                                        @if($prod->archivo)
                                                            @php
                                                                $ext = pathinfo($prod->archivo, PATHINFO_EXTENSION);
                                                                $icon = 'fas fa-file-alt text-secondary';
                                                                if(in_array(strtolower($ext), ['pdf'])) $icon = 'fas fa-file-pdf text-danger';
                                                                elseif(in_array(strtolower($ext), ['jpg','jpeg','png','gif'])) $icon = 'fas fa-file-image text-success';
                                                                elseif(in_array(strtolower($ext), ['doc','docx'])) $icon = 'fas fa-file-word text-primary';
                                                                elseif(in_array(strtolower($ext), ['xls','xlsx'])) $icon = 'fas fa-file-excel text-success';
                                                                
                                                                $fileName = basename($prod->archivo);
                                                                if (strlen($fileName) > 25) {
                                                                    $fileName = substr($fileName, 0, 10) . '...' . substr($fileName, -10);
                                                                }
                                                            @endphp
                                                            <div class="file-item-pill mt-3">
                                                                <div class="file-info d-flex align-items-center">
                                                                    <i class="{{ $icon }} fa-lg me-3"></i>
                                                                    <div>
                                                                        <span class="d-block text-muted small fw-bold mb-0">Archivo adjunto</span>
                                                                        <span class="file-name text-dark fw-medium" style="font-size: 0.85rem;">{{ $fileName }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="file-btn-group">
                                                                    <a href="{{ route('admin.productividades.archivo.view', $prod->id) }}" target="_blank" class="file-btn view" title="Ver"><i class="fas fa-eye"></i></a>
                                                                    <a href="{{ route('admin.productividades.archivo.download', $prod->id) }}" class="file-btn download" title="Descargar"><i class="fas fa-download"></i></a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="empty-tab-state text-center py-4">
                                <div class="empty-icon-box mb-3 mx-auto">
                                    <i class="fas fa-chart-line text-muted fa-2x"></i>
                                </div>
                                <p class="text-muted">No hay registros de productividad para este empleado.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- ■ TAB: CAPACIDAD INSTALADA ■ --}}
                    <div class="tab-pane fade" id="tab-capacidad" role="tabpanel">
                        <div class="tab-section-header mb-4">
                            <h5 class="fw-bold mb-1">Capacidad Instalada</h5>
                            <p class="text-muted small mb-0">Información de capacidad disponible y utilizada.</p>
                        </div>

                        @forelse($capacidadPorAnioMes as $year => $months)
                            <div class="folder-container mb-3">
                                <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-cap-{{ $year }}">
                                    <div class="folder-info">
                                        <i class="fas fa-folder folder-icon text-warning"></i>
                                        <span class="folder-title">Año {{ $year }}</span>
                                        <span class="folder-count">
                                            {{ $months->flatten(1)->count() }} registros
                                        </span>
                                    </div>
                                    <i class="fas fa-chevron-down folder-arrow"></i>
                                </div>
                                <div id="folder-cap-{{ $year }}" class="collapse folder-content">
                                    @foreach($months as $monthNum => $items)
                                        <div class="folder-container mb-2" style="border-style: dashed;">
                                            <div class="folder-header collapsed py-2" data-toggle="collapse" data-target="#folder-cap-{{ $year }}-{{ $monthNum }}">
                                                <div class="folder-info">
                                                    <i class="fas fa-folder-open folder-icon small text-muted"></i>
                                                    <span class="folder-title" style="font-size: 0.9rem;">{{ $mesesNombre[$monthNum] }}</span>
                                                    <span class="folder-count" style="font-size: 0.75rem; padding: 2px 8px;">
                                                        {{ $items->count() }} registros
                                                    </span>
                                                </div>
                                                <i class="fas fa-chevron-down folder-arrow small"></i>
                                            </div>
                                            <div id="folder-cap-{{ $year }}-{{ $monthNum }}" class="collapse folder-content py-2">
                                                @foreach($items as $item)
                                                    <div class="modern-doc-block mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                            <div class="doc-date">
                                                                <i class="far fa-calendar-alt me-2 text-primary"></i>
                                                                {{ \Carbon\Carbon::parse($item->fecha)->format('d/m/Y') }}
                                                            </div>

                                                            <span class="badge {{ $item->estado == 1 ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} rounded-pill px-3">
                                                                {{ $item->estado == 1 ? 'Activo' : 'Inactivo' }}
                                                            </span>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <strong>Proceso:</strong><br>
                                                                {{ $item->proceso }}
                                                            </div>

                                                            <div class="col-md-4">
                                                                <strong>Capacidad Disponible:</strong><br>
                                                                {{ $item->capacidad_disponible }}
                                                            </div>

                                                            <div class="col-md-4">
                                                                <strong>Capacidad Utilizada:</strong><br>
                                                                {{ $item->capacidad_utilizada }}
                                                            </div>
                                                        </div>

                                                        @if($item->observaciones)
                                                            <hr>
                                                            <strong>Observaciones:</strong>
                                                            <p class="mb-0">{{ $item->observaciones }}</p>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="empty-tab-state">
                                <i class="fas fa-folder-open mb-3"></i>
                                <p>No hay registros de capacidad instalada.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- ■ TAB: REPORTES NOVEDADES NOMINA ■ --}}
                    <div class="tab-pane fade" id="tab-nomina" role="tabpanel">
                        <div class="tab-section-header mb-4">
                            <h5 class="fw-bold mb-1">Reportes Novedades Nómina</h5>
                            <p class="text-muted small mb-0">Historial de novedades reportadas para nómina.</p>
                        </div>

                        @forelse($nominaPorAnioMes as $year => $months)
                            <div class="folder-container mb-3">
                                <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-nom-{{ $year }}">
                                    <div class="folder-info">
                                        <i class="fas fa-folder folder-icon text-warning"></i>
                                        <span class="folder-title">Año {{ $year }}</span>
                                        <span class="folder-count">
                                            {{ $months->flatten(1)->count() }} registros
                                        </span>
                                    </div>
                                    <i class="fas fa-chevron-down folder-arrow"></i>
                                </div>
                                <div id="folder-nom-{{ $year }}" class="collapse folder-content">
                                    @foreach($months as $monthNum => $items)
                                        <div class="folder-container mb-2" style="border-style: dashed;">
                                            <div class="folder-header collapsed py-2" data-toggle="collapse" data-target="#folder-nom-{{ $year }}-{{ $monthNum }}">
                                                <div class="folder-info">
                                                    <i class="fas fa-folder-open folder-icon small text-muted"></i>
                                                    <span class="folder-title" style="font-size: 0.9rem;">{{ $mesesNombre[$monthNum] }}</span>
                                                    <span class="folder-count" style="font-size: 0.75rem; padding: 2px 8px;">
                                                        {{ $items->count() }} registros
                                                    </span>
                                                </div>
                                                <i class="fas fa-chevron-down folder-arrow small"></i>
                                            </div>
                                            <div id="folder-nom-{{ $year }}-{{ $monthNum }}" class="collapse folder-content py-2">
                                                @foreach($items as $item)
                                                    <div class="modern-doc-block mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                            <div class="doc-date">
                                                                <i class="far fa-calendar-alt me-2 text-primary"></i>
                                                                {{ \Carbon\Carbon::parse($item->fecha)->format('d/m/Y') }}
                                                            </div>

                                                            <span class="badge {{ $item->estado == 1 ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} rounded-pill px-3">
                                                                {{ $item->estado == 1 ? 'Activo' : 'Inactivo' }}
                                                            </span>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <strong>Tipo de Novedad:</strong><br>
                                                                {{ $item->tipo_novedad }}
                                                            </div>

                                                            <div class="col-md-6">
                                                                <strong>Cantidad:</strong><br>
                                                                {{ $item->cantidad }}
                                                            </div>
                                                        </div>

                                                        @if($item->observaciones)
                                                            <hr>
                                                            <strong>Observaciones:</strong>
                                                            <p class="mb-0">{{ $item->observaciones }}</p>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="empty-tab-state">
                                <i class="fas fa-folder-open mb-3"></i>
                                <p>No hay reportes de novedades de nómina.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- ■ TAB: PLANTA PERSONAL SENA ■ --}}
                    <div class="tab-pane fade" id="tab-sena" role="tabpanel">
                        <div class="tab-section-header mb-4">
                            <h5 class="fw-bold mb-1">Planta Personal SENA</h5>
                            <p class="text-muted small mb-0">Información reportada al SENA.</p>
                        </div>

                        @forelse($senaPorAnioMes as $year => $months)
                            <div class="folder-container mb-3">
                                <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-sena-{{ $year }}">
                                    <div class="folder-info">
                                        <i class="fas fa-folder folder-icon text-warning"></i>
                                        <span class="folder-title">Año {{ $year }}</span>
                                        <span class="folder-count">
                                            {{ $months->flatten(1)->count() }} registros
                                        </span>
                                    </div>
                                    <i class="fas fa-chevron-down folder-arrow"></i>
                                </div>
                                <div id="folder-sena-{{ $year }}" class="collapse folder-content">
                                    @foreach($months as $monthNum => $items)
                                        <div class="folder-container mb-2" style="border-style: dashed;">
                                            <div class="folder-header collapsed py-2" data-toggle="collapse" data-target="#folder-sena-{{ $year }}-{{ $monthNum }}">
                                                <div class="folder-info">
                                                    <i class="fas fa-folder-open folder-icon small text-muted"></i>
                                                    <span class="folder-title" style="font-size: 0.9rem;">{{ $mesesNombre[$monthNum] }}</span>
                                                    <span class="folder-count" style="font-size: 0.75rem; padding: 2px 8px;">
                                                        {{ $items->count() }} registros
                                                    </span>
                                                </div>
                                                <i class="fas fa-chevron-down folder-arrow small"></i>
                                            </div>
                                            <div id="folder-sena-{{ $year }}-{{ $monthNum }}" class="collapse folder-content py-2">
                                                @foreach($items as $item)
                                                    <div class="modern-doc-block mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                            <div class="doc-date">
                                                                <i class="far fa-calendar-alt me-2 text-primary"></i>
                                                                {{ \Carbon\Carbon::parse($item->fecha_reporte)->format('d/m/Y') }}
                                                            </div>

                                                            <span class="badge {{ $item->estado == 1 ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} rounded-pill px-3">
                                                                {{ $item->estado == 1 ? 'Activo' : 'Inactivo' }}
                                                            </span>
                                                        </div>

                                                        <strong>Observaciones:</strong>
                                                        <p class="mb-0">
                                                            {{ $item->observaciones ?: 'Sin observaciones registradas.' }}
                                                        </p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="empty-tab-state">
                                <i class="fas fa-folder-open mb-3"></i>
                                <p>No hay registros de Planta Personal SENA.</p>
                            </div>
                        @endforelse
                    </div>



                    {{-- ■ TAB: calidad_documentos ■ --}}
                    <div class="tab-pane fade" id="tab-calidad" role="tabpanel">
                        <div class="tab-section-header mb-4">
                            <h5 class="fw-bold mb-1">Calidad Documentos</h5>
                            <p class="text-muted small mb-0">Documentación de calidad asociada al empleado.</p>
                        </div>

                        @forelse($calidadPorAnioMes as $year => $months)
                            <div class="folder-container mb-3">
                                <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-cal-{{ $year }}">
                                    <div class="folder-info">
                                        <i class="fas fa-folder folder-icon text-warning"></i>
                                        <span class="folder-title">Año {{ $year }}</span>
                                        <span class="folder-count">
                                            {{ $months->flatten(1)->count() }} registros
                                        </span>
                                    </div>
                                    <i class="fas fa-chevron-down folder-arrow"></i>
                                </div>
                                <div id="folder-cal-{{ $year }}" class="collapse folder-content">
                                    @foreach($months as $monthNum => $items)
                                        <div class="folder-container mb-2" style="border-style: dashed;">
                                            <div class="folder-header collapsed py-2" data-toggle="collapse" data-target="#folder-cal-{{ $year }}-{{ $monthNum }}">
                                                <div class="folder-info">
                                                    <i class="fas fa-folder-open folder-icon small text-muted"></i>
                                                    <span class="folder-title" style="font-size: 0.9rem;">{{ $mesesNombre[$monthNum] }}</span>
                                                    <span class="folder-count" style="font-size: 0.75rem; padding: 2px 8px;">
                                                        {{ $items->count() }} registros
                                                    </span>
                                                </div>
                                                <i class="fas fa-chevron-down folder-arrow small"></i>
                                            </div>
                                            <div id="folder-cal-{{ $year }}-{{ $monthNum }}" class="collapse folder-content py-2">
                                                @foreach($items as $doc)
                                                    <div class="modern-doc-block mb-3">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <h6 class="fw-bold mb-1">
                                                                    {{ $doc->nombre_documento }}
                                                                </h6>

                                                                <div class="text-muted small mb-2">
                                                                    <i class="fas fa-folder me-1"></i>
                                                                    {{ $doc->categoria ?? 'General' }}

                                                                    @if($doc->codigo)
                                                                        <span class="ms-2">
                                                                            <i class="fas fa-barcode me-1"></i>
                                                                            {{ $doc->codigo }}
                                                                        </span>
                                                                    @endif

                                                                    @if($doc->version)
                                                                        <span class="ms-2">
                                                                            <i class="fas fa-code-branch me-1"></i>
                                                                            Versión {{ $doc->version }}
                                                                        </span>
                                                                    @endif
                                                                </div>

                                                                <div class="text-muted small">
                                                                    <span>
                                                                        <i class="far fa-calendar-alt me-1"></i>
                                                                        Emisión:
                                                                        {{ \Carbon\Carbon::parse($doc->fecha_emision)->format('d/m/Y') }}
                                                                    </span>

                                                                    @if($doc->fecha_vencimiento)
                                                                        <span class="ms-3">
                                                                            <i class="fas fa-hourglass-end me-1"></i>
                                                                            Vence:
                                                                            {{ \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="text-end">
                                                                @php
                                                                    $hoy = \Carbon\Carbon::now();
                                                                    $vence = $doc->fecha_vencimiento
                                                                        ? \Carbon\Carbon::parse($doc->fecha_vencimiento)
                                                                        : null;
                                                                @endphp

                                                                @if($vence && $vence->isPast())
                                                                    <span class="badge bg-danger rounded-pill px-3 py-1"
                                                                        style="font-size: 0.75rem;">
                                                                        Vencido
                                                                    </span>
                                                                @elseif($vence && $hoy->diffInDays($vence, false) <= 30)
                                                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1"
                                                                        style="font-size: 0.75rem;">
                                                                        Próximo a vencer
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-soft-success text-success border border-success rounded-pill px-3 py-1"
                                                                        style="font-size: 0.75rem;">
                                                                        Vigente
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        @if($doc->archivo)
                                                            @php
                                                                $ext = pathinfo($doc->archivo, PATHINFO_EXTENSION);
                                                                $icon = 'fas fa-file-alt text-secondary';
                                                                if(in_array(strtolower($ext), ['pdf'])) {
                                                                    $icon = 'fas fa-file-pdf text-danger';
                                                                }
                                                                elseif(in_array(strtolower($ext), ['jpg','jpeg','png','gif'])) {
                                                                    $icon = 'fas fa-file-image text-success';
                                                                }
                                                                elseif(in_array(strtolower($ext), ['doc','docx'])) {
                                                                    $icon = 'fas fa-file-word text-primary';
                                                                }
                                                                elseif(in_array(strtolower($ext), ['xls','xlsx'])) {
                                                                    $icon = 'fas fa-file-excel text-success';
                                                                }

                                                                $fileName = basename($doc->archivo);
                                                                if(strlen($fileName) > 25) {
                                                                    $fileName = substr($fileName, 0, 10)
                                                                        . '...'
                                                                        . substr($fileName, -10);
                                                                }
                                                            @endphp

                                                            <div class="file-item-pill mt-3">
                                                                <div class="file-info d-flex align-items-center">
                                                                    <i class="{{ $icon }} fa-lg me-3"></i>
                                                                    <div>
                                                                        <span class="d-block text-muted small fw-bold mb-0">
                                                                            Archivo adjunto
                                                                        </span>
                                                                        <span class="file-name text-dark fw-medium"
                                                                            style="font-size: 0.85rem;">
                                                                            {{ $fileName }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="file-btn-group">
                                                                    <a href="{{ route('admin.calidad_documentos.archivo.view', $doc->id) }}"
                                                                    target="_blank"
                                                                    class="file-btn view"
                                                                    title="Ver">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    <a href="{{ route('admin.calidad_documentos.archivo.download', $doc->id) }}"
                                                                    class="file-btn download"
                                                                    title="Descargar">
                                                                        <i class="fas fa-download"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="empty-tab-state text-center py-4">
                                <div class="empty-icon-box mb-3 mx-auto">
                                    <i class="fas fa-folder-open text-muted fa-2x"></i>
                                </div>
                                <p class="text-muted">
                                    No hay documentos de calidad para este empleado.
                                </p>
                            </div>
                        @endforelse
                    </div>



                </div>{{-- /tab-content --}}
            </div>{{-- /main-tabs-card --}}
        </div>{{-- /col-9 --}}
    </div>{{-- /row --}}
</div>{{-- /container --}}

@stop

@section('css')
<style>
    .profile-nav-tabs {
    flex-wrap: nowrap !important;
    overflow-x: auto;
    overflow-y: hidden;
    scrollbar-width: thin;
    padding-bottom: 8px;
}

.profile-nav-tabs::-webkit-scrollbar {
    height: 6px;
}

.profile-nav-tabs::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 20px;
}

.profile-nav-tabs::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 20px;
}

.profile-nav-tabs .nav-link {
    flex-shrink: 0;
    white-space: nowrap;
}

:root {
    --primary-modern: #6366f1;
    --primary-hover: #4f46e5;
    --primary-light: rgba(99, 102, 241, 0.1);
    --bg-page: #f1f5f9;
    --text-main: #0f172a;
    --text-muted: #64748b;
    --text-light: #94a3b8; /* Slate 400 */
    --glass-bg: rgba(255, 255, 255, 0.85);
    --glass-border: rgba(255, 255, 255, 0.6);
    --radius-xl: 1rem;
    --shadow-soft: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-premium: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    
    --soft-blue: #e0f2fe;
    --soft-green: #dcfce7;
    --soft-red: #fee2e2;
    --soft-warning: #fef3c7;
    
    --accent-emerald: #10b981;
    --accent-amber: #f59e0b;
    --accent-rose: #f43f5e;
}

body { background-color: var(--bg-page) !important; color: #334155; font-family: 'Inter', sans-serif; }

.head-title-modern { font-size: 1.75rem; font-weight: 800; letter-spacing: -0.025em; color: var(--text-main); }
.text-primary-modern { color: var(--primary-modern) !important; }

/* Buttons */
.btn-primary-modern { 
    background: var(--primary-modern); color: white; border: none; border-radius: 10px; 
    padding: 0.6rem 1.4rem; font-weight: 600; transition: all 0.25s ease;
    box-shadow: 0 4px 10px rgba(99, 102, 241, 0.2);
}
.btn-primary-modern:hover { background: var(--primary-hover); transform: translateY(-1px); box-shadow: 0 6px 15px rgba(99, 102, 241, 0.3); color: white; }

.btn-outline-modern { 
    background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 10px; 
    padding: 0.6rem 1.4rem; font-weight: 600; transition: all 0.2s ease;
}
.btn-outline-modern:hover { background: #f8fafc; border-color: #cbd5e1; color: var(--text-main); transform: translateY(-1px); }

/* Hero Banner */
.glass-profile-hero {
    background: #f4f6f9; /* Suave y neutro Gris claro */
    border-radius: 1.5rem;
    padding: 3rem 2.5rem;
    color: var(--text-main);
    box-shadow: var(--shadow-soft);
    position: relative;
    overflow: hidden;
    border: 1px solid #e2e8f0;
}
.glass-profile-hero::after {
    content: ''; position: absolute; top: -50%; right: -10%; width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.05) 0%, transparent 70%);
}

.profile-avatar-xl {
    width: 110px; height: 110px; background: white; 
    border: 1px solid #e2e8f0; border-radius: 2rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 3.5rem; font-weight: 800; color: var(--primary-modern);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.profile-avatar-xl:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }

.fullname-display { font-size: 2.5rem; font-weight: 900; letter-spacing: -0.04em; margin-bottom: 0.25rem; color: var(--text-main); }
.profile-details { color: var(--text-muted); font-weight: 500; font-size: 0.95rem; }
.profile-details i { color: var(--primary-modern); margin-right: 4px; }
.text-white-50 { color: var(--text-light) !important; } /* Override for light background */

.active-contract-info { margin-top: 1rem; }
.active-contract-info .small { color: var(--text-light); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }

.badge-status { padding: 6px 14px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; }
.badge-status.active { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.badge-status.inactive { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

.mini-stat { text-align: center; background: white; padding: 1rem 1.5rem; border-radius: 1.25rem; border: 1px solid #e2e8f0; min-width: 100px; box-shadow: var(--shadow-soft); }
.mini-stat .val { font-size: 1.75rem; font-weight: 900; display: block; color: var(--text-main); }
.mini-stat .lbl { font-size: 0.65rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.1em; }

/* Side Cards */
.glass-card { background: white; border-radius: var(--radius-xl); border: 1px solid #e2e8f0; box-shadow: var(--shadow-soft); overflow: hidden; }
.card-title-bar { 
    padding: 1rem 1.5rem; background: #fcfcfd; border-bottom: 1px solid #f1f5f9; 
    font-weight: 800; font-size: 0.75rem; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; 
}
.card-title-bar.orange { color: var(--accent-amber); }

.card-info-item { display: flex; align-items: flex-start; gap: 1.25rem; padding: 1.25rem 1.5rem; border-bottom: 1px solid #f8fafc; transition: background 0.2s; }
.card-info-item:hover { background: #fcfcfd; }
.card-info-item:last-child { border-bottom: none; }
.card-info-item .icon { 
    width: 38px; height: 38px; border-radius: 12px; background: #f1f5f9; 
    display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 1rem; flex-shrink: 0; 
}
.card-info-item label { display: block; font-size: 0.7rem; color: #94a3b8; font-weight: 700; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.025em; }
.card-info-item span { display: block; font-size: 0.95rem; font-weight: 700; color: #1e293b; }

/* Main Tabs */
.main-tabs-card { background: white; border-radius: var(--radius-xl); border: 1px solid #e2e8f0; box-shadow: var(--shadow-soft); overflow: hidden; min-height: 550px; }
.profile-nav-tabs { background: #f8fafc; border-bottom: 1px solid #f1f5f9 !important; gap: 8px; padding: 6px 1.5rem 0; }
.profile-nav-tabs .nav-link { 
    border: none; border-radius: 8px 8px 0 0; padding: 1.25rem 1rem; 
    font-weight: 700; font-size: 0.85rem; color: #64748b; 
    transition: all 0.2s ease; position: relative;
    opacity: 0.7;
}
.profile-nav-tabs .nav-link:hover { color: var(--primary-modern); opacity: 1; }
.profile-nav-tabs .nav-link.active { 
    color: var(--primary-modern); background: transparent; opacity: 1;
}
.profile-nav-tabs .nav-link.active::after {
    content: ''; position: absolute; bottom: 0; left: 0; right: 0; 
    height: 3px; background: var(--primary-modern); border-radius: 3px 3px 0 0;
}

/* Folder UI */
.folder-container { margin-bottom: 1.25rem; border-radius: 1rem; overflow: hidden; border: 1px solid #e2e8f0; background: white; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.folder-container:hover { border-color: #cbd5e1; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04); }
.folder-header { 
    padding: 1.25rem 1.5rem; background: #fff; cursor: pointer; 
    display: flex; align-items: center; justify-content: space-between;
    user-select: none; transition: background 0.2s;
}
.folder-header:hover { background: #fbfbfc; }
.folder-header .folder-info { display: flex; align-items: center; gap: 1rem; }
.folder-header .folder-icon { color: var(--accent-amber); font-size: 1.5rem; }
.folder-header .folder-title { font-weight: 800; color: #1e293b; font-size: 1rem; letter-spacing: -0.01em; }
.folder-header .folder-count { font-size: 0.7rem; font-weight: 700; color: #64748b; background: #f1f5f9; padding: 4px 10px; border-radius: 99px; }
.folder-header .folder-arrow { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); color: #cbd5e1; }
.folder-header:not(.collapsed) .folder-arrow { transform: rotate(180deg); color: var(--primary-modern); }
.folder-header:not(.collapsed) { background: #fafbff; border-bottom: 1px solid #f1f5f9; }

.folder-content { padding: 1.5rem; background: #fff; }

.modern-doc-block { 
    background: #fcfcfd; border: 1px solid #f1f5f9; border-radius: 1.25rem; 
    padding: 1.5rem; transition: all 0.2s; 
    margin-bottom: 1.25rem;
}
.modern-doc-block:hover { border-color: #e2e8f0; background: #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
.modern-doc-block:last-child { margin-bottom: 0; }

.file-item-pill { 
    background: white; border: 1px solid #f1f5f9; border-radius: 0.85rem; 
    padding: 0.85rem 1.25rem; display: flex; justify-content: space-between; 
    align-items: center; margin-bottom: 0.75rem; transition: all 0.2s;
}
.file-item-pill:hover { border-color: #e2e8f0; transform: translateX(4px); }
.file-item-pill:last-child { margin-bottom: 0; }
.file-item-pill .file-name { font-size: 0.85rem; font-weight: 700; color: #334155; }

.file-btn { 
    width: 36px; height: 36px; border-radius: 10px; border: none; 
    display: inline-flex; align-items: center; justify-content: center; 
    font-size: 0.9rem; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); 
}
.file-btn.view { background: #eff6ff; color: #2563eb; margin-right: 6px; }
.file-btn.download { background: #ecfdf5; color: #059669; }
.file-btn:hover { transform: scale(1.1); filter: contrast(1.1); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }

/* Helpers */
.bg-soft-success { background: #ecfdf5; color: #059669; }
.bg-soft-primary { background: #eef2ff; color: #4f46e5; }
.bg-soft-warning { background: #fffbeb; color: #d97706; }
.bg-soft-danger { background: #fef2f2; color: #dc2626; }
.text-indigo { color: #6366f1; }
.text-orange { color: #f97316; }
.text-silver { color: #cbd5e1; }

.calificacion-pill { padding: 6px 18px; border-radius: 99px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.025em; }
.calificacion-pill.good { background: #dcfce7; color: #166534; }
.calificacion-pill.warn { background: #fef3c7; color: #92400e; }
.calificacion-pill.bad { background: #fee2e2; color: #991b1b; }

.eval-icon-circle {
    width: 45px; height: 45px; border-radius: 12px; background: #f1f5f9;
    display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
    flex-shrink: 0; transition: all 0.3s;
}
.eval-row-hover:hover .eval-icon-circle { background: #eef2ff; transform: rotate(5deg) scale(1.05); }
.eval-row-hover:hover { background-color: #fbfbfc !important; }

.empty-icon-box {
    width: 80px; height: 80px; background: #f8fafc; border-radius: 2rem;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto; font-size: 2.5rem; color: #cbd5e1;
}

/* Animations */
@keyframes fadeInSlide {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.tab-pane.active { animation: fadeInSlide 0.3s ease-out forwards; }

/* Responsive adjustments */
@media (max-width: 768px) {
    .glass-profile-hero { padding: 2.5rem 1.5rem; }
    .fullname-display { font-size: 2rem; }
    .profile-avatar-xl { width: 90px; height: 90px; font-size: 3rem; }
}
</style>
@stop

@section('js')
<script>
$(function () {
    // Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // URL Tab Persistence (Bootstrap 4)
    const hash = window.location.hash;
    if (hash) {
        $('.nav-tabs a[href="' + hash + '"]').tab('show');
    }

    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        window.location.hash = $(e.target).attr('href');
    });
});
</script>
@stop
