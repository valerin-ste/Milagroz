@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Solicitudes
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">
            Gestión y control de solicitudes del personal.
        </p>
    </div>

    <a href="{{ route('admin.solicitudes.create') }}" class="btn btn-orange">
        <i class="fas fa-plus me-2"></i> Nueva Solicitud
    </a>
</div>
@stop

@section('content')
@php use Illuminate\Support\Facades\Storage; @endphp

    <form method="GET" action="{{ route('admin.solicitudes.index') }}" class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="small font-weight-bold text-muted mb-1">Buscar por Empleado o Tipo</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-light text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="buscar" class="form-control border-light bg-light shadow-none" placeholder="Nombre o tipo..." value="{{ request('buscar') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted mb-1">Estado</label>
                    <select name="estado" class="form-control border-light bg-light shadow-none">
                        <option value="">-- Todos --</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="aprobado" {{ request('estado') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                        <option value="rechazado" {{ request('estado') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1 shadow-xs">
                            <i class="fas fa-filter mr-1"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-light border flex-grow-1 shadow-xs">
                            <i class="fas fa-undo mr-1"></i> Limpiar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card border-0">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead>
                        <tr>
                            <th class="ps-4">Empleado</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Soporte Digital</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($solicitudes as $s)
                        <tr>

                            {{-- EMPLEADO --}}
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                         style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); color: var(--primary-blue);">
                                        <span class="fw-bold">
                                            {{ strtoupper(substr($s->empleado->persona->nombres ?? 'X', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                            {{ $s->empleado->persona->nombres ?? '' }}
                                            {{ $s->empleado->persona->apellidos ?? '' }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.85rem;">
                                            CC: {{ $s->empleado->persona->numero_documento ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- TIPO --}}
                            <td style="color: #334155; font-weight: 500;">
                                {{ $s->tipo }}
                            </td>

                            {{-- ESTADO --}}
                            <td>
                                @if($s->estado == 'pendiente')
                                    <span class="badge-soft-warning">Pendiente</span>
                                @elseif($s->estado == 'aprobado')
                                    <span class="badge-soft-success">Aprobado</span>
                                @else
                                    <span class="badge-soft-danger">Rechazado</span>
                                @endif
                            </td>

                            {{-- FECHA --}}
                            <td style="color: #475569;">
                                <i class="fas fa-calendar-alt text-muted me-1"></i>
                                {{ \Carbon\Carbon::parse($s->fecha)->translatedFormat('d M Y') }}
                            </td>

                            {{-- ARCHIVOS --}}
                            <td>
                                @forelse($s->documentos as $doc)
                                    <a href="{{ route('admin.documentos.view', $doc->id) }}?t={{ time() }}"
                                       target="_blank"
                                       class="doc-file-clickable mb-1 d-inline-block"
                                       data-toggle="tooltip" data-boundary="window"
                                       title="{{ $doc->nombre_original }}"
                                       style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                                        <i class="fas fa-file-alt" style="color:#f97316;"></i>
                                        <span class="file-name-text text-truncate" style="max-width: 120px;">{{ $doc->nombre_original }}</span>
                                    </a>
                                @empty
                                    <span class="text-muted small fst-italic">Sin archivos</span>
                                @endforelse
                            </td>

                            {{-- ACCIONES --}}
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">

                                    @if($s->activo)

                                        {{-- EDITAR --}}
                                        <a href="{{ route('admin.solicitudes.edit', $s->id) }}"
                                           class="btn btn-sm btn-light-custom px-3"
                                           data-toggle="tooltip" data-placement="top"
                                           title="Editar solicitud">
                                            <i class="fas fa-pen text-muted"></i>
                                        </a>

                                        {{-- DESACTIVAR --}}
                                        <form action="{{ route('admin.solicitudes.toggle', $s->id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-light-custom px-3 text-danger"
                                                    data-toggle="tooltip" data-placement="top"
                                                    title="Desactivar solicitud"
                                                    onclick="return confirm('¿Desea desactivar esta solicitud?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>

                                    @else

                                        {{-- EDITAR DESHABILITADO --}}
                                        <button class="btn btn-sm btn-light-custom px-3 opacity-50"
                                                data-toggle="tooltip" data-placement="top"
                                                title="Edición no disponible (Inactivo)"
                                                style="cursor:not-allowed;" disabled>
                                            <i class="fas fa-pen text-muted"></i>
                                        </button>

                                        {{-- ACTIVAR --}}
                                        <form action="{{ route('admin.solicitudes.toggle', $s->id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-light-custom px-3 text-success"
                                                    data-toggle="tooltip" data-placement="top"
                                                    title="Reactivar solicitud">
                                                <i class="fas fa-check-circle"></i> Activar
                                            </button>
                                        </form>

                                    @endif

                                </div>
                            </td>

                        </tr>

                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state py-3">
                                    <div class="empty-state-icon"><i class="fas fa-envelope-open-text"></i></div>
                                    <h5 class="empty-state-title">No hay solicitudes registradas</h5>
                                    <p class="empty-state-description">Comienza registrando la primera solicitud del personal.</p>
                                    <a href="{{ route('admin.solicitudes.create') }}" class="btn btn-orange btn-sm px-4">
                                        <i class="fas fa-plus mr-1"></i> Nueva Solicitud
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>
        <div class="card-footer bg-white border-top border-light py-3 px-4">
            {{ $solicitudes->links() }}
        </div>
    </div>

</div>
@stop

@section('js')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip({ 
            placement: 'top', 
            trigger: 'hover',
            boundary: 'window' 
        });
    });
</script>
@stop