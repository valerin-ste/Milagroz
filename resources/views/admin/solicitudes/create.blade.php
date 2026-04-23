@extends('adminlte::page')

@section('content')

<div class="container-fluid px-2">

    <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
        <h2 class="fw-bold">➕ Nueva Solicitud</h2>

        <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="{{ route('admin.solicitudes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- 🔥 EMPLEADO CON AUTOCOMPLETE --}}
                <div class="mb-3 position-relative">
                    <label>Empleado <span class="text-danger">*</span></label>

                    <input type="text"
                           id="buscarEmpleado"
                           class="form-control"
                           placeholder="Escriba nombre o cédula..."
                           autocomplete="off">

                    <input type="hidden" name="empleado_id" id="empleado_id"
                           value="{{ old('empleado_id') }}">

                    <div id="listaEmpleados"
                         class="list-group position-absolute w-100"
                         style="z-index: 999; display:none; max-height: 250px; overflow-y: auto;">
                    </div>

                    <div id="empleadoError" class="text-danger small mt-1" style="display:none;">
                        Debe seleccionar un empleado de la lista.
                    </div>
                </div>

                <div class="mb-3">
                    <label>Tipo de Solicitud <span class="text-danger">*</span></label>
                    <select name="tipo_select" id="tipo_select" class="form-control" required>
                        <option value="Vacaciones" {{ old('tipo_select') == 'Vacaciones' ? 'selected' : '' }}>Vacaciones</option>
                        <option value="Incapacidad" {{ old('tipo_select') == 'Incapacidad' ? 'selected' : '' }}>Incapacidad</option>
                        <option value="Otro" {{ old('tipo_select') == 'Otro' ? 'selected' : '' }}>Otro (Especificar)</option>
                    </select>
                </div>

                <div class="mb-3" id="div_tipo_otro" style="display: none;">
                    <label>Especifique el Tipo <span class="text-danger">*</span></label>
                    <input type="text" name="tipo_otro" id="tipo_otro" class="form-control" value="{{ old('tipo_otro') }}" placeholder="Ej: Permiso por luto, Calamidad doméstica...">
                </div>

                <input type="hidden" name="tipo" id="tipo_final" value="{{ old('tipo', 'Vacaciones') }}">

                <div class="mb-3">
                    <label>Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="4"></textarea>
                </div>

                <div class="mb-3">
                    <label>Fecha</label>
                    <input type="date" name="fecha" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Estado</label>
                    <select name="estado" class="form-control" required>
                        <option value="pendiente" selected>Pendiente</option>
                        <option value="aprobado">Aprobado</option>
                        <option value="rechazado">Rechazado</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Archivos Adjuntos</label>
                    <input type="file" name="archivos[]" class="form-control" multiple>
                </div>

                <button class="btn btn-orange">
                    <i class="fas fa-save"></i> Guardar
                </button>

            </form>

        </div>
    </div>

</div>

@endsection

@section('js')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const empleados = @json($empleados);

    const input  = document.getElementById("buscarEmpleado");
    const hidden = document.getElementById("empleado_id");
    const lista  = document.getElementById("listaEmpleados");
    const errDiv = document.getElementById("empleadoError");
    const form   = input.closest('form');

    if (hidden.value) {
        const emp = empleados.find(e => e.id == hidden.value);
        if (emp) input.value = emp.persona.nombres + ' ' + emp.persona.apellidos + ' - ' + emp.persona.numero_documento;
    }

    input.addEventListener("input", function () {
        let valor = this.value.toLowerCase().trim();
        lista.innerHTML = "";
        hidden.value = "";
        errDiv.style.display = "none";

        if (valor.length < 1) { lista.style.display = "none"; return; }

        let filtrados = empleados.filter(e => {
            let nombre = (e.persona.nombres + " " + e.persona.apellidos).toLowerCase();
            let cedula = (e.persona.numero_documento || "").toLowerCase();
            return nombre.includes(valor) || cedula.includes(valor);
        });

        if (filtrados.length === 0) { lista.style.display = "none"; return; }

        filtrados.forEach(emp => {
            let nombre = emp.persona.nombres + " " + emp.persona.apellidos;
            let item = document.createElement("button");
            item.type = "button";
            item.className = "list-group-item list-group-item-action";
            item.innerHTML = `<strong>${nombre}</strong><br><small>${emp.persona.numero_documento}</small>`;
            item.onclick = function () {
                input.value  = nombre + " - " + emp.persona.numero_documento;
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
        if (!input.contains(e.target) && !lista.contains(e.target)) lista.style.display = "none";
    });

    // --- LÓGICA TIPO DE SOLICITUD (OTRO) ---
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

    updateTipo(); // Init

    form.addEventListener("submit", function (e) {
        if (!hidden.value) {
            e.preventDefault();
            input.classList.add("is-invalid");
            errDiv.style.display = "block";
            input.focus();
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

});
</script>
@endsection