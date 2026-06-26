@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">Nueva Solicitud</h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Cree una nueva solicitud para un empleado.</p>
    </div>
    <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-light-custom px-4">
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

    <form action="{{ route('admin.solicitudes.store') }}" method="POST" enctype="multipart/form-data" id="solicitudForm">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8 mx-auto">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header pt-4 px-4 pb-3">
                        <h5 class="card-title" style="color: var(--primary-blue);">
                            <div class="d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); border-radius: 10px;">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            Detalles de la Solicitud
                        </h5>
                    </div>

                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="row g-4">

                            {{-- 🔥 EMPLEADO CON BUSCADOR (FILA 1) --}}
                            <div class="col-md-12 position-relative">
                                <label class="form-label">Empleado <span class="text-danger">*</span></label>

                                <input type="text"
                                       id="buscarEmpleado"
                                       name="buscarEmpleado_text"
                                       class="form-control"
                                       placeholder="Escriba nombre o cédula y seleccione de la lista..."
                                       autocomplete="off"
                                       value="{{ old('buscarEmpleado_text') }}">

                                <input type="hidden" name="empleado_id" id="empleado_id" value="{{ old('empleado_id') }}">

                                <div id="listaEmpleados"
                                     class="list-group position-absolute w-100 shadow-sm"
                                     style="z-index: 999; display:none; max-height: 250px; overflow-y: auto;">
                                </div>
                                <div id="empleadoError" class="text-danger mt-1" style="display:none; font-size: 0.875em;">
                                    Debe seleccionar un empleado de la lista.
                                </div>
                            </div>

                            {{-- 🔥 FILA 2: TIPO, FECHA, ESTADO --}}
                            <div class="col-md-4">
                                <label class="form-label">Tipo de Solicitud <span class="text-danger">*</span></label>
                                <select id="tipo_select" class="form-select" required>
                                    <option value="Vacaciones" {{ old('tipo_select', 'Vacaciones') == 'Vacaciones' ? 'selected' : '' }}>Vacaciones</option>
                                    <option value="Solicitud" {{ old('tipo_select') == 'Solicitud' ? 'selected' : '' }}>Solicitud</option>
                                    <option value="Ausentismo" {{ old('tipo_select') == 'Ausentismo' ? 'selected' : '' }}>Ausentismo</option>
                                    <option value="Otro" {{ old('tipo_select') == 'Otro' ? 'selected' : '' }}>Otro (Especificar)</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" class="form-control" required value="{{ old('fecha', now()->toDateString()) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Estado <span class="text-danger">*</span></label>
                                <select name="estado" class="form-select" required>
                                    <option value="pendiente" {{ old('estado', 'pendiente') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="aprobado" {{ old('estado') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                                    <option value="rechazado" {{ old('estado') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                                </select>
                            </div>

                            {{-- 🔥 TIPO OTRO --}}
                            <div class="col-md-12" id="div_tipo_otro" style="display:none;">
                                <label class="form-label">Especifique el Tipo <span class="text-danger">*</span></label>
                                <input type="text" name="tipo_otro" id="tipo_otro" class="form-control" value="{{ old('tipo_otro') }}" placeholder="Ej: Permiso por luto, Calamidad doméstica...">
                            </div>

                            <input type="hidden" name="tipo" id="tipo_final" required value="{{ old('tipo', 'Vacaciones') }}">

                            {{-- 🔥 DESCRIPCIÓN --}}
                            <div class="col-md-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="4" placeholder="Ingrese detalles o comentarios sobre la solicitud...">{{ old('descripcion') }}</textarea>
                            </div>

                            {{-- 🔥 ANEXAR DOCUMENTOS --}}
                            <div class="col-12 mt-4">
                                <h5 class="fw-bold mb-3">
                                    <i class="fas fa-folder-open text-primary me-2"></i> Anexar Documentos
                                </h5>

                                <div class="file-drop-area" id="dropArea">
                                    <i class="fas fa-cloud-upload-alt file-drop-area-icon"></i>
                                    <span class="file-drop-area-text">Arrastra y suelta tus archivos aquí</span>
                                    <span class="file-drop-area-hint">o haz clic para seleccionar en tu computadora</span>
                                    <small class="text-muted d-block mt-1">PDF, Word, JPG, PNG - Máx 10MB</small>
                                    <input type="file" name="archivos[]" id="fileInput" class="file-input-hidden" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                </div>

                                <div class="file-list" id="fileList"></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-5 border-top pt-4">
                            <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-light-custom px-4">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-orange px-5">
                                <i class="fas fa-save me-2"></i> Guardar Solicitud
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection

@section('js')
<script>
document.addEventListener("DOMContentLoaded", function () {

    // =========================
    // 🔥 EMPLEADOS AUTOCOMPLETE
    // =========================
    const empleados = @json($empleados);

    const input = document.getElementById("buscarEmpleado");
    const hidden = document.getElementById("empleado_id");
    const lista = document.getElementById("listaEmpleados");
    const errorMsg = document.getElementById("empleadoError");

    input.addEventListener("input", function () {
        let valor = this.value.toLowerCase().trim();

        // Al cambiar el texto, borramos el ID oculto para obligar a seleccionar de nuevo
        hidden.value = "";
        errorMsg.style.display = "none";
        lista.innerHTML = "";

        if (valor.length < 1) {
            lista.style.display = "none";
            return;
        }

        let filtrados = empleados.filter(e => {
            let nombre = (e.persona.nombres + " " + e.persona.apellidos).toLowerCase();
            let cedula = (e.persona.numero_documento || "").toLowerCase();

            return nombre.includes(valor) || cedula.includes(valor);
        });

        if (filtrados.length === 0) {
            lista.style.display = "none";
            return;
        }

        filtrados.forEach(emp => {
            let nombre = emp.persona.nombres + " " + emp.persona.apellidos;

            let item = document.createElement("button");
            item.type = "button";
            item.className = "list-group-item list-group-item-action";
            item.innerHTML = `<strong>${nombre}</strong><br><small>${emp.persona.numero_documento}</small>`;

            // Usar onmousedown para evitar que el blur del input lo oculte antes de procesar el clic
            item.onmousedown = function (e) {
                e.preventDefault();

                input.value = nombre + " - " + emp.persona.numero_documento;
                hidden.value = parseInt(emp.id);

                errorMsg.style.display = "none";
                lista.style.display = "none";
            };

            lista.appendChild(item);
        });

        lista.style.display = "block";
    });

    // Si el usuario abandona el input y no ha seleccionado nada, limpiamos el input visual
    input.addEventListener("change", function() {
        if (!hidden.value) {
            input.value = "";
        }
    });

    document.addEventListener("click", function (e) {
        if (!input.contains(e.target)) {
            lista.style.display = "none";
        }
    });

    // Cargar empleado si hay valor anterior (old)
    if (hidden.value) {
        const emp = empleados.find(e => e.id == hidden.value);
        if (emp) {
            input.value = emp.persona.nombres + ' ' + emp.persona.apellidos + ' - ' + emp.persona.numero_documento;
        }
    }

    // ==================================
    // 🛡️ LÓGICA TIPO DE SOLICITUD (OTRO)
    // ==================================
    const tipoSelect = document.getElementById('tipo_select');
    const tipoOtroDiv = document.getElementById('div_tipo_otro');
    const tipoOtroInput = document.getElementById('tipo_otro');
    const tipoFinal = document.getElementById('tipo_final');

    function updateTipo() {
        if (tipoSelect.value === 'Otro') {
            tipoOtroDiv.style.display = 'block';
            tipoOtroInput.required = true;
            tipoFinal.value = tipoOtroInput.value;
        } else {
            tipoOtroDiv.style.display = 'none';
            tipoOtroInput.required = false;
            tipoFinal.value = tipoSelect.value;
        }
    }

    tipoSelect.addEventListener('change', updateTipo);
    tipoOtroInput.addEventListener('input', () => {
        if (tipoSelect.value === 'Otro') {
            tipoFinal.value = tipoOtroInput.value;
        }
    });

    // Inicializar estado visual si hay valor antiguo
    if (tipoFinal.value) {
        const predefinidos = ['Vacaciones', 'Solicitud', 'Ausentismo'];
        if (predefinidos.includes(tipoFinal.value)) {
            tipoSelect.value = tipoFinal.value;
        } else {
            tipoSelect.value = 'Otro';
            tipoOtroInput.value = tipoFinal.value;
        }
    }
    updateTipo(); // Init

    // ==================================
    // 📎 DRAG & DROP ARCHIVOS
    // ==================================
    const dropArea = document.getElementById("dropArea");
    const fileInput = document.getElementById("fileInput");
    const fileList = document.getElementById("fileList");
    let selectedFiles = new DataTransfer();

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.add('dragover'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragover'), false);
    });

    dropArea.addEventListener('drop', e => handleFiles(e.dataTransfer.files), false);
    fileInput.addEventListener('change', function() { handleFiles(this.files); });

    function handleFiles(files) {
        [...files].forEach(file => {
            if(file.size > 10 * 1024 * 1024) {
                alert('El archivo ' + file.name + ' supera los 10MB permitidos.');
                return;
            }
            selectedFiles.items.add(file);
        });
        updateDOM();
    }

    function updateDOM() {
        fileInput.files = selectedFiles.files;
        fileList.innerHTML = '';
        
        [...selectedFiles.files].forEach((file, index) => {
            const size = (file.size / 1024 / 1024).toFixed(2);
            const fileExt = file.name.split('.').pop().toLowerCase();
            
            let iconClass = 'fa-file-alt text-secondary';
            if (fileExt === 'pdf') iconClass = 'fa-file-pdf text-danger';
            else if (['jpg', 'jpeg', 'png'].includes(fileExt)) iconClass = 'fa-file-image text-primary';
            else if (['doc', 'docx'].includes(fileExt)) iconClass = 'fa-file-word text-info';

            const fileCard = document.createElement('div');
            fileCard.className = 'file-card';
            fileCard.innerHTML = `
                <div class="file-details">
                    <i class="fas ${iconClass} file-icon"></i>
                    <div class="file-info">
                        <span class="file-name" title="${file.name}">${file.name}</span>
                        <span class="file-size">${size} MB</span>
                    </div>
                </div>
                <button type="button" class="file-remove" onclick="removeFile(${index})" title="Eliminar archivo">
                    <i class="fas fa-times"></i>
                </button>
            `;
            fileList.appendChild(fileCard);
        });
    }

    window.removeFile = function (index) {
        let newSelectedFiles = new DataTransfer();
        let filesArray = Array.from(selectedFiles.files);
        filesArray.splice(index, 1);
        filesArray.forEach(file => newSelectedFiles.items.add(file));
        selectedFiles = newSelectedFiles;
        updateDOM();
    };

    // ==================================
    // 🔍 VALIDACIÓN PRE-SUBMIT
    // ==================================
    const form = document.getElementById('solicitudForm');
    form.addEventListener('submit', function(e) {
        const empleadoHidden = document.getElementById('empleado_id');
        
        // Validar que tenga valor el empleado
        if (!empleadoHidden.value) {
            e.preventDefault();
            errorMsg.style.display = "block";
            input.focus();
            return false;
        }

        // Asegurarnos que los campos deshabilitados se envíen
        const disabledInputs = form.querySelectorAll(':disabled');
        disabledInputs.forEach(input => input.disabled = false);
    });

});
</script>
@endsection