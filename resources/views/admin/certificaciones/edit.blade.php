@extends('adminlte::page')

@section('title', 'Editar Certificación')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">Editar Certificación</h2>
        <p class="text-muted mb-0">Actualice la información de la certificación oficial.</p>
    </div>
    <a href="{{ route('admin.certificaciones.index') }}" class="btn btn-light border px-4 shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Volver
    </a>
</div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-body p-5">
                <form action="{{ route('admin.certificaciones.update', $certificacion) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- EMPLEADO (SOLO LECTURA EN EDICIÓN) --}}
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                            Empleado
                        </label>
                        <input type="text" class="form-control border-light bg-light py-2 px-3 shadow-none" 
                               value="{{ $certificacion->empleado->persona->nombres }} {{ $certificacion->empleado->persona->apellidos }}" readonly>
                    </div>

                    <div class="row g-4 mb-4">
                        {{-- NOMBRE DE LA CERTIFICACION --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Nombre de la Certificación <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nombre_certificacion" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('nombre_certificacion') is-invalid @enderror" 
                                   placeholder="Ej: Certificación OSHAS..."
                                   value="{{ old('nombre_certificacion', $certificacion->nombre_certificacion) }}" required>
                            @error('nombre_certificacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TIPO DE CERTIFICACION --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Tipo de Certificación
                            </label>
                            <input type="text" name="tipo_certificacion" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('tipo_certificacion') is-invalid @enderror" 
                                   placeholder="Ej: Técnica, Profesional..."
                                   value="{{ old('tipo_certificacion', $certificacion->tipo_certificacion) }}">
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        {{-- INSTITUCIÓN --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Institución Emisora <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="institucion" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('institucion') is-invalid @enderror" 
                                   placeholder="Ej: SENA, ICONTEC..."
                                   value="{{ old('institucion', $certificacion->institucion) }}" required>
                        </div>

                        {{-- CODIGO --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Código del Certificado
                            </label>
                            <input type="text" name="codigo_certificado" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('codigo_certificado') is-invalid @enderror" 
                                   placeholder="Ej: CERT-12345..."
                                   value="{{ old('codigo_certificado', $certificacion->codigo_certificado) }}">
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        {{-- FECHA EXPEDICIÓN --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Fecha de Expedición <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="fecha_expedicion"
                                   class="form-control border-light bg-light py-2 px-3 shadow-none"
                                   value="{{ old('fecha_expedicion', $certificacion->fecha_expedicion->format('Y-m-d')) }}" required>
                        </div>

                        {{-- FECHA VENCIMIENTO --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Fecha de Vencimiento (Opcional)
                            </label>
                            <input type="date" name="fecha_vencimiento"
                                   class="form-control border-light bg-light py-2 px-3 shadow-none"
                                   value="{{ old('fecha_vencimiento', $certificacion->fecha_vencimiento ? $certificacion->fecha_vencimiento->format('Y-m-d') : '') }}">
                        </div>
                    </div>

                    {{-- OBSERVACIONES --}}
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                            Observaciones (Opcional)
                        </label>
                        <textarea name="observaciones" rows="3" 
                                  class="form-control border-light bg-light py-2 px-3 shadow-none" 
                                  placeholder="Detalles adicionales...">{{ old('observaciones', $certificacion->observaciones) }}</textarea>
                    </div>

                    {{-- ARCHIVOS ACTUALES --}}
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                            Certificados actuales
                        </label>

                        <div class="list-group mb-3">
                            @forelse($certificacion->documentos as $doc)
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
                        <label for="archivo" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                            Actualizar Soporte / Diploma (PDF, JPG, PNG)
                        </label>

                        <div class="p-3 border rounded border-dashed text-center bg-light-soft">
                            <input type="file" name="archivo" id="archivo" accept=".pdf,.jpg,.jpeg,.png"
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('archivo') is-invalid @enderror">
                            
                            <small class="text-muted mt-2 d-block">
                                Seleccione un nuevo archivo si desea reemplazar o añadir un soporte (Máx. 5MB).
                            </small>
                        </div>
                        @error('archivo')
                            <div class="invalid-feedback text-danger small d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-orange text-white py-3 fw-bold rounded shadow-sm">
                            <i class="fas fa-sync-alt me-2"></i> ACTUALIZAR CERTIFICACIÓN
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
    if (!confirm('¿Eliminar este certificado?')) return;

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

@section('css')
<style>
.bg-light-soft { background-color: #f8fafc; }
.border-dashed { border-style: dashed !important; border-width: 2px !important; border-color: #cbd5e1 !important; }
.btn-orange {
    background-color: #ff6a00;
    border: none;
    color: #fff;
}
.btn-orange:hover {
    background-color: #e65c00;
    color: #fff;
}
</style>
@endsection
