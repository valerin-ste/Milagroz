@extends('adminlte::page')

@section('title', 'Gestión de Áreas')

@section('content')
<div class="container-fluid px-2">

    {{-- CABECERA --}}
    <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
        <div>
            <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">Gestión de Áreas</h2>
            <p class="text-muted mb-0">Organiza y administra las áreas</p>
        </div>
        <a href="{{ route('admin.areas.create') }}" class="btn btn-orange shadow-sm">
            <i class="fas fa-plus me-2"></i> Nueva Área
        </a>
    </div>

    {{-- ALERTA --}}
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center border-0 shadow-sm mb-3" style="background-color: #ecfdf5; color: #047857;">
            <i class="fas fa-check-circle fa-lg me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- FILTRO --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.areas.index') }}" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-light text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="buscar" class="form-control border-light bg-light shadow-none" placeholder="Buscar área..." value="{{ request('buscar') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info text-white w-100">Buscar</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.areas.index') }}" class="btn btn-light-custom w-100 border">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre del Área</th>
                            <th>Sede</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($areas as $area)
                        <tr @if($area->estado == 0) style="background-color: #f8fafc; opacity: 0.85;" @endif>
                            <td><strong>#{{ $area->id }}</strong></td>
                            <td class="fw-semibold text-truncate" style="max-width: 180px;">{{ $area->nombre }}</td>
                            <td>{{ $area->sede->nombre ?? 'Sin sede' }}</td>
                            <td class="text-muted text-truncate" style="max-width: 200px;">{{ $area->descripcion ?? 'Sin descripción' }}</td>
                            <td>
                                @if($area->estado == 1)
                                    <span class="badge bg-success px-3 py-1 rounded-pill" style="font-size: 0.8rem; font-weight:600;">
                                        <i class="fas fa-check-circle me-1"></i> Activa
                                    </span>
                                @else
                                    <span class="badge bg-danger px-3 py-1 rounded-pill" style="font-size: 0.8rem; font-weight:600;">
                                        <i class="fas fa-times-circle me-1"></i> Inactiva
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    @if($area->estado == 1)
                                        <a href="{{ route('admin.areas.edit', $area) }}" class="btn btn-sm btn-light-custom px-3" title="Editar Área">
                                            <i class="fas fa-pen text-primary"></i>
                                        </a>
                                        <form action="{{ route('admin.areas.destroy', $area) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light-custom px-3 text-danger" onclick="return confirm('¿Confirma que desea DESACTIVAR esta área?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-light-custom px-3 opacity-50" disabled title="No se puede editar registro inactivo">
                                            <i class="fas fa-pen text-muted"></i>
                                        </button>
                                        <form action="{{ route('admin.areas.toggle', $area->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light-custom px-3 text-success shadow-sm" onclick="return confirm('¿Desea ACTIVAR esta área nuevamente?')">
                                                <i class="fas fa-check-circle"></i> Activar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted fst-italic">
                                No hay áreas registradas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($areas->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4">
            {{ $areas->links() }}
        </div>
        @endif
    </div>

    {{-- CARDS DE RESUMEN --}}
    <div class="row mt-4 g-3">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center p-3">
                <h6 class="text-muted">TOTAL ÁREAS</h6>
                <h3 class="fw-bold">{{ $totalAreas }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center p-3">
                <h6 class="text-muted">ÁREAS ACTIVAS</h6>
                <h3 class="fw-bold text-success">{{ $areasActivas }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center p-3">
                <h6 class="text-muted">ÁREAS INACTIVAS</h6>
                <h3 class="fw-bold text-danger">{{ $areasInactivas }}</h3>
            </div>
        </div>
    </div>

</div>
@endsection

@section('css')
<style>
    .btn-orange { background-color: #f97316; color: #fff; border: none; }
    .btn-orange:hover { background-color: #ea580c; color: #fff; }

    .btn-light-custom { background-color: #f9fafb; border: 1px solid #e5e7eb; color: #374151; }
    .btn-light-custom:hover { background-color: #f3f4f6; color: #111827; }

    .badge-success { background-color: #dcfce7; color: #166534; }
    .badge-danger { background-color: #fee2e2; color: #991b1b; }
</style>
@endsection