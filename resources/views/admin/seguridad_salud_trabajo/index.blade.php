@extends('adminlte::page')

@section('title', 'Seguridad y Salud en el Trabajo')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">Seguridad y Salud en el Trabajo</h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Gestión de documentos de seguridad y salud de los empleados.</p>
    </div>
    <a href="{{ route('admin.seguridad_salud_trabajo.create') }}" class="btn btn-orange">
        <i class="fas fa-plus me-2"></i> Nuevo Documento
    </a>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    <form method="GET" action="{{ route('admin.seguridad_salud_trabajo.index') }}" class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label class="small font-weight-bold text-muted mb-1">Buscar Empleado</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-light text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="buscar" class="form-control border-light bg-light shadow-none" placeholder="Nombre o apellido..." value="{{ request('buscar') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="small font-weight-bold text-muted mb-1">Tipo Documento</label>
                    <input type="text" name="tipo_documento" class="form-control border-light bg-light shadow-none" placeholder="Ej: Certificado..." value="{{ request('tipo_documento') }}">
                </div>

                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1 shadow-xs">
                            <i class="fas fa-filter mr-1"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.seguridad_salud_trabajo.index') }}" class="btn btn-light border flex-grow-1 shadow-xs">
                            <i class="fas fa-undo mr-1"></i> Limpiar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Empleado</th>
                            <th>Tipo de Documento</th>
                            <th>Fecha</th>
                            <th>Archivo</th>
                            <th>Vencimiento</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $doc)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); color: var(--primary-blue);">
                                        <span class="fw-bold">{{ strtoupper(substr($doc->empleado->persona->nombres ?? 'X', 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 1.1rem; color: #1e293b !important;">{{ $doc->empleado->persona->nombres ?? '' }} {{ $doc->empleado->persona->apellidos ?? '' }}</div>
                                        <div class="text-muted" style="font-size: 0.85rem;">Documentación de SST</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="badge" style="background-color: #f1f5f9; color: #334155; font-size: 0.9rem; font-weight: 500; padding: 0.5rem 0.8rem;">
                                    <i class="fas fa-file-alt text-muted me-1"></i> {{ $doc->tipo_documento }}
                                </span>
                            </td>

                            <td style="color: #475569;">
                                <i class="fas fa-calendar-alt text-muted me-1"></i>
                                {{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }}
                            </td>

                            <td>
                                @if($doc->documentos->count() > 0)
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($doc->documentos as $archivo)
                                            <a href="{{ route('admin.documentos.view', $archivo->id) }}"
                                               target="_blank"
                                               class="doc-file-clickable"
                                               data-toggle="tooltip" data-boundary="window"
                                               title="{{ $archivo->nombre_original }}"
                                               style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                                                <i class="fas fa-file-pdf"></i>
                                                <span class="file-name-text text-truncate" style="max-width: 120px;">{{ $archivo->nombre_original }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small italic">Sin archivos</span>
                                @endif
                            </td>

                            <td>
                                @php $badge = $doc->getStatusBadge($doc->fecha); @endphp
                                <span class="{{ $badge['class'] }} px-3 py-2 shadow-sm rounded-pill font-weight-bold" style="font-size: 0.8rem; border: 1px solid {{ $badge['color'] }}20;">
                                    <i class="{{ $badge['icon'] }} me-1"></i> {{ strtoupper($badge['label']) }}
                                </span>
                            </td>

                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.seguridad_salud_trabajo.edit', $doc) }}"
                                       class="btn btn-sm btn-light-custom px-3"
                                       data-toggle="tooltip" data-placement="top" title="Editar documento">
                                        <i class="fas fa-pen text-muted"></i>
                                    </a>

                                    <form action="{{ route('admin.seguridad_salud_trabajo.destroy', $doc) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-light-custom px-3 text-danger"
                                                data-toggle="tooltip" data-placement="top" title="Eliminar registro"
                                                onclick="return confirm('¿Confirma que desea ELIMINAR este registro?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background-color: #f1f5f9;">
                                        <i class="fas fa-shield-alt fa-2x" style="color: #cbd5e1;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1" style="color: #64748b;">No hay registros de Seguridad y Salud</h5>
                                    <p class="mb-0">Comience registrando el primer documento.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($documentos->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4">
            {{ $documentos->links() }}
        </div>
        @endif
    </div>

</div>
@stop

@section('js')
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
