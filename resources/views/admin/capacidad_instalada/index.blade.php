@extends('adminlte::page')

@section('title', 'Gestión de Capacidad Instalada')

@section('content_header')
<div class="page-header d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="page-title fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Módulo de Capacidad Instalada
        </h2>
        <p class="page-subtitle text-muted mb-0">
            Listado de las capacidades instaladas y utilizadas por el personal.
        </p>
    </div>

    <div class="page-actions">
        <a href="{{ route('admin.capacidad_instalada.create') }}" class="btn btn-orange">
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

    <form method="GET" action="{{ route('admin.capacidad_instalada.index') }}" class="mb-4">
        <div class="card shadow-sm border-0 rounded-4" style="background-color: #f8fafc;">
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    
                    {{-- CAMPO: BUSCAR --}}
                    <div class="col-md-4">
                        <label class="text-muted small fw-bold mb-2 ps-1">Buscar Empleado / Proceso</label>
                        <div class="d-flex align-items-center bg-white" style="border-radius: 30px; height: 45px; padding: 0 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="fas fa-search text-muted" style="font-size: 14px;"></i>
                            <input type="text" name="buscar" class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                   placeholder="Nombre, Documento o Proceso..." value="{{ request('buscar') }}" style="outline: none;">
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

                    <a href="{{ route('admin.capacidad_instalada.index') }}"
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
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Proceso</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Capacidad (Disp / Util)</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Fecha</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Estado</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Documentos</th>
                            <th class="text-center pe-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($capacidades as $p)
                        <tr style="border-bottom: 1px solid #f1f5f9; {{ $p->estado == 0 ? 'background-color: #f8fafc; opacity: 0.8;' : '' }}">
                            
                            {{-- EMPLEADO --}}
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center" style="gap: 1rem;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 45px; height: 45px; background-color: rgba(255,106,0,0.1); color: #ff6a00;">
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
                            
                            {{-- PROCESO --}}
                            <td class="py-3">
                                <span class="d-block" style="color: #334155; font-weight: 500;">
                                    {{ $p->proceso ?? 'N/A' }}
                                </span>
                            </td>

                            {{-- CAPACIDADES --}}
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.85rem;" data-toggle="tooltip" title="Capacidad Disponible">
                                        <i class="fas fa-box-open text-primary me-1"></i> {{ $p->capacidad_disponible ?? 0 }}
                                    </span>
                                    <span class="text-muted">/</span>
                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.85rem;" data-toggle="tooltip" title="Capacidad Utilizada">
                                        <i class="fas fa-box text-success me-1"></i> {{ $p->capacidad_utilizada ?? 0 }}
                                    </span>
                                </div>
                            </td>

                            {{-- FECHA --}}
                            <td class="py-3" style="color: #475569;">
                                {{ \Carbon\Carbon::parse($p->fecha)->format('d/m/Y') }}
                            </td>

                            {{-- ESTADO --}} 
                            <td class="py-3">
                                @if($p->estado == 1)
                                    <span class="badge bg-success px-2 py-1 rounded-pill" style="font-size: 0.8rem;">Activo</span>
                                @else
                                    <span class="badge bg-secondary px-2 py-1 rounded-pill" style="font-size: 0.8rem;">Inactivo</span>
                                @endif
                            </td>

                            {{-- DOCUMENTOS --}}
                            <td class="py-3">
                                @if($p->documentos->count() > 0)
                                    <button type="button" 
                                            class="btn btn-sm btn-light border fw-bold text-primary" 
                                            data-toggle="modal" 
                                            data-target="#documentosModal{{ $p->id }}"
                                            style="border-radius: 10px;">
                                        📁 Ver documentos ({{ $p->documentos->count() }})
                                    </button>
                                @else
                                    <span class="text-muted small"><i class="fas fa-ban me-1"></i> Sin documentos</span>
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td class="text-center pe-4 py-3">
                                <div class="d-flex justify-content-center gap-2">
                                    
                                    @if($p->estado == 1)
                                        <a href="{{ route('admin.capacidad_instalada.edit', $p) }}"
                                           class="btn btn-sm btn-icon btn-outline-primary"
                                           data-toggle="tooltip" data-placement="top" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <form action="{{ route('admin.capacidad_instalada.destroy', $p) }}" method="POST" class="d-inline">
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
                                        <form action="{{ route('admin.capacidad_instalada.toggle', $p->id) }}" method="POST" class="d-inline">
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
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background-color: rgba(255,106,0,0.1);">
                                        <i class="fas fa-chart-pie fa-2x" style="color: #ff6a00;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1" style="color: #64748b;">Sin registros de capacidad instalada</h5>
                                    <p class="mb-0">Aún no se han registrado datos para este módulo.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($capacidades->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4 rounded-bottom-4">
            {{ $capacidades->links() }}
        </div>
        @endif
    </div>

    {{-- MODALES DE DOCUMENTOS --}}
    @foreach($capacidades as $p)
        @if($p->documentos->count() > 0)
        <div class="modal fade" id="documentosModal{{ $p->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="border-radius: 20px; border: none;">
                    <div class="modal-header bg-light border-0" style="border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title fw-bold">Documentos</h5>
                        <button type="button" class="close border-0 bg-transparent" data-dismiss="modal" aria-label="Close" style="font-size: 1.5rem; cursor: pointer;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="list-group list-group-flush">
                        @foreach($p->documentos as $doc)
                            <div class="list-group-item px-0 border-0 mb-3 bg-transparent">
                                <div class="d-flex align-items-center justify-content-between p-3" style="background-color: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                                    <div class="d-flex align-items-center text-truncate pe-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: rgba(255,106,0,0.1); color: #ff6a00;">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <span class="text-truncate fw-semibold" style="max-width: 200px; color: #334155;">{{ $doc->nombre_original }}</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.documentos.view', $doc->id) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-light text-primary border shadow-sm"
                                           title="Ver documento"
                                           style="border-radius: 8px;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.documentos.download', $doc->id) }}" 
                                           class="btn btn-sm btn-light text-success border shadow-sm"
                                           title="Descargar"
                                           style="border-radius: 8px;">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endforeach

</div>

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
