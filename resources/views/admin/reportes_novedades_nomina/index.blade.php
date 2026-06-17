@extends('adminlte::page')

@section('title', 'Reportes Novedades Nómina')

@section('content_header')
<div class="page-header d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="page-title fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Módulo de Novedades de Nómina
        </h2>
        <p class="page-subtitle text-muted mb-0">
            Listado de reportes y novedades de nómina registrados.
        </p>
    </div>

    <div class="page-actions">
        <a href="{{ route('admin.reportes-novedades-nomina.create') }}" class="btn btn-orange">
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

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center border-0 shadow-sm" style="background-color: #fef2f2; color: #b91c1c; border-radius: var(--radius-md);">
            <i class="fas fa-exclamation-circle fa-lg me-3"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <form method="GET" action="{{ route('admin.reportes-novedades-nomina.index') }}" class="mb-4">
        <div class="card shadow-sm border-0 rounded-4" style="background-color: #f8fafc;">
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    
                    {{-- CAMPO: BUSCAR --}}
                    <div class="col-md-4">
                        <label class="text-muted small fw-bold mb-2 ps-1">Buscar Empleado / Novedad</label>
                        <div class="d-flex align-items-center bg-white" style="border-radius: 30px; height: 45px; padding: 0 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="fas fa-search text-muted" style="font-size: 14px;"></i>
                            <input type="text" name="buscar" class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                   placeholder="Nombre, Documento, Tipo..." value="{{ request('buscar') }}" style="outline: none;">
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

                    <a href="{{ route('admin.reportes-novedades-nomina.index') }}"
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
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Tipo Novedad</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Cantidad</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Fecha</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Archivo</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Estado</th>
                            <th class="text-center pe-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportes as $r)
                        <tr style="border-bottom: 1px solid #f1f5f9; {{ $r->estado == 0 ? 'background-color: #f8fafc; opacity: 0.8;' : '' }}">
                            
                            {{-- EMPLEADO --}}
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center" style="gap: 1rem;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 45px; height: 45px; background-color: rgba(255,106,0,0.1); color: #ff6a00;">
                                        <span class="fw-bold" style="font-size: 1.1rem;">
                                            {{ strtoupper(substr($r->empleado->persona->nombres ?? 'U', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                            {{ $r->empleado->persona->nombres ?? '' }} {{ $r->empleado->persona->apellidos ?? '' }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.85rem;">
                                            {{ $r->empleado->cargo ?? 'Sin cargo' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            {{-- TIPO NOVEDAD --}}
                            <td class="py-3">
                                <span class="d-block" style="color: #334155; font-weight: 500;">
                                    {{ $r->tipo_novedad }}
                                </span>
                            </td>

                            {{-- CANTIDAD --}}
                            <td class="py-3">
                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.85rem;">
                                    {{ $r->cantidad }}
                                </span>
                            </td>

                            {{-- FECHA --}}
                            <td class="py-3" style="color: #475569;">
                                {{ \Carbon\Carbon::parse($r->fecha)->format('d/m/Y') }}
                            </td>
                            
                            {{-- ARCHIVO --}}
                            <td class="py-3">
                                @if($r->archivo)
                                    <button type="button"
                                            class="btn btn-sm btn-light border"
                                            data-toggle="modal"
                                            data-target="#modal-archivo-{{ $r->id }}"
                                            style="border-radius: 8px;">
                                        <i class="fas fa-folder text-warning me-1"></i>
                                        Ver documentos (1)
                                    </button>
                                @else
                                    <span class="text-muted small fst-italic">
                                        <i class="fas fa-file-slash me-1"></i> Sin soporte
                                    </span>
                                @endif
                            </td>

                            {{-- ESTADO --}}
                            <td class="py-3">
                                @if($r->estado == 1)
                                    <span class="badge bg-success px-2 py-1 rounded-pill" style="font-size: 0.8rem;">Activo</span>
                                @else
                                    <span class="badge bg-secondary px-2 py-1 rounded-pill" style="font-size: 0.8rem;">Inactivo</span>
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td class="py-3 text-center">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    @if($r->estado == 1)
                                        <a href="{{ route('admin.reportes-novedades-nomina.edit', $r) }}"
                                           class="btn btn-sm btn-icon btn-outline-primary"
                                           data-toggle="tooltip" data-placement="top" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <form action="{{ route('admin.reportes-novedades-nomina.destroy', $r) }}" method="POST" class="d-inline">
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
                                        <form action="{{ route('admin.reportes-novedades-nomina.toggle', $r->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-light border text-success"
                                                    data-toggle="tooltip" data-placement="top" title="Reactivar registro" style="border-radius: 30px;">
                                                <i class="fas fa-check-circle"></i> Activar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL ARCHIVO --}}
                        @if($r->archivo)
                        <div class="modal fade" id="modal-archivo-{{ $r->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content" style="border-radius: 15px; border: none;">
                                    <div class="modal-header bg-light border-bottom-0" style="border-radius: 15px 15px 0 0;">
                                        <h5 class="modal-title fw-bold text-dark"><i class="fas fa-file-alt text-primary me-2"></i> Soporte Novedad</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                            <div class="d-flex justify-content-between align-items-center border rounded p-2">

                                                <!-- NOMBRE ARCHIVO -->
                                                <div class="text-truncate">
                                                    <i class="fas fa-file-alt text-secondary me-2"></i>
                                                    {{ basename($r->archivo) }}
                                                </div>

                                                <!-- BOTONES -->
                                                <div class="d-flex gap-1">

                                                    <!-- VER -->
                                                    <a href="{{ route('admin.reportes-novedades-nomina.archivo.view', $r->id) }}"
                                                    target="_blank"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Ver">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    <!-- DESCARGAR -->
                                                    <a href="{{ route('admin.reportes-novedades-nomina.archivo.download', $r->id) }}"
                                                    class="btn btn-sm btn-outline-success"
                                                    title="Descargar">
                                                        <i class="fas fa-download"></i>
                                                    </a>

                                                </div>

                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background-color: rgba(255,106,0,0.1);">
                                        <i class="fas fa-file-invoice-dollar fa-2x" style="color: #ff6a00;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1" style="color: #64748b;">Sin reportes de novedades</h5>
                                    <p class="mb-0">Aún no se han registrado novedades de nómina en este módulo.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($reportes->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4 rounded-bottom-4">
            {{ $reportes->links() }}
        </div>
        @endif
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
