@extends('adminlte::page')

@section('title', 'Gestión de Certificaciones')

@section('content_header')
<div class="page-header d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="page-title fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Certificaciones Oficiales
        </h2>
        <p class="page-subtitle text-muted mb-0">
            Listado de certificaciones, licencias y documentos oficiales del personal.
        </p>
    </div>

    <div class="page-actions">
        <a href="{{ route('admin.certificaciones.create') }}" class="btn btn-orange">
            <i class="fas fa-plus me-2"></i> Nueva Certificación
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

    <form method="GET" action="{{ route('admin.certificaciones.index') }}" class="mb-4">
        <div class="card shadow-sm border-0 rounded-4" style="background-color: #f8fafc;">
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    
                    {{-- CAMPO: BUSCAR --}}
                    <div class="col-md-5">
                        <label class="text-muted small fw-bold mb-2 ps-1">Buscar Certificación/Institución</label>
                        <div class="d-flex align-items-center bg-white" style="border-radius: 30px; height: 45px; padding: 0 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="fas fa-search text-muted" style="font-size: 14px;"></i>
                            <input type="text" name="buscar" class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                   placeholder="Nombre, institución..." value="{{ request('buscar') }}" style="outline: none;">
                        </div>
                    </div>

                    {{-- CAMPO: ESTADO --}}
                    <div class="col-md-4">
                        <label class="text-muted small fw-bold mb-2 ps-1">Estado del Registro</label>
                        <div class="d-flex align-items-center bg-white" style="border-radius: 30px; height: 45px; padding: 0 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="fas fa-toggle-on text-muted" style="font-size: 14px;"></i>
                            <select name="estado" class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0" style="outline: none;">
                                <option value="">-- Todos --</option>
                                <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                                <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                    </div>

                    {{-- BOTONES --}}
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-orange flex-grow-1 fw-bold d-flex justify-content-center align-items-center" style="border-radius: 30px; height: 45px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                <i class="fas fa-filter me-2"></i> Filtrar
                            </button>
                            <a href="{{ route('admin.certificaciones.index') }}" class="btn bg-white border flex-grow-1 fw-bold text-secondary d-flex justify-content-center align-items-center" style="border-radius: 30px; height: 45px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                                <i class="fas fa-sync-alt me-2"></i> Limpiar
                            </a>
                        </div>
                    </div>

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
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Certificación</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Institución</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Expedición / Vencimiento</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Soportes</th>
                            <th class="text-center pe-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($certificaciones as $c)
                        <tr style="border-bottom: 1px solid #f1f5f9; {{ $c->estado == 0 ? 'background-color: #f8fafc; opacity: 0.8;' : '' }}">
                            
                            {{-- EMPLEADO --}}
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center" style="gap: 1rem;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 45px; height: 45px; background-color: rgba(19,182,236,0.1); color: var(--primary-blue);">
                                        <span class="fw-bold" style="font-size: 1.1rem;">
                                            {{ strtoupper(substr($c->empleado->persona->nombres ?? 'U', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                            {{ $c->empleado->persona->nombres ?? '' }} {{ $c->empleado->persona->apellidos ?? '' }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.85rem;">
                                            {{ $c->empleado->cargo }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- CERTIFICACIÓN --}}
                            <td class="py-3">
                                <span class="d-block text-dark fw-bold" style="font-size: 0.95rem;">
                                    {{ $c->nombre_certificacion }}
                                </span>
                                <span class="badge bg-light text-muted border px-2 py-1 mt-1" style="font-size: 0.75rem;">
                                    {{ $c->tipo_certificacion ?? 'General' }}
                                </span>
                            </td>

                            {{-- INSTITUCIÓN --}}
                            <td class="py-3">
                                <span class="text-muted" style="font-size: 0.9rem;">
                                    <i class="fas fa-university me-1"></i> {{ $c->institucion }}
                                </span>
                                @if($c->codigo_certificado)
                                    <div class="small text-muted mt-1">Cód: {{ $c->codigo_certificado }}</div>
                                @endif
                            </td>

                            {{-- FECHAS --}}
                            <td class="py-3">
                                <div class="mb-1" style="font-size: 0.85rem;">
                                    <span class="text-muted">Exp:</span> {{ $c->fecha_expedicion->format('d/m/Y') }}
                                </div>
                                <div style="font-size: 0.85rem;">
                                    <span class="text-muted">Ven:</span> {{ $c->fecha_vencimiento ? $c->fecha_vencimiento->format('d/m/Y') : 'Permanente' }}
                                </div>
                            </td>

                            {{-- SOPORTES --}}
                                <td class="py-3">

                                    @if($c->documentos->count() > 0)

                                        <button type="button"
                                                class="btn btn-sm btn-light border"
                                                data-toggle="modal"
                                                data-target="#documentosModal{{ $c->id }}"
                                                style="border-radius: 8px;">

                                            <i class="fas fa-folder text-warning me-1"></i>
                                            Ver documentos ({{ $c->documentos->count() }})

                                        </button>

                                    @else

                                        <span class="text-muted small fst-italic">
                                            <i class="fas fa-file-slash me-1"></i>
                                            Sin soporte
                                        </span>

                                    @endif

                                </td>


                                {{-- ACCIONES --}}
                                <td class="text-center pe-4 py-3">

                                    <div class="d-flex justify-content-center gap-2">

                                        @if($c->estado == 1)

                                            <a href="{{ route('admin.certificaciones.edit', $c) }}"
                                            class="btn btn-sm btn-icon btn-outline-primary"
                                            data-toggle="tooltip"
                                            title="Editar">

                                                <i class="fas fa-pen"></i>

                                            </a>

                                            <form action="{{ route('admin.certificaciones.toggle', $c->id) }}"
                                                method="POST"
                                                class="d-inline">

                                                @csrf

                                                <button type="submit"
                                                        class="btn btn-sm btn-icon btn-outline-danger"
                                                        data-toggle="tooltip"
                                                        title="Desactivar">

                                                    <i class="fas fa-ban"></i>

                                                </button>

                                            </form>

                                        @else

                                            <button class="btn btn-sm btn-icon border-0 text-muted opacity-50"
                                                    disabled>

                                                <i class="fas fa-pen"></i>

                                            </button>

                                            <form action="{{ route('admin.certificaciones.toggle', $c->id) }}"
                                                method="POST"
                                                class="d-inline">

                                                @csrf

                                                <button type="submit"
                                                        class="btn btn-sm btn-light border text-success"
                                                        data-toggle="tooltip"
                                                        title="Activar">

                                                    <i class="fas fa-check-circle"></i>

                                                </button>

                                            </form>

                                        @endif
                                    </div>
                                </td>
                                </tr>

                                {{-- MODAL DOCUMENTOS --}}
                                @if($c->documentos->count() > 0)

                                <div class="modal fade"
                                    id="documentosModal{{ $c->id }}"
                                    tabindex="-1"
                                    role="dialog">

                                    <div class="modal-dialog modal-lg"
                                        role="document">

                                        <div class="modal-content border-0 shadow">

                                            <div class="modal-header">

                                                <h5 class="modal-title fw-bold">
                                                    Documentos
                                                </h5>

                                                <button type="button"
                                                        class="close"
                                                        data-dismiss="modal">

                                                    <span>&times;</span>

                                                </button>

                                            </div>

                                            <div class="modal-body">

                                                @foreach($c->documentos as $doc)

                                                    <div class="border rounded p-3 mb-2 d-flex justify-content-between align-items-center">

                                                        <div>
                                                            <i class="fas fa-file-pdf text-danger me-2"></i>
                                                            {{ $doc->nombre_original }}
                                                        </div>

                                                        <div class="d-flex gap-2">

                                                            <a href="{{ route('admin.documentos.view', $doc->id) }}"
                                                            target="_blank"
                                                            class="btn btn-sm btn-outline-primary">

                                                                <i class="fas fa-eye"></i>

                                                            </a>

                                                            <a href="{{ route('admin.documentos.download', $doc->id) }}"
                                                            class="btn btn-sm btn-outline-success">

                                                                <i class="fas fa-download"></i>

                                                            </a>

                                                        </div>

                                                    </div>

                                                @endforeach

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                @endif
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background-color: rgba(19,182,236,0.1);">
                                        <i class="fas fa-certificate fa-2x" style="color: var(--primary-blue, #13b6ec);"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1" style="color: #64748b;">Sin certificaciones registradas</h5>
                                    <p class="mb-0">Comience registrando una nueva certificación oficial.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($certificaciones->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4 rounded-bottom-4">
            {{ $certificaciones->links() }}
        </div>
        @endif
    </div>
</div>
@stop

@section('js')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
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
