@extends('adminlte::page')

@section('title', 'Nueva Etapa Precontractual')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Nueva Etapa Precontractual
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">
            Registro de candidato y documentos
        </p>
    </div>

    <a href="{{ route('admin.etapa_precontractual.index') }}" class="btn btn-light-custom px-4">
        <i class="fas fa-arrow-left me-2"></i> Volver
    </a>
</div>
@stop

@section('content')

<div class="container-fluid px-2">

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
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

                <div class="row g-3 align-items-end">

                    <div class="col-md-5">
                        <label class="form-label fw-bold">Candidato</label>

                        <select name="persona_id"
                                id="persona_id"
                                class="form-control input-style select2"
                                required>
                            <option value="">Buscar candidato...</option>

                            @foreach($personas as $persona)
                                <option value="{{ $persona->id }}">
                                    {{ $persona->nombres }} {{ $persona->apellidos }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Fecha Registro</label>

                        <input type="date"
                               name="fecha_registro"
                               class="form-control input-style"
                               value="{{ now()->toDateString() }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Estado Revisión</label>

                        <select name="estado"
                                class="form-control input-style"
                                required>
                            <option value="0">En Proceso</option>
                            <option value="1">Aprobado</option>
                            <option value="2">Rechazado</option>
                        </select>
                    </div>

                </div>

                {{-- DOCUMENTOS --}}
                <div class="mt-4">

                    <label class="form-label fw-bold mb-2">
                        Documentos de Soporte
                    </label>

                    <div class="file-drop-area" id="dropArea">

                        <i class="fas fa-cloud-upload-alt file-drop-icon"></i>

                        <h5 class="mt-2 mb-1 fw-bold">
                            Arrastra y suelta tus archivos aquí
                        </h5>

                        <p class="text-muted mb-2">
                            o haz clic para seleccionar
                        </p>

                        <small class="text-muted">
                            PDF, Word, JPG, PNG - Máx 10MB
                        </small>

                        <input type="file"
                               name="documentos[]"
                               id="fileInput"
                               class="file-input-hidden"
                               multiple
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    </div>

                    <div class="file-list mt-3" id="fileList"></div>

                </div>

                <div class="mt-5 text-end border-top pt-4">
                    <button type="submit" class="btn btn-orange px-5">
                        <i class="fas fa-save me-2"></i> Guardar Registro
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@stop

@section('css')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
.input-style {
    height: 42px !important;
    border-radius: 6px !important;
    font-size: 14px;
}

.select2-container .select2-selection--single {
    height: 42px !important;
    display: flex !important;
    align-items: center !important;
    border-radius: 6px !important;
}

.file-drop-area {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    cursor: pointer;
    background: #f8fafc;
}

.file-drop-icon {
    font-size: 42px;
    color: #13b6ec;
}

.file-input-hidden {
    display: none;
}

.file-card {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 6px;
    background: #fff;
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

    dropArea.addEventListener("dragover", e => e.preventDefault());

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

            const div = document.createElement("div");
            div.className = "file-card d-flex justify-content-between align-items-center p-2 border rounded mb-2";
            div.innerHTML = `
                <div class="small text-truncate" style="max-width: 80%;">
                    <i class="fas fa-file-alt me-2 text-primary"></i>${file.name}
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeFile(${i})">
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