@extends('adminlte::page')

@section('content_header')
<div class="container-fluid px-2">
    <div class="row">
        <div class="col-lg-8 mx-auto d-flex justify-content-between align-items-center mt-3 mb-2">

            <div>
                <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">
                    Nueva Comunicación
                </h2>
                <p class="text-muted mb-0" style="font-size: 0.95rem;">
                    Registro y envío de comunicaciones a empleados.
                </p>
            </div>

            <a href="{{ route('admin.comunicaciones.index') }}" class="btn btn-light-custom px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver al listado
            </a>

        </div>
    </div>
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

    <form action="{{ route('admin.comunicaciones.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8 mx-auto">
                <div class="card h-100 border-0">

                    <div class="card-header pt-4 px-4 pb-3">
                        <h5 class="card-title d-flex align-items-center mb-0" style="color: #f97316;">
                            <div class="d-flex align-items-center justify-content-center me-3"
                                style="width: 40px; height: 40px; background-color: rgba(249, 115, 22, 0.1); border-radius: 10px;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            Detalles de la Comunicación
                        </h5>
                    </div>

                    <div class="card-body px-4 pb-4 pt-2">

                        <div class="row g-4">

                            {{-- 🔥 EMPLEADO AUTOCOMPLETE --}}
                            <div class="col-md-12 position-relative">

                                <label class="form-label">
                                    Empleado <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       id="buscarEmpleado"
                                       class="form-control"
                                       placeholder="Buscar empleado por nombre o cédula..."
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

                            <div class="col-md-12">
                                <label class="form-label">Asunto <span class="text-danger">*</span></label>
                                <input type="text" name="asunto" class="form-control" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Mensaje</label>
                                <textarea name="mensaje" class="form-control" rows="4"></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" class="form-control" required>
                            </div>

                            {{-- ARCHIVOS --}}
                            <div class="col-12 mt-4">

                                <h5 class="fw-bold mb-3" style="color: var(--text-main);">
                                    <i class="fas fa-folder-open text-primary me-2"></i> Anexar Documentos
                                </h5>

                                <p class="text-muted small mb-3">
                                    Puede subir varios archivos (PDF, Word, imágenes).
                                </p>

                                <div class="file-drop-area border rounded p-4 text-center" id="dropArea" style="cursor:pointer;">
                                    <i class="fas fa-cloud-upload-alt fa-2x mb-2 text-primary"></i>
                                    <div>Arrastra archivos aquí o haz clic</div>
                                    <small class="text-muted">Archivos múltiples permitidos</small>
                                    <input type="file" name="archivos[]" id="fileInput" multiple hidden>
                                </div>

                                <div id="fileList" class="mt-3"></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BOTONES --}}
        <div class="row">
            <div class="col-lg-8 mx-auto d-flex justify-content-end gap-3 mt-4 mb-5 px-2">

                <a href="{{ route('admin.comunicaciones.index') }}" class="btn btn-light-custom px-4">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-orange px-5">
                    <i class="fas fa-save me-2"></i> Guardar Comunicación
                </button>

            </div>
        </div>

    </form>
</div>
@endsection

@section('js')
<script>
document.addEventListener("DOMContentLoaded", function () {

    // 🔥 AUTOCOMPLETE EMPLEADOS
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

    // Validar antes de enviar
    form.addEventListener("submit", function (e) {
        if (!hidden.value) {
            e.preventDefault();
            input.classList.add("is-invalid");
            errDiv.style.display = "block";
            input.focus();
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    // 🔥 ARCHIVOS (TU MISMO CÓDIGO)
    const dropArea = document.getElementById("dropArea");
    const fileInput = document.getElementById("fileInput");
    const fileList = document.getElementById("fileList");

    let dataTransfer = new DataTransfer();

    dropArea.addEventListener("click", () => fileInput.click());

    fileInput.addEventListener("change", function () {
        handleFiles(this.files);
    });

    dropArea.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropArea.classList.add("bg-light");
    });

    dropArea.addEventListener("dragleave", () => {
        dropArea.classList.remove("bg-light");
    });

    dropArea.addEventListener("drop", (e) => {
        e.preventDefault();
        dropArea.classList.remove("bg-light");
        handleFiles(e.dataTransfer.files);
    });

    function handleFiles(files) {
        [...files].forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;
        renderFiles();
    }

    function renderFiles() {
        fileList.innerHTML = "";
        Array.from(fileInput.files).forEach((file, index) => {
            let div = document.createElement("div");
            div.className = "border rounded p-2 mb-2 d-flex justify-content-between";
            div.innerHTML = `
                <span>📎 ${file.name}</span>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeFile(${index})">X</button>
            `;
            fileList.appendChild(div);
        });
    }

    window.removeFile = function(index) {
        let newData = new DataTransfer();
        let filesList = Array.from(fileInput.files);
        filesList.splice(index, 1);
        filesList.forEach(f => newData.items.add(f));
        dataTransfer = newData;
        fileInput.files = dataTransfer.files;
        renderFiles();
    }

});
</script>
@endsection