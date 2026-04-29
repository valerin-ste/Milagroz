@extends('adminlte::page')

@section('content_header')
<div class="page-header d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="page-title fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Etapa Contractual
        </h2>
        <p class="page-subtitle text-muted mb-0">
            Gestión y control de los contratos laborales firmados.
        </p>
    </div>

    <div class="page-actions">
        <a href="{{ route('admin.etapa_contractual.create') }}" class="btn btn-orange">
            <i class="fas fa-plus me-2"></i> Nuevo Contrato
        </a>
    </div>
</div>

<form method="GET" action="{{ route('admin.etapa_contractual.index') }}" class="mb-4">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                
                {{-- CAMPO: BUSCAR EMPLEADO --}}
                <div class="col-md-3">
                    <label class="text-muted small fw-bold mb-1">Buscar Empleado</label>
                    <div class="search-container" style="border-radius: 10px; padding: 0;">
                        <i class="fas fa-search search-icon" style="left: 15px;"></i>
                        <input type="text" name="buscar" class="form-control search-input py-2" style="border-radius: 10px;"
                               placeholder="Nombre o apellido..." value="{{ request('buscar') }}">
                    </div>
                </div>

                {{-- CAMPO: DOCUMENTO --}}
                <div class="col-md-3">
                    <label class="text-muted small fw-bold mb-1">Número de Documento</label>
                    <div class="search-container" style="border-radius: 10px; padding: 0;">
                        <i class="fas fa-id-card search-icon" style="left: 15px;"></i>
                        <input type="text" name="documento" class="form-control search-input py-2" style="border-radius: 10px;"
                               placeholder="Documento..." value="{{ request('documento') }}">
                    </div>
                </div>

                {{-- CAMPO: TIPO DE CONTRATO --}}
                <div class="col-md-3">
                    <label class="text-muted small fw-bold mb-1">Tipo de Contrato</label>
                    <div class="search-container" style="border-radius: 10px; padding: 0;">
                        <i class="fas fa-file-contract search-icon" style="left: 15px;"></i>
                        <select name="tipo_contrato" class="form-control search-input py-2" style="border-radius: 10px; cursor: pointer; appearance: none; background-color: transparent;">
                            <option value="">Todos</option>
                            @foreach($tiposContrato as $t)
                                <option value="{{ $t }}" {{ request('tipo_contrato') == $t ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down" style="position: absolute; right: 15px; color: #999; font-size: 12px; pointer-events: none;"></i>
                    </div>
                </div>

                {{-- BOTONES --}}
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-orange flex-grow-1 fw-bold" style="border-radius: 10px; height: 42px;">
                            <i class="fas fa-filter me-1"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.etapa_contractual.index') }}" class="btn btn-light border flex-grow-1 fw-bold text-secondary" style="border-radius: 10px; height: 42px; display: flex; align-items: center; justify-content: center;">
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
                            <th class="ps-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Empleado Contratado</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Tipo de Contrato</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Fechas de Vigencia</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Estado Actual</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Soporte Digital</th>
                            <th class="text-center pe-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contratos as $c)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center" style="gap: 1rem;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 45px; height: 45px; background-color: rgba(19,182,236,0.1); color: var(--primary-blue);">
                                        <span class="fw-bold" style="font-size: 1.1rem;">
                                            {{ strtoupper(substr($c->empleado->persona->nombres ?? 'X', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                            {{ $c->empleado->persona->nombres ?? '' }} {{ $c->empleado->persona->apellidos ?? '' }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.85rem;">
                                            CC: {{ $c->empleado->persona->numero_documento ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3">
                                <span class="d-block" style="color: #334155; font-weight: 500;">
                                    <i class="fas fa-file-signature text-muted me-1"></i> {{ $c->tipo_contrato }}
                                </span>
                            </td>

                            <td class="py-3" style="color: #475569;">
                                <div class="mb-1" style="font-size: 0.9rem;">
                                    📅 {{ \Carbon\Carbon::parse($c->fecha_inicio)->format('d M Y') }}
                                </div>
                                <div class="text-muted" style="font-size: 0.85rem;">
                                    Fin: {{ $c->fecha_fin ? \Carbon\Carbon::parse($c->fecha_fin)->format('d M Y') : 'Indefinido' }}
                                </div>
                            </td>

                            <td class="py-3">
                                @php
                                    $vencido = $c->fecha_fin && \Carbon\Carbon::parse($c->fecha_fin)->isPast();
                                @endphp

                                @if($c->estado == 0)
                                    <span class="badge bg-danger">Inactivo</span>
                                @elseif($vencido)
                                    <span class="badge bg-warning text-dark">Vencido</span>
                                @else
                                    <span class="badge bg-success">Vigente</span>
                                @endif
                            </td>
                            
                            <td class="py-3">
                                <button class="btn btn-outline-secondary btn-sm"
                                        data-toggle="modal"
                                        data-target="#docsModal{{ $c->id }}">
                                    📂 Ver documentos ({{ $c->documentos->count() }})
                                </button>
                            </td>

                            <td class="text-center pe-4 py-3">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    @if($c->estado == 1)
                                        <a href="{{ route('admin.etapa_contractual.edit', $c) }}"
                                           class="btn btn-sm btn-icon btn-outline-primary"
                                           data-toggle="tooltip" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <form action="{{ route('admin.etapa_contractual.destroy', $c) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-icon btn-outline-danger"
                                                    data-toggle="tooltip" title="Inactivar"
                                                    onclick="return confirm('¿Confirma que desea INACTIVAR este contrato?');">
                                                <i class="fas fa-toggle-off"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.etapa_contractual.toggle', $c->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light border btn-icon"
                                                    data-toggle="tooltip" title="Reactivar">
                                                🔄
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <h5>No hay registros</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($contratos->hasPages())
        <div class="card-footer bg-white border-top py-3 px-4 rounded-bottom-4">
            {{ $contratos->links() }}
        </div>
        @endif
    </div>

</div>

{{-- MODALES DE DOCUMENTOS --}}
@foreach($contratos as $c)
    <div class="modal fade" id="docsModal{{ $c->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4">

                <div class="modal-header">
                    <h5 class="modal-title">Documentos</h5>
                    <button type="button" class="btn-close" data-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <ul class="list-group">
                        @forelse($c->documentos as $doc)
                        <li class="list-group-item d-flex justify-content-between align-items-center">

                            <span class="text-truncate" style="max-width: 200px;">
                                📄 {{ $doc->nombre_original }}
                            </span>

                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.documentos.view', $doc->id) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    👁️
                                </a>

                                <a href="{{ route('admin.documentos.download', $doc->id) }}"
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush

@push('css')
<style>
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
@endpush
