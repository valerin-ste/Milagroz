@extends('adminlte::page')

@section('title', 'Editar Reporte Novedad')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">Editar Novedad de Nómina</h2>
        <p class="text-muted mb-0">Actualice la información del reporte o novedad.</p>
    </div>
    <a href="{{ route('admin.reportes-novedades-nomina.index') }}" class="btn btn-light border px-4 shadow-sm" style="border-radius: 10px;">
        <i class="fas fa-arrow-left me-2"></i> Volver
    </a>
</div>
@stop

@section('content')
<div class="row justify-content-center pb-5">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.reportes-novedades-nomina.update', $reporte->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- 🔥 EMPLEADO AUTOCOMPLETE --}}
                    <div class="mb-4 position-relative text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                            Empleado <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light" style="border-radius: 12px 0 0 12px;">
                                <i class="fas fa-user text-muted"></i>
                            </span>
                            <input type="text"
                                   id="buscarEmpleado"
                                   class="form-control border-0 bg-light py-2 px-3 shadow-none" 
                                   placeholder="Escriba nombre o documento para buscar..."
                                   autocomplete="off"
                                   style="border-radius: 0 12px 12px 0; height: 50px;">
                        </div>

                        <input type="hidden" name="empleado_id" id="empleado_id" value="{{ old('empleado_id', $reporte->empleado_id) }}">

                        <div id="listaEmpleados"
                             class="list-group position-absolute w-100 shadow-lg border-0 mt-1"
                             style="z-index: 1050; display:none; max-height: 250px; overflow-y: auto; border-radius: 12px;">
                        </div>

                        <div id="empleadoError" class="text-danger small mt-2 ps-1" style="display:none;">
                            <i class="fas fa-exclamation-circle me-1"></i> Debe seleccionar un empleado de la lista.
                        </div>
                        @error('empleado_id')
                            <div class="text-danger small mt-2 ps-1"><i class="fas fa-exclamation-circle me-1"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4" style="opacity: 0.1;">

                    <div class="row g-4 mb-4">
                        {{-- TIPO DE NOVEDAD --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                                Tipo de Novedad <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="tipo_novedad" 
                                   class="form-control border-0 bg-light py-2 px-3 shadow-none @error('tipo_novedad') is-invalid @enderror" 
                                   placeholder="Ej: Horas Extras, Incapacidad..."
                                   value="{{ old('tipo_novedad', $reporte->tipo_novedad) }}" 
                                   required
                                   style="border-radius: 12px; height: 50px;">
                            @error('tipo_novedad')
                                <div class="invalid-feedback ps-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- CANTIDAD --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                                Cantidad (Valor / Horas / Días) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="cantidad" 
                                   class="form-control border-0 bg-light py-2 px-3 shadow-none @error('cantidad') is-invalid @enderror" 
                                   placeholder="0"
                                   min="0"
                                   step="0.01"
                                   value="{{ old('cantidad', $reporte->cantidad) }}" 
                                   required
                                   style="border-radius: 12px; height: 50px;">
                            @error('cantidad')
                                <div class="invalid-feedback ps-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        {{-- FECHA --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                                Fecha de la Novedad <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="fecha"
                                   class="form-control border-0 bg-light py-2 px-3 shadow-none @error('fecha') is-invalid @enderror"
                                   value="{{ old('fecha', \Carbon\Carbon::parse($reporte->fecha)->format('Y-m-d')) }}" 
                                   required
                                   style="border-radius: 12px; height: 50px;">
                            @error('fecha')
                                <div class="invalid-feedback ps-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- OBSERVACIONES --}}
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                            Observaciones (Opcional)
                        </label>
                        <textarea name="observaciones" rows="3" 
                                  class="form-control border-0 bg-light py-3 px-3 shadow-none" 
                                  placeholder="Detalles adicionales sobre la novedad..."
                                  style="border-radius: 15px;">{{ old('observaciones', $reporte->observaciones) }}</textarea>
                    </div>

                    {{-- ARCHIVO --}}
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                            Soporte / Documento (Opcional)
                        </label>
                        
                        @if($reporte->archivo)
                            <div id="archivo-actual" class="p-3 bg-light rounded-4 mb-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file-alt text-primary me-2"></i>
                                    <span class="text-dark fw-bold">Archivo actual adjunto</span>
                                </div>
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="checkbox" name="eliminar_archivo" value="1" id="eliminar_archivo">
                                    <label class="form-check-label text-danger small fw-bold" for="eliminar_archivo">
                                        Eliminar archivo
                                    </label>
                                </div>
                            </div>
                        @endif

                        <div class="p-4 border-dashed rounded-4 text-center bg-light-soft position-relative">
                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                            <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                   class="form-control border-0 bg-transparent py-2 px-3 shadow-none"
                                   style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                            <div class="file-name-display text-muted small">
                                {{ $reporte->archivo ? 'Haz clic para reemplazar el archivo actual' : 'Haz clic para seleccionar o arrastra el archivo aquí' }}
                            </div>
                            <small class="text-muted d-block mt-1">Formatos: PDF, JPG, PNG, DOC (Máx. 5MB).</small>
                        </div>
                        @error('archivo')
                            <div class="text-danger small mt-2 ps-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-orange text-white py-3 fw-bold shadow-sm" style="border-radius: 15px; font-size: 1.1rem; letter-spacing: 0.5px;">
                            <i class="fas fa-save me-2"></i> ACTUALIZAR NOVEDAD
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
    const errDiv = document.getElementById("empleadoError");
    const form = input.closest('form');

    // Manejo de archivo visual
    const fileInput = document.querySelector('input[type="file"]');
    const fileDisplay = document.querySelector('.file-name-display');
    const checkEliminar = document.getElementById('eliminar_archivo');
    
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            fileDisplay.innerHTML = `<i class="fas fa-file-check text-success me-2"></i><strong>${this.files[0].name}</strong>`;
            fileDisplay.classList.add('text-dark');
            if (checkEliminar) {
                checkEliminar.checked = false;
            }
        }
    });

    if (hidden.value) {
        const emp = empleados.find(e => e.id == hidden.value);
        if (emp) {
            input.value = emp.persona.nombres + ' ' + emp.persona.apellidos + ' - ' + (emp.persona.numero_documento || '');
        } else {
            input.value = "{{ $reporte->empleado->persona->nombres ?? '' }} {{ $reporte->empleado->persona->apellidos ?? '' }} - {{ $reporte->empleado->persona->numero_documento ?? '' }}";
        }
    }

    input.addEventListener("input", function () {
        let valor = this.value.toLowerCase().trim();
        lista.innerHTML = "";
        hidden.value = "";
        errDiv.style.display = "none";

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
            item.className = "list-group-item list-group-item-action border-0 py-3 px-4";
            item.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <strong class="d-block">${nombre}</strong>
                        <small class="text-muted">${emp.persona.numero_documento || 'N/A'} | ${emp.cargo || 'Sin cargo'}</small>
                    </div>
                </div>
            `;
            item.onclick = function () {
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
.border-dashed { border-style: dashed !important; border-width: 2px !important; border-color: #cbd5e1 !important; transition: all 0.3s; }
.border-dashed:hover { border-color: #ff6a00 !important; background-color: rgba(255,106,0,0.02); }

.btn-orange {
    background-color: #ff6a00;
    border: none;
    color: #fff;
    transition: all 0.3s ease;
}
.btn-orange:hover {
    background-color: #e65c00;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255,106,0,0.3);
}

.list-group-item-action:hover {
    background-color: #fdf2f0 !important;
    color: #ff6a00 !important;
}

.bg-soft-primary { background-color: rgba(255,106,0,0.1); }
.text-primary { color: #ff6a00 !important; }

.rounded-4 { border-radius: 1rem !important; }
</style>
@endsection
