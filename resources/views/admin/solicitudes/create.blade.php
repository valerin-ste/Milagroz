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
                    <label>Empleado</label>

                    <input type="text"
                           id="buscarEmpleado"
                           class="form-control"
                           placeholder="Escriba nombre o cédula..."
                           autocomplete="off"
                           required>

                    <input type="hidden" name="empleado_id" id="empleado_id" required>

                    <div id="listaEmpleados"
                         class="list-group position-absolute w-100"
                         style="z-index: 999; display:none; max-height: 250px; overflow-y: auto;">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Tipo de Solicitud</label>
                    <input type="text" name="tipo" class="form-control" required>
                </div>

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
                    <label>Archivo</label>
                    <input type="file" name="archivo" class="form-control">
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

    const input = document.getElementById("buscarEmpleado");
    const hidden = document.getElementById("empleado_id");
    const lista = document.getElementById("listaEmpleados");

    input.addEventListener("input", function () {

        let valor = this.value.toLowerCase().trim();
        lista.innerHTML = "";

        if (valor.length < 1) {
            lista.style.display = "none";
            hidden.value = "";
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

            item.innerHTML = `
                <strong>${nombre}</strong><br>
                <small>${emp.persona.numero_documento}</small>
            `;

            item.onclick = function () {
                input.value = nombre + " - " + emp.persona.numero_documento;
                hidden.value = emp.id;
                lista.style.display = "none";
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

});
</script>
@endsection