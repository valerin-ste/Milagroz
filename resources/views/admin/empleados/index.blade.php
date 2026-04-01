@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.65rem; letter-spacing: -0.5px;">
            <i class="fas fa-users mr-2" style="color: var(--primary-blue); font-size: 1.3rem;"></i>
            Gestión de Empleados
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.875rem;">
            Administración y control del personal médico y administrativo.
        </p>
    </div>
    <a href="{{ route('admin.empleados.create') }}" class="btn btn-orange">
        <i class="fas fa-plus mr-2"></i> Nuevo Empleado
    </a>
</div>
@stop

@section('content')
<div class="container-fluid px-2 pb-4">

    @if(session('success'))
        <div class="alert d-flex align-items-center mb-4"
             style="background:#ecfdf5; color:#047857; border:none; border-radius:var(--radius-lg); padding:0.85rem 1.25rem; font-size:0.9rem;">
            <i class="fas fa-check-circle mr-3" style="font-size:1.1rem;"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- ── FILTROS ─────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('admin.empleados.index') }}" class="filter-card">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="filter-label">Búsqueda general</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="buscar" class="form-control"
                           placeholder="Nombre, apellido o documento..."
                           value="{{ request('buscar') }}">
                </div>
            </div>

            <div class="col-md-2">
                <label class="filter-label">Estado</label>
                <select name="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="filter-label">Área</label>
                <select name="area_id" class="form-control">
                    <option value="">Todas</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                            {{ $area->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="filter-label">Sede</label>
                <select name="sede_id" class="form-control">
                    <option value="">Todas</option>
                    @foreach($sedes as $sede)
                        <option value="{{ $sede->id }}" {{ request('sede_id') == $sede->id ? 'selected' : '' }}>
                            {{ $sede->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1" style="height:38px;">
                        <i class="fas fa-filter mr-1"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.empleados.index') }}" class="btn btn-light border btn-sm flex-grow-1" style="height:38px;">
                        <i class="fas fa-undo mr-1"></i> Limpiar
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- ── TABLA ────────────────────────────────────────────── --}}
    <div class="card border-0">

        {{-- Header strip --}}
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom" style="background:#fafbfc;">
            <span style="font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#94a3b8;">
                {{ $empleados->total() }} empleado(s) encontrado(s)
            </span>
            <span style="font-size:0.78rem; color:#cbd5e1;">
                Página {{ $empleados->currentPage() }} de {{ $empleados->lastPage() }}
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="padding-left:1.5rem;">Empleado</th>
                            <th>Área / Cargo</th>
                            <th>Sede</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th class="text-center" style="padding-right:1.5rem;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empleados as $e)
                        <tr>
                            {{-- Empleado --}}
                            <td style="padding-left:1.5rem;">
                                <div class="d-flex align-items-center" style="gap:0.85rem;">
                                    <div class="cell-avatar">
                                        {{ strtoupper(substr($e->persona->nombres ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="cell-primary">
                                            {{ $e->persona->nombres ?? '' }} {{ $e->persona->apellidos ?? '' }}
                                        </div>
                                        <div class="cell-secondary">
                                            <i class="fas fa-id-card mr-1"></i>{{ $e->persona->numero_documento ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Área / Cargo --}}
                            <td>
                                <div class="cell-primary">{{ $e->area->nombre ?? 'Sin área' }}</div>
                                <div class="cell-secondary">{{ $e->cargo }}</div>
                            </td>

                            {{-- Sede --}}
                            <td>
                                <span style="color:#475569; font-size:0.875rem;">
                                    <i class="fas fa-hospital-alt mr-1 text-muted"></i>
                                    {{ $e->sede->nombre ?? 'Sin sede' }}
                                </span>
                            </td>

                            {{-- Rol --}}
                            <td>
                                <span style="display:inline-flex; align-items:center; background:#f1f5f9; color:#475569; font-size:0.775rem; font-weight:600; padding:0.3rem 0.7rem; border-radius:6px;">
                                    {{ $e->rol->nombre ?? 'Sin rol' }}
                                </span>
                            </td>

                            {{-- Estado --}}
                            <td>
                                @if($e->estado == 1)
                                    <span class="badge-soft-success">
                                        <i class="fas fa-check-circle"></i> Activo
                                    </span>
                                @else
                                    <span class="badge-soft-danger">
                                        <i class="fas fa-times-circle"></i> Inactivo
                                    </span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="text-center" style="padding-right:1.5rem;">
                                <div class="action-container">
                                    <a href="{{ route('admin.empleados.show', $e) }}"
                                       class="btn-table-action"
                                       data-toggle="tooltip" data-placement="top" title="Ver perfil completo">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($e->estado == 1)
                                        <a href="{{ route('admin.empleados.edit', $e) }}"
                                           class="btn-table-action"
                                           data-toggle="tooltip" data-placement="top" title="Editar datos">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <form action="{{ route('admin.empleados.destroy', $e) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn-table-action"
                                                    data-toggle="tooltip" data-placement="top" title="Desactivar empleado"
                                                    onclick="return confirm('¿Confirma DESACTIVAR a este empleado?');">
                                                <i class="fas fa-user-slash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn-table-action opacity-50"
                                                data-toggle="tooltip" data-placement="top" title="Inactivo — no editable"
                                                style="cursor:not-allowed;" disabled>
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <form action="{{ route('admin.empleados.toggle', $e->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-table-action"
                                                    data-toggle="tooltip" data-placement="top" title="Reactivar empleado">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state py-4">
                                    <div class="empty-state-icon"><i class="fas fa-users"></i></div>
                                    <h5 class="empty-state-title">No hay empleados registrados</h5>
                                    <p class="empty-state-description">Comience agregando el primer talento a su organización.</p>
                                    <a href="{{ route('admin.empleados.create') }}" class="btn btn-orange btn-sm px-4">
                                        <i class="fas fa-plus mr-1"></i> Agregar Empleado
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($empleados->hasPages())
        <div class="card-footer bg-white border-top py-3 px-4">
            {{ $empleados->links() }}
        </div>
        @endif
    </div>

</div>
@endsection