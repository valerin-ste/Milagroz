@extends('adminlte::page')

@section('title', 'Gestión de Áreas')

@section('content_header')
<div class="page-header d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="page-title fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Gestión de Áreas
        </h2>
        <p class="page-subtitle text-muted mb-0">
            Organiza y administra las áreas del sistema.
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.areas.create') }}" class="btn btn-orange">
            <i class="fas fa-plus me-2"></i> Nueva Área
        </a>
    </div>
</div>

<form method="GET" action="{{ route('admin.areas.index') }}" class="mb-4">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="text-muted small fw-bold mb-1">Buscar Área</label>
                    <div class="search-container" style="border-radius: 10px; padding: 0;">
                        <i class="fas fa-search search-icon" style="left: 15px;"></i>
                        <input type="text" name="buscar" class="form-control search-input py-2"
                               style="border-radius: 10px;"
                               placeholder="Nombre del área..." value="{{ request('buscar') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-orange flex-grow-1 fw-bold" style="border-radius: 10px; height: 42px;">
                            <i class="fas fa-filter me-1"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.areas.index') }}" class="btn btn-light border flex-grow-1 fw-bold text-secondary"
                           style="border-radius: 10px; height: 42px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-sync-alt me-1"></i> Limpiar
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
        <div class="alert alert-success d-flex align-items-center" style="background-color: #ecfdf5; color: #047857; border: none;">
            <i class="fas fa-check-circle fa-lg me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th class="ps-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">#</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Nombre del Área</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Sede</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Descripción</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Estado</th>
                            <th class="text-center pe-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($areas as $area)
                        <tr style="border-bottom: 1px solid #f1f5f9;">

                            <td class="ps-4 py-3 text-muted" style="font-size: 0.9rem;">{{ $loop->iteration }}</td>

                            <td class="py-3">
                                <div class="d-flex align-items-center" style="gap: 0.75rem;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px; background-color: rgba(19,182,236,0.1); color: var(--primary-blue);">
                                        <i class="fas fa-sitemap" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <span class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $area->nombre }}</span>
                                </div>
                            </td>

                            <td class="py-3" style="color: #475569; font-size: 0.9rem;">
                                {{ $area->sede->nombre ?? 'Sin sede' }}
                            </td>

                            <td class="py-3 text-muted" style="font-size: 0.88rem; max-width: 220px;">
                                <span class="d-inline-block text-truncate" style="max-width: 200px;">
                                    {{ $area->descripcion ?? 'Sin descripción' }}
                                </span>
                            </td>

                            <td class="py-3">
                                @if($area->estado == 1)
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-danger">Inactiva</span>
                                @endif
                            </td>

                            <td class="text-center pe-4 py-3">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    @if($area->estado == 1)
                                        <a href="{{ route('admin.areas.edit', $area) }}"
                                           class="btn btn-sm btn-icon btn-outline-primary"
                                           data-toggle="tooltip" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="{{ route('admin.areas.destroy', $area) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-danger"
                                                    data-toggle="tooltip" title="Desactivar"
                                                    onclick="return confirm('¿Confirma que desea DESACTIVAR esta área?');">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-icon btn-outline-secondary opacity-50" disabled>
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <form action="{{ route('admin.areas.toggle', $area->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light border btn-icon"
                                                    data-toggle="tooltip" title="Activar"
                                                    onclick="return confirm('¿Desea ACTIVAR esta área nuevamente?');">
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
                                <h5>No hay áreas registradas</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($areas->hasPages())
        <div class="card-footer bg-white border-top py-3 px-4 rounded-bottom-4">
            {{ $areas->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('css')
<style>
/* ── Botones ── */
.btn-orange { background-color: #ff6a00; border: none; color: #fff; border-radius: 8px; transition: all 0.2s; font-weight: 600; }
.btn-orange:hover { background-color: #e65c00; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(255,106,0,0.25); }
.btn-icon { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s; }
.btn-icon:hover { transform: scale(1.1); }
/* ── Tabla ── */
.table thead th { letter-spacing: 0.04em; }
.table tbody tr:hover { background-color: #f8fafc; }
/* ── Badge ── */
.badge { font-size: 0.78rem; padding: 0.35em 0.7em; border-radius: 6px; font-weight: 600; }

.search-container {
    position: relative; display: flex; align-items: center;
    background: #fff; border-radius: 10px; padding: 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08); border: 1px solid #ddd;
}
.search-input { border: none; outline: none; box-shadow: none; flex: 1; padding-left: 35px; border-radius: 10px; }
.search-input:focus { box-shadow: none; }
.search-icon { position: absolute; left: 15px; color: #999; font-size: 14px; }
</style>
@endpush