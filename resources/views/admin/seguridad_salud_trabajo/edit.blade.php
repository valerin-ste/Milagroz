@extends('adminlte::page')

@section('title', 'Editar Documento SST')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">Editar Documento</h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Modifique los datos del documento de Seguridad y Salud.</p>
    </div>
    <a href="{{ route('admin.seguridad_salud_trabajo.index') }}" class="btn btn-light-custom px-4">
        <i class="fas fa-arrow-left me-2"></i> Volver al listado
    </a>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0 mb-4" style="border-radius: var(--radius-md); background-color: #fef2f2; color: #991b1b;">
            <div class="d-flex align-items-center border-bottom pb-2 mb-2" style="border-color: #fecaca !important;">
                <i class="fas fa-exclamation-circle fa-lg me-2"></i> 
                <strong>Revise los siguientes errores:</strong>
            </div>
            <ul class="mb-0 mt-2 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.seguridad_salud_trabajo.update', $documento) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card h-100 shadow-sm border-0 rounded-lg">
                    <div class="card-header pt-4 px-4 pb-3 bg-white border-0">
                        <h5 class="card-title fw-bold" style="color: #f97316;">
                            <div class="d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); border-radius: 10px;">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            Detalles del Documento SST
                        </h5>
                    </div>

                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Empleado <span class="text-danger">*</span></label>
                                <select name="empleado_id" class="form-select form-control" required>
                                    @foreach($empleados as $e)
                                        <option value="{{ $e->id }}" {{ old('empleado_id', $documento->empleado_id) == $e->id ? 'selected' : '' }}>
                                            {{ $e->persona->nombres }} {{ $e->persona->apellidos }} - {{ $e->persona->numero_documento }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Tipo de Documento <span class="text-danger">*</span></label>
                                <select name="tipo_documento" class="form-control" required>
                                    <option value="">Seleccione...</option>

                                    <option value="Ingresos"
                                        {{ old('tipo_documento', $documento->tipo_documento) == 'Ingresos' || old('tipo_documento', $documento->tipo_documento) == 'Ingreso' ? 'selected' : '' }}>
                                        Ingresos
                                    </option>

                                    <option value="Periódicos"
                                        {{ old('tipo_documento', $documento->tipo_documento) == 'Periódicos' || old('tipo_documento', $documento->tipo_documento) == 'Periódico' ? 'selected' : '' }}>
                                        Periódicos
                                    </option>

                                    <option value="ARL"
                                        {{ old('tipo_documento', $documento->tipo_documento) == 'ARL' ? 'selected' : '' }}>
                                        ARL
                                    </option>

                                    <option value="Retiros"
                                        {{ old('tipo_documento', $documento->tipo_documento) == 'Retiros' || old('tipo_documento', $documento->tipo_documento) == 'Retiro' ? 'selected' : '' }}>
                                        Retiros
                                    </option>
                                </select>
                            </div>  

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" class="form-control" 
                                       value="{{ old('fecha', $documento->fecha instanceof \Carbon\Carbon ? $documento->fecha->format('Y-m-d') : $documento->fecha) }}" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                                    Archivos / Soportes actuales
                                </label>

                                <div class="list-group mb-3">
                                    @forelse($documento->documentos as $archivo)
                                        <div id="doc-{{ $archivo->id }}" class="list-group-item d-flex justify-content-between align-items-center">
                                            {{-- NOMBRE DEL ARCHIVO (CLICKEABLE) --}}
                                            <a href="{{ route('admin.documentos.view', $archivo->id) }}" target="_blank" class="text-decoration-none fw-semibold text-dark">
                                                📄 {{ $archivo->nombre_original }}
                                            </a>

                                            {{-- ELIMINAR --}}
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeExistingDoc({{ $archivo->id }})">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    @empty
                                        <div class="text-muted small">Sin archivos adjuntos.</div>
                                    @endforelse
                                </div>

                                {{-- OCULTOS PARA ELIMINACIÓN --}}
                                <div id="hiddenDeleteInputs"></div>

                                {{-- INPUT MULTIPLE --}}
                                <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                    Cargar más archivos
                                </label>

                                <div class="file-drop-area" id="dropArea">
                                    <i class="fas fa-cloud-upload-alt file-drop-area-icon"></i>
                                    <span class="file-drop-area-text">Arrastra y suelta nuevos archivos aquí</span>
                                    <span class="file-drop-area-hint">o haz clic para explorar en tu computadora</span>
                                    <input type="file" name="documentos[]" id="fileInput"
                                           class="file-input-hidden" multiple
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                </div>

                                <div class="file-list" id="fileList"></div>

                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Formatos aceptados: PDF, Word, Excel, JPG, PNG. Máx. 10MB por archivo.
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- BOTONES DE ACCIÓN --}}
                    <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                        <hr class="mt-0 mb-4 opacity-50">
                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('admin.seguridad_salud_trabajo.index') }}" class="btn btn-light-custom px-4 border shadow-sm">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-orange px-5 text-white shadow">
                                <i class="fas fa-save me-2"></i> ACTUALIZAR DOCUMENTO
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function removeExistingDoc(id) {
    if (!confirm('¿Eliminar este archivo?')) return;

    // Eliminar el elemento visualmente
    const element = document.getElementById('doc-' + id);
    if (element) element.remove();

    // Agregar el ID al input oculto para que el controlador lo procese
    const inp = document.createElement('input');
    inp.type = 'hidden';
    inp.name = 'eliminar_documentos[]';
    inp.value = id;
    document.getElementById('hiddenDeleteInputs').appendChild(inp);
}

document.addEventListener('DOMContentLoaded', function () {
    // =========================
    // 📂 DRAG & DROP FILE UPLOAD
    // =========================
    const dropArea  = document.getElementById('dropArea');
    const fileInput = document.getElementById('fileInput');
    const fileList  = document.getElementById('fileList');
    let selectedFiles = [];

    dropArea.addEventListener('click', (e) => {
        if (e.target !== fileInput) fileInput.click();
    });

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(evt => {
        dropArea.addEventListener(evt, e => { e.preventDefault(); e.stopPropagation(); }, false);
    });
    ['dragenter', 'dragover'].forEach(evt =>
        dropArea.addEventListener(evt, () => dropArea.classList.add('dragover'), false)
    );
    ['dragleave', 'drop'].forEach(evt =>
        dropArea.addEventListener(evt, () => dropArea.classList.remove('dragover'), false)
    );

    dropArea.addEventListener('drop', e => addFiles(e.dataTransfer.files));
    fileInput.addEventListener('change', function () {
        addFiles(this.files);
        this.value = '';
    });

    function addFiles(files) {
        [...files].forEach(file => {
            if (file.size > 10 * 1024 * 1024) {
                alert('El archivo "' + file.name + '" supera los 10MB permitidos.');
                return;
            }
            const allowed = ['pdf','doc','docx','xls','xlsx','jpg','jpeg','png'];
            const ext = file.name.split('.').pop().toLowerCase();
            if (!allowed.includes(ext)) {
                alert('El archivo "' + file.name + '" no es un formato permitido.');
                return;
            }
            selectedFiles.push(file);
        });
        renderFiles();
    }

    function getFileIcon(ext) {
        if (ext === 'pdf')                             return 'fa-file-pdf text-danger';
        if (['jpg','jpeg','png','gif'].includes(ext))  return 'fa-file-image text-primary';
        if (['doc','docx'].includes(ext))              return 'fa-file-word text-info';
        if (['xls','xlsx'].includes(ext))              return 'fa-file-excel text-success';
        return 'fa-file-alt text-secondary';
    }

    function renderFiles() {
        const dt = new DataTransfer();
        fileList.innerHTML = '';
        selectedFiles.forEach((file, i) => {
            dt.items.add(file);
            const ext  = file.name.split('.').pop().toLowerCase();
            const size = (file.size / 1024 / 1024).toFixed(2);
            const card = document.createElement('div');
            card.className = 'file-card';
            card.innerHTML = `
                <div class="file-details">
                    <i class="fas ${getFileIcon(ext)} file-icon"></i>
                    <div class="file-info">
                        <span class="file-name" title="${file.name}">${file.name}</span>
                        <span class="file-size">${size} MB</span>
                    </div>
                </div>
                <button type="button" class="file-remove" onclick="removeNewFile(${i})" title="Eliminar">
                    <i class="fas fa-times"></i>
                </button>`;
            fileList.appendChild(card);
        });
        fileInput.files = dt.files;
    }

    window.removeNewFile = function(index) {
        selectedFiles.splice(index, 1);
        renderFiles();
    };

    // Sincronizar al enviar
    document.querySelector('form').addEventListener('submit', function () {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    });
});
</script>
@endsection

@section('css')
<style>
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.2rem rgba(19, 182, 236, 0.1);
    }
    .btn-orange {
        background-color: #f97316;
        color: white;
        border: none;
        font-weight: 500;
        transition: transform 0.2s;
    }
    .btn-orange:hover {
        background-color: #ea580c;
        color: white;
        transform: translateY(-2px);
    }
    .btn-light-custom {
        background-color: white;
        color: #475569;
        font-weight: 500;
        transition: all 0.2s;
    }
    .btn-light-custom:hover {
        background-color: #f1f5f9;
        color: #1e293b;
    }
</style>
@stop

