@extends('adminlte::page')

@section('title', 'Evaluaciones de Desempeño')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">Evaluaciones de Desempeño</h2>
        <p class="text-muted mb-0">Seguimiento y calificación del rendimiento laboral.</p>
    </div>
    <a href="{{ route('admin.evaluaciones_desempeno.create') }}" class="btn btn-orange shadow-sm">
        <i class="fas fa-plus me-2"></i> Nueva Evaluación
    </a>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center border-0 shadow-sm" style="background-color: #ecfdf5; color: #047857;">
            <i class="fas fa-check-circle fa-lg me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center border-0 shadow-sm" style="background-color: #fef2f2; color: #991b1b;">
            <i class="fas fa-exclamation-circle fa-lg me-3"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
    <form method="GET" action="{{ route('admin.evaluaciones_desempeno.index') }}" class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="small font-weight-bold text-muted mb-1">Buscar Empleado</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-light text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="buscar" class="form-control border-light bg-light shadow-none" placeholder="Nombre o apellido..." value="{{ request('buscar') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted mb-1">Estado</label>
                    <select name="estado" class="form-control border-light bg-light shadow-none">
                        <option value="">-- Todos --</option>
                        <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Vigente</option>
                        <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactiva</option>
                    </select>
                </div>

                <div class="col-md-5">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1 shadow-xs">
                            <i class="fas fa-filter mr-1"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.evaluaciones_desempeno.index') }}" class="btn btn-light border flex-grow-1 shadow-xs">
                            <i class="fas fa-undo mr-1"></i> Limpiar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Empleado</th>
                            <th>Fecha</th>
                            <th>Calificación</th>
                            <th>Archivos Adjuntos</th>
                            <th>Estado</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($evaluaciones as $e)
                        <tr @if($e->estado == 0) style="background-color: #f8fafc; opacity: 0.8;" @endif>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); color: var(--primary-blue);">
                                        <span class="fw-bold">{{ strtoupper(substr($e->empleado->persona->nombres ?? 'U', 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $e->empleado->persona->nombres ?? '' }} {{ $e->empleado->persona->apellidos ?? '' }}</div>
                                        <div class="text-muted small">{{ $e->empleado->cargo }}</div>
                                    </div>
                                </div>
                            </td>

                            <td style="color: #475569;">
                                <i class="fas fa-calendar-alt text-muted me-1 small"></i>
                                {{ \Carbon\Carbon::parse($e->fecha)->format('d/m/Y') }}
                            </td>

                            <td>
                                @php
                                    $color = $e->calificacion >= 8 ? 'success' : ($e->calificacion >= 6 ? 'warning' : 'danger');
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 6px; border-radius: 10px;">
                                        <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $e->calificacion * 10 }}%" aria-valuenow="{{ $e->calificacion }}" aria-valuemin="0" aria-valuemax="10"></div>
                                    </div>
                                    <span class="fw-bold text-{{ $color }}">{{ $e->calificacion }}/10</span>
                                </div>
                            </td>

                            <td>
                                @if($e->documentos->count() > 0)
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($e->documentos as $doc)
                                            <a href="{{ Storage::url($doc->ruta) }}" target="_blank" class="btn btn-sm btn-outline-secondary text-truncate" style="max-width: 160px; font-size: 0.8rem;" title="{{ $doc->nombre_original }}">
                                                <i class="fas fa-file-pdf text-danger me-1"></i> {{ $doc->nombre_original }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic">Sin archivos</span>
                                @endif
                            </td>

                            <td>
                                @php $badge = $e->getStatusBadge($e->fecha); @endphp

                                @if($e->estado == 0)
                                    <span class="badge-soft-danger px-3 py-1 rounded-pill" style="font-size: 0.8rem; font-weight: 600;">Inactiva</span>
                                @else
                                    <span class="{{ $badge['class'] }} px-3 py-1 rounded-pill" style="font-size: 0.8rem; font-weight: 600;">
                                        <i class="{{ $badge['icon'] }} me-1"></i> {{ $badge['label'] }}
                                    </span>
                                @endif
                            </td>

                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    @if($e->estado == 1)
                                        <a href="{{ route('admin.evaluaciones_desempeno.edit', $e) }}"
                                           class="btn btn-sm btn-light-custom px-3"
                                           data-toggle="tooltip" data-placement="top" title="Editar evaluación de desempeño">
                                            <i class="fas fa-pen text-muted"></i>
                                        </a>

                                        <form action="{{ route('admin.evaluaciones_desempeno.destroy', $e) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light-custom px-3 text-danger"
                                                    data-toggle="tooltip" data-placement="top" title="Desactivar evaluación"
                                                    onclick="return confirm('¿Confirma que desea DESACTIVAR esta evaluación?');">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-light-custom px-3 opacity-50"
                                                data-toggle="tooltip" data-placement="top" title="Edición no disponible (Inactiva)"
                                                disabled>
                                            <i class="fas fa-pen text-muted"></i>
                                        </button>

                                        <form action="{{ route('admin.evaluaciones_desempeno.toggle', $e->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light-custom px-3 text-success"
                                                    data-toggle="tooltip" data-placement="top" title="Reactivar evaluación">
                                                <i class="fas fa-check-circle"></i> Activar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background-color: #f1f5f9;">
                                        <i class="fas fa-chart-line fa-2x text-muted"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1">No hay evaluaciones registradas</h5>
                                    <p class="mb-0">Registre la evaluación de desempeño para comenzar.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($evaluaciones->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4">
            {{ $evaluaciones->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    .btn-orange { background-color: #f97316; color: #fff; border: none; }
    .btn-orange:hover { background-color: #ea580c; color: #fff; }

    .btn-light-custom { background-color: #f9fafb; border: 1px solid #e5e7eb; color: #374151; }
    .btn-light-custom:hover { background-color: #f3f4f6; color: #111827; }

    .badge-success { background-color: #dcfce7; color: #166534; }
    .badge-danger { background-color: #fee2e2; color: #991b1b; }
</style>
@endsection