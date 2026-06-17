@extends('adminlte::page')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'Gestión de Dotaciones')

@section('content_header')
<div class="page-header d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="page-title fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Dotación de Personal
        </h2>
        <p class="page-subtitle text-muted mb-0">
            Control y registro de dotaciones, uniformes y equipos entregados al personal.
        </p>
    </div>

    <div class="page-actions">
        <a href="{{ route('admin.dotaciones.create') }}" class="btn btn-orange shadow-sm px-4">
            <i class="fas fa-plus me-2"></i> Nueva Dotación
        </a>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center border-0 shadow-sm" style="background-color: #ecfdf5; color: #047857; border-radius: 12px;">
            <i class="fas fa-check-circle fa-lg me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- BARRA DE FILTROS --}}
    <form method="GET" action="{{ route('admin.dotaciones.index') }}" class="mb-4">
        <div class="card shadow-sm border-0 rounded-4" style="background-color: #f8fafc;">
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    
                    {{-- CAMPO: BUSCAR --}}
                    <div class="col-md-5">
                        <label class="text-muted small fw-bold mb-2 ps-1">Buscar por Empleado / Tipo / Talla</label>
                        <div class="d-flex align-items-center bg-white" style="border-radius: 30px; height: 45px; padding: 0 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="fas fa-search text-muted" style="font-size: 14px;"></i>
                            <input type="text" name="buscar" class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                   placeholder="Nombre, tipo, talla..." value="{{ $buscar }}" style="outline: none;">
                        </div>
                    </div>

                    {{-- CAMPO: ESTADO --}}
                    <div class="col-md-4">
                        <label class="text-muted small fw-bold mb-2 ps-1">Estado del Registro</label>
                        <div class="d-flex align-items-center bg-white" style="border-radius: 30px; height: 45px; padding: 0 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="fas fa-toggle-on text-muted" style="font-size: 14px;"></i>
                            <select name="estado" class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0" style="outline: none;">
                                <option value="">-- Todos --</option>
                                <option value="1" {{ $estado === '1' ? 'selected' : '' }}>Activos</option>
                                <option value="0" {{ $estado === '0' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                    </div>

                    {{-- BOTONES --}}
                    <div class="col-md-3">
                        {{-- BOTONES --}}
                        <div class="col-md-3">
                            <div class="d-flex gap-2">

                                <button type="submit"
                                        class="btn btn-orange flex-grow-1 fw-bold d-flex justify-content-center align-items-center"
                                        style="border-radius: 30px; height: 45px;">
                                    <i class="fas fa-filter me-2"></i>
                                    Filtrar
                                </button>

                                <a href="{{ route('admin.dotaciones.index') }}"
                                class="btn bg-white border flex-grow-1 fw-bold text-secondary d-flex justify-content-center align-items-center"
                                style="border-radius: 30px; height: 45px;">
                                    <i class="fas fa-sync-alt me-2"></i>
                                    Limpiar
                                </a>

                            </div>
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
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Tipo Dotación</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Talla / Cantidad</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Fecha Entrega</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Soportes</th>
                            <th class="text-center pe-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dotaciones as $d)
                        <tr style="border-bottom: 1px solid #f1f5f9; {{ $d->estado == 0 ? 'background-color: #f8fafc; opacity: 0.8;' : '' }}">
                            
                            {{-- EMPLEADO --}}
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center" style="gap: 1rem;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 45px; height: 45px; background-color: rgba(255,106,0,0.1); color: #ff6a00;">
                                        <span class="fw-bold" style="font-size: 1.1rem;">
                                            {{ strtoupper(substr($d->empleado->persona->nombres ?? 'U', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                            {{ $d->empleado->persona->nombres ?? '' }} {{ $d->empleado->persona->apellidos ?? '' }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.85rem;">
                                            {{ $d->empleado->cargo }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- TIPO DOTACIÓN --}}
                            <td class="py-3">
                                <span class="d-block text-dark fw-bold" style="font-size: 0.95rem;">
                                    {{ $d->tipo_dotacion }}
                                </span>
                                @if($d->observaciones)
                                    <span class="text-muted text-truncate d-inline-block" style="max-width: 150px; font-size: 0.8rem;" title="{{ $d->observaciones }}">
                                        {{ $d->observaciones }}
                                    </span>
                                @endif
                            </td>

                            {{-- TALLA / CANTIDAD --}}
                            <td class="py-3">
                                <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill" style="font-size: 0.85rem;">
                                    Talla: {{ $d->talla }}
                                </span>
                                <div class="mt-1 small text-muted">
                                    Cant: <span class="fw-bold">{{ $d->cantidad }}</span> unds.
                                </div>
                            </td>

                            {{-- FECHA --}}
                            <td class="py-3">
                                <div class="text-dark fw-semibold" style="font-size: 0.9rem;">
                                    <i class="far fa-calendar-alt text-muted me-2"></i>
                                    {{ $d->fecha->format('d/m/Y') }}
                                </div>
                            </td>

                            {{-- SOPORTES --}}
                                <td class="py-3">
                                    @if($d->documentos->count() > 0)
                                        <button type="button"
                                                class="btn btn-sm btn-light border"
                                                data-toggle="modal"
                                                data-target="#documentosModal{{ $d->id }}"
                                                style="border-radius: 8px;">
                                            <i class="fas fa-folder text-warning me-1"></i>
                                            Ver documentos ({{ $d->documentos->count() }})
                                        </button>
                                    @else
                                        <span class="text-muted small fst-italic">
                                            <i class="fas fa-file-slash me-1"></i> Sin archivo
                                        </span>
                                    @endif
                                </td>

                            {{-- ACCIONES --}}
                            <td class="text-center pe-4 py-3">
                                <div class="d-flex justify-content-center gap-2">
                                    @if($d->estado == 1)
                                        <a href="{{ route('admin.dotaciones.edit', $d) }}"
                                           class="btn btn-sm btn-icon btn-outline-primary"
                                           data-toggle="tooltip"
                                           title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <form action="{{ route('admin.dotaciones.toggle', $d->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-sm btn-icon btn-outline-danger"
                                                    data-toggle="tooltip"
                                                    title="Desactivar">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-icon border-0 text-muted opacity-50" disabled>
                                            <i class="fas fa-pen"></i>
                                        </button>

                                        <form action="{{ route('admin.dotaciones.toggle', $d->id) }}" method="POST" class="d-inline">
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
                        @if($d->documentos->count() > 0)
                        <div class="modal fade" id="documentosModal{{ $d->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                                    {{-- HEADER --}}
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Documentos</h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    {{-- BODY --}}
                                    <div class="modal-body">
                                        @foreach($d->documentos as $doc)
                                            <div class="border rounded p-3 mb-2 d-flex justify-content-between align-items-center">
                                                
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-file text-secondary opacity-50 me-2" style="font-size: 1.1rem;"></i>
                                                    <span class="text-dark small fw-bold text-truncate" style="max-width: 250px;">
                                                        {{ $doc->nombre_original }}
                                                    </span>
                                                </div>

                                                <div class="d-flex align-items-center gap-1">

                                                {{-- VER --}}
                                                <a href="{{ route('admin.documentos.view', $doc->id) }}"
                                                target="_blank"
                                                class="btn btn-sm btn-outline-primary btn-doc-action">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                {{-- DESCARGAR --}}
                                                <a href="{{ route('admin.documentos.download', $doc->id) }}"
                                                class="btn btn-sm btn-outline-success btn-doc-action">
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
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background-color: rgba(255,106,0,0.1);">
                                        <i class="fas fa-tshirt fa-2x" style="color: #ff6a00;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1" style="color: #64748b;">Sin registros de dotación</h5>
                                    <p class="mb-0 small">No hay entregas registradas para el criterio de búsqueda.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($dotaciones->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4 rounded-bottom-4">
            {{ $dotaciones->links() }}
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
:root {
    --primary-orange: #ff6a00;
    --primary-orange-hover: #e65c00;
    --soft-primary: rgba(255, 106, 0, 0.1);
    --bg-light: #f8fafc;
}

.btn-orange {
    background-color: var(--primary-orange);
    border: none;
    color: #fff;
    border-radius: 30px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-orange:hover {
    background-color: var(--primary-orange-hover);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 106, 0, 0.2);
}

.bg-soft-primary { background-color: var(--soft-primary); }
.text-primary { color: var(--primary-orange) !important; }

.btn-icon {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all 0.2s;
}

.btn-icon:hover {
    transform: scale(1.1);
}

.rounded-4 { border-radius: 1rem !important; }

.table thead th {
    letter-spacing: 0.5px;
    border-top: none;
}

.hover-light:hover {
    background-color: #f1f5f9 !important;
}

.extra-small { font-size: 0.75rem; }
.bg-soft-danger { background-color: rgba(239, 68, 68, 0.1); }

.btn-doc-action{
    width: 36px;
    height: 32px;
    padding: 0;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all .2s ease;
}

.btn-doc-action:hover{
    transform: scale(1.05);
}
</style>
@endsection
