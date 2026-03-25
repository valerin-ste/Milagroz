@extends('adminlte::page')

@section('title', 'Gestión de Formación')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">Formación Continua</h2>
        <p class="text-muted mb-0">Listado de capacitaciones, cursos y certificados del personal.</p>
    </div>
    <a href="{{ route('admin.formaciones.create') }}" class="btn btn-orange shadow-sm text-white">
        <i class="fas fa-plus me-2"></i> Nueva Formación
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

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-light">
            <form method="GET" action="{{ route('admin.formaciones.index') }}" class="row g-3 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-light text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="buscar" class="form-control border-light bg-light shadow-none" placeholder="Buscar por empleado o curso..." value="{{ request('buscar') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info text-white w-100">Filtrar</button>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Empleado</th>
                            <th>Curso / Formación</th>
                            <th>Institución</th>
                            <th>Fechas</th>
                            <th>Certificados</th>
                            <th>Estado</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($formaciones as $f)
                        <tr @if($f->estado == 0) style="background-color: #f8fafc; opacity: 0.8;" @endif>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $f->empleado->persona->nombres }} {{ $f->empleado->persona->apellidos }}</div>
                                <small class="text-muted">{{ $f->empleado->cargo }}</small>
                            </td>
                            <td><span class="fw-bold text-primary">{{ $f->nombre_curso }}</span></td>
                            <td>{{ $f->institucion }}</td>
                            <td>
                                <small class="text-muted d-block">Desde: {{ $f->fecha_inicio->format('d/m/Y') }}</small>
                                <small class="text-muted d-block">Hasta: {{ $f->fecha_fin ? $f->fecha_fin->format('d/m/Y') : 'En curso' }}</small>
                            </td>
                            <td>
                                @if($f->documentos->count() > 0)
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($f->documentos as $doc)
                                            <a href="{{ Storage::url($doc->ruta) }}" target="_blank" class="btn btn-sm btn-light-custom text-truncate" style="max-width: 160px; font-size: 0.8rem;" title="{{ $doc->nombre_original }}">
                                                <i class="fas fa-file-pdf text-danger me-1"></i> {{ $doc->nombre_original }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic">Sin archivos</span>
                                @endif
                            </td>
                            <td>
                                @if($f->estado == 1)
                                    <span class="badge bg-success px-3 py-1 rounded-pill" style="font-size: 0.8rem; font-weight: 600;">Activo</span>
                                @else
                                    <span class="badge bg-danger px-3 py-1 rounded-pill" style="font-size: 0.8rem; font-weight: 600;">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    @if($f->estado == 1)
                                        <a href="{{ route('admin.formaciones.edit', $f) }}" class="btn btn-sm btn-light-custom px-3" title="Editar">
                                            <i class="fas fa-pen text-muted"></i>
                                        </a>
                                        <form action="{{ route('admin.formaciones.destroy', $f) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light-custom px-3 text-danger" onclick="return confirm('¿Desactivar formación?');">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.formaciones.toggle', $f->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light-custom px-3 text-success">
                                                <i class="fas fa-check-circle"></i> Activar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted fst-italic">No se encontraron formaciones registradas.</td>
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
@endsection

@section('css')
<style>
    .btn-orange { background-color: #f97316; color: #fff; border: none; }
    .btn-orange:hover { background-color: #ea580c; color: #fff; }

    .btn-light-custom { background-color: #f9fafb; border: 1px solid #e5e7eb; color: #374151; }
    .btn-light-custom:hover { background-color: #f3f4f6; color: #111827; }

    .badge-success { background-color: #dcfce7; color: #166534; }
    .badge-danger { background-color: #fee2e2; color: #991b1b; }

    .hover-underline:hover { text-decoration: underline !important; }
</style>
@endsection