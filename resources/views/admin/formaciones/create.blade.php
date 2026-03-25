@extends('adminlte::page')

@section('title', 'Nueva Formación')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">Registrar Formación</h2>
        <p class="text-muted mb-0">Añada una nueva certificación o curso al perfil del empleado.</p>
    </div>
    <a href="{{ route('admin.formaciones.index') }}" class="btn btn-light border px-4 shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Volver
    </a>
</div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-body p-5">
                <form action="{{ route('admin.formaciones.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- EMPLEADO --}}
                    <div class="mb-4 text-start">
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
                        {{-- CURSO --}}
                        <div class="col-md-6 mb-4 text-start">
                            <label for="nombre_curso" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Nombre del Curso / Formación <span class="text-danger">*</span></label>
                            <input type="text" name="nombre_curso" id="nombre_curso" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('nombre_curso') is-invalid @enderror" 
                                   value="{{ old('nombre_curso') }}" placeholder="Ej: Diplomado en Salud Ocupacional" required>
                            @error('nombre_curso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- INSTITUCION --}}
                        <div class="col-md-6 mb-4 text-start">
                            <label for="institucion" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Institución / Entidad <span class="text-danger">*</span></label>
                            <input type="text" name="institucion" id="institucion" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('institucion') is-invalid @enderror" 
                                   value="{{ old('institucion') }}" placeholder="Ej: Universidad Nacional" required>
                            @error('institucion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-4">
                        {{-- FECHA INICIO --}}
                        <div class="col-md-6 mb-4 text-start">
                            <label for="fecha_inicio" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Fecha Inicio <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('fecha_inicio') is-invalid @enderror" 
                                   value="{{ old('fecha_inicio', date('Y-m-d')) }}" required>
                            @error('fecha_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- FECHA FIN --}}
                        <div class="col-md-6 mb-4 text-start">
                            <label for="fecha_fin" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Fecha Fin <span class="text-muted">(Opcional)</span></label>
                            <input type="date" name="fecha_fin" id="fecha_fin" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('fecha_fin') is-invalid @enderror" 
                                   value="{{ old('fecha_fin') }}">
                            @error('fecha_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- CARGA DE ARCHIVOS --}}
                    <div class="mb-4 text-start">
                        <label for="archivos" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Soportes / Diplomas <i class="fas fa-info-circle ms-1 text-info" title="Puede subir más de un archivo a la vez"></i></label>
                        <div class="p-3 border rounded border-dashed text-center bg-light-soft">
                             <input type="file" name="archivos[]" id="archivos" multiple 
                                    class="form-control border-light bg-light py-2 px-3 shadow-none @error('archivos') is-invalid @enderror">
                             <small class="text-muted mt-2 d-block">Seleccione uno o varios archivos para cargar. (Formato PDF, Imágenes).</small>
                        </div>
                        @error('archivos')
                            <div class="invalid-feedback text-danger d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-orange text-white py-3 fw-bold rounded shadow-sm ripple">
                            <i class="fas fa-save me-2"></i> GUARDAR REGISTRO DE FORMACIÓN
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
        $('.select2').select2({ theme: 'bootstrap4', placeholder: "-- Seleccionar --", allowClear: true });
    });
</script>
@stop

@section('css')
<style>
    .bg-light-soft { background-color: #f8fafc; }
    .border-dashed { border-style: dashed !important; border-width: 2px !important; border-color: #cbd5e1 !important; }
</style>
@endsection
