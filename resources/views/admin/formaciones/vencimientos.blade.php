@extends('adminlte::page')

@section('title', 'Vencimientos de Formación')

@section('content_header')
<div class="page-header d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="page-title fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Cursos con Vencimiento
        </h2>
        <p class="page-subtitle text-muted mb-0">
            Gestión específica de capacitaciones que requieren renovación periódica.
        </p>
    </div>

    <div class="page-actions">
        <a href="{{ route('admin.formaciones.create') }}" class="btn btn-orange">
            <i class="fas fa-plus me-2"></i> Registrar Vencimiento
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

    {{-- ── FILTROS ── --}}
    <form method="GET" action="{{ route('admin.formaciones.vencimientos') }}" class="mb-4">
        <div class="card shadow-sm border-0 rounded-4" style="background-color: #f8fafc;">
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    
                    {{-- CAMPO: BUSCAR EMPLEADO --}}
                    <div class="col-md-4">
                        <label class="text-muted small fw-bold mb-2 ps-1">Buscar Empleado</label>
                        <div class="d-flex align-items-center bg-white" style="border-radius: 30px; height: 45px; padding: 0 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="fas fa-user text-muted" style="font-size: 14px;"></i>
                            <input type="text" name="buscar" class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                   placeholder="Nombre o documento..." value="{{ request('buscar') }}" style="outline: none;">
                        </div>
                    </div>

                    {{-- CAMPO: NOMBRE DEL CURSO --}}
                    <div class="col-md-4">
                        <label class="text-muted small fw-bold mb-2 ps-1">Nombre del Curso</label>
                        <div class="d-flex align-items-center bg-white" style="border-radius: 30px; height: 45px; padding: 0 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="fas fa-graduation-cap text-muted" style="font-size: 14px;"></i>
                            <input type="text" name="nombre_curso" class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                   placeholder="Buscar por nombre del curso..." value="{{ request('nombre_curso') }}" style="outline: none;">
                        </div>
                    </div>

                    {{-- BOTONES --}}
                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-orange flex-grow-1 fw-bold d-flex justify-content-center align-items-center" style="border-radius: 30px; height: 45px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                <i class="fas fa-filter me-2"></i> Filtrar
                            </button>
                            <a href="{{ route('admin.formaciones.vencimientos') }}" class="btn bg-white border flex-grow-1 fw-bold text-secondary d-flex justify-content-center align-items-center" style="border-radius: 30px; height: 45px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
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
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Curso</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Vencimiento</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Documentos</th>
                            <th class="text-center py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Días</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Estado</th>
                            <th class="text-center pe-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Acciones</th>
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

                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            
                            {{-- EMPLEADO --}}
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center" style="gap: 1rem;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 45px; height: 45px; background-color: rgba(19,182,236,0.1); color: var(--primary-blue);">
                                        <span class="fw-bold" style="font-size: 1.1rem;">
                                            {{ strtoupper(substr($f->empleado->persona->nombres ?? 'U', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                            {{ $f->empleado->persona->nombres ?? '' }} {{ $f->empleado->persona->apellidos ?? '' }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.85rem;">
                                            {{ $f->empleado->cargo }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- CURSO --}}
                            <td class="py-3">
                                <span class="d-block" style="color: #334155; font-weight: 500;">
                                    {{ $f->nombre_curso }}
                                </span>
                                <span class="d-block text-muted small mt-1">
                                    <i class="fas fa-university me-1"></i> {{ $f->institucion }}
                                </span>
                            </td>

                            {{-- FECHA VENCIMIENTO --}}
                            <td class="py-3">
                                <div class="fw-bold" style="color: #475569; font-size: 0.9rem;">
                                    📅 {{ $f->fecha_fin ? $f->fecha_fin->format('d M, Y') : 'N/A' }}
                                </div>
                            </td>

                            {{-- ARCHIVOS --}}
                            <td class="py-3">
                                @if($f->documentos->count() > 0)
                                    <button class="btn btn-outline-secondary btn-sm"
                                            data-toggle="modal"
                                            data-target="#docsModal{{ $f->id }}">
                                        📂 Ver documentos ({{ $f->documentos->count() }})
                                    </button>
                                @else
                                    <span class="text-muted small fst-italic"><i class="fas fa-file-slash me-1"></i> Sin archivos</span>
                                @endif
                            </td>

                            {{-- DIAS RESTANTES --}}
                            <td class="text-center py-3">
                                <span class="fw-bold" style="font-size: 1.1rem; color: #334155;">
                                    {{ $diasRestantes ?? '-' }}
                                </span>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">Días</small>
                            </td>

                            {{-- ESTADO --}}
                            <td class="py-3">
                                <span class="modern-tag-v2 {{ $alertClass }}">
                                    <i class="fas {{ $icon }} me-1"></i> {{ $alertLabel }}
                                </span>
                            </td>

                            {{-- ACCIONES --}}
                            <td class="text-center pe-4 py-3">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.formaciones.edit', $f) }}"
                                       class="btn btn-sm btn-icon btn-outline-primary"
                                       data-toggle="tooltip" data-placement="top" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    <form action="{{ route('admin.formaciones.destroy', $f) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-icon btn-outline-danger"
                                                data-toggle="tooltip" data-placement="top" title="Desactivar"
                                                onclick="return confirm('¿Está seguro de desactivar esta formación?');">
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
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background-color: rgba(19,182,236,0.1);">
                                        <i class="fas fa-graduation-cap fa-2x" style="color: var(--primary-blue, #13b6ec);"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1" style="color: #64748b;">No hay vencimientos registrados</h5>
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
        <div class="card-footer bg-white border-top border-light py-3 px-4 rounded-bottom-4">
            {{ $formaciones->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODALES DE DOCUMENTOS --}}
@foreach($formaciones as $f)
    <div class="modal fade" id="docsModal{{ $f->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4">

                <div class="modal-header">
                    <h5 class="modal-title">Certificados / Documentos</h5>
                    <button type="button" class="btn-close" data-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <ul class="list-group">
                        @forelse($f->documentos as $archivo)
                        <li class="list-group-item d-flex justify-content-between align-items-center">

                            <span class="text-truncate" style="max-width: 200px;">
                                📄 {{ $archivo->nombre_original }}
                            </span>

                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.documentos.view', $archivo->id) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    👁️
                                </a>

                                <a href="{{ route('admin.documentos.download', $archivo->id) }}"
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

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip({ 
            placement: 'top', 
            trigger: 'hover',
            boundary: 'window' 
        });
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

</style>
@endsection
