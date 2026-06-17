@extends('adminlte::page')

@section('title', 'Nuevo Registro - Planta Personal SENA')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="font-size:1.75rem;">Registrar Planta Personal SENA</h2>
        <p class="text-muted mb-0">Vincule un empleado al registro de Planta Personal SENA.</p>
    </div>
    <a href="{{ route('admin.planta_personal_sena.index') }}" class="btn btn-light border px-4 shadow-sm" style="border-radius:10px;">
        <i class="fas fa-arrow-left me-2"></i> Volver
    </a>
</div>
@stop

@section('content')
<div class="row justify-content-center pb-5">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm" style="border-radius:20px;">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.planta_personal_sena.store') }}" method="POST">
                    @csrf

                    {{-- EMPLEADO AUTOCOMPLETE --}}
                    <div class="mb-4 position-relative text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color:#64748b; letter-spacing:.5px;">
                            Empleado <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light" style="border-radius:12px 0 0 12px;">
                                <i class="fas fa-user text-muted"></i>
                            </span>
                            <input type="text" id="buscarEmpleado"
                                   class="form-control border-0 bg-light py-2 px-3 shadow-none"
                                   placeholder="Escriba nombre o número de documento..."
                                   autocomplete="off"
                                   style="border-radius:0 12px 12px 0; height:50px;">
                        </div>
                        <input type="hidden" name="empleado_id" id="empleado_id" value="{{ old('empleado_id') }}">
                        <div id="listaEmpleados"
                             class="list-group position-absolute w-100 shadow-lg border-0 mt-1"
                             style="z-index:1050; display:none; max-height:250px; overflow-y:auto; border-radius:12px;"></div>
                        <div id="empleadoError" class="text-danger small mt-2 ps-1" style="display:none;">
                            <i class="fas fa-exclamation-circle me-1"></i> Debe seleccionar un empleado de la lista.
                        </div>
                        @error('empleado_id')
                            <div class="text-danger small mt-2 ps-1"><i class="fas fa-exclamation-circle me-1"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4" style="opacity:.1;">

                    {{-- FECHA REPORTE --}}
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color:#64748b; letter-spacing:.5px;">
                            Fecha de Reporte <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="fecha_reporte"
                               class="form-control border-0 bg-light py-2 px-3 shadow-none @error('fecha_reporte') is-invalid @enderror"
                               value="{{ old('fecha_reporte', date('Y-m-d')) }}"
                               required
                               style="border-radius:12px; height:50px;">
                        @error('fecha_reporte')
                            <div class="invalid-feedback ps-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- OBSERVACIONES --}}
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color:#64748b; letter-spacing:.5px;">
                            Observaciones (Opcional)
                        </label>
                        <textarea name="observaciones" rows="4"
                                  class="form-control border-0 bg-light py-3 px-3 shadow-none"
                                  placeholder="Describa cualquier detalle relevante del registro..."
                                  style="border-radius:15px;">{{ old('observaciones') }}</textarea>
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-orange text-white py-3 fw-bold shadow-sm"
                                style="border-radius:15px; font-size:1.1rem; letter-spacing:.5px;">
                            <i class="fas fa-save me-2"></i> GUARDAR REGISTRO
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
    const input   = document.getElementById("buscarEmpleado");
    const hidden  = document.getElementById("empleado_id");
    const lista   = document.getElementById("listaEmpleados");
    const errDiv  = document.getElementById("empleadoError");
    const form    = input.closest('form');

    if (hidden.value) {
        const emp = empleados.find(e => e.id == hidden.value);
        if (emp) input.value = emp.persona.nombres + ' ' + emp.persona.apellidos + ' - ' + (emp.persona.numero_documento || '');
    }

    input.addEventListener("input", function () {
        const val = this.value.toLowerCase().trim();
        lista.innerHTML = "";
        hidden.value = "";
        errDiv.style.display = "none";

        if (!val) { lista.style.display = "none"; return; }

        const filtrados = empleados.filter(e => {
            const nombre = (e.persona.nombres + " " + e.persona.apellidos).toLowerCase();
            const cedula = (e.persona.numero_documento || "").toLowerCase();
            return nombre.includes(val) || cedula.includes(val);
        });

        if (!filtrados.length) { lista.style.display = "none"; return; }

        filtrados.forEach(emp => {
            const nombre = emp.persona.nombres + " " + emp.persona.apellidos;
            const item = document.createElement("button");
            item.type = "button";
            item.className = "list-group-item list-group-item-action border-0 py-3 px-4";
            item.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width:40px;height:40px;background-color:rgba(255,106,0,.1);color:#ff6a00;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <strong class="d-block">${nombre}</strong>
                        <small class="text-muted">${emp.persona.numero_documento || 'N/A'} | ${emp.cargo || 'Sin cargo'}</small>
                    </div>
                </div>`;
            item.onclick = () => {
                input.value = nombre + " - " + (emp.persona.numero_documento || '');
                hidden.value = emp.id;
                lista.style.display = "none";
                errDiv.style.display = "none";
                input.classList.remove("is-invalid");
            };
            lista.appendChild(item);
        });
        lista.style.display = "block";
    });

    document.addEventListener("click", e => {
        if (!input.contains(e.target) && !lista.contains(e.target)) lista.style.display = "none";
    });

    form.addEventListener("submit", e => {
        if (!hidden.value) {
            e.preventDefault();
            input.classList.add("is-invalid");
            errDiv.style.display = "block";
            input.scrollIntoView({ behavior:'smooth', block:'center' });
        }
    });
});
</script>
@stop

@section('css')
<style>
.btn-orange { background-color:#ff6a00; border:none; color:#fff; transition:all .3s; }
.btn-orange:hover { background-color:#e65c00; color:#fff; transform:translateY(-2px); box-shadow:0 5px 15px rgba(255,106,0,.3); }
.list-group-item-action:hover { background-color:#fdf2f0 !important; color:#ff6a00 !important; }
</style>
@endsection
