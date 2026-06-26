@extends('adminlte::page')

@section('title', 'Registrar Documento SST')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">
            Registrar Documento
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">
            Asocie un documento de Seguridad y Salud a un empleado.
        </p>
    </div>

    <a href="{{ route('admin.seguridad_salud_trabajo.index') }}" class="btn btn-light-custom px-4">
        <i class="fas fa-arrow-left me-2"></i> Volver al listado
    </a>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    {{-- ERRORES --}}
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0 mb-4"
             style="border-radius: var(--radius-md); background-color: #fef2f2; color: #991b1b;">

            <div class="d-flex align-items-center border-bottom pb-2 mb-2"
                 style="border-color: #fecaca !important;">
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

    {{-- FORM --}}
    <form action="{{ route('admin.seguridad_salud_trabajo.store') }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm border-0 rounded-lg">

                    <div class="card-header bg-white border-0 pt-4 px-4 pb-3">
                        <h5 class="fw-bold" style="color: #f97316;">
                            <i class="fas fa-shield-alt me-2"></i>
                            Detalles del Documento SST
                        </h5>
                    </div>

                    <div class="card-body px-4 pb-4 pt-2">

                        <div class="row g-4">

                            {{-- EMPLEADO AUTOCOMPLETE --}}
                            <div class="col-md-12 position-relative">

                                <label class="form-label fw-semibold">
                                    Empleado <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       id="buscarEmpleado"
                                       class="form-control"
                                       placeholder="Escriba nombre o cédula..."
                                       autocomplete="off">

                                <input type="hidden" name="empleado_id" id="empleado_id"
                                       value="{{ old('empleado_id') }}">

                                <div id="listaEmpleados"
                                     class="list-group position-absolute w-100"
                                     style="z-index:999; display:none; max-height:250px; overflow-y:auto;">
                                </div>

                                <div id="empleadoError"
                                     class="text-danger small mt-1"
                                     style="display:none;">Debe seleccionar un empleado de la lista.</div>
                            </div>

                            {{-- TIPO DOCUMENTO --}}
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">
                                    Tipo de Documento <span class="text-danger">*</span>
                                </label>

                                <select name="tipo_documento" class="form-control" required>
                                    <option value="">Seleccione...</option>

                                    <option value="Ingresos"
                                        {{ old('tipo_documento') == 'Ingresos' ? 'selected' : '' }}>
                                        Ingresos
                                    </option>

                                    <option value="Periódicos"
                                        {{ old('tipo_documento') == 'Periódicos' ? 'selected' : '' }}>
                                        Periódicos
                                    </option>

                                    <option value="ARL"
                                        {{ old('tipo_documento') == 'ARL' ? 'selected' : '' }}>
                                        ARL
                                    </option>

                                    <option value="Retiros"
                                        {{ old('tipo_documento') == 'Retiros' ? 'selected' : '' }}>
                                        Retiros
                                    </option>
                                </select>
                            </div>

                            {{-- FECHA --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Fecha <span class="text-danger">*</span>
                                </label>

                                <input type="date"
                                       name="fecha"
                                       class="form-control"
                                       value="{{ old('fecha', now()->toDateString()) }}"
                                       required>
                            </div>

                            {{-- DOCUMENTOS --}}
                            <div class="col-12 mt-4">
                                <h5 class="fw-bold mb-3">
                                    <i class="fas fa-folder-open text-primary me-2"></i>
                                    Anexar Documentos de Soporte
                                </h5>

                                <div class="file-drop-area" id="dropArea">
                                    <i class="fas fa-cloud-upload-alt file-drop-area-icon"></i>
                                    <span class="file-drop-area-text">Arrastra y suelta tus archivos aquí</span>
                                    <span class="file-drop-area-hint">o haz clic para explorar en tu computadora</span>
                                    <input type="file" name="documentos[]" id="fileInput"
                                           class="file-input-hidden" multiple
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                </div>

                                <div class="file-list" id="fileList"></div>

                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Formatos aceptados: PDF, Word, Excel, JPG, PNG. Máx. 10MB por archivo.
                                </small>
                            </div>

                        </div>
                    </div>
                </div>
                {{-- BOTONES --}}
                <div class="d-flex justify-content-end gap-3 mt-4 mb-5 pb-4">
                    <a href="{{ route('admin.seguridad_salud_trabajo.index') }}"
                    class="btn btn-light-custom px-4 border">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-orange px-5">
                        <i class="fas fa-save me-2"></i>
                        Guardar Registro
                    </button>
                </div>
            </div>
            
        </div>

        

    </form>
</div>
@endsection

{{-- JS AUTOCOMPLETE --}}
@section('js')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const empleados = @json($empleados);

    const input  = document.getElementById("buscarEmpleado");
    const hidden = document.getElementById("empleado_id");
    const lista  = document.getElementById("listaEmpleados");
    const errDiv = document.getElementById("empleadoError");
    const form   = input.closest('form');

    // Pre-rellenar si hay old() value
    if (hidden.value) {
        const emp = empleados.find(e => e.id == hidden.value);
        if (emp) {
            const nombre = `${emp.persona?.nombres ?? ''} ${emp.persona?.apellidos ?? ''}`;
            const cedula = emp.persona?.numero_documento ?? '';
            input.value = nombre + ' - ' + cedula;
        }
    }

    const norm = (t) => (t ?? "").toString().toLowerCase().trim();

    input.addEventListener("input", function () {
        const valor = norm(this.value);
        lista.innerHTML = "";
        hidden.value = "";
        errDiv.style.display = "none";

        if (valor.length < 1) { lista.style.display = "none"; return; }

        const filtrados = empleados.filter(e => {
            const nombre = norm(`${e.persona?.nombres ?? ''} ${e.persona?.apellidos ?? ''}`);
            const cedula = norm(e.persona?.numero_documento);
            return nombre.includes(valor) || cedula.includes(valor);
        });

        if (filtrados.length === 0) { lista.style.display = "none"; return; }

        filtrados.forEach(emp => {
            const nombre = `${emp.persona?.nombres ?? ''} ${emp.persona?.apellidos ?? ''}`;
            const cedula = emp.persona?.numero_documento ?? '';
            const item = document.createElement("button");
            item.type = "button";
            item.className = "list-group-item list-group-item-action";
            item.innerHTML = `<strong>${nombre}</strong><br><small>${cedula}</small>`;
            item.onclick = function () {
                input.value  = nombre + " - " + cedula;
                hidden.value = emp.id;
                lista.style.display = "none";
                errDiv.style.display = "none";
                input.classList.remove("is-invalid");
            };
            lista.appendChild(item);
        });

        lista.style.display = "block";
    });

    document.addEventListener("click", function (e) {
        if (!input.contains(e.target) && !lista.contains(e.target)) {
            lista.style.display = "none";
        }
    });

    // Validacion antes de enviar
    form.addEventListener("submit", function (e) {
        if (!hidden.value) {
            e.preventDefault();
            input.classList.add("is-invalid");
            errDiv.style.display = "block";
            input.focus();
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        // Sincronizar archivos seleccionados al input antes de enviar
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        document.getElementById('fileInput').files = dt.files;
    });

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
                <button type="button" class="file-remove" onclick="removeFile(${i})" title="Eliminar">
                    <i class="fas fa-times"></i>
                </button>`;
            fileList.appendChild(card);
        });
        fileInput.files = dt.files;
    }

    window.removeFile = function(index) {
        selectedFiles.splice(index, 1);
        renderFiles();
    };

});
</script>
@endsection