@extends('adminlte::page')

@section('title', 'Seguridad y Salud en el Trabajo')

@section('content_header')
<div class="page-header d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="page-title fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Seguridad y Salud en el Trabajo
        </h2>
        <p class="page-subtitle text-muted mb-0">
            Gestión de documentos de seguridad y salud de los empleados.
        </p>
    </div>

    <div class="page-actions">
        <a href="{{ route('admin.seguridad_salud_trabajo.create') }}" class="btn btn-orange">
            <i class="fas fa-plus me-2"></i> Nuevo Documento
        </a>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    <form method="GET" action="{{ route('admin.seguridad_salud_trabajo.index') }}" class="mb-4">
        <div class="card shadow-sm border-0 rounded-4" style="background-color: #f8fafc;">
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    
                    {{-- CAMPO: BUSCAR EMPLEADO --}}
                    <div class="col-md-5">
                        <label class="text-muted small fw-bold mb-2 ps-1">Buscar Empleado</label>
                        <div class="d-flex align-items-center bg-white" style="border-radius: 30px; height: 45px; padding: 0 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="fas fa-search text-muted" style="font-size: 14px;"></i>
                            <input type="text" name="buscar" class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                   placeholder="Nombre o apellido..." value="{{ request('buscar') }}" style="outline: none;">
                        </div>
                    </div>

                    {{-- CAMPO: TIPO DOCUMENTO --}}
                    <div class="col-md-4">
                        <label class="text-muted small fw-bold mb-2 ps-1">Tipo Documento</label>
                        <div class="d-flex align-items-center bg-white" style="border-radius: 30px; height: 45px; padding: 0 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="fas fa-file-alt text-muted" style="font-size: 14px;"></i>
                            <input type="text" name="tipo_documento" class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                   placeholder="Ej: Certificado..." value="{{ request('tipo_documento') }}" style="outline: none;">
                        </div>
                    </div>

                    {{-- BOTONES --}}
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-orange flex-grow-1 fw-bold d-flex justify-content-center align-items-center" style="border-radius: 30px; height: 45px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                <i class="fas fa-filter me-2"></i> Filtrar
                            </button>
                            <a href="{{ route('admin.seguridad_salud_trabajo.index') }}" class="btn bg-white border flex-grow-1 fw-bold text-secondary d-flex justify-content-center align-items-center" style="border-radius: 30px; height: 45px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                                <i class="fas fa-sync-alt me-2"></i> Limpiar
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center"
             style="background-color: #ecfdf5; color: #047857; border: none;">
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
                            <th class="ps-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Empleado</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Tipo de Documento</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Fecha</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Archivo</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Vencimiento</th>
                            <th class="text-center pe-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $doc)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center" style="gap: 1rem;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 45px; height: 45px; background-color: rgba(19,182,236,0.1); color: var(--primary-blue);">
                                        <span class="fw-bold" style="font-size: 1.1rem;">
                                            {{ strtoupper(substr($doc->empleado->persona->nombres ?? 'X', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                            {{ $doc->empleado->persona->nombres ?? '' }} {{ $doc->empleado->persona->apellidos ?? '' }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.85rem;">
                                            CC: {{ $doc->empleado->persona->numero_documento ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3">
                                <span class="d-block" style="color: #334155; font-weight: 500;">
                                    <i class="fas fa-file-alt text-muted me-1"></i> {{ $doc->tipo_documento }}
                                </span>
                                <span class="badge bg-light text-muted border px-2 py-1 mt-1" style="font-size: 0.75rem;">
                                    Periodo {{ \Carbon\Carbon::parse($doc->fecha)->format('Y') }}
                                </span>
                            </td>

                            <td class="py-3" style="color: #475569;">
                                <div class="mb-1" style="font-size: 0.9rem;">
                                    📅 {{ \Carbon\Carbon::parse($doc->fecha)->format('d M Y') }}
                                </div>
                                <small class="text-muted" style="font-size: 0.7rem;">(Última carga)</small>
                            </td>

                            <td class="py-3">
                                @if($doc->documentos->count() > 0)
                                    <button class="btn btn-outline-secondary btn-sm"
                                            data-toggle="modal"
                                            data-target="#docsModal{{ $doc->id }}">
                                        📂 Ver documentos ({{ $doc->documentos->count() }})
                                    </button>
                                @else
                                    <span class="text-muted small fst-italic"><i class="fas fa-file-slash me-1"></i> Sin archivos</span>
                                @endif
                            </td>

                             <td class="py-3">
                                @php $badge = $doc->getStatusBadge($doc->fecha); @endphp
                                <span class="{{ $badge['class'] }} px-3 py-2 shadow-sm rounded-pill font-weight-bold" style="font-size: 0.8rem; border: 1px solid {{ $badge['color'] }}20;">
                                    <i class="{{ $badge['icon'] }} me-1"></i> {{ strtoupper($badge['label']) }}
                                </span>
                            </td>

                            <td>
                    <div class="acciones-btns">

                                    <!-- EDITAR -->
                                    <a href="{{ route('admin.seguridad_salud_trabajo.edit', $doc->id) }}"
                                        class="btn btn-icon btn-outline-primary">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>

                                    <!-- ACTIVAR / INACTIVAR -->
                                    <form action="{{ route('admin.seguridad_salud_trabajo.destroy', $doc) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')

                                        @if($doc->estado == 1)
                                            <button type="submit"
                                                    class="btn btn-icon btn-outline-warning"
                                                    onclick="return confirm('¿Desea inactivar este registro?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <button type="submit"
                                                    class="btn btn-icon btn-outline-success"
                                                    onclick="return confirm('¿Desea activar este registro?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                    </form>

                                </div>
                            </td>
                                    </tr>
                                        @empty
                                    <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <div class="d-flex flex-column align-items-center">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background-color: rgba(19,182,236,0.1);">
                                                        <i class="fas fa-shield-alt fa-2x" style="color: var(--primary-blue, #13b6ec);"></i>
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
                        <div class="card-footer bg-white border-top border-light py-3 px-4 rounded-bottom-4">
                            {{ $documentos->links() }}
                        </div>
                        @endif
                    </div>

</div>

{{-- MODALES DE DOCUMENTOS --}}
@foreach($documentos as $doc)
    <div class="modal fade" id="docsModal{{ $doc->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4">

                <div class="modal-header">
                    <h5 class="modal-title">Documentos</h5>
                    <button type="button" class="btn-close" data-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <ul class="list-group">
                        @forelse($doc->documentos as $archivo)
                        <li class="list-group-item d-flex justify-content-between align-items-center">

                            <span class="text-truncate" style="max-width: 200px;">
                                📄 {{ $archivo->nombre_original }}
                            </span>

                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.documentos.view', $archivo->id) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    👁️
                                </a>

                                <a href="{{ route('admin.documentos.download', $archivo->id) }}"
                                   class="btn btn-sm btn-outline-success">
                                    ⬇️
                                </a>
                            </div>

                        </li>
                        @empty
                        <li class="list-group-item text-muted">
                            Sin documentos
                        </li>
                        @endforelse
                    </ul>
                </div>

            </div>
        </div>
    </div>
@endforeach

@endsection

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
@endsection

@section('css')
<style>
    .acciones-btns {
    display: flex;
    align-items: center;
    justify-content: center; /* centra como en tu imagen */
    gap: 4px; /* espacio pequeño entre botones */
}

.btn-icon {
    width: 38px;
    height: 38px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.btn-icon:hover {
    transform: scale(1.1);
    transition: 0.2s;
}

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
    padding-left: 35px;
    border-radius: 30px;
}

.search-input:focus {
    box-shadow: none;
}

.search-btn {
    border-radius: 50%;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    transition: all 0.2s ease;
}

.search-btn:hover {
    transform: scale(1.1);
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
}

.btn-orange:hover {
    background-color: #e65c00;
    color: #fff;
}
</style>
@endsection
