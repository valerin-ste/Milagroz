@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
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

    <div class="card border-0">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead>
                        <tr>
                            <th class="ps-4">Empleado</th>
                            <th>Fechas</th>
                            <th>Estado</th>
                            <th>Documentos</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($etapas as $c)
                        <tr>

                            {{-- EMPLEADO --}}
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                         style="width: 40px; height: 40px; background-color: rgba(19,182,236,0.1); color: var(--primary-blue);">
                                        <span class="fw-bold">
                                            {{ strtoupper(substr($c->persona->nombres ?? 'X', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">
                                            {{ $c->persona->nombres ?? '' }} {{ $c->persona->apellidos ?? '' }}
                                        </div>
                                        <div class="text-muted small">
                                            CC: {{ $c->persona->numero_documento ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td style="color: #475569;">
                                <div class="mb-1">
                                    <i class="fas fa-calendar-check text-success me-1"></i>
                                    {{ \Carbon\Carbon::parse($c->fecha_registro)->format('d M, Y') }}
                                </div>
                            </td>

                            {{-- ESTADO --}}
                            <td>
                                @php
                                    $vencido = $c->fecha_fin && \Carbon\Carbon::parse($c->fecha_fin)->isPast();
                                @endphp

                                @if($c->estado == 0)
                                    <span class="badge-soft-danger">
                                        <i class="fas fa-times-circle"></i> Inactivo
                                    </span>
                                @elseif($vencido)
                                    <span class="badge-soft-danger">
                                        <i class="fas fa-exclamation-triangle"></i> Vencido
                                    </span>
                                @else
                                    <span class="badge-soft-success">
                                        <i class="fas fa-check-circle"></i> Vigente
                                    </span>
                                @endif
                            </td>

                            {{-- DOCUMENTOS --}}
                            <td>
                                @if($c->documentos->count() > 0)
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($c->documentos as $doc)
                                            <a href="{{ Storage::url($doc->ruta) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-light-custom text-start text-truncate"
                                               title="{{ $doc->nombre_original }}"
                                               style="border: 1px solid #e2e8f0; color:#b91c1c; max-width:160px; font-size:0.8rem;">
                                                <i class="fas fa-file-pdf"></i>
                                                {{ $doc->nombre_original }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic">
                                        Sin archivos
                                    </span>
                                @endif
                            </td>

                            <td class="text-center pe-4">
                                <div class="action-container">

                                    @if($c->estado == 1)
                                        <a href="{{ route('admin.etapa_precontractual.edit', $c) }}"
                                           class="btn-table-action"
                                           data-toggle="tooltip" data-placement="top" title="Editar registro precontractual">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <form action="{{ route('admin.etapa_precontractual.destroy', $c) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-table-action"
                                                    data-toggle="tooltip" data-placement="top" title="Desactivar registro"
                                                    onclick="return confirm('¿Desactivar registro?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn-table-action opacity-50"
                                                data-toggle="tooltip" data-placement="top" title="Edición no disponible (Inactivo)"
                                                style="cursor:not-allowed;" disabled>
                                            <i class="fas fa-pen"></i>
                                        </button>

                                        <form action="{{ route('admin.etapa_precontractual.toggle', $c->id) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn-table-action"
                                                    data-toggle="tooltip" data-placement="top" title="Reactivar registro">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3"
                                         style="width: 60px; height: 60px; background-color: #f1f5f9;">
                                        <i class="fas fa-handshake fa-2x" style="color: #cbd5e1;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1">Aún no hay registros</h5>
                                    <p class="mb-0">Crea el primero</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>

        @if($etapas->hasPages())
        <div class="card-footer bg-white border-top py-3 px-4">
            {{ $etapas->links() }}
        </div>
        @endif

    </div>

</div>
@endsection