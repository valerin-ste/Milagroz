@extends('adminlte::page')

@section('title', 'Nueva Evaluación de Desempeño')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">Nueva Evaluación</h2>
        <p class="text-muted mb-0">Registre la calificación actual del empleado.</p>
    </div>
    <a href="{{ route('admin.evaluaciones_desempeno.index') }}" class="btn btn-light-custom px-4 border shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Volver
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

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-body p-5">
                    <form action="{{ route('admin.evaluaciones_desempeno.store') }}" method="POST" enctype="multipart/form-data" id="evaluacionForm">
                        @csrf

                        {{-- 🔥 EMPLEADO AUTOCOMPLETE --}}
                        <div class="mb-4 position-relative">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Empleado <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   id="buscarEmpleado"
                                   class="form-control border-light bg-light py-2 px-3 shadow-none"
                                   placeholder="Escriba nombre o cédula y seleccione de la lista..."
                                   autocomplete="off"
                                   value="{{ old('buscarEmpleado_text') }}"
                                   required>

                            <input type="hidden" name="empleado_id" id="empleado_id" value="{{ old('empleado_id') }}">

                            <div id="listaEmpleados"
                                 class="list-group position-absolute w-100 shadow-sm"
                                 style="z-index: 999; display:none; max-height: 250px; overflow-y: auto;">
                            </div>
                            <div id="empleadoError" class="text-danger mt-1" style="display:none; font-size: 0.875em;">
                                Debe seleccionar un empleado de la lista.
                            </div>
                        </div>

                        <div class="row g-4">
                            {{-- FECHA --}}
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                    Fecha de Evaluación <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="fecha"
                                       class="form-control border-light bg-light py-2 px-3 shadow-none"
                                       value="{{ old('fecha', date('Y-m-d')) }}" required>
                            </div>

                            {{-- ESTADO --}}
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                    Estado <span class="text-danger">*</span>
                                </label>
                                <select name="estado" class="form-control border-light bg-light py-2 px-3 shadow-none" required>
                                    <option value="1" {{ old('estado') == 1 ? 'selected' : '' }}>Pendiente</option>
                                    <option value="2" {{ old('estado') == 2 ? 'selected' : '' }}>En proceso</option>
                                    <option value="3" {{ old('estado', 3) == 3 ? 'selected' : '' }}>Finalizada</option>
                                </select>
                            </div>
                        </div>

                        {{-- OBSERVACIONES --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Observaciones / Comentarios
                            </label>
                            <textarea name="observaciones" rows="4"
                                      class="form-control border-light bg-light py-2 px-3 shadow-none"
                                      placeholder="Detalle el rendimiento del empleado...">{{ old('observaciones') }}</textarea>
                        </div>

                        {{-- ARCHIVOS --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Soportes / Documentos
                            </label>
                            <input type="file" name="archivos[]" multiple
                                   class="form-control border-light bg-light py-2 px-3 shadow-none">
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-5 mb-4">
                            <a href="{{ route('admin.evaluaciones_desempeno.index') }}"
                            class="btn btn-light border px-4 fw-bold shadow-sm"
                            style="border-radius:15px; font-size:1.1rem; letter-spacing:0.5px;">
                                <i class="fas fa-times me-2"></i> CANCELAR
                            </a>

                            <button type="submit"
                                    class="btn btn-orange text-white px-4 fw-bold shadow-sm"
                                    style="border-radius:15px; font-size:1.1rem; letter-spacing:0.5px;">
                                <i class="fas fa-save me-2"></i> GUARDAR EVALUACIÓN
                            </button>
                        </div>

                    </form>
                </div>
            </div>
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
    const errorMsg = document.getElementById("empleadoError");

    // Pre-seleccionar si viene por URL o por old() y no se llenó el input visual
    const urlParams = new URLSearchParams(window.location.search);
    const preId = hidden.value || urlParams.get('empleado_id');
    if (preId && !input.value) {
        const emp = empleados.find(e => e.id == preId);
        if (emp) {
            input.value = (emp.persona.nombres + " " + emp.persona.apellidos) + " - " + emp.persona.numero_documento;
            hidden.value = emp.id;
        }
    }

    input.addEventListener("input", function () {
        let valor = this.value.toLowerCase().trim();
        
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

            item.innerHTML = `
                <strong>${nombre}</strong><br>
                <small>${emp.persona.numero_documento}</small>
            `;

            item.onmousedown = function (e) {
                e.preventDefault();
                input.value = nombre + " - " + emp.persona.numero_documento;
                hidden.value = emp.id;
                errorMsg.style.display = "none";
                lista.style.display = "none";
            };

            lista.appendChild(item);
        });

        lista.style.display = "block";
    });

    input.addEventListener("change", function() {
        if (!hidden.value) {
            input.value = "";
        }
    });

    document.addEventListener("click", function (e) {
        if (!input.contains(e.target) && !lista.contains(e.target)) {
            lista.style.display = "none";
        }
    });

    const form = document.getElementById('evaluacionForm');
    form.addEventListener('submit', function(e) {
        if (!hidden.value) {
            e.preventDefault();
            errorMsg.style.display = "block";
            input.focus();
            return false;
        }
    });

});
</script>
@stop