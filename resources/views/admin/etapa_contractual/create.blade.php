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

    <form action="{{ route('admin.etapa_contractual.store') }}" method="POST" enctype="multipart/form-data">
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

                            {{-- 🔥 EMPLEADO CON BUSCADOR --}}
                            <div class="col-md-12 position-relative">
                                <label class="form-label">Empleado / Candidato <span class="text-danger">*</span></label>

                                <input type="text"
                                       id="buscarEmpleado"
                                       class="form-control"
                                       placeholder="Escriba nombre o cédula...">

                                <input type="hidden" name="empleado_id" id="empleado_id" required>

                                <div id="listaEmpleados"
                                     class="list-group position-absolute w-100"
                                     style="z-index: 999; display:none; max-height: 250px; overflow-y: auto;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tipo de Contrato <span class="text-danger">*</span></label>
                                <select name="tipo_contrato" class="form-select" required>
                                    <option value="" disabled selected>Seleccione</option>
                                    <option value="Término Indefinido" {{ old('tipo_contrato') == 'Término Indefinido' ? 'selected' : '' }}>Término Indefinido</option>
                                    <option value="Término Fijo" {{ old('tipo_contrato') == 'Término Fijo' ? 'selected' : '' }}>Término Fijo</option>
                                    <option value="Prestación de Servicios" {{ old('tipo_contrato') == 'Prestación de Servicios' ? 'selected' : '' }}>Prestación de Servicios</option>
                                    <option value="Obra o Labor" {{ old('tipo_contrato') == 'Obra o Labor' ? 'selected' : '' }}>Obra o Labor</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Salario Base <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    <input type="number" step="0.01" name="salario" class="form-control" required value="{{ old('salario') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_inicio" class="form-control" required value="{{ old('fecha_inicio', now()->toDateString()) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Fecha de Fin (Opcional)</label>
                                <input type="date" name="fecha_fin" class="form-control" value="{{ old('fecha_fin') }}">
                            </div>

                            <div class="col-12 mt-5">
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

            item.innerHTML = `<strong>${nombre}</strong><br><small>${emp.persona.numero_documento}</small>`;

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
        if (!input.contains(e.target)) {
            lista.style.display = "none";
        }
    });

});
</script>
@endsection