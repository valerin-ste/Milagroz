@extends('adminlte::page')

@section('content_header')
<div class="page-header d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="page-title fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Solicitudes
        </h2>
        <p class="page-subtitle text-muted mb-0">
            Gestión y control de solicitudes del personal.
        </p>
    </div>

    <div class="page-actions">
        <a href="{{ route('admin.solicitudes.create') }}" class="btn btn-orange">
            <i class="fas fa-plus me-2"></i> Nueva Solicitud
        </a>
    </div>
</div>
@stop

@section('content')
@php use Illuminate\Support\Facades\Storage; @endphp
<div class="container-fluid px-2">

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center"
             style="background-color: #ecfdf5; color: #047857; border: none; border-radius: var(--radius-md);">
            <i class="fas fa-check-circle fa-lg me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <form method="GET" action="{{ route('admin.solicitudes.index') }}" class="mb-4">
        <div class="card shadow-sm border-0 rounded-4" style="background-color: #f8fafc;">
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    
                    {{-- CAMPO: BUSCAR EMPLEADO --}}
                    <div class="col-md-5">
                        <label class="text-muted small fw-bold mb-2 ps-1">Buscar por Empleado o Tipo</label>
                        <div class="d-flex align-items-center bg-white" style="border-radius: 30px; height: 45px; padding: 0 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="fas fa-search text-muted" style="font-size: 14px;"></i>
                            <input type="text" name="buscar" class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                   placeholder="Nombre o tipo..." value="{{ request('buscar') }}" style="outline: none;">
                        </div>
                    </div>

                    {{-- CAMPO: ESTADO --}}
                    <div class="col-md-4">
                        <label class="text-muted small fw-bold mb-2 ps-1">Estado</label>
                        <div class="d-flex align-items-center bg-white" style="border-radius: 30px; height: 45px; padding: 0 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="fas fa-tasks text-muted" style="font-size: 14px;"></i>
                            <select name="estado" class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0" style="outline: none;">
                                <option value="">-- Todos --</option>
                                <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="aprobado" {{ request('estado') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                                <option value="rechazado" {{ request('estado') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                            </select>
                        </div>
                    </div>

                    {{-- BOTONES --}}
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-orange flex-grow-1 fw-bold d-flex justify-content-center align-items-center" style="border-radius: 30px; height: 45px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                <i class="fas fa-filter me-2"></i> Filtrar
                            </button>
                            <a href="{{ route('admin.solicitudes.index') }}" class="btn bg-white border flex-grow-1 fw-bold text-secondary d-flex justify-content-center align-items-center" style="border-radius: 30px; height: 45px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                                <i class="fas fa-sync-alt me-2"></i> Limpiar
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th class="ps-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Empleado</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Tipo</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Estado</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Fecha</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Soporte Digital</th>
                            <th class="text-center pe-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $s)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            {{-- EMPLEADO --}}
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center" style="gap: 1rem;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 45px; height: 45px; background-color: rgba(19,182,236,0.1); color: var(--primary-blue);">
                                        <span class="fw-bold" style="font-size: 1.1rem;">
                                            {{ strtoupper(substr($s->empleado->persona->nombres ?? 'X', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                            {{ $s->empleado->persona->nombres ?? '' }} {{ $s->empleado->persona->apellidos ?? '' }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.85rem;">
                                            CC: {{ $s->empleado->persona->numero_documento ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- TIPO --}}
                            <td class="py-3">
                                <span class="d-block" style="color: #334155; font-weight: 500;">
                                    {{ $s->tipo }}
                                </span>
                            </td>

                            {{-- ESTADO --}}
                            <td class="py-3">
                                @if($s->estado == 'pendiente')
                                    <span class="badge bg-warning text-dark px-2 py-1 rounded-pill">Pendiente</span>
                                @elseif($s->estado == 'aprobado')
                                    <span class="badge bg-success px-2 py-1 rounded-pill">Aprobado</span>
                                @else
                                    <span class="badge bg-danger px-2 py-1 rounded-pill">Rechazado</span>
                                @endif
                            </td>

                            {{-- FECHA --}}
                            <td class="py-3" style="color: #475569;">
                                <div class="mb-1" style="font-size: 0.9rem;">
                                    📅 {{ \Carbon\Carbon::parse($s->fecha)->translatedFormat('d M Y') }}
                                </div>
                            </td>

                            {{-- ARCHIVOS --}}
                            <td class="py-3">
                                @if($s->documentos->count() > 0)
                                    <button class="btn btn-outline-secondary btn-sm"
                                            data-toggle="modal"
                                            data-target="#docsModal{{ $s->id }}">
                                        📂 Ver documentos ({{ $s->documentos->count() }})
                                    </button>
                                @else
                                    <span class="text-muted small fst-italic"><i class="fas fa-file-slash me-1"></i> Sin archivos</span>
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td class="text-center pe-4 py-3">
                                <div class="d-flex justify-content-center gap-2">
                                    @if($s->activo)

                                        {{-- EDITAR --}}
                                        <a href="{{ route('admin.solicitudes.edit', $s->id) }}"
                                           class="btn btn-sm btn-icon btn-outline-primary"
                                           data-toggle="tooltip" data-placement="top" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        {{-- DESACTIVAR --}}
                                        <form action="{{ route('admin.solicitudes.toggle', $s->id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-icon btn-outline-danger"
                                                    data-toggle="tooltip" data-placement="top"
                                                    title="Desactivar"
                                                    onclick="return confirm('¿Desea desactivar esta solicitud?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>

                                    @else

                                        {{-- EDITAR DESHABILITADO --}}
                                        <button class="btn btn-sm btn-icon border-0 text-muted opacity-50"
                                                data-toggle="tooltip" data-placement="top"
                                                title="Edición no disponible (Inactivo)"
                                                style="cursor:not-allowed;" disabled>
                                            <i class="fas fa-pen"></i>
                                        </button>

                                        {{-- ACTIVAR --}}
                                        <form action="{{ route('admin.solicitudes.toggle', $s->id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-light border text-success"
                                                    data-toggle="tooltip" data-placement="top"
                                                    title="Reactivar solicitud" style="border-radius: 30px;">
                                                <i class="fas fa-check-circle"></i> Activar
                                            </button>
                                        </form>

                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background-color: rgba(19,182,236,0.1);">
                                        <i class="fas fa-envelope-open-text fa-2x" style="color: var(--primary-blue, #13b6ec);"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1" style="color: #64748b;">No hay solicitudes registradas</h5>
                                    <p class="mb-0">Comienza registrando la primera solicitud del personal.</p>
                                    <a href="{{ route('admin.solicitudes.create') }}" class="btn btn-orange btn-sm px-4 mt-3 rounded-pill">
                                        <i class="fas fa-plus mr-1"></i> Nueva Solicitud
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($solicitudes->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4 rounded-bottom-4">
            {{ $solicitudes->links() }}
        </div>
        @endif
    </div>

</div>

{{-- MODALES DE DOCUMENTOS --}}
@foreach($solicitudes as $s)
    <div class="modal fade" id="docsModal{{ $s->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4">

                <div class="modal-header">
                    <h5 class="modal-title">Soporte Digital</h5>
                    <button type="button" class="btn-close" data-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <ul class="list-group">
                        @forelse($s->documentos as $archivo)
                        <li class="list-group-item d-flex justify-content-between align-items-center">

                            <span class="text-truncate" style="max-width: 200px;">
                                📄 {{ $archivo->nombre_original }}
                            </span>

                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.documentos.view', $archivo->id) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    👁️
                                </a>

                                <a href="{{ route('admin.documentos.download', $archivo->id) }}"
                                   class="btn btn-sm btn-outline-success">
                                    ⬇️
                                </a>
                            </div>

                        </li>
                        @empty
                        <li class="list-group-item text-muted">
                            Sin documentos
                        </li>
                        @endforelse
                    </ul>
                </div>

            </div>
        </div>
    </div>
@endforeach
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

@section('css')
<style>
.btn-icon:hover {
    transform: scale(1.1);
    transition: 0.2s;
}

.btn-orange {
    background-color: #ff6a00;
    border: none;
    color: #fff;
}

.btn-orange:hover {
    background-color: #e65c00;
    color: #fff;
}
</style>
@stop