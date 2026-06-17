@extends('adminlte::page')

@section('content')

<div class="container-fluid px-2">

    <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
        <h2 class="fw-bold">✏ Editar Solicitud</h2>

        <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="{{ route('admin.solicitudes.update', $solicitud->id) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- EMPLEADO --}}
                <div class="mb-3">
                    <label>Empleado</label>
                    <select name="empleado_id" class="form-control" required>
                        @foreach($empleados as $emp)
                            <option value="{{ $emp->id }}"
                                {{ $solicitud->empleado_id == $emp->id ? 'selected' : '' }}>
                                {{ $emp->persona->nombres ?? '' }}
                                {{ $emp->persona->apellidos ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- TIPO --}}
                <div class="mb-3">
                    <label>Tipo de Solicitud <span class="text-danger">*</span></label>
                    @php
                        $tiposPredefinidos = ['Vacaciones', 'Incapacidad'];
                        $esOtro = !in_array($solicitud->tipo, $tiposPredefinidos);
                    @endphp
                    <select name="tipo_select" id="tipo_select" class="form-control" required>
                        <option value="Vacaciones" {{ $solicitud->tipo == 'Vacaciones' ? 'selected' : '' }}>Vacaciones</option>
                        <option value="Solicitud" {{ $solicitud->tipo == 'Solicitud' ? 'selected' : '' }}>Solicitud</option>
                        <option value="Ausentismo" {{ $solicitud->tipo == 'Ausentismo' ? 'selected' : '' }}>Ausentismo</option>
                        <option value="Otro" {{ $esOtro ? 'selected' : '' }}>Otro (Especificar)</option>
                    </select>
                </div>

                <div class="mb-3" id="div_tipo_otro" style="display: {{ $esOtro ? 'block' : 'none' }};">
                    <label>Especifique el Tipo <span class="text-danger">*</span></label>
                    <input type="text" name="tipo_otro" id="tipo_otro" class="form-control" 
                           value="{{ $esOtro ? $solicitud->tipo : '' }}" placeholder="Ej: Permiso por luto...">
                </div>

                <input type="hidden" name="tipo" id="tipo_final" value="{{ $solicitud->tipo }}">

                {{-- DESCRIPCIÓN --}}
                <div class="mb-3">
                    <label>Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="4">{{ $solicitud->descripcion }}</textarea>
                </div>

                {{-- ESTADO --}}
                <div class="mb-3">
                    <label>Estado</label>
                    <select name="estado" class="form-control" required>
                        <option value="pendiente" {{ $solicitud->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="aprobado" {{ $solicitud->estado == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                        <option value="rechazado" {{ $solicitud->estado == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                    </select>
                </div>

                {{-- FECHA --}}
                <div class="mb-3">
                    <label>Fecha</label>
                    <input type="date"
                           name="fecha"
                           class="form-control"
                           value="{{ $solicitud->fecha }}"
                           required>
                </div>

                {{-- ARCHIVOS ACTUALES --}}
                <div class="col-12 mt-4">
                    <h5 class="fw-bold mb-3" style="color: var(--text-main);">
                        <i class="fas fa-folder-open text-primary me-2"></i> Archivos Adjuntos
                    </h5>

                    <div class="mb-3">
                        @forelse($solicitud->documentos as $doc)
                            <div id="doc-{{ $doc->id }}"
                                 class="d-flex align-items-center justify-content-between mb-2 p-2 border rounded"
                                 style="max-width: 450px;">

                                <a href="{{ route('admin.documentos.view', $doc->id) }}"
                                   target="_blank"
                                   class="text-truncate"
                                   title="{{ $doc->nombre_original }}">
                                    📎 {{ $doc->nombre_original }}
                                </a>

                                <button type="button"
                                        class="btn btn-sm btn-danger"
                                        onclick="removeExistingDoc({{ $doc->id }})">
                                    Eliminar
                                </button>
                            </div>
                        @empty
                            <span class="text-muted">Sin archivos</span>
                        @endforelse
                    </div>

                    {{-- OCULTOS --}}
                    <div id="hiddenDeleteInputs"></div>

                    {{-- NUEVOS --}}
                    <label class="form-label mt-3">Anexar nuevos documentos</label>
                    <input type="file" name="archivos[]" class="form-control" multiple>
                </div>

                <button class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar
                </button>

            </form>

        </div>
    </div>

</div>

{{-- DELETE AJAX --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
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
});

function removeExistingDoc(id) {
    if (!confirm('¿Eliminar este archivo?')) return;

    const element = document.getElementById('doc-' + id);
    if (element) element.remove();

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'eliminar_documentos[]';
    input.value = id;

    document.getElementById('hiddenDeleteInputs').appendChild(input);
}
</script>
@endsection