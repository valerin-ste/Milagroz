@extends('adminlte::page')

@section('title', 'Vencimientos de Formación')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">Cursos con Vencimiento</h2>
        <p class="text-muted mb-0">Gestión específica de capacitaciones que requieren renovación periódica.</p>
    </div>
    <a href="{{ route('admin.formaciones.create') }}" class="btn btn-orange shadow-sm text-white">
        <i class="fas fa-plus me-2"></i> Registrar Vencimiento
    </a>    
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center border-0 shadow-sm mb-4" style="background-color: #ecfdf5; color: #047857;">
            <i class="fas fa-check-circle fa-lg me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- ── FILTROS ── --}}
    <div class="card border-0 shadow-sm mb-4">
        <form method="GET" action="{{ route('admin.formaciones.vencimientos') }}">
            <div class="card-body p-3">
                <div class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label class="small font-weight-bold text-muted mb-1">Buscar Empleado</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-light text-muted"><i class="fas fa-user"></i></span>
                            <input type="text" name="buscar" class="form-control border-light bg-light shadow-none" 
                                   placeholder="Nombre o documento..." value="{{ $buscar }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="small font-weight-bold text-muted mb-1">Nombre del Curso</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-light text-muted">
                                <i class="fas fa-graduation-cap"></i>
                            </span>
                            <input 
                                type="text" 
                                name="nombre_curso" 
                                class="form-control border-light bg-light shadow-none"
                                placeholder="Buscar por nombre del curso..."
                                value="{{ request('nombre_curso') }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1 shadow-xs">
                                <i class="fas fa-filter mr-1"></i> Filtrar
                            </button>
                            <a href="{{ route('admin.formaciones.vencimientos') }}" class="btn btn-light border flex-grow-1 shadow-xs">
                                <i class="fas fa-undo mr-1"></i> Limpiar
                            </a>
                        </div>
                    </div>              
                </div>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="bg-light">
                        <tr class="text-muted small text-uppercase font-weight-bold">
                            <th class="ps-4">Empleado</th>
                            <th>Curso</th>
                            <th>Fecha de Vencimiento</th>   
                            <th>Documento</th>
                            <th class="text-center">Días Restantes</th>
                            <th>Estado Alerta</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>                                               
                    </thead>
                    <tbody>
                        @forelse($formaciones as $f)
                        @php
                            $diasRestantes = $f->fecha_fin ? intval($hoy->diffInDays($f->fecha_fin, false)) : null;
                            $alertClass = '';
                            $alertLabel = '';
                            $icon = 'fa-check-circle';

                            if ($diasRestantes <= 0) {
                                $alertClass = 'tag-danger';
                                $alertLabel = 'VENCIDO HOY';
                                if ($diasRestantes < 0) $alertLabel = 'VENCIDO';
                                $icon = 'fa-exclamation-circle';
                            } elseif ($diasRestantes <= 30) {
                                $alertClass = 'tag-warning';
                                $alertLabel = 'PRÓXIMO A VENCER';
                                $icon = 'fa-clock';
                            } else {
                                $alertClass = 'tag-info';
                                $alertLabel = 'VIGENTE';
                                $icon = 'fa-check';
                            }
                        @endphp



                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $f->empleado->persona->nombres }} {{ $f->empleado->persona->apellidos }}</div>
                                <small class="text-muted small">{{ $f->empleado->cargo }}</small>
                            </td>
                            <td>
                                <span class="fw-bold text-primary">{{ $f->nombre_curso }}</span>
                            </td>
                            <td>
                                <div class="fw-bold" style="color: #475569;">
                                    <i class="far fa-calendar-alt me-1 text-muted"></i>
                                    {{ $f->fecha_fin ? $f->fecha_fin->format('d M, Y') : 'N/A' }}
                                </div>
                            </td>
                            <td>
                                @if($f->documentos->count() > 0)
                                    @foreach($f->documentos as $doc)
                                        <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" 
                                           class="btn btn-sm btn-outline-danger px-3 rounded-pill" style="font-size: 0.75rem;">
                                            <i class="fas fa-file-pdf me-1"></i> Ver PDF
                                        </a>
                                    @endforeach
                                @else
                                    <span class="text-muted small fst-italic">Sin adjunto</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="fw-bold" style="font-size: 1.1rem;">
                                    {{ $diasRestantes ?? '-' }}
                                </span>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">Días</small>
                            </td>
                            <td>
                                <span class="modern-tag-v2 {{ $alertClass }}">
                                    <i class="fas {{ $icon }} me-1"></i> {{ $alertLabel }}
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.formaciones.edit', $f) }}"
                                       class="btn btn-sm btn-icon-soft"
                                       data-toggle="tooltip" title="Editar registro">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.formaciones.destroy', $f) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon-soft text-danger"
                                                onclick="return confirm('¿Está seguro de desactivar esta formación?');"
                                                data-toggle="tooltip" title="Desactivar">
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
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background-color: #f1f5f9;">
                                        <i class="fas fa-graduation-cap fa-2x" style="color: #cbd5e1;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1">No hay vencimientos registrados</h5>
                                    <p class="mb-0">Todos los cursos actuales tienen vigencia permanente o no hay datos.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($formaciones->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4">
            {{ $formaciones->links() }}
        </div>
        @endif
    </div>
</div>
@stop

@section('css')
<style>
    .btn-orange { background-color: #f97316; color: #fff; border: none; }
    .btn-orange:hover { background-color: #ea580c; color: #fff; }

    .btn-icon-soft {
        width: 34px; height: 34px; border-radius: 8px;
        background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;
        display: inline-flex; align-items: center; justify-content: center;
        transition: all 0.2s;
    }
    .btn-icon-soft:hover { background: #eef2ff; color: #4f46e5; border-color: #c7d2fe; transform: translateY(-2px); }

    .modern-tag-v2 {
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    .tag-danger { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    .tag-warning { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
    .tag-info { background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }

    .table thead th { border-top: none; }
</style>
@stop

@section('js')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@stop
