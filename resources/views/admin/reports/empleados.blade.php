@extends('adminlte::page')

@section('title', 'Reporte de Empleados — Milagroz')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.65rem; letter-spacing: -0.5px;">
            <i class="fas fa-list-ul mr-2" style="color: #6366f1; font-size: 1.3rem;"></i>
            Reporte Detallado de Empleados
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.875rem;">
            Listado completo de personal con filtros avanzados y opciones de exportación.
        </p>
    </div>
    <div class="d-flex" style="gap: 10px;">
        <a href="{{ route('admin.reportes.index') }}" class="btn btn-light border px-3 shadow-sm">
            <i class="fas fa-arrow-left mr-2"></i>Volver al Tablero
        </a>
        <a href="{{ route('admin.reportes.empleados.pdf', request()->all()) }}" class="btn btn-danger btn-sm px-3 shadow-sm">
            <i class="fas fa-file-pdf mr-2"></i>PDF
        </a>
        <a href="{{ route('admin.reportes.empleados.excel', request()->all()) }}" class="btn btn-success btn-sm px-3 shadow-sm">
            <i class="fas fa-file-excel mr-2"></i>Excel
        </a>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-2 pb-4">

    {{-- ── FILTROS ─────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.reportes.empleados') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label font-weight-bold small text-muted text-uppercase">Búsqueda General</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="buscar" class="form-control border-left-0 shadow-none" 
                                   placeholder="Nombre o documento..." value="{{ request('buscar') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label font-weight-bold small text-muted text-uppercase">Estado</label>
                        <select name="estado" class="form-control shadow-none">
                            <option value="">Todos</option>
                            <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label font-weight-bold small text-muted text-uppercase">Área</label>
                        <select name="area_id" class="form-control shadow-none">
                            <option value="">Todas</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label font-weight-bold small text-muted text-uppercase">Sede</label>
                        <select name="sede_id" class="form-control shadow-none">
                            <option value="">Todas</option>
                            @foreach($sedes as $sede)
                                <option value="{{ $sede->id }}" {{ request('sede_id') == $sede->id ? 'selected' : '' }}>
                                    {{ $sede->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fas fa-filter mr-2"></i>Aplicar Filtros
                        </button>
                        <a href="{{ route('admin.reportes.empleados') }}" class="btn btn-link text-muted">Limpiar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── TABLA DE RESULTADOS ──────────────────────────────── --}}
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 font-weight-bold">Resultados del Reporte</h6>
                <span class="badge badge-pill badge-light border px-3 py-2 text-muted">
                    {{ $empleados->total() }} registros encontrados
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr class="text-muted small text-uppercase font-weight-bold">
                            <th class="px-4 py-3 border-0">Empleado</th>
                            <th class="py-3 border-0">Documento</th>
                            <th class="py-3 border-0">Cargo / Área</th>
                            <th class="py-3 border-0">Sede</th>
                            <th class="py-3 border-0">Contrato</th>
                            <th class="py-3 border-0 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($empleados as $e)
                        <tr class="border-bottom">
                            <td class="px-4 py-3">
                                <span class="font-weight-600 text-dark d-block">
                                    {{ $e->persona->nombres ?? '' }} {{ $e->persona->apellidos ?? '' }}
                                </span>
                            </td>
                            <td class="py-3 text-muted">
                                {{ $e->persona->numero_documento ?? 'N/A' }}
                            </td>
                            <td class="py-3">
                                <span class="d-block font-weight-500">{{ $e->cargo }}</span>
                                <small class="text-muted">{{ $e->area->nombre ?? 'N/A' }}</small>
                            </td>
                            <td class="py-3 text-muted">
                                {{ $e->sede->nombre ?? 'N/A' }}
                            </td>
                            <td class="py-3">
                                @php $latest = $e->etapaContractuales->first(); @endphp
                                <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 0.75rem;">
                                    {{ $latest->tipo_contrato ?? 'No registrado' }}
                                </span>
                            </td>
                            <td class="py-3 text-center">
                                @if($e->estado == 1)
                                    <span class="badge-dot" style="background: #10b981;"></span>
                                    <span class="text-success small font-weight-600">Activo</span>
                                @else
                                    <span class="badge-dot" style="background: #ef4444;"></span>
                                    <span class="text-danger small font-weight-600">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="60" class="mb-3 opacity-25">
                                <p class="text-muted">No se encontraron empleados que coincidan con los filtros.</p>
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

<style>
    .font-weight-600 { font-weight: 600; }
    .font-weight-500 { font-weight: 500; }
    .badge-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 5px;
    }
</style>
@stop
