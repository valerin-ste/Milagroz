@extends('adminlte::page')

@section('content_header')
<div class="page-header d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="page-title fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Gestión de Empleados
        </h2>
        <p class="page-subtitle text-muted mb-0">
            Administración y control del personal médico y administrativo.
        </p>
    </div>

    <div class="page-actions d-flex gap-2">
        @can('exportar-empleados')
        <div class="dropdown">
            <button class="btn btn-light border dropdown-toggle" type="button" data-toggle="dropdown">
                Reportes
            </button>
            <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                <a class="dropdown-item py-2" href="{{ route('admin.empleados.reporte.pdf', request()->all()) }}">
                    Exportar PDF
                </a>
            </div>
        </div>
        @endcan

        @can('crear-empleados')
        <a href="{{ route('admin.empleados.create') }}" class="btn btn-orange">
            Nuevo Empleado
        </a>
        @endcan
    </div>
</div>

<form method="GET" action="{{ route('admin.empleados.index') }}" class="mb-4">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                
                {{-- BÚSQUEDA GENERAL --}}
                <div class="col-md-3">
                    <label class="text-muted small fw-bold mb-1">Buscar Empleado</label>
                    <div class="search-container" style="border-radius: 10px; padding: 0;">
                        <input type="text" name="buscar" class="form-control search-input py-2" style="border-radius: 10px;"
                               placeholder="Nombre o documento..." value="{{ request('buscar') }}">
                    </div>
                </div>

                {{-- ESTADO --}}
                <div class="col-md-2">
                    <label class="text-muted small fw-bold mb-1">Estado</label>
                    <div class="search-container" style="border-radius: 10px; padding: 0;">
                        <select name="estado" class="form-control search-input py-2" style="border-radius: 10px; cursor: pointer; appearance: none; background-color: transparent;">
                            <option value="">Todos</option>
                            <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ request('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        <i class="fas fa-chevron-down" style="position: absolute; right: 15px; color: #999; font-size: 12px; pointer-events: none;"></i>
                    </div>
                </div>

                {{-- ÁREA --}}
                <div class="col-md-2">
                    <label class="text-muted small fw-bold mb-1">Área</label>
                    <div class="search-container" style="border-radius: 10px; padding: 0;">
                        <select name="area_id" class="form-control search-input py-2" style="border-radius: 10px; cursor: pointer; appearance: none; background-color: transparent;">
                            <option value="">Todas</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down" style="position: absolute; right: 15px; color: #999; font-size: 12px; pointer-events: none;"></i>
                    </div>
                </div>

                {{-- SEDE --}}
                <div class="col-md-2">
                    <label class="text-muted small fw-bold mb-1">Sede</label>
                    <div class="search-container" style="border-radius: 10px; padding: 0;">
                        <select name="sede_id" class="form-control search-input py-2" style="border-radius: 10px; cursor: pointer; appearance: none; background-color: transparent;">
                            <option value="">Todas</option>
                            @foreach($sedes as $sede)
                                <option value="{{ $sede->id }}" {{ request('sede_id') == $sede->id ? 'selected' : '' }}>
                                    {{ $sede->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down" style="position: absolute; right: 15px; color: #999; font-size: 12px; pointer-events: none;"></i>
                    </div>
                </div>

                {{-- BOTONES ACCIÓN --}}
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-orange flex-grow-1 fw-bold" style="border-radius: 10px; height: 42px;">
                            Filtrar
                        </button>
                        <a href="{{ route('admin.empleados.index') }}" class="btn btn-light border flex-grow-1 fw-bold text-secondary" style="border-radius: 10px; height: 42px; display: flex; align-items: center; justify-content: center;">
                            Limpiar
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>
@stop

@section('content')
<div class="container-fluid px-2">

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center"
             style="background-color: #ecfdf5; color: #047857; border: none;">
            <i class="fas fa-check-circle fa-lg me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-bottom py-3 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small fw-bold">
                    {{ $empleados->total() }} EMPLEADOS ENCONTRADOS
                </span>
                <span class="text-muted small">
                    Página {{ $empleados->currentPage() }}
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th class="ps-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Empleado</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Área / Sede</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Rol</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Estado</th>
                            <th class="text-center pe-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Tipo Contrato</th>
                            <th class="text-center pe-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empleados as $e)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center" style="gap: 1rem;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 45px; height: 45px; background-color: rgba(19,182,236,0.1); color: var(--primary-blue);">
                                        <span class="fw-bold" style="font-size: 1.1rem;">
                                            {{ strtoupper(substr($e->persona->nombres ?? 'X', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                            {{ $e->persona->nombres }} {{ $e->persona->apellidos }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.85rem;">
                                            CC: {{ $e->persona->numero_documento }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3">
                                <div class="mb-1" style="color: #334155; font-weight: 500;">
                                    {{ $e->area->nombre ?? 'Sin área' }}
                                </div>
                                <div class="text-muted small">
                                    {{ $e->sede->nombre ?? 'Sin sede' }}
                                </div>
                            </td>

                            <td class="py-3">
                                <span class="badge bg-light text-secondary border px-3 py-2" style="border-radius: 8px; font-weight: 500;">
                                    {{ $e->rol->nombre ?? 'Sin rol' }}
                                </span>
                            </td>

                            <td class="py-3">
                                @if($e->estado == 1)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>

                            <td class="text-center py-3">
                                @if($e->tipo_contrato)
                                    <span class="badge bg-info text-dark px-3 py-2">
                                        {{ $e->tipo_contrato }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2">
                                        Sin contrato
                                    </span>
                                @endif
                            </td>

                            <td class="text-center pe-4 py-3">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a href="{{ route('admin.empleados.show', $e) }}"
                                       class="btn btn-sm btn-icon btn-outline-info"
                                       data-toggle="tooltip" title="Ver Perfil">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($e->estado == 1)
                                        @can('editar-empleados')
                                        <a href="{{ route('admin.empleados.edit', $e) }}"
                                           class="btn btn-sm btn-icon btn-outline-primary"
                                           data-toggle="tooltip" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        @endcan

                                        @can('eliminar-empleados')
                                        <form method="POST" action="{{ route('admin.empleados.destroy', $e) }}" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-danger"
                                                    data-toggle="tooltip" title="Inactivar"
                                                    onclick="return confirm('¿Confirma que desea inactivar este empleado?');">
                                                <i class="fas fa-user-slash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    @else
                                        @can('editar-empleados')
                                        <form method="POST" action="{{ route('admin.empleados.toggle', $e->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-success"
                                                    data-toggle="tooltip" title="Activar"
                                                    onclick="return confirm('¿Desea activar este empleado nuevamente?');">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <h5>No se encontraron empleados con los filtros aplicados</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($empleados->hasPages())
        <div class="card-footer bg-white border-top py-3 px-4 rounded-bottom-4">
            {{ $empleados->links() }}
        </div>
        @endif
    </div>

</div>
@stop

@push('css')
<style>
.btn-icon { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s; }
.btn-icon:hover { transform: scale(1.1); }

.search-container {
    position: relative;
    display: flex;
    align-items: center;
    background: #fff;
    border-radius: 30px;
    padding: 5px 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: 1px solid #ddd;
}

.search-input {
    border: none;
    outline: none;
    box-shadow: none;
    flex: 1;
    padding-left: 15px;
    border-radius: 30px;
}

.search-input:focus {
    box-shadow: none;
}

.search-icon {
    position: absolute;
    left: 15px;
    color: #999;
    font-size: 14px;
}

.btn-orange {
    background-color: #ff6a00;
    border: none;
    color: #fff;
    border-radius: 8px;
    transition: all 0.2s;
    font-weight: 600;
}

.btn-orange:hover {
    background-color: #e65c00;
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255,106,0,0.25);
}

.badge { font-size: 0.78rem; padding: 0.35em 0.7em; border-radius: 6px; font-weight: 600; }
</style>
@endpush

@push('scripts')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush