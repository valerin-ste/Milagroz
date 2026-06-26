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
                        {{-- FECHA --}}
                        <div class="col-md-6 mb-4">
                            <label for="fecha" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Fecha de Evaluación <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" id="fecha" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('fecha') is-invalid @enderror" 
                                   value="{{ old('fecha', $evaluacion->fecha->format('Y-m-d')) }}" required>
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ESTADO --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Estado <span class="text-danger">*</span>
                            </label>
                            <select name="estado" class="form-control border-light bg-light py-2 px-3 shadow-none" required>
                                <option value="1" {{ old('estado', $evaluacion->estado) == 1 ? 'selected' : '' }}>Pendiente</option>
                                <option value="2" {{ old('estado', $evaluacion->estado) == 2 ? 'selected' : '' }}>En proceso</option>
                                <option value="3" {{ old('estado', $evaluacion->estado) == 3 ? 'selected' : '' }}>Finalizada</option>
                            </select>
                        </div>
                    </div>

                    {{-- OBSERVACIONES --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                            Observaciones / Comentarios
                        </label>
                        <textarea name="observaciones" rows="4"
                                  class="form-control border-light bg-light py-2 px-3 shadow-none"
                                  placeholder="Detalle el rendimiento del empleado...">{{ old('observaciones', $evaluacion->observaciones) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                            Archivos actuales
                        </label>

                        <div class="list-group mb-3">
                            @forelse($evaluacion->documentos as $doc)
                                <div id="doc-{{ $doc->id }}" class="list-group-item d-flex justify-content-between align-items-center">

                                    {{-- NOMBRE DEL ARCHIVO (CLICK PARA VER) --}}
                                    <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" 
                                       class="text-decoration-none fw-semibold text-dark">
                                        📄 {{ $doc->nombre_original }}
                                    </a>

                                    {{-- BOTÓN ELIMINAR --}}
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeExistingDoc({{ $doc->id }})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            @empty
                                <div class="text-muted small">No hay archivos previos.</div>
                            @endforelse
                        </div>

                        {{-- OCULTOS PARA ELIMINACIÓN --}}
                        <div id="hiddenDeleteInputs"></div>
                    </div>

                    <div class="mb-4">
                        <label for="archivos" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Subir más archivos</label>
                        <input type="file" name="archivos[]" id="archivos" multiple 
                               class="form-control border-light bg-light py-2 px-3 shadow-none @error('archivos') is-invalid @enderror">
                        @error('archivos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-5 mb-4">
                        <a href="{{ route('admin.evaluaciones_desempeno.index') }}"
                        class="btn btn-light border px-4 fw-bold shadow-sm"
                        style="border-radius:15px; font-size:1.1rem; letter-spacing:0.5px;">
                            <i class="fas fa-times me-2"></i> CANCELAR
                        </a>

                        <button type="submit"
                                class="btn btn-orange text-white px-4 fw-bold shadow-sm"
                                style="border-radius:15px; font-size:1.1rem; letter-spacing:0.5px;">
                            <i class="fas fa-save me-2"></i> ACTUALIZAR EVALUACIÓN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
function removeExistingDoc(id) {
    if (!confirm('¿Eliminar este archivo?')) return;

    const element = document.getElementById('doc-' + id);
    if (element) element.remove();

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'eliminar_documentos[]';
    input.value = id;

    document.getElementById('hiddenDeleteInputs').appendChild(input);
}
</script>
@stop
