@extends('adminlte::page')

@section('title', 'Nueva Evaluación de Desempeño')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">Nueva Evaluación</h2>
        <p class="text-muted mb-0">Registre la calificación actual del empleado.</p>
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
                <form action="{{ route('admin.evaluaciones_desempeno.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- EMPLEADO --}}
                    <div class="mb-4">
                        <label for="empleado_id" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Empleado <span class="text-danger">*</span></label>
                        <select name="empleado_id" id="empleado_id" class="form-control select2 border-light bg-light py-2 px-3 shadow-none @error('empleado_id') is-invalid @enderror" required>
                            <option value="">-- Seleccionar Empleado --</option>
                            @foreach($empleados as $e)
                                <option value="{{ $e->id }}" {{ old('empleado_id') == $e->id ? 'selected' : '' }}>
                                    {{ $e->persona->nombres }} {{ $e->persona->apellidos }} ({{ $e->cargo }})
                                </option>
                            @endforeach
                        </select>
                        @error('empleado_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-4">
                        {{-- CALIFICACIÓN --}}
                        <div class="col-md-6 mb-4">
                            <label for="calificacion" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Calificación (1-10) <span class="text-danger">*</span></label>
                            <input type="number" name="calificacion" id="calificacion" min="1" max="10" step="1" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('calificacion') is-invalid @enderror" 
                                   value="{{ old('calificacion') }}" required>
                            @error('calificacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- FECHA --}}
                        <div class="col-md-6 mb-4">
                            <label for="fecha" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Fecha de Evaluación <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" id="fecha" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('fecha') is-invalid @enderror" 
                                   value="{{ old('fecha', date('Y-m-d')) }}" required>
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- OBSERVACIONES --}}
                    <div class="mb-4">
                        <label for="observaciones" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Observaciones / Comentarios</label>
                        <textarea name="observaciones" id="observaciones" rows="4" 
                                  class="form-control border-light bg-light py-2 px-3 shadow-none @error('observaciones') is-invalid @enderror" 
                                  placeholder="Detalle el rendimiento del empleado...">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ARCHIVOS --}}
                    <div class="mb-4">
                        <label for="archivos" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Soportes / Documentos <span class="text-muted">(Opcional)</span></label>
                        <input type="file" name="archivos[]" id="archivos" multiple 
                               class="form-control border-light bg-light py-2 px-3 shadow-none @error('archivos') is-invalid @enderror">
                        <small class="text-muted mt-1 d-block"><i class="fas fa-info-circle me-1"></i> Puede seleccionar múltiples archivos (PDF, Imágenes, Word).</small>
                        @error('archivos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-orange text-white py-3 fw-bold rounded shadow-sm ripple">
                            <i class="fas fa-save me-2"></i> GUARDAR EVALUACIÓN
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
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: "-- Seleccionar Empleado --",
            allowClear: true
        });
    });
</script>
@stop
