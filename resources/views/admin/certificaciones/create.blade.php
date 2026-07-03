@extends('adminlte::page')

@section('title', 'Nueva Certificación')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">Registrar Certificación</h2>
        <p class="text-muted mb-0">Añada una nueva certificación oficial al perfil del empleado.</p>
    </div>
    <a href="{{ route('admin.certificaciones.index') }}" class="btn btn-light border px-4 shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Volver
    </a>
</div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-body p-5">
                <form action="{{ route('admin.certificaciones.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{--  EMPLEADO AUTOCOMPLETE --}}
                    <div class="mb-4 position-relative text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                            Empleado <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               id="buscarEmpleado"
                               class="form-control border-light bg-light py-2 px-3 shadow-none"
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
                        @error('empleado_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-4 mb-4">
                        {{-- NOMBRE DE LA CERTIFICACION --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Nombre de la Certificación <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nombre_certificacion" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('nombre_certificacion') is-invalid @enderror" 
                                   placeholder="Ej: Certificación OSHAS, Trabajo Seguro en Alturas..."
                                   value="{{ old('nombre_certificacion') }}" required>
                            @error('nombre_certificacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TIPO DE CERTIFICACION --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Tipo de Certificación
                            </label>
                            <input type="text" name="tipo_certificacion" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('tipo_certificacion') is-invalid @enderror" 
                                   placeholder="Ej: Técnica, Profesional, Legal..."
                                   value="{{ old('tipo_certificacion') }}">
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        {{-- INSTITUCIÓN --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Institución Emisora <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="institucion" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('institucion') is-invalid @enderror" 
                                   placeholder="Ej: SENA, ICONTEC, Universidad..."
                                   value="{{ old('institucion') }}" required>
                        </div>

                        {{-- CODIGO --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Código del Certificado
                            </label>
                            <input type="text" name="codigo_certificado" 
                                   class="form-control border-light bg-light py-2 px-3 shadow-none @error('codigo_certificado') is-invalid @enderror" 
                                   placeholder="Ej: CERT-12345..."
                                   value="{{ old('codigo_certificado') }}">
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        {{-- FECHA EXPEDICIÓN --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Fecha de Expedición <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="fecha_expedicion"
                                   class="form-control border-light bg-light py-2 px-3 shadow-none"
                                   value="{{ old('fecha_expedicion', date('Y-m-d')) }}" required>
                        </div>

                        {{-- FECHA VENCIMIENTO --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                Fecha de Vencimiento (Opcional)
                            </label>
                            <input type="date" name="fecha_vencimiento"
                                   class="form-control border-light bg-light py-2 px-3 shadow-none"
                                   value="{{ old('fecha_vencimiento') }}">
                            <small class="text-muted">Deje en blanco si no tiene fecha de vencimiento.</small>
                        </div>
                    </div>

                    {{-- OBSERVACIONES --}}
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                            Observaciones (Opcional)
                        </label>
                        <textarea name="observaciones" rows="3" 
                                  class="form-control border-light bg-light py-2 px-3 shadow-none" 
                                  placeholder="Detalles adicionales sobre la certificación...">{{ old('observaciones') }}</textarea>
                    </div>

                    {{-- ARCHIVO --}}
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                            Soporte / Archivo (PDF, JPG, PNG) - Opcional
                        </label>
                        <div class="p-3 border rounded border-dashed text-center bg-light-soft">
                            <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png"
                                   class="form-control border-light bg-light py-2 px-3 shadow-none">
                            <small class="text-muted mt-2 d-block">
                                Seleccione el archivo soporte (Máx. 5MB).
                            </small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-5">
                        <a href="{{ route('admin.certificaciones.index') }}"
                        class="btn btn-light border px-4 fw-bold shadow-sm"
                        style="border-radius:15px; font-size:1.1rem; letter-spacing:0.5px;">
                            <i class="fas fa-times me-2"></i> CANCELAR
                        </a>

                        <button type="submit"
                                class="btn btn-orange text-white px-4 fw-bold shadow-sm"
                                style="border-radius:15px; font-size:1.1rem; letter-spacing:0.5px;">
                            <i class="fas fa-save me-2"></i> GUARDAR CERTIFICACIÓN
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

});
</script>
@stop

@section('css')
<style>
.bg-light-soft { background-color: #f8fafc; }
.border-dashed { border-style: dashed !important; border-width: 2px !important; border-color: #cbd5e1 !important; }
.btn-orange {
    background-color: #ff6a00;
    border: none;
    color: #fff;
}
.btn-orange:hover {
    background-color: #e65c00;
    color: #fff;
}
</style>
@endsection
