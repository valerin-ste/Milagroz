@extends('adminlte::page')

@section('content_header')

<div class="d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Etapa Precontractual
        </h2>
        <p class="text-muted mb-0">
            Gestión y control de los registros precontractuales.
        </p>
    </div>

    <a href="{{ route('admin.etapa_precontractual.create') }}" class="btn btn-orange">
        <i class="fas fa-plus me-2"></i> Nuevo Registro
    </a>
</div>

<form method="GET" action="{{ route('admin.etapa_precontractual.index') }}" class="mb-3 px-2">
    <div class="input-group">

        <input type="text" name="buscar" class="form-control"
               placeholder="Buscar por nombre o número de documento..."
               value="{{ request('buscar') }}">

        <button class="btn btn-primary">
            Buscar
        </button>

        @if(request('buscar'))
            <a href="{{ route('admin.etapa_precontractual.index') }}" class="btn btn-secondary">
                Limpiar
            </a>
        @endif

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

    {{-- 🔥 NUEVO DISEÑO EN TARJETAS --}}
    <div class="row p-3">

        @forelse($etapas as $c)
        <div class="col-md-6 col-lg-4 mb-4">

            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body d-flex flex-column">

                    {{-- EMPLEADO --}}
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width: 45px; height: 45px; background-color: rgba(19,182,236,0.1); color: var(--primary-blue);">
                            <span class="fw-bold">
                                {{ strtoupper(substr($c->persona->nombres ?? 'X', 0, 1)) }}
                            </span>
                        </div>

                        <div>
                            <div class="fw-bold">
                                {{ $c->persona->nombres ?? '' }} {{ $c->persona->apellidos ?? '' }}
                            </div>
                            <small class="text-muted">
                                CC: {{ $c->persona->numero_documento ?? '' }}
                            </small>
                        </div>
                    </div>

                    {{-- FECHA --}}
                    <div class="mb-2">
                        <small class="text-muted d-block">Fecha</small>
                        <span>📅 {{ \Carbon\Carbon::parse($c->fecha_registro)->format('d M Y') }}</span>
                    </div>

                    {{-- ESTADO --}}
                    <div class="mb-3">
                        <small class="text-muted d-block">Estado</small>

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
                    </div>

                    {{-- ACCIONES --}}
                    <div class="d-flex justify-content-between align-items-center mt-auto">

            {{-- BOTÓN PRINCIPAL --}}
            <button class="btn btn-outline-secondary btn-sm"
                    data-toggle="modal"
                    data-target="#docsModal{{ $c->id }}">
                📂 Ver documentos ({{ $c->documentos->count() }})
            </button>

            {{-- ACCIONES --}}
            <div class="d-flex align-items-center gap-2">

            @if($c->estado == 1)

                {{-- EDITAR --}}
                <a href="{{ route('admin.etapa_precontractual.edit', $c) }}"
                    class="btn btn-sm btn-icon btn-outline-primary"
                    data-toggle="tooltip"
                    title="Editar">
                        <i class="fas fa-pen"></i>
                </a>

                {{-- ELIMINAR --}}
                <form action="{{ route('admin.etapa_precontractual.destroy', $c) }}"
                    method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-icon btn-outline-danger"
                            data-toggle="tooltip"
                            title="Inactivar"
                            onclick="return confirm('¿Inactivar registro?')">
                        <i class="fas fa-toggle-off"></i>
                    </button>
                </form>

            @else

                {{-- REACTIVAR --}}
                <form action="{{ route('admin.etapa_precontractual.toggle', $c->id) }}"
                    method="POST">
                    @csrf
                    <button class="btn btn-sm btn-light border">
                        🔄
                    </button>
                </form>

            @endif

        </div>
    </div>
</div>
</div>
</div>

        {{-- MODAL DOCUMENTOS --}}
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

        @empty
        <div class="col-12 text-center py-5 text-muted">
            <h5>No hay registros</h5>
        </div>
        @endforelse

    </div>

    {{-- PAGINACIÓN --}}
    @if($etapas->hasPages())
    <div class="card-footer bg-white border-top py-3 px-4">
        {{ $etapas->links() }}
    </div>
    @endif

</div>
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
</style>
@endpush