@extends('adminlte::page')

@section('title', 'Fechas Especiales')

@section('content_header')
<div class="page-header d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="page-title fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Fechas Especiales
        </h2>
        <p class="page-subtitle text-muted mb-0">
            Gestión de eventos, reconocimientos y fechas importantes para los empleados.
        </p>
    </div>

    <div class="page-actions">
        <a href="{{ route('admin.fechas_especiales.create') }}" class="btn btn-orange">
            <i class="fas fa-plus me-2"></i> Nueva Fecha Especial
        </a>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    <form method="GET" action="{{ route('admin.fechas_especiales.index') }}" class="mb-4">
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

                    {{-- CAMPO: TIPO --}}
                    <div class="col-md-4">
                        <label class="text-muted small fw-bold mb-2 ps-1">Tipo de Fecha</label>
                        <div class="d-flex align-items-center bg-white" style="border-radius: 30px; height: 45px; padding: 0 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="fas fa-calendar-star text-muted" style="font-size: 14px;"></i>
                            <input type="text" name="tipo" class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                   placeholder="Ej: Cumpleaños, Aniversario..." value="{{ request('tipo') }}" style="outline: none;">
                        </div>
                    </div>

                    {{-- BOTONES --}}
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-orange flex-grow-1 fw-bold d-flex justify-content-center align-items-center" style="border-radius: 30px; height: 45px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                <i class="fas fa-filter me-2"></i> Filtrar
                            </button>
                            <a href="{{ route('admin.fechas_especiales.index') }}" class="btn bg-white border flex-grow-1 fw-bold text-secondary d-flex justify-content-center align-items-center" style="border-radius: 30px; height: 45px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                                <i class="fas fa-sync-alt me-2"></i> Limpiar
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center shadow-sm rounded-3 mb-4"
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
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Tipo</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Fecha</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Documentos</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Estado</th>
                            <th class="text-center pe-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fechas as $fecha)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center" style="gap: 1rem;">
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                            {{ $fecha->empleado->persona->nombres ?? '' }} {{ $fecha->empleado->persona->apellidos ?? '' }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.85rem;">
                                            {{ $fecha->empleado->cargo ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3">
                                <span class="badge bg-soft-info text-info rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                                    {{ $fecha->tipo }}
                                </span>
                            </td>

                            <td class="py-3" style="color: #475569;">
                                <div style="font-size: 0.9rem;">
                                    📅 {{ \Carbon\Carbon::parse($fecha->fecha)->format('d M Y') }}
                                </div>
                            </td>

                            <td class="py-3">
                                @if($fecha->archivo)
                                    <button type="button"
                                            class="btn btn-outline-secondary btn-sm"
                                            data-toggle="modal"
                                            data-target="#docsModal{{ $fecha->id }}">
                                        <i class="fas fa-folder text-warning me-2"></i>
                                        Ver documentos (1)
                                    </button>
                                @else
                                    <span class="text-muted">Sin documentos</span>
                                @endif
                            </td>

                            <td class="py-3">
                                @if($fecha->estado == 1)
                                    <span class="badge bg-soft-success text-success rounded-pill px-3 py-2 fw-bold" style="font-size: 0.75rem;">
                                        <i class="fas fa-check-circle me-1"></i> ACTIVO
                                    </span>
                                @else
                                    <span class="badge bg-soft-danger text-danger rounded-pill px-3 py-2 fw-bold" style="font-size: 0.75rem;">
                                        <i class="fas fa-times-circle me-1"></i> INACTIVO
                                    </span>
                                @endif
                            </td>

                            <td class="text-center pe-4 py-3">
                                <div class="d-flex justify-content-center align-items-center gap-2">

                                    {{-- EDITAR --}}
                                    <a href="{{ route('admin.fechas_especiales.edit', $fecha) }}"
                                    class="btn btn-sm btn-outline-primary d-flex justify-content-center align-items-center"
                                    style="width:38px; height:38px;">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    {{-- ELIMINAR --}}
                                    <form action="{{ route('admin.fechas_especiales.destroy', $fecha) }}"
                                        method="POST"
                                        class="m-0">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger d-flex justify-content-center align-items-center"
                                                style="width:38px; height:38px;">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>

                                    </form>

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background-color: rgba(255,106,0,0.1);">
                                        <i class="fas fa-calendar-star fa-2x" style="color: #ff6a00;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1" style="color: #64748b;">No hay fechas especiales registradas</h5>
                                    <p class="mb-0">Comience registrando la primera fecha especial.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($fechas->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4 rounded-bottom-4">
            {{ $fechas->links() }}
        </div>
        @endif
    </div>

</div>
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

@foreach($fechas as $fecha)
<div class="modal fade" id="docsModal{{ $fecha->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4">

            <div class="modal-header">
                <h5 class="modal-title">Documentos</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <ul class="list-group">

                    @if($fecha->archivo)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center w-100">

                            <span class="text-truncate" style="max-width:220px;">
                                <i class="fas fa-file-pdf text-danger me-2"></i>

                                @php
                                    $nombre = basename($fecha->archivo);
                                    $pos = strpos($nombre, '_');
                                    $nombreLimpio = $pos !== false ? substr($nombre, $pos + 1) : $nombre;
                                @endphp

                                {{ $nombreLimpio }}
                            </span>

                            <div class="d-flex align-items-center gap-2">

                                <a href="{{ asset('storage/'.$fecha->archivo) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center"
                                   style="width:38px;height:38px;">
                                    <i class="fas fa-eye"></i>
                                </a>   

                                <a href="{{ asset('storage/'.$fecha->archivo) }}"
                                   download
                                   class="btn btn-sm btn-outline-success d-flex align-items-center justify-content-center"
                                   style="width:38px;height:38px;">
                                    <i class="fas fa-download"></i>
                                </a>

                            </div>

                        </div>
                    </li>
                    @else
                    <li class="list-group-item text-muted">
                        Sin documentos
                    </li>
                    @endif

                </ul>
            </div>

        </div>
    </div>
</div>
@endforeach

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

.bg-soft-success { background-color: rgba(16, 185, 129, 0.1); }
.bg-soft-danger { background-color: rgba(239, 68, 68, 0.1); }
.bg-soft-info { background-color: rgba(6, 182, 212, 0.1); }

.text-success { color: #10b981 !important; }
.text-danger { color: #ef4444 !important; }
.text-info { color: #06b6d4 !important; }

.rounded-4 { border-radius: 1rem !important; }
.text-silver { color: #cbd5e1; }
</style>
@endsection
