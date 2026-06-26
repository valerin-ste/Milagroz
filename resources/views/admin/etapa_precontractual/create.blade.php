@extends('adminlte::page')

@section('title', 'Nueva Etapa Precontractual')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">
            Nueva Etapa Precontractual
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">
            Registro de candidato y documentos
        </p>
    </div>
    <a href="{{ route('admin.etapa_precontractual.index') }}" class="btn btn-light-custom px-4">
        <i class="fas fa-arrow-left me-2"></i> Volver al listado
    </a>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    @if ($errors->any())
        <div class="alert alert-danger" style="border-radius: var(--radius-md); border: none; background-color: #fef2f2; color: #991b1b;">
            <div class="d-flex align-items-center border-bottom pb-2 mb-2" style="border-color: #fecaca !important;">
                <i class="fas fa-exclamation-circle fa-lg me-2"></i>
                <strong>Revise los siguientes errores:</strong>
            </div>
            <ul class="mb-0 mt-2 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="formEtapa"
          action="{{ route('admin.etapa_precontractual.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        <div class="row g-4">
            <div class="col-lg-8 mx-auto">
                <div class="card h-100">
                    <div class="card-header pt-4 px-4 pb-3">
                        <h5 class="card-title" style="color: var(--primary-blue);">
                            <div class="d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); border-radius: 10px;">
                                <i class="fas fa-user-check"></i>
                            </div>
                            Detalles de la Etapa
                        </h5>
                    </div>

                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="row g-4">

                            {{-- CANDIDATO --}}
                            <div class="col-md-12">
                                <label class="form-label">Candidato <span class="text-danger">*</span></label>

                                <select name="persona_id"
                                        id="persona_id"
                                        class="form-select select2"
                                        required>
                                    <option value="">Buscar candidato...</option>

                                    @foreach($personas as $persona)
                                        <option value="{{ $persona->id }}">
                                            {{ $persona->nombres }} {{ $persona->apellidos }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- FECHA REGISTRO --}}
                            <div class="col-md-6">
                                <label class="form-label">Fecha Registro <span class="text-danger">*</span></label>
                                <input type="date"
                                       name="fecha_registro"
                                       class="form-control"
                                       value="{{ now()->toDateString() }}">
                            </div>

                            {{-- ESTADO --}}
                            <div class="col-md-6">
                                <label class="form-label">Estado Revisión <span class="text-danger">*</span></label>
                                <select name="estado"
                                        class="form-select"
                                        required>
                                    <option value="0">En Proceso</option>
                                    <option value="1">Aprobado</option>
                                    <option value="2">Rechazado</option>
                                </select>
                            </div>

                            {{-- DOCUMENTOS --}}
                            <div class="col-12 mt-5">
                                <h5 class="fw-bold mb-3">
                                    <i class="fas fa-folder-open text-primary me-2"></i> Documentos de Soporte
                                </h5>

                                <div class="file-drop-area" id="dropArea">
                                    <i class="fas fa-cloud-upload-alt file-drop-area-icon"></i>
                                    <span class="file-drop-area-text">Arrastra y suelta tus archivos aquí</span>
                                    <span class="file-drop-area-hint">o haz clic para seleccionar en tu computadora</span>
                                    <small class="text-muted d-block mt-1">PDF, Word, JPG, PNG - Máx 10MB</small>
                                    <input type="file"
                                           name="documentos[]"
                                           id="fileInput"
                                           class="file-input-hidden"
                                           multiple
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                </div>

                                <div class="file-list" id="fileList"></div>
                            </div>
                            <div class="d-flex justify-content-end align-items-center gap-3 w-100 mt-4 mb-5">
                                <a href="{{ route('admin.etapa_precontractual.index') }}" class="btn btn-light-custom">
                                    Cancelar
                                </a>

                                <button type="submit" class="btn btn-orange">
                                    <i class="fas fa-save me-2"></i> Guardar Registro
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        

    </form>

</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container .select2-selection--single {
    height: 42px !important;
    display: flex !important;
    align-items: center !important;
    border-radius: 6px !important;
    border: 1px solid #dee2e6 !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 42px !important;
    color: #495057;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 42px !important;
}

.file-drop-area {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    cursor: pointer;
    background: #f8fafc;
    transition: border-color 0.2s ease, background 0.2s ease;
}

.file-drop-area:hover,
.file-drop-area.dragover {
    border-color: var(--primary-blue, #13b6ec);
    background: #f0f9ff;
}

.file-drop-area-icon {
    font-size: 42px;
    color: #13b6ec;
    display: block;
    margin-bottom: 10px;
}

.file-drop-area-text {
    display: block;
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 4px;
}

.file-drop-area-hint {
    display: block;
    color: #94a3b8;
    font-size: 0.875rem;
}

.file-input-hidden {
    display: none;
}

.file-list {
    margin-top: 12px;
}

.file-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 8px;
    background: #fff;
    transition: box-shadow 0.2s;
}

.file-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
}

.file-details {
    display: flex;
    align-items: center;
    gap: 12px;
    overflow: hidden;
}

.file-icon {
    font-size: 1.4rem;
    flex-shrink: 0;
}

.file-info {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.file-name {
    font-size: 0.875rem;
    font-weight: 500;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 300px;
}

.file-size {
    font-size: 0.78rem;
    color: #64748b;
}

.file-remove {
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 6px;
    border-radius: 6px;
    transition: color 0.2s, background 0.2s;
    flex-shrink: 0;
}

.file-remove:hover {
    color: #ef4444;
    background: #fef2f2;
}
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {

    $('#persona_id').select2({
        placeholder: "Buscar candidato...",
        width: '100%'
    });

    const dropArea = document.getElementById("dropArea");
    const fileInput = document.getElementById("fileInput");
    const fileList = document.getElementById("fileList");

    let selectedFiles = [];

    dropArea.addEventListener("click", () => fileInput.click());

    fileInput.addEventListener("change", function () {
        addFiles(this.files);
        fileInput.value = "";
    });

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, e => { e.preventDefault(); e.stopPropagation(); }, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.add('dragover'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragover'), false);
    });

    dropArea.addEventListener("drop", function (e) {
        e.preventDefault();
        addFiles(e.dataTransfer.files);
    });

    function addFiles(files) {
        [...files].forEach(file => {
            if (file.size > 10 * 1024 * 1024) {
                alert(file.name + " supera 10MB");
                return;
            }
            selectedFiles.push(file);
        });
        renderFiles();
    }

    function renderFiles() {
        const dt = new DataTransfer();
        fileList.innerHTML = "";

        selectedFiles.forEach((file, i) => {
            dt.items.add(file);

            const size = (file.size / 1024 / 1024).toFixed(2);
            const fileExt = file.name.split('.').pop().toLowerCase();

            let iconClass = 'fa-file-alt text-secondary';
            if (fileExt === 'pdf') iconClass = 'fa-file-pdf text-danger';
            else if (['jpg', 'jpeg', 'png'].includes(fileExt)) iconClass = 'fa-file-image text-primary';
            else if (['doc', 'docx'].includes(fileExt)) iconClass = 'fa-file-word text-info';

            const div = document.createElement("div");
            div.className = "file-card";
            div.innerHTML = `
                <div class="file-details">
                    <i class="fas ${iconClass} file-icon"></i>
                    <div class="file-info">
                        <span class="file-name" title="${file.name}">${file.name}</span>
                        <span class="file-size">${size} MB</span>
                    </div>
                </div>
                <button type="button" class="file-remove" onclick="removeFile(${i})" title="Eliminar archivo">
                    <i class="fas fa-times"></i>
                </button>
            `;
            fileList.appendChild(div);
        });

        fileInput.files = dt.files;
    }

    // Asegurar sincronización al enviar
    $('#formEtapa').on('submit', function() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    });

    window.removeFile = function (index) {
        selectedFiles.splice(index, 1);
        renderFiles();
    };

});
</script>
@stop