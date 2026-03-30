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
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-body p-5">
                <form action="{{ route('admin.evaluaciones_desempeno.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- 🔥 EMPLEADO AUTOCOMPLETE --}}
                    <div class="mb-4 position-relative">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                            Empleado <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               id="buscarEmpleado"
                               class="form-control border-light bg-light py-2 px-3 shadow-none"
                               placeholder="Escriba nombre o cédula..."
                               autocomplete="off"
                               required>

                        <input type="hidden" name="empleado_id" id="empleado_id" required>

                        <div id="listaEmpleados"
                             class="list-group position-absolute w-100"
                             style="z-index: 999; display:none; max-height: 250px; overflow-y: auto;">
                        </div>
                    </div>

                    <div class="row g-4">
                        {{-- CALIFICACIÓN --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Calificación (1-10) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="calificacion" min="1" max="10" step="1"
                                   class="form-control border-light bg-light py-2 px-3 shadow-none"
                                   value="{{ old('calificacion') }}" required>
                        </div>

                        {{-- FECHA --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Fecha de Evaluación <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="fecha"
                                   class="form-control border-light bg-light py-2 px-3 shadow-none"
                                   value="{{ old('fecha', date('Y-m-d')) }}" required>
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

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-orange text-white py-3 fw-bold rounded shadow-sm">
                            <i class="fas fa-save me-2"></i> GUARDAR EVALUACIÓN
                        </button>
                    </div>

                </form>
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
@stop