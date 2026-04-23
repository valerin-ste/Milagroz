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
                    <div class="row g-4 mb-4">
                        {{-- NOMBRE DEL CURSO --}}
                        <div class="col-md-8 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Nombre del Curso / Formación <span class="text-danger">*</span></label>
                            <input type="text" name="nombre_curso" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('nombre_curso') is-invalid @enderror" 
                                   value="{{ old('nombre_curso', $formacion->nombre_curso) }}" required>
                        </div>

                        {{-- TIPO DE FORMACIÓN (VENCE) --}}
                        <div class="col-md-4 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">¿Vence? <span class="text-danger">*</span></label>
                            <select name="vence" id="vence" class="form-control border-light bg-light py-2 px-3 shadow-none" required>
                                <option value="1" {{ old('vence', $formacion->vence) == '1' ? 'selected' : '' }}>Sí, vence</option>
                                <option value="0" {{ old('vence', $formacion->vence) == '0' ? 'selected' : '' }}>No, es permanente</option>
                            </select>
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
                        <div class="col-md-6 text-start" id="container_fecha_fin">
                            <label for="fecha_fin" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">Fecha Fin <span class="text-danger">*</span></label>
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

                        <div class="list-group mb-3">
                            @forelse($formacion->documentos as $doc)
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
                                <div class="text-muted small">Sin certificados previos.</div>
                            @endforelse
                        </div>

                        {{-- OCULTOS PARA ELIMINACIÓN --}}
                        <div id="hiddenDeleteInputs"></div>
                    </div>

                    {{-- SUBIR NUEVO DOCUMENTO --}}
                    <div class="mb-4 text-start">
                        <label for="documento" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                            Actualizar Soporte / Diploma (PDF)
                        </label>

                        <div class="p-3 border rounded border-dashed text-center bg-light-soft">
                            <input type="file" name="documento" id="documento" accept=".pdf"
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('documento') is-invalid @enderror">
                            
                            <small class="text-muted mt-2 d-block">
                                Seleccione un nuevo archivo PDF si desea reemplazar o añadir un soporte (Máx. 2MB).
                            </small>
                        </div>
                        @error('documento')
                            <div class="invalid-feedback text-danger small d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-orange text-white py-3 fw-bold rounded shadow-sm ripple">
                            <i class="fas fa-save me-2"></i> ACTUALIZAR FORMACIÓN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
function removeExistingDoc(id) {
    if (!confirm('¿Eliminar este certificado?')) return;

    const element = document.getElementById('doc-' + id);
    if (element) element.remove();

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'eliminar_documentos[]';
    input.value = id;

    document.getElementById('hiddenDeleteInputs').appendChild(input);
}

// =========================
// 🛡️ LÓGICA VENCIMIENTO
// =========================
document.addEventListener('DOMContentLoaded', function() {
    const selectVence = document.getElementById('vence');
    const containerFechaFin = document.getElementById('container_fecha_fin');
    const inputFechaFin = document.getElementById('fecha_fin');
 
    function toggleVence() {
        if (selectVence.value == '1') {
            containerFechaFin.style.display = 'block';
            inputFechaFin.required = true;
        } else {
            containerFechaFin.style.display = 'none';
            inputFechaFin.required = false;
            inputFechaFin.value = '';
        }
    }
 
    selectVence.addEventListener('change', toggleVence);
    toggleVence(); // Inicializar

    // ==========================================
    // 🛡️ VALIDACIÓN DE DOCUMENTO OBLIGATORIO
    // ==========================================
    const form = selectVence.closest('form');
    form.addEventListener('submit', function(e) {
        const existingDocs = document.querySelectorAll('[id^="doc-"]').length;
        const newDoc = document.getElementById('documento').value;

        if (existingDocs === 0 && !newDoc) {
            e.preventDefault();
            alert('El registro de formación debe tener al menos un documento de soporte (PDF).');
        }
    });
});
</script>
@stop
@section('css')
<style>
.bg-light-soft { background-color: #f8fafc; }
.border-dashed { border-style: dashed !important; border-width: 2px !important; border-color: #cbd5e1 !important; }
.ripple { position: relative; overflow: hidden; }
</style>
@stop
@endsection
