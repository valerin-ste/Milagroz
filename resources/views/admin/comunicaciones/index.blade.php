@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
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
                                            <a href="{{ Storage::url($doc->ruta) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-light-custom text-start text-truncate"
                                               title="{{ $doc->nombre_original }}"
                                               style="border: 1px solid #e2e8f0; color: #b91c1c; max-width: 160px; font-size: 0.8rem;">
                                                <i class="fas fa-file"></i>
                                                {{ $doc->nombre_original }}
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

                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state py-3">
                                    <div class="empty-state-icon"><i class="fas fa-bullhorn"></i></div>
                                    <h5 class="empty-state-title">No hay comunicaciones registradas</h5>
                                    <p class="empty-state-description">Comience creando la primera comunicación interna.</p>
                                    <a href="{{ route('admin.comunicaciones.create') }}" class="btn btn-orange btn-sm px-4">
                                        <i class="fas fa-plus mr-1"></i> Nueva Comunicación
                                    </a>
                                </div>
                            </td>
                        </tr>

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
@endsection