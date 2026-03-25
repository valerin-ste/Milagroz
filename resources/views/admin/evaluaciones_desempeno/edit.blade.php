@extends('adminlte::page')

@section('title', 'Editar Evaluación')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">Editar Evaluación</h2>
        <p class="text-muted mb-0">Actualice la calificación del empleado.</p>
    </div>
    <a href="{{ route('admin.evaluaciones_desempeno.index') }}" class="btn btn-light-custom px-4 border shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Volver
    </a>
</div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-body p-5">
                <form action="{{ route('admin.evaluaciones_desempeno.update', $evaluacion->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Empleado</label>
                        <input type="text" class="form-control border-light bg-light py-2 px-3 shadow-none text-muted" value="{{ $evaluacion->empleado->persona->nombres }} {{ $evaluacion->empleado->persona->apellidos }}" disabled>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6 mb-4">
                            <label for="calificacion" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Calificación (1-10) <span class="text-danger">*</span></label>
                            <input type="number" name="calificacion" id="calificacion" min="1" max="10" step="1" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('calificacion') is-invalid @enderror" 
                                   value="{{ old('calificacion', $evaluacion->calificacion) }}" required>
                            @error('calificacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="fecha" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Fecha de Evaluación <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" id="fecha" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('fecha') is-invalid @enderror" 
                                   value="{{ old('fecha', $evaluacion->fecha->format('Y-m-d')) }}" required>
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
    <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
        Archivos actuales
    </label>

    <div class="list-group">
        @forelse($evaluacion->documentos as $doc)
            <div class="list-group-item d-flex justify-content-between align-items-center">

                {{-- NOMBRE DEL ARCHIVO (CLICK PARA VER) --}}
                <a href="{{ Storage::url($doc->ruta) }}" target="_blank" 
                   class="text-decoration-none fw-semibold text-dark">
                    📄 {{ $doc->nombre_original }}
                </a>

                {{-- BOTÓN ELIMINAR --}}
                <form action="{{ route('admin.documentos.destroy', $doc->id) }}" 
                      method="POST" 
                      onsubmit="return confirm('¿Eliminar este archivo permanentemente?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>

            </div>
        @empty
            <div class="text-muted small">No hay archivos previos.</div>
        @endforelse
    </div>
</div>

                    <div class="mb-4">
                        <label for="archivos" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Subir más archivos</label>
                        <input type="file" name="archivos[]" id="archivos" multiple 
                               class="form-control border-light bg-light py-2 px-3 shadow-none @error('archivos') is-invalid @enderror">
                        @error('archivos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-orange text-white py-3 fw-bold rounded shadow-sm ripple">
                            <i class="fas fa-save me-2"></i> ACTUALIZAR EVALUACIÓN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
