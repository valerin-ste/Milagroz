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
                        <h5 class="fw-bold" style="color: var(--primary-blue);">
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

                                    <option value="Ingreso"
                                        {{ old('tipo_documento') == 'Ingreso' ? 'selected' : '' }}>
                                        Ingreso
                                    </option>

                                    <option value="Periódico"
                                        {{ old('tipo_documento') == 'Periódico' ? 'selected' : '' }}>
                                        Periódico
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

                                <input type="file"
                                       name="documentos[]"
                                       class="form-control"
                                       multiple>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BOTONES --}}
        <div class="d-flex justify-content-center gap-3 mt-4 mb-5 pb-4 px-2">
            <a href="{{ route('admin.seguridad_salud_trabajo.index') }}"
               class="btn btn-light-custom px-4 border">
                Cancelar
            </a>

            <button type="submit" class="btn btn-primary px-5">
                <i class="fas fa-save me-2"></i>
                Guardar Registro
            </button>
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
        }
    });

});
</script>
@endsection