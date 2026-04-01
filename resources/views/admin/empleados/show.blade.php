@extends('adminlte::page')

@section('title', 'Perfil – ' . ($empleado->persona->nombres ?? 'Empleado'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.65rem;">
            <i class="fas fa-id-badge mr-2" style="color: var(--primary-blue);"></i>
            Perfil del Empleado
        </h2>
        <p class="text-muted mb-0" style="font-size:0.9rem;">
            Expediente digital completo &mdash; información y documentos asociados
        </p>
    </div>
    <div class="d-flex gap-2">
        @if($empleado->estado == 1)
        <a href="{{ route('admin.empleados.edit', $empleado) }}"
           class="btn btn-sm btn-light-custom border shadow-sm"
           data-toggle="tooltip" title="Editar datos del empleado">
            <i class="fas fa-pen mr-1"></i> Editar
        </a>
        @endif
        <a href="{{ route('admin.empleados.index') }}"
           class="btn btn-sm btn-light-custom border shadow-sm"
           data-toggle="tooltip" title="Volver al listado">
            <i class="fas fa-arrow-left mr-1"></i> Volver
        </a>
    </div>
</div>
@stop

@section('content')
@php
    $fullname     = ($empleado->persona->nombres ?? '') . ' ' . ($empleado->persona->apellidos ?? '');
    $initial      = strtoupper(substr($empleado->persona->nombres ?? 'X', 0, 1));
    $diasEmpresa  = \Carbon\Carbon::parse($empleado->fecha_ingreso)->diffInDays(now());
    $nContratos   = $empleado->etapaContractuales->count();
    $nSST         = $empleado->seguridadSaludTrabajo->count();
    $nEvals       = $empleado->evaluacionesDesempeno->count();
    $contratoActivo = $empleado->etapaContractuales->where('estado', 1)->sortByDesc('fecha_fin')->first();
@endphp

<div class="container-fluid px-2 pb-5">

    {{-- ══════════════════════════════════════════════════════
         HERO BANNER
    ══════════════════════════════════════════════════════ --}}
    <div class="profile-hero mb-4">
        <div class="d-flex align-items-start flex-wrap" style="gap: 1.75rem; position:relative;z-index:1;">
            <div class="profile-avatar-lg mt-1">{{ $initial }}</div>

            <div class="flex-grow-1">
                <div class="d-flex align-items-center flex-wrap mb-2" style="gap:0.6rem;">
                    <h3 class="fw-bold mb-0" style="color:#fff; font-size:1.45rem; letter-spacing:-0.5px;">{{ $fullname }}</h3>
                    @if($empleado->estado == 1)
                        <span class="badge" style="background:rgba(16,185,129,0.25); color:#6ee7b7; border:1px solid rgba(16,185,129,0.4); font-size:0.7rem;">
                            <i class="fas fa-circle" style="font-size:0.45rem; vertical-align:middle;"></i> Activo
                        </span>
                    @else
                        <span class="badge" style="background:rgba(239,68,68,0.2); color:#fca5a5; border:1px solid rgba(239,68,68,0.3); font-size:0.7rem;">
                            <i class="fas fa-circle" style="font-size:0.45rem; vertical-align:middle;"></i> Inactivo
                        </span>
                    @endif
                </div>

                <p class="mb-0" style="color:rgba(255,255,255,0.6); font-size:0.875rem; line-height:1.6;">
                    <i class="fas fa-briefcase mr-1"></i> {{ $empleado->cargo }}
                    &nbsp;&middot;&nbsp;
                    <i class="fas fa-building mr-1"></i> {{ $empleado->area->nombre ?? 'Sin área' }}
                    &nbsp;&middot;&nbsp;
                    <i class="fas fa-hospital-alt mr-1"></i> {{ $empleado->sede->nombre ?? 'Sin sede' }}
                </p>

                @if($contratoActivo)
                @php $badge = $contratoActivo->getStatusBadge($contratoActivo->fecha_fin); @endphp
                <div class="mt-2">
                    <span style="font-size:0.75rem; color:rgba(255,255,255,0.45); margin-right:0.5rem;">Contrato activo:</span>
                    <span class="{{ $badge['class'] }}" style="font-size:0.72rem; padding:0.2rem 0.6rem; border-radius:99px;">
                        <i class="{{ $badge['icon'] }} mr-1"></i>{{ $badge['label'] }}
                        @if($contratoActivo->fecha_fin)
                            &mdash; vence {{ \Carbon\Carbon::parse($contratoActivo->fecha_fin)->format('d/m/Y') }}
                        @endif
                    </span>
                </div>
                @endif
            </div>
        </div>

        {{-- Stats row below --}}
        <div class="d-flex flex-wrap mt-4" style="gap:0.75rem; position:relative;z-index:1;">
            <div class="profile-stat-card">
                <div class="stat-value">{{ number_format($diasEmpresa) }}</div>
                <div class="stat-label">Días en empresa</div>
            </div>
            <div class="profile-stat-card">
                <div class="stat-value">{{ $nContratos }}</div>
                <div class="stat-label">Contratos</div>
            </div>
            <div class="profile-stat-card">
                <div class="stat-value">{{ $nSST }}</div>
                <div class="stat-label">Docs. SST</div>
            </div>
            <div class="profile-stat-card">
                <div class="stat-value">{{ $nEvals }}</div>
                <div class="stat-label">Evaluaciones</div>
            </div>
            <div class="profile-stat-card">
                <div class="stat-value">{{ $empleado->formaciones->count() }}</div>
                <div class="stat-label">Formaciones</div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         BODY: Sidebar + Tabs
    ══════════════════════════════════════════════════════ --}}
    <div class="row g-4">

        {{-- ─── SIDEBAR INFO ─────────────────────────────── --}}
        <div class="col-lg-3">

            {{-- Datos personales --}}
            <div class="card mb-3">
                <div class="card-header" style="padding:1rem 1.25rem;">
                    <h6 class="fw-bold mb-0" style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted);">
                        <i class="fas fa-user mr-2" style="color:var(--primary-blue);"></i>Datos Personales
                    </h6>
                </div>
                <div class="card-body" style="padding:1rem 1.25rem;">
                    <div class="info-row">
                        <div class="info-icon"><i class="fas fa-id-card"></i></div>
                        <div>
                            <span class="info-label">Documento</span>
                            <span class="info-value">{{ $empleado->persona->tipo_documento ?? '' }} {{ $empleado->persona->numero_documento ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon"><i class="fas fa-birthday-cake"></i></div>
                        <div>
                            <span class="info-label">Fecha Nacimiento</span>
                            <span class="info-value">
                                {{ $empleado->persona->fecha_nacimiento ? \Carbon\Carbon::parse($empleado->persona->fecha_nacimiento)->format('d/m/Y') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon"><i class="fas fa-phone"></i></div>
                        <div>
                            <span class="info-label">Teléfono</span>
                            <span class="info-value">{{ $empleado->persona->telefono ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon"><i class="fas fa-envelope"></i></div>
                        <div>
                            <span class="info-label">Correo</span>
                            <span class="info-value" style="word-break:break-all;">{{ $empleado->persona->correo ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <span class="info-label">Dirección</span>
                            <span class="info-value">{{ $empleado->persona->direccion ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Datos laborales --}}
            <div class="card">
                <div class="card-header" style="padding:1rem 1.25rem;">
                    <h6 class="fw-bold mb-0" style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted);">
                        <i class="fas fa-briefcase mr-2" style="color:var(--accent-orange);"></i>Datos Laborales
                    </h6>
                </div>
                <div class="card-body" style="padding:1rem 1.25rem;">
                    <div class="info-row">
                        <div class="info-icon"><i class="fas fa-sitemap"></i></div>
                        <div>
                            <span class="info-label">Área</span>
                            <span class="info-value">{{ $empleado->area->nombre ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon"><i class="fas fa-hospital-alt"></i></div>
                        <div>
                            <span class="info-label">Sede</span>
                            <span class="info-value">{{ $empleado->sede->nombre ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon"><i class="fas fa-user-tag"></i></div>
                        <div>
                            <span class="info-label">Rol</span>
                            <span class="info-value">{{ $empleado->rol->nombre ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon"><i class="fas fa-calendar-alt"></i></div>
                        <div>
                            <span class="info-label">Fecha Ingreso</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($empleado->fecha_ingreso)->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon"><i class="fas fa-toggle-on"></i></div>
                        <div>
                            <span class="info-label">Estado</span>
                            @if($empleado->estado == 1)
                                <span class="info-value" style="color:#10b981;"><i class="fas fa-check-circle mr-1"></i>Activo</span>
                            @else
                                <span class="info-value" style="color:#ef4444;"><i class="fas fa-times-circle mr-1"></i>Inactivo</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ─── MAIN CONTENT WITH TABS ───────────────────── --}}
        <div class="col-lg-9">
            <div class="card" style="overflow:visible;">
                {{-- Tab nav --}}
                <div class="card-header" style="padding:0; border-bottom:none;">
                    <ul class="nav profile-tabs px-3">
                        <li class="nav-item">
                            <a class="nav-link active" href="#tab-precontractual" data-toggle="tab">
                                <i class="fas fa-file-contract tab-icon"></i>Precontractual
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tab-contractual" data-toggle="tab">
                                <i class="fas fa-handshake tab-icon"></i>Contractual
                                @if($nContratos > 0)
                                    <span class="badge badge-pill ml-1" style="background:rgba(19,182,236,0.15); color:var(--primary-blue); font-size:0.65rem;">{{ $nContratos }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tab-sst" data-toggle="tab">
                                <i class="fas fa-heartbeat tab-icon"></i>SST
                                @if($nSST > 0)
                                    <span class="badge badge-pill ml-1" style="background:rgba(239,68,68,0.12); color:#ef4444; font-size:0.65rem;">{{ $nSST }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tab-evaluaciones" data-toggle="tab">
                                <i class="fas fa-chart-line tab-icon"></i>Evaluaciones
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tab-formacion" data-toggle="tab">
                                <i class="fas fa-graduation-cap tab-icon"></i>Formación
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tab-comunicaciones" data-toggle="tab">
                                <i class="fas fa-bullhorn tab-icon"></i>Comunicaciones
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tab-solicitudes" data-toggle="tab">
                                <i class="fas fa-envelope-open-text tab-icon"></i>Solicitudes
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body" style="padding:1.75rem;">
                    <div class="tab-content">

                        {{-- ■ TAB: PRECONTRACTUAL ■ --}}
                        <div class="tab-pane fade show active" id="tab-precontractual">
                            <div class="section-divider">
                                <div class="section-icon" style="background:rgba(14,165,233,0.1); color:#0ea5e9;">
                                    <i class="fas fa-file-contract"></i>
                                </div>
                                <div>
                                    <h5>Etapa Precontractual</h5>
                                    <small>Documentos registrados previos a la contratación</small>
                                </div>
                            </div>
                            @forelse($empleado->persona->etapaPrecontractuales as $ep)
                                <div class="doc-section-card">
                                    <div class="doc-header">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-soft-blue" style="font-size:0.8rem;">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($ep->fecha_registro)->format('d/m/Y') }}
                                            </span>
                                            <small class="text-muted">Registro #{{ $ep->id }}</small>
                                        </div>
                                        @php
                                            $epEstado = $ep->estado == 1 ? ['lbl'=>'Activo','cls'=>'bg-soft-green'] : ['lbl'=>'Inactivo','cls'=>'bg-soft-red'];
                                        @endphp
                                        <span class="{{ $epEstado['cls'] }}" style="font-size:0.75rem; padding:0.25rem 0.65rem;">{{ $epEstado['lbl'] }}</span>
                                    </div>
                                    @if($ep->documentos->count() > 0)
                                        @foreach($ep->documentos as $doc)
                                        <div class="doc-file-row">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-file-pdf" style="color:#b91c1c;"></i>
                                                <span class="file-name-text">{{ $doc->nombre_original }}</span>
                                            </div>
                                            <div class="file-actions">
                                                <a href="{{ Storage::url($doc->ruta) }}" target="_blank"
                                                   class="btn-table-action" data-toggle="tooltip" title="Ver en nueva ventana">
                                                    <i class="fas fa-eye" style="color:var(--primary-blue);"></i>
                                                </a>
                                                <a href="{{ Storage::url($doc->ruta) }}" download="{{ $doc->nombre_original }}"
                                                   class="btn-table-action" data-toggle="tooltip" title="Descargar archivo">
                                                    <i class="fas fa-download" style="color:#10b981;"></i>
                                                </a>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-3 text-muted small">
                                            <i class="fas fa-folder-open mr-1"></i> Sin archivos adjuntos
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="empty-state py-4">
                                    <div class="empty-state-icon"><i class="fas fa-file-contract"></i></div>
                                    <h5 class="empty-state-title">Sin registros precontractuales</h5>
                                    <p class="empty-state-description">No se han registrado documentos de esta etapa.</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- ■ TAB: CONTRACTUAL ■ --}}
                        <div class="tab-pane fade" id="tab-contractual">
                            <div class="section-divider">
                                <div class="section-icon" style="background:rgba(16,185,129,0.1); color:#10b981;">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <div>
                                    <h5>Etapa Contractual</h5>
                                    <small>Contratos laborales y documentos de vinculación</small>
                                </div>
                            </div>
                            @forelse($empleado->etapaContractuales as $c)
                            @php $badge = $c->getStatusBadge($c->fecha_fin); @endphp
                            <div class="doc-section-card">
                                <div class="doc-header">
                                    <div>
                                        <span class="fw-bold" style="color:var(--text-main);">{{ $c->tipo_contrato }}</span>
                                        <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-check text-success mr-1"></i>
                                                {{ \Carbon\Carbon::parse($c->fecha_inicio)->format('d/m/Y') }}
                                                @if($c->fecha_fin)
                                                    → {{ \Carbon\Carbon::parse($c->fecha_fin)->format('d/m/Y') }}
                                                @else
                                                    → <em>Indefinido</em>
                                                @endif
                                            </small>
                                            <span class="badge" style="background:#f1f5f9; color:#334155;">
                                                $ {{ number_format($c->salario) }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($c->estado == 0)
                                        <span class="bg-soft-red" style="font-size:0.75rem; padding:0.25rem 0.65rem; border-radius:99px;">
                                            <i class="fas fa-ban mr-1"></i>Inactivo
                                        </span>
                                    @else
                                        <span class="{{ $badge['class'] }}" style="font-size:0.75rem; padding:0.25rem 0.75rem; border-radius:99px; font-weight:600;">
                                            <i class="{{ $badge['icon'] }} mr-1"></i>{{ $badge['label'] }}
                                        </span>
                                    @endif
                                </div>
                                @foreach($c->documentos as $doc)
                                <div class="doc-file-row">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-file-contract" style="color:#10b981;"></i>
                                        <span class="file-name-text">{{ $doc->nombre_original }}</span>
                                    </div>
                                    <div class="file-actions">
                                        <a href="{{ Storage::url($doc->ruta) }}" target="_blank"
                                           class="btn-table-action" data-toggle="tooltip" title="Ver en nueva ventana">
                                            <i class="fas fa-eye" style="color:var(--primary-blue);"></i>
                                        </a>
                                        <a href="{{ Storage::url($doc->ruta) }}" download="{{ $doc->nombre_original }}"
                                           class="btn-table-action" data-toggle="tooltip" title="Descargar">
                                            <i class="fas fa-download" style="color:#10b981;"></i>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                                @if($c->documentos->count() == 0)
                                    <div class="text-center py-3 text-muted small">
                                        <i class="fas fa-folder-open mr-1"></i> Sin archivos adjuntos
                                    </div>
                                @endif
                            </div>
                            @empty
                                <div class="empty-state py-4">
                                    <div class="empty-state-icon"><i class="fas fa-handshake"></i></div>
                                    <h5 class="empty-state-title">Sin contratos registrados</h5>
                                    <p class="empty-state-description">No se han vinculado contratos a este empleado.</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- ■ TAB: SST ■ --}}
                        <div class="tab-pane fade" id="tab-sst">
                            <div class="section-divider">
                                <div class="section-icon" style="background:rgba(239,68,68,0.1); color:#ef4444;">
                                    <i class="fas fa-heartbeat"></i>
                                </div>
                                <div>
                                    <h5>Seguridad y Salud en el Trabajo</h5>
                                    <small>Documentos médicos y de bienestar laboral</small>
                                </div>
                            </div>
                            @forelse($empleado->seguridadSaludTrabajo as $sst)
                            @php $sstBadge = $sst->getStatusBadge($sst->fecha); @endphp
                            <div class="doc-section-card">
                                <div class="doc-header">
                                    <div>
                                        <span class="fw-bold" style="color:var(--text-main);">{{ $sst->tipo_documento }}</span>
                                        <div class="mt-1">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                Fecha: {{ \Carbon\Carbon::parse($sst->fecha)->format('d/m/Y') }}
                                            </small>
                                        </div>
                                    </div>
                                    @if($sst->estado == 0)
                                        <span class="bg-soft-slate" style="font-size:0.75rem; padding:0.25rem 0.65rem; border-radius:99px; font-weight:600;">
                                            <i class="fas fa-ban mr-1"></i>Inactivo
                                        </span>
                                    @else
                                        <span class="{{ $sstBadge['class'] }}" style="font-size:0.75rem; padding:0.25rem 0.75rem; border-radius:99px; font-weight:600;">
                                            <i class="{{ $sstBadge['icon'] }} mr-1"></i>{{ strtoupper($sstBadge['label']) }}
                                        </span>
                                    @endif
                                </div>
                                @foreach($sst->documentos as $doc)
                                <div class="doc-file-row">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-file-medical" style="color:#ef4444;"></i>
                                        <span class="file-name-text">{{ $doc->nombre_original }}</span>
                                    </div>
                                    <div class="file-actions">
                                        <a href="{{ Storage::url($doc->ruta) }}" target="_blank"
                                           class="btn-table-action" data-toggle="tooltip" title="Ver documento">
                                            <i class="fas fa-eye" style="color:var(--primary-blue);"></i>
                                        </a>
                                        <a href="{{ Storage::url($doc->ruta) }}" download="{{ $doc->nombre_original }}"
                                           class="btn-table-action" data-toggle="tooltip" title="Descargar">
                                            <i class="fas fa-download" style="color:#10b981;"></i>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                                @if($sst->documentos->count() == 0)
                                    <div class="text-center py-3 text-muted small">
                                        <i class="fas fa-folder-open mr-1"></i> Sin archivos adjuntos
                                    </div>
                                @endif
                            </div>
                            @empty
                                <div class="empty-state py-4">
                                    <div class="empty-state-icon"><i class="fas fa-heartbeat"></i></div>
                                    <h5 class="empty-state-title">Sin documentos SST</h5>
                                    <p class="empty-state-description">No se han registrado documentos de seguridad y salud.</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- ■ TAB: EVALUACIONES ■ --}}
                        <div class="tab-pane fade" id="tab-evaluaciones">
                            <div class="section-divider">
                                <div class="section-icon" style="background:rgba(14,165,233,0.1); color:#0ea5e9;">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div>
                                    <h5>Evaluaciones de Desempeño</h5>
                                    <small>Historial de calificaciones y retroalimentación</small>
                                </div>
                            </div>
                            @forelse($empleado->evaluacionesDesempeno->where('estado', 1)->sortByDesc('fecha') as $ev)
                            @php
                                $scoreColor = $ev->calificacion >= 8 ? '#10b981' : ($ev->calificacion >= 5 ? '#f59e0b' : '#ef4444');
                                $scorePct   = ($ev->calificacion / 10) * 100;
                            @endphp
                            <div class="doc-section-card">
                                <div class="doc-header">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="text-align:center; min-width:56px;">
                                            <div style="font-size:1.5rem; font-weight:700; color:{{ $scoreColor }}; line-height:1;">
                                                {{ $ev->calificacion }}<span style="font-size:0.85rem; color:var(--text-muted);">/10</span>
                                            </div>
                                            <div class="score-bar-wrap" style="width:54px;">
                                                <div class="score-bar-fill" style="width:{{ $scorePct }}%; background:{{ $scoreColor }};"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="fw-bold" style="color:var(--text-main); font-size:0.9rem;">
                                                Evaluación del {{ \Carbon\Carbon::parse($ev->fecha)->format('d/m/Y') }}
                                            </span>
                                            @if($ev->observaciones)
                                            <p class="mb-0 mt-1 text-muted" style="font-size:0.82rem; font-style:italic;">
                                                "{{ Str::limit($ev->observaciones, 100) }}"
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @foreach($ev->documentos as $doc)
                                <div class="doc-file-row">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-chart-line" style="color:#0ea5e9;"></i>
                                        <span class="file-name-text">{{ $doc->nombre_original }}</span>
                                    </div>
                                    <div class="file-actions">
                                        <a href="{{ Storage::url($doc->ruta) }}" target="_blank"
                                           class="btn-table-action" data-toggle="tooltip" title="Ver documento">
                                            <i class="fas fa-eye" style="color:var(--primary-blue);"></i>
                                        </a>
                                        <a href="{{ Storage::url($doc->ruta) }}" download="{{ $doc->nombre_original }}"
                                           class="btn-table-action" data-toggle="tooltip" title="Descargar">
                                            <i class="fas fa-download" style="color:#10b981;"></i>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                                @if($ev->documentos->count() == 0)
                                    <div class="text-center py-3 text-muted small">
                                        <i class="fas fa-folder-open mr-1"></i>Sin archivos adjuntos
                                    </div>
                                @endif
                            </div>
                            @empty
                                <div class="empty-state py-4">
                                    <div class="empty-state-icon"><i class="fas fa-chart-line"></i></div>
                                    <h5 class="empty-state-title">Sin evaluaciones</h5>
                                    <p class="empty-state-description">No se han registrado evaluaciones de desempeño activas.</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- ■ TAB: FORMACIÓN ■ --}}
                        <div class="tab-pane fade" id="tab-formacion">
                            <div class="section-divider">
                                <div class="section-icon" style="background:rgba(16,185,129,0.1); color:#10b981;">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div>
                                    <h5>Formación y Certificaciones</h5>
                                    <small>Cursos, capacitaciones y títulos registrados</small>
                                </div>
                            </div>
                            @forelse($empleado->formaciones->sortByDesc('fecha_inicio') as $f)
                            <div class="doc-section-card">
                                <div class="doc-header">
                                    <div>
                                        <span class="fw-bold" style="color:var(--text-main);">{{ $f->nombre_curso }}</span>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="badge" style="background:#f1f5f9; color:#475569; font-size:0.75rem;">
                                                <i class="fas fa-university mr-1"></i>{{ $f->institucion ?? 'N/A' }}
                                            </span>
                                            @if($f->fecha_inicio)
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($f->fecha_inicio)->format('d/m/Y') }}
                                                @if($f->fecha_fin) → {{ \Carbon\Carbon::parse($f->fecha_fin)->format('d/m/Y') }} @endif
                                            </small>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="bg-soft-green" style="font-size:0.75rem; padding:0.25rem 0.65rem; border-radius:99px; font-weight:600;">
                                        <i class="fas fa-certificate mr-1"></i>Completado
                                    </span>
                                </div>
                                @forelse($f->documentos as $doc)
                                <div class="doc-file-row">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-graduation-cap" style="color:#10b981;"></i>
                                        <span class="file-name-text">{{ $doc->nombre_original }}</span>
                                    </div>
                                    <div class="file-actions">
                                        <a href="{{ Storage::url($doc->ruta) }}" target="_blank"
                                           class="btn-table-action" data-toggle="tooltip" title="Ver certificado">
                                            <i class="fas fa-eye" style="color:var(--primary-blue);"></i>
                                        </a>
                                        <a href="{{ Storage::url($doc->ruta) }}" download="{{ $doc->nombre_original }}"
                                           class="btn-table-action" data-toggle="tooltip" title="Descargar">
                                            <i class="fas fa-download" style="color:#10b981;"></i>
                                        </a>
                                    </div>
                                </div>
                                @empty
                                    <div class="text-center py-3 text-muted small">
                                        <i class="fas fa-folder-open mr-1"></i>Sin certificados adjuntos
                                    </div>
                                @endforelse
                            </div>
                            @empty
                                <div class="empty-state py-4">
                                    <div class="empty-state-icon"><i class="fas fa-graduation-cap"></i></div>
                                    <h5 class="empty-state-title">Sin formaciones registradas</h5>
                                    <p class="empty-state-description">No se han agregado cursos o certificaciones.</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- ■ TAB: COMUNICACIONES ■ --}}
                        <div class="tab-pane fade" id="tab-comunicaciones">
                            <div class="section-divider">
                                <div class="section-icon" style="background:rgba(99,102,241,0.1); color:#6366f1;">
                                    <i class="fas fa-bullhorn"></i>
                                </div>
                                <div>
                                    <h5>Comunicaciones</h5>
                                    <small>Notificaciones y comunicados enviados al empleado</small>
                                </div>
                            </div>
                            @forelse($empleado->comunicaciones->sortByDesc('fecha') as $com)
                            <div class="doc-section-card">
                                <div class="doc-header">
                                    <div>
                                        <span class="fw-bold" style="color:var(--text-main);">{{ $com->asunto }}</span>
                                        <p class="mb-0 mt-1 text-muted" style="font-size:0.82rem;">{{ Str::limit($com->mensaje, 120) }}</p>
                                    </div>
                                    <small class="text-muted text-nowrap ml-3">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        {{ \Carbon\Carbon::parse($com->fecha)->format('d/m/Y') }}
                                    </small>
                                </div>
                                @forelse($com->documentos as $doc)
                                <div class="doc-file-row">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-paperclip" style="color:#6366f1;"></i>
                                        <span class="file-name-text">{{ $doc->nombre_original }}</span>
                                    </div>
                                    <div class="file-actions">
                                        <a href="{{ Storage::url($doc->ruta) }}" target="_blank"
                                           class="btn-table-action" data-toggle="tooltip" title="Ver adjunto">
                                            <i class="fas fa-eye" style="color:var(--primary-blue);"></i>
                                        </a>
                                        <a href="{{ Storage::url($doc->ruta) }}" download="{{ $doc->nombre_original }}"
                                           class="btn-table-action" data-toggle="tooltip" title="Descargar">
                                            <i class="fas fa-download" style="color:#10b981;"></i>
                                        </a>
                                    </div>
                                </div>
                                @empty
                                    <div class="text-center py-3 text-muted small">Sin archivos adjuntos</div>
                                @endforelse
                            </div>
                            @empty
                                <div class="empty-state py-4">
                                    <div class="empty-state-icon"><i class="fas fa-bullhorn"></i></div>
                                    <h5 class="empty-state-title">Sin comunicaciones</h5>
                                    <p class="empty-state-description">No se han emitido comunicaciones para este empleado.</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- ■ TAB: SOLICITUDES ■ --}}
                        <div class="tab-pane fade" id="tab-solicitudes">
                            <div class="section-divider">
                                <div class="section-icon" style="background:rgba(249,115,22,0.1); color:#f97316;">
                                    <i class="fas fa-envelope-open-text"></i>
                                </div>
                                <div>
                                    <h5>Solicitudes</h5>
                                    <small>Permisos, vacaciones y peticiones del empleado</small>
                                </div>
                            </div>
                            @forelse($empleado->solicitudes->sortByDesc('fecha') as $sol)
                            @php
                                $solColor = [
                                    'Aprobada' => ['cls'=>'bg-soft-green','icon'=>'fa-check-circle'],
                                    'Rechazada'=> ['cls'=>'bg-soft-red','icon'=>'fa-times-circle'],
                                    'Pendiente'=> ['cls'=>'bg-soft-yellow','icon'=>'fa-clock'],
                                ][$sol->estado] ?? ['cls'=>'bg-soft-slate','icon'=>'fa-question-circle'];
                            @endphp
                            <div class="doc-section-card">
                                <div class="doc-header">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge" style="background:#f1f5f9; color:#334155; font-size:0.8rem;">
                                            <i class="fas fa-tag mr-1"></i>{{ ucfirst($sol->tipo) }}
                                        </span>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            {{ \Carbon\Carbon::parse($sol->fecha)->format('d/m/Y') }}
                                        </small>
                                    </div>
                                    <span class="{{ $solColor['cls'] }}" style="font-size:0.75rem; padding:0.25rem 0.75rem; border-radius:99px; font-weight:600;">
                                        <i class="fas {{ $solColor['icon'] }} mr-1"></i>{{ $sol->estado }}
                                    </span>
                                </div>
                                @if($sol->archivo)
                                <div class="doc-file-row">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-file-alt" style="color:#f97316;"></i>
                                        <span class="file-name-text">{{ $sol->nombre_archivo ?? basename($sol->archivo) }}</span>
                                    </div>
                                    <div class="file-actions">
                                        <a href="{{ Storage::url($sol->archivo) }}" target="_blank"
                                           class="btn-table-action" data-toggle="tooltip" title="Ver documento">
                                            <i class="fas fa-eye" style="color:var(--primary-blue);"></i>
                                        </a>
                                        <a href="{{ Storage::url($sol->archivo) }}" download
                                           class="btn-table-action" data-toggle="tooltip" title="Descargar">
                                            <i class="fas fa-download" style="color:#10b981;"></i>
                                        </a>
                                    </div>
                                </div>
                                @else
                                    <div class="text-center py-3 text-muted small">Sin archivo adjunto</div>
                                @endif
                            </div>
                            @empty
                                <div class="empty-state py-4">
                                    <div class="empty-state-icon"><i class="fas fa-envelope-open-text"></i></div>
                                    <h5 class="empty-state-title">Sin solicitudes</h5>
                                    <p class="empty-state-description">No se han registrado solicitudes para este empleado.</p>
                                </div>
                            @endforelse
                        </div>

                    </div>{{-- /tab-content --}}
                </div>{{-- /card-body --}}
            </div>{{-- /card --}}
        </div>{{-- /col --}}
    </div>{{-- /row --}}
</div>{{-- /container --}}

@endsection

@section('js')
<script>
    // Init tooltips on page load
    $(function () {
        $('[data-toggle="tooltip"]').tooltip({ placement: 'top', trigger: 'hover' });

        // Persist active tab via localStorage
        var activeTab = localStorage.getItem('empProfileTab_{{ $empleado->id }}');
        if (activeTab) {
            $('[href="' + activeTab + '"]').tab('show');
        }

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            localStorage.setItem('empProfileTab_{{ $empleado->id }}', $(e.target).attr('href'));
        });
    });
</script>
@stop
