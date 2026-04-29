@extends('adminlte::page')

@section('content_header')
<div class="page-header d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="page-title fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Gestión de Sedes
        </h2>
        <p class="page-subtitle text-muted mb-0">
            Administra las sedes registradas en el sistema.
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.sedes.create') }}" class="btn btn-orange">
            <i class="fas fa-plus me-2"></i> Nueva Sede
        </a>
    </div>
</div>
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
                            <th class="ps-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Nombre</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Ciudad</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Teléfono</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Áreas</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Estado</th>
                            <th class="text-center pe-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sedes as $sede)
                        <tr style="border-bottom: 1px solid #f1f5f9;">

                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center" style="gap: 1rem;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 42px; height: 42px; background-color: rgba(19,182,236,0.1); color: var(--primary-blue);">
                                        <i class="fas fa-hospital"></i>
                                    </div>
                                    <span class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $sede->nombre }}</span>
                                </div>
                            </td>

                            <td class="py-3" style="color: #475569; font-size: 0.9rem;">{{ $sede->ciudad }}</td>
                            <td class="py-3" style="color: #475569; font-size: 0.9rem;">{{ $sede->telefono }}</td>

                            <td class="py-3" style="min-width: 220px;">
                                @php 
                                    $areas = $sede->areas->pluck('nombre')->sort(); 
                                    $max = 2; 
                                @endphp

                                @if($areas->isEmpty())
                                    <span class="text-muted small">Sin áreas</span>
                                @else
                                    <div class="dropdown d-inline">
                                        
                                        {{-- TEXTO COMPACTO --}}
                                        <span class="dropdown-toggle"
                                            data-bs-toggle="dropdown"
                                            style="font-size: 0.85rem; color: #334155; cursor: pointer;">
                                            {{ $areas->take($max)->join(' · ') }}

                                            @if($areas->count() > $max)
                                                · +{{ $areas->count() - $max }}
                                            @endif
                                        </span>

                                        {{-- LISTA --}}
                                        <ul class="dropdown-menu shadow-sm p-2" style="min-width: 200px;">
                                            @foreach($areas as $area)
                                                <li>
                                                    <span class="dropdown-item small">
                                                        {{ $area }}
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>

                                    </div>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($sede->estado == 1)
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-danger">Inactiva</span>
                                @endif
                            </td>

                            <td class="text-center pe-4 py-3">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    @if($sede->estado == 1)
                                        <a href="{{ route('admin.sedes.edit', $sede) }}"
                                           class="btn btn-sm btn-icon btn-outline-primary"
                                           data-toggle="tooltip" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="{{ route('admin.sedes.destroy', $sede) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-icon btn-outline-danger"
                                                    data-toggle="tooltip" title="Desactivar"
                                                    onclick="return confirm('¿Confirma que desea DESACTIVAR esta sede?');">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-icon btn-outline-secondary opacity-50" disabled title="No editable">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <form action="{{ route('admin.sedes.toggle', $sede->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light border btn-icon"
                                                    data-toggle="tooltip" title="Activar"
                                                    onclick="return confirm('¿Desea ACTIVAR esta sede nuevamente?');">
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
                                <h5>No hay sedes registradas</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($sedes->hasPages())
        <div class="card-footer bg-white border-top py-3 px-4 rounded-bottom-4">
            {{ $sedes->links() }}
        </div>
        @endif
    </div>

</div>
@stop

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
.dropdown-toggle::after {
    margin-left: 4px;
    font-size: 0.6rem;
}
</style>
@endpush