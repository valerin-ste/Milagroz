@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">Gestión de Empleados</h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Administración y control del personal médico y administrativo.</p>
    </div>
    <a href="{{ route('admin.empleados.create') }}" class="btn btn-orange">
        <i class="fas fa-plus me-2"></i> Nuevo Empleado
    </a>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center" style="background-color: #ecfdf5; color: #047857; border: none; border-radius: var(--radius-md);">
            <i class="fas fa-check-circle fa-lg me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <form method="GET" action="{{ route('admin.empleados.index') }}" class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="small font-weight-bold text-muted mb-1">Búsqueda General</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-light text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="buscar" class="form-control border-light bg-light shadow-none" placeholder="Nombre, apellido o documento..." value="{{ request('buscar') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted mb-1">Estado</label>
                    <select name="estado" class="form-control border-light bg-light shadow-none">
                        <option value="">-- Todos --</option>
                        <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted mb-1">Área</label>
                    <select name="area_id" class="form-control border-light bg-light shadow-none text-truncate">
                        <option value="">-- Todas --</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted mb-1">Sede</label>
                    <select name="sede_id" class="form-control border-light bg-light shadow-none">
                        <option value="">-- Todas --</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ request('sede_id') == $sede->id ? 'selected' : '' }}>{{ $sede->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1 shadow-xs">
                            <i class="fas fa-filter mr-1"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.empleados.index') }}" class="btn btn-light border flex-grow-1 shadow-xs">
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
                            <th>Área / Departamento</th>
                            <th>Sede / Ubicación</th>
                            <th>Rol Sistema</th>
                            <th>Estado</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empleados as $e)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); color: var(--primary-blue);">
                                        <span class="fw-bold">{{ strtoupper(substr($e->persona->nombres ?? 'U', 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $e->persona->nombres ?? '' }} {{ $e->persona->apellidos ?? '' }}</div>
                                        <div class="text-muted" style="font-size: 0.85rem;">CI: {{ $e->persona->numero_documento ?? '' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="d-block" style="color: #334155; font-weight: 500;">{{ $e->area->nombre ?? 'Sin área' }}</span>
                                <span class="text-muted" style="font-size: 0.85rem;">{{ $e->cargo }}</span>
                            </td>

                            <td style="color: #475569;">
                                <i class="fas fa-hospital-alt me-1 text-muted"></i> {{ $e->sede->nombre ?? 'Sin sede' }}
                            </td>

                            <td>
                                <span class="px-2 py-1 rounded" style="background-color: #f1f5f9; color: #475569; font-size: 0.85rem; font-weight: 500;">
                                    {{ $e->rol->nombre ?? 'Sin rol' }}
                                </span>
                            </td>

                            <td>
                                @if($e->estado == 1)
                                    <span class="badge rounded-pill px-3 py-2 fw-semibold"
                                        style="background-color: #ecfdf5; color: #047857; font-size: 0.8rem;">
                                        <i class="fas fa-check-circle me-1"></i> Activo
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-3 py-2 fw-semibold"
                                        style="background-color: #fef2f2; color: #b91c1c; font-size: 0.8rem;">
                                        <i class="fas fa-times-circle me-1"></i> Inactivo
                                    </span>
                                @endif
                            </td>

                            <td class="text-center pe-4">
                                <div class="action-container">
                                    <a href="{{ route('admin.empleados.show', $e) }}" class="btn-table-action" title="Ver Perfil">
                                        <i class="fas fa-eye text-primary"></i>
                                    </a>

                                    @if($e->estado == 1)
                                        <a href="{{ route('admin.empleados.edit', $e) }}" class="btn-table-action" title="Editar">
                                            <i class="fas fa-pen text-muted"></i>
                                        </a>

                                        <form action="{{ route('admin.empleados.destroy', $e) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-table-action" title="Desactivar" onclick="return confirm('¿Confirma que desea DESACTIVAR a este empleado?');">
                                                <i class="fas fa-user-slash text-danger"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn-table-action opacity-50" title="No editable (Inactivo)" style="cursor:not-allowed;">
                                            <i class="fas fa-pen text-muted"></i>
                                        </button>
                                        <form action="{{ route('admin.empleados.toggle', $e->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-table-action" title="Activar">
                                                <i class="fas fa-check-circle text-success"></i>
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
                                    <i class="fas fa-users fa-3x mb-3" style="color: #cbd5e1;"></i>
                                    <h5 class="fw-bold mb-1" style="color: #64748b;">No hay empleados registrados</h5>
                                    <p class="mb-0">Comience agregando uno nuevo.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($empleados->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4">
            {{ $empleados->links() }}
        </div>
        @endif
    </div>

</div>
@endsection