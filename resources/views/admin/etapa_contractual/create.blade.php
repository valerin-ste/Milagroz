@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">Registrar Contrato</h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Asocie un contrato a un empleado existente.</p>
    </div>
    <a href="{{ route('admin.etapa_contractual.index') }}" class="btn btn-light-custom px-4">
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

    <form action="{{ route('admin.etapa_contractual.store') }}" method="POST" enctype="multipart/form-data" id="contratoForm">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8 mx-auto">
                <div class="card h-100">
                    <div class="card-header pt-4 px-4 pb-3">
                        <h5 class="card-title" style="color: var(--primary-blue);">
                            <div class="d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); border-radius: 10px;">
                                <i class="fas fa-handshake"></i>
                            </div>
                            Detalles del Contrato
                        </h5>
                    </div>

                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="row g-4">

                            {{-- 🔥 EMPLEADO CON BUSCADOR (FILA 1) --}}
                            <div class="col-md-12 position-relative">
                                <label class="form-label">Empleado / Candidato <span class="text-danger">*</span></label>

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

                            {{-- 🔥 FILA 2: TIPO, INICIO, FIN --}}
                            <div class="col-md-4">
                                <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                                <select id="tipo_documento_select" class="form-select" required>
                                    <option value="" disabled selected>Seleccione</option>
                                    <option value="Antecedentes Judiciales">Antecedentes Judiciales</option>
                                    <option value="Rethus">Rethus</option>
                                    <option value="Vacunas">Vacunas</option>
                                    <option value="Otros">Otros</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_inicio" class="form-control" required value="{{ old('fecha_inicio', now()->toDateString()) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Fecha de Fin (Opcional)</label>
                                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ old('fecha_fin') }}">
                            </div>

                            {{-- 🔥 RETHUS --}}
                            <div class="col-md-12" id="div_rethus" style="display:none;">
                                <label class="form-label">¿Aplica Rethus? <span class="text-danger">*</span></label>
                                <select id="rethus_select" class="form-select">
                                    <option value="" disabled selected>Seleccione</option>
                                    <option value="Aplica">Sí aplica</option>
                                    <option value="No aplica">No aplica</option>
                                </select>
                            </div>

                            <input type="hidden" name="tipo_contrato" id="tipo_contrato_final" required value="{{ old('tipo_contrato') }}">

                            <div class="col-12 mt-4">
                                <h5 class="fw-bold mb-3">
                                    <i class="fas fa-folder-open text-primary me-2"></i> Anexar Documentos
                                </h5>

                                <div class="file-drop-area" id="dropArea">
                                    <input type="file" name="documentos[]" id="fileInput" multiple>
                                </div>

                                <div class="file-list" id="fileList"></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mt-4 mb-5 pb-4 px-2">
            <a href="{{ route('admin.etapa_contractual.index') }}" class="btn btn-light-custom px-4">
                Cancelar
            </a>
            <button type="submit" class="btn btn-orange px-5">
                Guardar Contrato
            </button>
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

    // =========================
    // 🛡️ LÓGICA FECHA FIN Y TIPOS
    // =========================
    const divRethus = document.getElementById('div_rethus');
    
    const tipoDocumentoSelect = document.getElementById('tipo_documento_select');
    const rethusSelect = document.getElementById('rethus_select');
    const tipoContratoFinal = document.getElementById('tipo_contrato_final');

    const inputFechaFin = document.getElementById('fecha_fin');

    tipoDocumentoSelect.addEventListener('change', function() {
        if (this.value === 'Rethus') {
            divRethus.style.display = 'block';
            rethusSelect.required = true;
        } else {
            divRethus.style.display = 'none';
            rethusSelect.required = false;
        }
        updateFinalValue();
    });

    rethusSelect.addEventListener('change', updateFinalValue);

    function updateFinalValue() {
        if (tipoDocumentoSelect.value) {
            if (tipoDocumentoSelect.value === 'Rethus') {
                tipoContratoFinal.value = rethusSelect.value ? 'Rethus - ' + rethusSelect.value : '';
            } else {
                tipoContratoFinal.value = tipoDocumentoSelect.value;
            }
        } else {
            tipoContratoFinal.value = '';
        }
        checkFechaFin();
    }

    function checkFechaFin() {
        if (tipoContratoFinal.value === 'Contrato indefinido') {
            inputFechaFin.value = "";
            // Eliminamos la restricción estricta porque el backend lo acepta nulo
            // y visualmente se puede dejar vacío si es indefinido.
        }
    }

    // Inicializar estado visual si hay valor antiguo
    if (tipoContratoFinal.value) {
        let val = tipoContratoFinal.value;
        if (val.startsWith('Rethus')) {
            tipoDocumentoSelect.value = 'Rethus';
            divRethus.style.display = 'block';
            if(val.includes('Aplica') && !val.includes('No')) rethusSelect.value = 'Aplica';
            else if(val.includes('No aplica')) rethusSelect.value = 'No aplica';
        } else {
            tipoDocumentoSelect.value = val;
        }
    }
    checkFechaFin();

    // =========================
    // 🔍 VALIDACIÓN PRE-SUBMIT SOLICITADA
    // =========================
    const form = document.getElementById('contratoForm');
    form.addEventListener('submit', function(e) {
        const empleadoHidden = document.getElementById('empleado_id');
        
        // Validar que tenga valor el empleado
        if (!empleadoHidden.value) {
            e.preventDefault();
            errorMsg.style.display = "block";
            input.focus();
            return false;
        }

        // Asegurarnos que los campos deshabilitados (si los hubiese) se envíen
        const disabledInputs = form.querySelectorAll(':disabled');
        disabledInputs.forEach(input => input.disabled = false);
    });

});
</script>
@endsection