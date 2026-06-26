@extends('adminlte::page')

@section('title', 'Editar Dotación')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">Editar Dotación</h2>
        <p class="text-muted mb-0">Actualice la información de la dotación entregada.</p>
    </div>
    <a href="{{ route('admin.dotaciones.index') }}" class="btn btn-light border px-4 shadow-sm" style="border-radius: 10px;">
        <i class="fas fa-arrow-left me-2"></i> Volver
    </a>
</div>
@stop

@section('content')
<div class="row justify-content-center pb-5">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.dotaciones.update', $dotacion->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- EMPLEADO --}}
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                            Empleado
                        </label>
                        <div class="d-flex align-items-center bg-light p-3" style="border-radius: 12px; border: 1px solid #f1f5f9;">
                            <div class="rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <span class="d-block fw-bold text-dark">{{ $dotacion->empleado->persona->nombres }} {{ $dotacion->empleado->persona->apellidos }}</span>
                                <small class="text-muted">{{ $dotacion->empleado->persona->numero_documento }} | {{ $dotacion->empleado->cargo }}</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" style="opacity: 0.1;">

                    <div class="row g-4 mb-4">
                        {{-- TIPO DE DOTACIÓN --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                                Tipo de Dotación <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="tipo_dotacion" 
                                   class="form-control border-0 bg-light py-2 px-3 shadow-none @error('tipo_dotacion') is-invalid @enderror" 
                                   placeholder="Ej: Uniforme, Calzado..."
                                   value="{{ old('tipo_dotacion', $dotacion->tipo_dotacion) }}" 
                                   required 
                                   style="border-radius: 12px; height: 50px;">
                            @error('tipo_dotacion')
                                <div class="invalid-feedback ps-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TALLA --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" 
                                style="color: #64748b; letter-spacing: 0.5px;">
                                Talla / Medida
                            </label>

                            <input type="text"
                                name="talla"
                                class="form-control border-0 bg-light py-2 px-3 shadow-none @error('talla') is-invalid @enderror"
                                placeholder="Ej: M, 40..."
                                value="{{ old('talla', $dotacion->talla) }}"
                                style="border-radius: 12px; height: 50px;">

                            @error('talla')
                                <div class="invalid-feedback ps-1">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        {{-- CANTIDAD --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                                Cantidad Entregada <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="cantidad" 
                                   class="form-control border-0 bg-light py-2 px-3 shadow-none @error('cantidad') is-invalid @enderror" 
                                   min="1"
                                   value="{{ old('cantidad', $dotacion->cantidad) }}" 
                                   required
                                   style="border-radius: 12px; height: 50px;">
                        </div>

                        {{-- FECHA --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                                Fecha de Entrega <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="fecha"
                                   class="form-control border-0 bg-light py-2 px-3 shadow-none @error('fecha') is-invalid @enderror"
                                   value="{{ old('fecha', $dotacion->fecha->format('Y-m-d')) }}" 
                                   required
                                   style="border-radius: 12px; height: 50px;">
                        </div>
                    </div>

                    {{-- OBSERVACIONES --}}
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                            Observaciones (Opcional)
                        </label>
                        <textarea name="observaciones" rows="3" 
                                  class="form-control border-0 bg-light py-3 px-3 shadow-none" 
                                  placeholder="Detalles adicionales..."
                                  style="border-radius: 15px;">{{ old('observaciones', $dotacion->observaciones) }}</textarea>
                    </div>

                    {{-- ARCHIVOS ACTUALES --}}
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                            Soportes Actuales
                        </label>

                        <div class="list-group mb-3 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                            @forelse($dotacion->documentos as $doc)
                                <div id="doc-{{ $doc->id }}" class="list-group-item d-flex justify-content-between align-items-center border-light py-3 px-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf text-danger me-3 fa-lg"></i>
                                        <div>
                                            <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" 
                                               class="text-decoration-none fw-bold text-dark hover-orange">
                                                {{ $doc->nombre_original }}
                                            </a>
                                            <span class="d-block text-muted extra-small">Cargado el {{ $doc->created_at->format('d/m/Y') }}</span>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger border-0" onclick="removeExistingDoc({{ $doc->id }})" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            @empty
                                <div class="list-group-item text-muted text-center py-4 bg-light border-0">
                                    <i class="fas fa-info-circle me-2"></i> No hay soportes previos.
                                </div>
                            @endforelse
                        </div>

                        <div id="hiddenDeleteInputs"></div>
                    </div>

                    {{-- SUBIR NUEVO DOCUMENTO --}}
                    <div class="mb-4 text-start">
                        <label for="archivo" class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                            Subir Nuevo Soporte
                        </label>
                        <div class="p-4 border-dashed rounded-4 text-center bg-light-soft position-relative">
                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                            <input type="file" name="archivo" id="archivo" accept=".pdf,.jpg,.jpeg,.png"
                                   class="form-control border-0 bg-transparent py-2 px-3 shadow-none"
                                   style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                            <div class="file-name-display text-muted small">Haz clic para seleccionar o arrastra el archivo aquí</div>
                            <small class="text-muted d-block mt-1">Soporte firmado o evidencia fotográfica (Máx. 5MB).</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-5">
                        <a href="{{ route('admin.dotaciones.index') }}"
                        class="btn btn-light border px-4 fw-bold shadow-sm"
                        style="border-radius:15px; font-size:1.1rem; letter-spacing:0.5px;">
                            <i class="fas fa-times me-2"></i> CANCELAR
                        </a>

                        <button type="submit"
                                class="btn btn-orange text-white px-4 fw-bold shadow-sm"
                                style="border-radius:15px; font-size:1.1rem; letter-spacing:0.5px;">
                            <i class="fas fa-sync-alt me-2"></i> ACTUALIZAR DOTACIÓN
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
    if (!confirm('¿Eliminar este soporte?')) return;

    const element = document.getElementById('doc-' + id);
    if (element) {
        element.style.opacity = '0.5';
        element.style.backgroundColor = '#fef2f2';
        element.querySelector('button').disabled = true;
    }

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'eliminar_documentos[]';
    input.value = id;

    document.getElementById('hiddenDeleteInputs').appendChild(input);
}

document.addEventListener("DOMContentLoaded", function () {
    // Manejo de archivo visual
    const fileInput = document.querySelector('input[type="file"]');
    const fileDisplay = document.querySelector('.file-name-display');
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            fileDisplay.innerHTML = `<i class="fas fa-file-check text-success me-2"></i><strong>${this.files[0].name}</strong>`;
            fileDisplay.classList.add('text-dark');
        }
    });
});
</script>
@stop

@section('css')
<style>
.bg-light-soft { background-color: #f8fafc; }
.border-dashed { border-style: dashed !important; border-width: 2px !important; border-color: #cbd5e1 !important; transition: all 0.3s; }
.border-dashed:hover { border-color: #ff6a00 !important; background-color: rgba(255,106,0,0.02); }

.btn-orange {
    background-color: #ff6a00;
    border: none;
    color: #fff;
    transition: all 0.3s ease;
}
.btn-orange:hover {
    background-color: #e65c00;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255,106,0,0.3);
}

.bg-soft-primary { background-color: rgba(255,106,0,0.1); }
.text-primary { color: #ff6a00 !important; }

.hover-orange:hover { color: #ff6a00 !important; }
.extra-small { font-size: 0.75rem; }
.rounded-4 { border-radius: 1rem !important; }

.btn-icon {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    transition: all 0.2s;
}
</style>
@endsection
