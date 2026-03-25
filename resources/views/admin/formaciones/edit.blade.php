@extends('adminlte::page')

@section('title', 'Editar Formación')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">Editar Registro de Formación</h2>
        <p class="text-muted mb-0">Actualice la información del curso o certificación.</p>
    </div>
    <a href="{{ route('admin.formaciones.index') }}" class="btn btn-light border px-4 shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Volver
    </a>
</div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
            <div class="card-body p-5">
                <form action="{{ route('admin.formaciones.update', $formacion->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- EMPLEADO --}}
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Empleado</label>
                        <input type="text" class="form-control border-light bg-light py-2 px-3 shadow-none text-muted" 
                               value="{{ $formacion->empleado->persona->nombres }} {{ $formacion->empleado->persona->apellidos }}" disabled>
                    </div>

                    <div class="row g-4">
                        {{-- CURSO --}}
                        <div class="col-md-6 mb-4 text-start">
                            <label for="nombre_curso" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Nombre del Curso <span class="text-danger">*</span></label>
                            <input type="text" name="nombre_curso" id="nombre_curso" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('nombre_curso') is-invalid @enderror" 
                                   value="{{ old('nombre_curso', $formacion->nombre_curso) }}" required>
                            @error('nombre_curso')
                                <div class="invalid-feedback text-danger small d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- INSTITUCION --}}
                        <div class="col-md-6 mb-4 text-start">
                            <label for="institucion" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Institución <span class="text-danger">*</span></label>
                            <input type="text" name="institucion" id="institucion" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('institucion') is-invalid @enderror" 
                                   value="{{ old('institucion', $formacion->institucion) }}" required>
                            @error('institucion')
                                <div class="invalid-feedback text-danger small d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        {{-- FECHA INICIO --}}
                        <div class="col-md-6 text-start">
                            <label for="fecha_inicio" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('fecha_inicio') is-invalid @enderror" 
                                   value="{{ old('fecha_inicio', $formacion->fecha_inicio->format('Y-m-d')) }}">
                        </div>

                        {{-- FECHA FIN --}}
                        <div class="col-md-6 text-start">
                            <label for="fecha_fin" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Fecha Fin</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('fecha_fin') is-invalid @enderror" 
                                   value="{{ $formacion->fecha_fin ? $formacion->fecha_fin->format('Y-m-d') : '' }}">
                        </div>
                    </div>

                    {{-- ARCHIVOS ACTUALES --}}
                    <div class="mb-4 text-start">
    <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
        Certificados actuales
    </label>

    <div class="list-group">
        @forelse($formacion->documentos as $doc)
            <div class="list-group-item d-flex justify-content-between align-items-center">

                {{-- NOMBRE DEL ARCHIVO (CLICK PARA VER) --}}
                <a href="{{ Storage::url($doc->ruta) }}" target="_blank" 
                   class="text-decoration-none fw-semibold text-dark">
                    📄 {{ $doc->nombre_original }}
                </a>

                {{-- BOTÓN ELIMINAR --}}
                <form action="{{ route('admin.documentos.destroy', $doc->id) }}" 
                      method="POST" 
                      onsubmit="return confirm('¿Eliminar este certificado?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>

            </div>
        @empty
            <div class="text-muted small">Sin certificados previos.</div>
        @endforelse
    </div>
</div>

{{-- SUBIR MÁS ARCHIVOS --}}
<div class="mb-4 text-start">
    <label for="archivos" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
        Cargar más certificados
    </label>

    <input type="file" name="archivos[]" id="archivos" multiple 
           class="form-control border-light bg-light py-2 px-3 shadow-none @error('archivos') is-invalid @enderror">

    <small class="text-muted mt-2 d-block">
        Seleccione los archivos adicionales que desea vincular a esta formación.
    </small>
</div>

<div class="d-grid mt-5">
    <button type="submit" class="btn btn-orange text-white py-3 fw-bold rounded shadow-sm ripple">
        <i class="fas fa-save me-2"></i> ACTUALIZAR INFORMACIÓN FACTURACIÓN
    </button>
</div>
@endsection
