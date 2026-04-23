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
                    <div class="mini-stat">
                        <div class="val">{{ $nContratos }}</div>
                        <div class="lbl">Contratos</div>
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
                </div>
            </div>
        </div>

        {{-- ─── MAIN CONTENT: TABS SYSTEM ───────────────────────── --}}
        <div class="col-lg-9">
            <div class="main-tabs-card">
                <nav>
                    <div class="nav nav-tabs profile-nav-tabs px-3 border-0" id="emp-tabs" role="tablist">
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
                        <a class="nav-link" data-toggle="tab" href="#tab-comunicaciones" role="tab">
                            <i class="fas fa-bullhorn me-2"></i> Comunicados
                        </a>
                        <a class="nav-link" data-toggle="tab" href="#tab-solicitudes" role="tab">
                            <i class="fas fa-tasks me-2"></i> Solicitudes
                        </a>
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
                        @forelse($empleado->etapaContractuales->sortByDesc('fecha_inicio') as $c)
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
                                            <span class="mx-2 text-silver">|</span>
                                            <i class="fas fa-coins me-1"></i> $ {{ number_format($c->salario) }}
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
                            <div class="empty-tab-state"><i class="fas fa-handshake mb-3"></i><p>No se encontraron contratos registrados.</p></div>
                        @endforelse
                    </div>

                    {{-- ■ TAB: SST ■ --}}
                    <div class="tab-pane fade" id="tab-sst" role="tabpanel">
                        <div class="tab-section-header mb-4">
                            <h5 class="fw-bold mb-1">Seguridad y Salud en el Trabajo</h5>
                            <p class="text-muted small mb-0">Exámenes médicos, EPPs y documentos preventivos.</p>
                        </div>

                        @php
                            $sstGroups = $empleado->seguridadSaludTrabajo->sortByDesc('fecha')->groupBy(function($item) {
                                $tipo = strtolower($item->tipo_documento);
                                if (str_contains($tipo, 'ingreso')) return 'Ingreso';
                                if (str_contains($tipo, 'periódic') || str_contains($tipo, 'periodic')) return 'Periódicos';
                                return 'Otros';
                            });
                        @endphp
                        @forelse($sstGroups as $groupName => $items)
                            <div class="folder-container">
                                <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-sst-{{ Str::slug($groupName) }}">
                                    <div class="folder-info">
                                        <i class="fas fa-folder folder-icon"></i>
                                        <span class="folder-title">{{ $groupName }}</span>
                                        <span class="folder-count">{{ $items->count() }} registros</span>
                                    </div>
                                    <i class="fas fa-chevron-down folder-arrow"></i>
                                </div>
                                <div id="folder-sst-{{ Str::slug($groupName) }}" class="collapse folder-content">
                                    @foreach($items as $sst)
                                        @php $sstBadge = $sst->getStatusBadge($sst->fecha); @endphp
                                        <div class="modern-doc-block">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <h6 class="fw-bold mb-1 text-dark">{{ $sst->tipo_documento }}</h6>
                                                    <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> Fecha: {{ \Carbon\Carbon::parse($sst->fecha)->format('d/m/Y') }}</small>
                                                </div>
                                                <span class="badge {{ $sst->estado == 1 ? $sstBadge['class'] : 'bg-soft-danger text-danger' }} rounded-pill px-3">
                                                    {{ $sst->estado == 1 ? $sstBadge['label'] : 'Inactivo' }}
                                                </span>
                                            </div>
                                            @foreach($sst->documentos as $doc)
                                                <div class="file-item-pill">
                                                    <div class="file-info"><i class="fas fa-file-medical text-danger me-3"></i><span class="file-name">{{ $doc->nombre_original }}</span></div>
                                                    <div class="file-btn-group">
                                                        <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" class="file-btn view"><i class="fas fa-eye"></i></a>
                                                        <a href="{{ route('admin.documentos.download', $doc->id) }}" class="file-btn download"><i class="fas fa-download"></i></a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="empty-tab-state"><i class="fas fa-heartbeat mb-3"></i><p>Sin registros de SST.</p></div>
                        @endforelse
                    </div>

                    {{-- ■ TAB: EVALUACIONES ■ --}}
                    <div class="tab-pane fade" id="tab-evaluaciones" role="tabpanel">
                        <div class="tab-section-header mb-4"><h5 class="fw-bold mb-1">Evaluaciones de Desempeño</h5><p class="text-muted small mb-0">Historial de rendimiento laboral.</p></div>
                        @forelse($empleado->evaluacionesDesempeno->sortByDesc('fecha') as $ev)
                            <div class="modern-doc-block mb-3">
                                <div class="row align-items-center">
                                    <div class="col-md">
                                        <h6 class="fw-bold mb-1">Evaluación Periodo {{ \Carbon\Carbon::parse($ev->fecha)->format('d/m/Y') }}</h6>
                                        <p class="text-muted small italic mb-0">"{{ $ev->observaciones ?? 'Sin observaciones registradas.' }}"</p>
                                    </div>
                                    <div class="col-md-auto text-end">
                                        <div class="calificacion-pill shadow-sm {{ $ev->calificacion >= 8 ? 'good' : ($ev->calificacion >= 5 ? 'warn' : 'bad') }}">
                                            <span>Calificación: </span><strong>{{ $ev->calificacion }}/10</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    @foreach($ev->documentos as $doc)
                                        <div class="file-item-pill">
                                            <div class="file-info"><i class="fas fa-chart-line text-info me-3"></i><span class="file-name">{{ $doc->nombre_original }}</span></div>
                                            <div class="file-btn-group">
                                                <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" class="file-btn view"><i class="fas fa-eye"></i></a>
                                                <a href="{{ route('admin.documentos.download', $doc->id) }}" class="file-btn download"><i class="fas fa-download"></i></a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="empty-tab-state"><i class="fas fa-chart-bar mb-3"></i><p>No se han registrado evaluaciones activas.</p></div>
                        @endforelse
                    </div>

                    {{-- ■ TAB: FORMACIÓN ■ --}}
                    <div class="tab-pane fade" id="tab-formacion" role="tabpanel">
                        <div class="tab-section-header mb-4">
                            <h5 class="fw-bold mb-1">Registro de Formación Académica</h5>
                            <p class="text-muted small mb-0">Expediente digital de capacitaciones y certificaciones.</p>
                        </div>

                        {{-- CARPETA: CURSOS QUE VENCEN --}}
                        <div class="folder-container">
                            <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-form-vence">
                                <div class="folder-info">
                                    <i class="fas fa-folder folder-icon" style="color: #f97316;"></i>
                                    <span class="folder-title">Cursos que vencen</span>
                                    <span class="folder-count">{{ $empleado->formaciones->where('vence', 1)->count() }} registros</span>
                                </div>
                                <i class="fas fa-chevron-down folder-arrow"></i>
                            </div>
                            <div id="folder-form-vence" class="collapse folder-content">
                                @forelse($empleado->formaciones->where('vence', 1)->sortByDesc('fecha_inicio') as $f)
                                    <div class="modern-doc-block">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold text-dark mb-0"><i class="fas fa-graduation-cap me-2 text-muted"></i>{{ $f->nombre_curso }}</h6>
                                            @if($f->fecha_fin)
                                                @php $badge = $f->getStatusBadge($f->fecha_fin); @endphp
                                                <span class="badge {{ $badge['class'] }} rounded-pill px-3">
                                                    <i class="fas fa-clock me-1"></i> {{ $badge['label'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-muted small mb-3">
                                            <i class="far fa-calendar-alt me-1"></i> 
                                            {{ $f->fecha_inicio ? \Carbon\Carbon::parse($f->fecha_inicio)->format('d/m/Y') : 'N/A' }}
                                            <span class="mx-2 text-silver">|</span>
                                            <span class="fw-bold text-danger">Vence: {{ \Carbon\Carbon::parse($f->fecha_fin)->format('d/m/Y') }}</span>
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
                                @empty
                                    <div class="text-center py-3 text-muted small">No hay cursos con vencimiento.</div>
                                @endforelse
                            </div>
                        </div>

                        {{-- CARPETA: CURSOS QUE NO VENCEN --}}
                        <div class="folder-container">
                            <div class="folder-header collapsed" data-toggle="collapse" data-target="#folder-form-novence">
                                <div class="folder-info">
                                    <i class="fas fa-folder folder-icon" style="color: #10b981;"></i>
                                    <span class="folder-title">Cursos que no vencen</span>
                                    <span class="folder-count">{{ $empleado->formaciones->where('vence', 0)->count() }} registros</span>
                                </div>
                                <i class="fas fa-chevron-down folder-arrow"></i>
                            </div>
                            <div id="folder-form-novence" class="collapse folder-content">
                                @forelse($empleado->formaciones->where('vence', 0)->sortByDesc('fecha_inicio') as $f)
                                    <div class="modern-doc-block">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold text-dark mb-0"><i class="fas fa-graduation-cap me-2 text-muted"></i>{{ $f->nombre_curso }}</h6>
                                            <span class="badge bg-soft-success text-success rounded-pill px-3">Permanente</span>
                                        </div>
                                        <div class="text-muted small mb-3">
                                            <i class="far fa-calendar-alt me-1"></i> 
                                            {{ $f->fecha_inicio ? \Carbon\Carbon::parse($f->fecha_inicio)->format('d/m/Y') : 'N/A' }}
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
                                @empty
                                    <div class="text-center py-3 text-muted small">No hay cursos permanentes.</div>
                                @endforelse
                            </div>
                        </div>
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
                        <div class="tab-section-header mb-4"><h5 class="fw-bold mb-1">Solicitudes y Requerimientos</h5><p class="text-muted small mb-0">Estado de vacaciones, permisos y solicitudes.</p></div>
                        @forelse($empleado->solicitudes->sortByDesc('fecha') as $sol)
                            @php
                                $statusConf = [
                                    'aprobado'  => ['cls'=>'bg-soft-success text-success', 'icon'=>'fa-check-circle'],
                                    'rechazado' => ['cls'=>'bg-soft-danger text-danger', 'icon'=>'fa-times-circle'],
                                    'pendiente' => ['cls'=>'bg-soft-warning text-warning', 'icon'=>'fa-clock']
                                ][strtolower($sol->estado)] ?? ['cls'=>'bg-soft-secondary text-secondary', 'icon'=>'fa-question-circle'];
                            @endphp
                            <div class="modern-doc-block mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <span class="type-pill me-2">{{ strtoupper($sol->tipo) }}</span>
                                        <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($sol->fecha)->format('d/m/Y') }}</small>
                                    </div>
                                    <span class="badge {{ $statusConf['cls'] }} rounded-pill px-3">
                                        <i class="fas {{ $statusConf['icon'] }} me-1"></i> {{ ucfirst($sol->estado) }}
                                    </span>
                                </div>
                                @foreach($sol->documentos as $doc)
                                    <div class="file-item-pill">
                                        <div class="file-info"><i class="fas fa-file-invoice-dollar text-orange me-3"></i><span class="file-name">{{ $doc->nombre_original }}</span></div>
                                        <div class="file-btn-group">
                                            <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" class="file-btn view"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('admin.documentos.download', $doc->id) }}" class="file-btn download"><i class="fas fa-download"></i></a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @empty
                            <div class="empty-tab-state"><i class="fas fa-tasks mb-3"></i><p>Sin solicitudes activas.</p></div>
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
:root {
    --primary-modern: #6366f1; /* Indigo vibrante profesional */
    --primary-hover: #4f46e5;
    --primary-light: rgba(99, 102, 241, 0.1);
    --bg-page: #f1f5f9; /* Slate 100 para mejor contraste */
    --text-main: #0f172a; /* Slate 900 */
    --text-muted: #64748b; /* Slate 500 */
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
