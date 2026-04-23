@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">
            Comunicaciones
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">
            Gestión y control de comunicaciones internas.
        </p>
    </div>

    <a href="{{ route('admin.comunicaciones.create') }}" class="btn btn-orange">
        <i class="fas fa-plus me-2"></i> Nueva Comunicación
    </a>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center"
            style="background-color: #ecfdf5; color: #047857; border: none; border-radius: var(--radius-md);">
            <i class="fas fa-check-circle fa-lg me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <form method="GET" action="{{ route('admin.comunicaciones.index') }}" class="filter-card mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="filter-label">Buscar Empleado / Destinatario</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="buscar" class="form-control"
                           placeholder="Nombre o apellido..." value="{{ request('buscar') }}">
                </div>
            </div>

            <div class="col-md-4">
                <label class="filter-label">Número de Documento</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                    <input type="text" name="documento" class="form-control"
                           placeholder="Documento..." value="{{ request('documento') }}">
                </div>
            </div>

            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1" style="height:38px;">
                        <i class="fas fa-filter mr-1"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.comunicaciones.index') }}" class="btn btn-light border btn-sm flex-grow-1" style="height:38px;">
                        <i class="fas fa-undo mr-1"></i> Limpiar
                    </a>
                </div>
            </div>
        </div>
    </form>

    <div class="card border-0">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead>
                        <tr>
                            <th class="ps-4">Empleado</th>
                            <th>Asunto</th>
                            <th>Fecha</th>
                            <th>Documentos</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($comunicaciones as $c)

                        <tr>

                            {{-- EMPLEADO --}}
                            <td class="ps-4">
                                <div class="d-flex align-items-center">

                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); color: var(--primary-blue);">
                                        <span class="fw-bold">
                                            {{ strtoupper(substr($c->empleado->persona->nombres ?? 'X', 0, 1)) }}
                                        </span>
                                    </div>

                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                            {{ $c->empleado->persona->nombres ?? '' }}
                                            {{ $c->empleado->persona->apellidos ?? '' }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.85rem;">
                                            CC: {{ $c->empleado->persona->numero_documento ?? '' }}
                                        </div>
                                    </div>

                                </div>
                            </td>

                            {{-- ASUNTO --}}
                            <td>
                                <span class="d-block" style="color: #334155; font-weight: 500;">
                                    <i class="fas fa-envelope text-muted me-1"></i>
                                    {{ $c->asunto }}
                                </span>
                            </td>

                            {{-- FECHA --}}
                            <td style="color: #475569;">
                                <i class="fas fa-calendar-alt text-primary me-1"></i>
                                {{ \Carbon\Carbon::parse($c->fecha)->format('d M, Y') }}
                            </td>

                            {{-- DOCUMENTOS --}}
                            <td>
                                @if($c->documentos->count() > 0)
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($c->documentos as $doc)
                                            <a href="{{ route('admin.documentos.view', $doc->id) }}?t={{ time() }}"
                                               target="_blank"
                                               class="doc-file-clickable"
                                               data-toggle="tooltip" data-boundary="window"
                                               title="{{ $doc->nombre_original }}"
                                               style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                                                <i class="fas fa-file-pdf"></i>
                                                <span class="file-name-text text-truncate" style="max-width: 120px;">{{ $doc->nombre_original }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic">Sin archivos</span>
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td class="text-center pe-4">

                                <div class="d-flex justify-content-center gap-2">

                                    <a href="{{ route('admin.comunicaciones.edit', $c) }}"
                                       class="btn btn-sm btn-light-custom px-3"
                                       data-toggle="tooltip" data-placement="top"
                                       title="Editar comunicación">
                                        <i class="fas fa-pen text-muted"></i>
                                    </a>

                                    <form action="{{ route('admin.comunicaciones.destroy', $c) }}"
                                          method="POST"
                                          class="d-inline">

                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-light-custom px-3"
                                                data-toggle="tooltip" data-placement="top"
                                                title="Eliminar comunicación permanentemente"
                                                onclick="return confirm('¿Eliminar esta comunicación?');">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                        @empty

                        @endforelse

                    </tbody>

                </table>
            </div>

        </div>

        @if($comunicaciones->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4">
            {{ $comunicaciones->links() }}
        </div>
        @endif

    </div>

    
</div>
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
@endsection