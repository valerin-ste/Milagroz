@extends('adminlte::page')

@section('title', 'Nueva Formación')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">Registrar Formación</h2>
        <p class="text-muted mb-0">Añada una nueva certificación o curso al perfil del empleado.</p>
    </div>
    <a href="{{ route('admin.formaciones.index') }}" class="btn btn-light border px-4 shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Volver
    </a>
</div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-body p-5">
                <form action="{{ route('admin.formaciones.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- 🔥 EMPLEADO AUTOCOMPLETE --}}
                    <div class="mb-4 position-relative text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                            Empleado <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               id="buscarEmpleado"
                               class="form-control border-light bg-light py-2 px-3 shadow-none"
                               placeholder="Escriba nombre o cédula y seleccione de la lista..."
                               autocomplete="off">

                        <input type="hidden" name="empleado_id" id="empleado_id"
                               value="{{ old('empleado_id') }}">

                        <div id="listaEmpleados"
                             class="list-group position-absolute w-100 shadow-sm"
                             style="z-index: 999; display:none; max-height: 250px; overflow-y: auto;">
                        </div>

                        <div id="empleadoError" class="text-danger small mt-1" style="display:none;">
                            Debe seleccionar un empleado de la lista.
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        {{-- NOMBRE DEL CURSO --}}
                        <div class="col-md-12 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Nombre del Curso / Formación <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nombre_curso" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('nombre_curso') is-invalid @enderror" 
                                   placeholder="Ej: Curso de Alturas, Diplomado en RRHH..."
                                   value="{{ old('nombre_curso') }}" required>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        {{-- ESTADO CURSO --}}
                        <div class="col-md-3 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Estado <span class="text-danger">*</span>
                            </label>
                            <select name="estado_curso" class="form-control border-light bg-light py-2 px-3 shadow-none" required>
                                <option value="en curso" {{ old('estado_curso') == 'en curso' ? 'selected' : '' }}>En Curso</option>
                                <option value="finalizado" {{ old('estado_curso') == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                                <option value="pendiente" {{ old('estado_curso') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            </select>
                        </div>

                        {{-- TIPO DE FORMACIÓN (VENCE) --}}
                        <div class="col-md-3 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                ¿Vence? <span class="text-danger">*</span>
                            </label>
                            <select name="vence" id="vence" class="form-control border-light bg-light py-2 px-3 shadow-none" required>
                                <option value="1" {{ old('vence') == '1' ? 'selected' : '' }}>Sí, vence</option>
                                <option value="0" {{ old('vence') == '0' ? 'selected' : '' }}>No, permanente</option>
                            </select>
                        </div>

                        {{-- FECHA INICIO --}}
                        <div class="col-md-3 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Fecha Inicio <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="fecha_inicio"
                                   class="form-control border-light bg-light py-2 px-3 shadow-none"
                                   value="{{ old('fecha_inicio', date('Y-m-d')) }}" required>
                        </div>

                        {{-- FECHA FIN --}}
                        <div class="col-md-3 text-start" id="container_fecha_fin">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Fecha Fin <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="fecha_fin" id="fecha_fin"
                                   class="form-control border-light bg-light py-2 px-3 shadow-none"
                                   value="{{ old('fecha_fin') }}">
                        </div>
                    </div>

                    {{-- OBSERVACIONES --}}
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                            Observaciones (Opcional)
                        </label>
                        <textarea name="observaciones" rows="3" 
                                  class="form-control border-light bg-light py-2 px-3 shadow-none" 
                                  placeholder="Detalles adicionales sobre la formación...">{{ old('observaciones') }}</textarea>
                    </div>

                    {{-- ARCHIVO --}}
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                            Soporte / Diploma (PDF) - Opcional
                        </label>
                        <div class="p-4 border rounded border-dashed text-center bg-light-soft position-relative" style="transition: all 0.3s ease;">
                            <i class="fas fa-file-pdf fa-2x text-muted mb-2"></i>
                            <input type="file" name="documento" accept=".pdf"
                                   class="form-control border-light bg-white py-2 px-3 shadow-none" style="max-width: 400px; margin: 0 auto;">
                            <small class="text-muted mt-2 d-block">
                                Seleccione el archivo soporte en formato PDF (Máx. 2MB).
                            </small>
                        </div>
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-orange text-white py-3 fw-bold rounded shadow-sm">
                            <i class="fas fa-save me-2"></i> GUARDAR FORMACIÓN
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

    form.addEventListener("submit", function (e) {
        if (!hidden.value) {
            e.preventDefault();
            input.classList.add("is-invalid");
            errDiv.style.display = "block";
            input.focus();
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    // =========================
    // 🛡️ LÓGICA VENCIMIENTO
    // =========================
    const selectVence = document.getElementById('vence');
    const containerFechaFin = document.getElementById('container_fecha_fin');
    const inputFechaFin = document.getElementById('fecha_fin');
 
    function toggleVence() {
        if (selectVence.value == '1') {
            containerFechaFin.style.display = 'block';
            inputFechaFin.required = true;
        } else {
            containerFechaFin.style.display = 'none';
            inputFechaFin.required = false;
            inputFechaFin.value = '';
        }
    }
 
    selectVence.addEventListener('change', toggleVence);
    toggleVence(); // Inicializar

});
</script>
@stop

@section('css')
<style>
.bg-light-soft { background-color: #f8fafc; }
.border-dashed { border-style: dashed !important; border-width: 2px !important; border-color: #cbd5e1 !important; }
</style>
@endsection