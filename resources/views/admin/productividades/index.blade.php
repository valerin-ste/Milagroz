@extends('adminlte::page')

@section('title', 'Gestión de Productividad')

@section('content_header')
<div class="page-header d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="page-title fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Módulo de Productividad
        </h2>
        <p class="page-subtitle text-muted mb-0">
            Listado de seguimientos, observaciones y actividades del personal.
        </p>
    </div>

    <div class="page-actions">
        <a href="{{ route('admin.productividades.create') }}" class="btn btn-orange">
            <i class="fas fa-plus me-2"></i> Nuevo Registro
        </a>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center border-0 shadow-sm" style="background-color: #ecfdf5; color: #047857; border-radius: var(--radius-md);">
            <i class="fas fa-check-circle fa-lg me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <form method="GET" action="{{ route('admin.productividades.index') }}" class="mb-4">
        <div class="card shadow-sm border-0 rounded-4" style="background-color: #f8fafc;">
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    
                    {{-- CAMPO: BUSCAR --}}
                    <div class="col-md-4">
                        <label class="text-muted small fw-bold mb-2 ps-1">Buscar Empleado / Título</label>
                        <div class="d-flex align-items-center bg-white" style="border-radius: 30px; height: 45px; padding: 0 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="fas fa-search text-muted" style="font-size: 14px;"></i>
                            <input type="text" name="buscar" class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                   placeholder="Nombres o Título..." value="{{ request('buscar') }}" style="outline: none;">
                        </div>
                    </div>

                    {{-- CAMPO: ESTADO --}}
                    <div class="col-md-3">
                        <label class="text-muted small fw-bold mb-2 ps-1">Estado</label>
                        <div class="d-flex align-items-center bg-white" style="border-radius: 30px; height: 45px; padding: 0 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="fas fa-filter text-muted" style="font-size: 14px;"></i>
                            <select name="estado" class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0" style="outline: none;">
                                <option value="">-- Todos --</option>
                                <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                                <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                    </div>

                    {{-- CAMPO: FECHA --}}
                    <div class="col-md-3">
                        <label class="text-muted small fw-bold mb-2 ps-1">Fecha</label>
                        <div class="d-flex align-items-center bg-white" style="border-radius: 30px; height: 45px; padding: 0 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="far fa-calendar-alt text-muted" style="font-size: 14px;"></i>
                            <input type="date" name="fecha" class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                   value="{{ request('fecha') }}" style="outline: none;">
                        </div>
                    </div>

                    {{-- BOTONES --}}
                    <button type="submit"
                        class="btn btn-orange flex-grow-1 fw-bold d-flex justify-content-center align-items-center gap-2"
                        style="border-radius: 30px; height: 45px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">

                        <i class="fas fa-filter"></i>
                        Filtrar

                    </button>

                    <a href="{{ route('admin.productividades.index') }}"
                        class="btn bg-white border flex-grow-1 fw-bold text-secondary d-flex justify-content-center align-items-center gap-2"
                        style="border-radius: 30px; height: 45px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">

                            <i class="fas fa-sync-alt"></i>
                            Limpiar
                    </a>

                </div>
            </div>
        </div>
    </form>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th class="ps-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Empleado</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Detalles</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Fecha</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Archivo</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Estado</th>
                            <th class="text-center pe-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productividades as $p)
                        <tr style="border-bottom: 1px solid #f1f5f9; {{ $p->estado == 0 ? 'background-color: #f8fafc; opacity: 0.8;' : '' }}">
                
                            {{-- EMPLEADO --}}
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center" style="gap: 1rem;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 45px; height: 45px; background-color: rgba(19,182,236,0.1); color: var(--primary-blue);">
                                        <span class="fw-bold" style="font-size: 1.1rem;">
                                            {{ strtoupper(substr($p->empleado->persona->nombres ?? 'U', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                            {{ $p->empleado->persona->nombres ?? '' }} {{ $p->empleado->persona->apellidos ?? '' }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.85rem;">
                                            {{ $p->empleado->cargo }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            {{-- DETALLES --}}
                            <td class="py-3">
                                <span class="d-block" style="color: #334155; font-weight: 500;">
                                    {{ $p->titulo }}
                                </span>
                                <span class="d-block text-muted small mt-1">
                                    <i class="fas fa-tag me-1"></i> {{ $p->tipo ?? 'General' }}
                                </span>
                            </td>

                            {{-- FECHA --}}
                            <td class="py-3" style="color: #475569;">
                                {{ \Carbon\Carbon::parse($p->fecha)->format('d/m/Y') }}
                            </td>

                            {{-- ARCHIVO --}}
                            <td class="py-3">
                                @if($p->archivo)
                                    <button class="btn btn-outline-secondary btn-sm"
                                            data-toggle="modal"
                                            data-target="#docsModal{{ $p->id }}" style="border-radius: 8px; font-weight: 500;">
                                        📂 Documentos (1)
                                    </button>
                                @else
                                    <span class="text-muted small fst-italic"><i class="fas fa-file-slash me-1"></i> Sin archivo</span>
                                @endif
                            </td>

                            {{-- ESTADO --}}
                            <td class="py-3">
                                @if($p->estado == 1)
                                    <span class="badge bg-success px-2 py-1 rounded-pill" style="font-size: 0.8rem;">Activo</span>
                                @else
                                    <span class="badge bg-secondary px-2 py-1 rounded-pill" style="font-size: 0.8rem;">Inactivo</span>
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td class="text-center pe-4 py-3">
                                <div class="d-flex justify-content-center gap-2">
                                    @if($p->estado == 1)
                                        <a href="{{ route('admin.productividades.edit', $p) }}"
                                           class="btn btn-sm btn-icon btn-outline-primary"
                                           data-toggle="tooltip" data-placement="top" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <form action="{{ route('admin.productividades.destroy', $p) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-icon btn-outline-danger"
                                                    data-toggle="tooltip" data-placement="top" title="Desactivar"
                                                    onclick="return confirm('¿Desactivar registro?');">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-icon border-0 text-muted opacity-50"
                                                data-toggle="tooltip" data-placement="top" title="Edición no disponible"
                                                disabled>
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <form action="{{ route('admin.productividades.toggle', $p->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light border text-success"
                                                    data-toggle="tooltip" data-placement="top" title="Reactivar registro" style="border-radius: 30px;">
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
                                        <i class="fas fa-chart-line fa-2x" style="color: var(--primary-blue, #13b6ec);"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1" style="color: #64748b;">Sin registros de productividad</h5>
                                    <p class="mb-0">Registre las actividades, seguimientos o productividad del personal.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($productividades->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4 rounded-bottom-4">
            {{ $productividades->links() }}
        </div>
        @endif
    </div>
</div>

            {{-- MODALES DE DOCUMENTOS --}}
            @foreach($productividades as $p)

                @if($p->archivo)

                <div class="modal fade" id="docsModal{{ $p->id }}" tabindex="-1" role="dialog">

                    <div class="modal-dialog modal-dialog-centered" role="document">

                        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">

                            {{-- HEADER --}}
                            <div class="modal-header bg-light border-0 py-3"
                                style="border-radius: 15px 15px 0 0;">

                                <h5 class="modal-title fw-bold text-dark">
                                    <i class="fas fa-folder-open text-warning me-2"></i>
                                    Documentos
                                </h5>

                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>

                            </div>

                            {{-- BODY --}}
                            <div class="modal-body p-4">

                                    @foreach($p->documentos as $doc)

                                        @php

                                            $extension = strtolower(pathinfo($doc->nombre_original, PATHINFO_EXTENSION));

                                            if($extension == 'pdf') {
                                                $icon = 'fas fa-file-pdf text-danger';
                                            } elseif(in_array($extension, ['jpg', 'jpeg', 'png'])) {
                                                $icon = 'fas fa-file-image text-primary';
                                            } elseif(in_array($extension, ['doc', 'docx'])) {
                                                $icon = 'fas fa-file-word text-info';
                                            } elseif(in_array($extension, ['xls', 'xlsx'])) {
                                                $icon = 'fas fa-file-excel text-success';
                                            } else {
                                                $icon = 'fas fa-file text-secondary';
                                            }

                                        @endphp

                                        <div class="border rounded p-3 mb-2 d-flex justify-content-between align-items-center" style="border-color: #e2e8f0 !important; min-height: 85px;">

                                            {{-- IZQUIERDA --}}
                                            <div class="d-flex align-items-center gap-3">

                                            {{-- ICONO --}}
                                            <div class="d-flex align-items-center justify-content-center"
                                                style="width: 45px; height: 45px; border-radius: 10px; background-color: #f8fafc;">

                                                <i class="{{ $icon }}"
                                                style="font-size: 1.4rem;"></i>

                                            </div>

                                        {{-- TEXTO --}}
                                        <div class="d-flex flex-column">

                                                <span class="text-muted"
                                                    style="font-size: 0.78rem;">
                                                    Archivo adjunto
                                                </span>

                                                <span class="fw-bold text-dark"
                                                    style="font-size: 0.92rem; line-height: 1.2;">
                                                    {{ $doc->nombre_original }}
                                                </span>

                                            </div>

                                        </div>

                                            {{-- DERECHA --}}
                                            <div class="d-flex gap-2">

                                                <a href="{{ route('admin.productividades.archivo.view', $p->id) }}"
                                                target="_blank"
                                                class="btn btn-sm btn-outline-primary">

                                                    <i class="fas fa-eye"></i>

                                                </a>

                                                <a href="{{ route('admin.productividades.archivo.download', $p->id) }}"
                                                class="btn btn-sm btn-outline-success">

                                                    <i class="fas fa-download"></i>

                                                </a>

                                            </div>

                                        </div>

                                    @endforeach

                                    </div>

                                    </div>

                                </div>

                            </div>

                        @endif

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
@endsection
