@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">
            Editar Comunicación
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">
            Modifique los datos de la comunicación.
        </p>
    </div>

    <a href="{{ route('admin.comunicaciones.index') }}" class="btn btn-light-custom px-4">
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

    <div class="row g-4">
        <div class="col-lg-8 mx-auto">

            <div class="card h-100 border-0 shadow-sm">

                <div class="card-header pt-4 px-4 pb-3">
                    <h5 class="card-title d-flex align-items-center" style="color: var(--primary-blue);">
                        <div class="d-flex align-items-center justify-content-center me-3"
                             style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); border-radius: 10px;">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                        Detalles de la Comunicación
                    </h5>
                </div>

                <div class="card-body px-4 pb-4 pt-2">

                    <form action="{{ route('admin.comunicaciones.update', $comunicacion) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">

                            <div class="col-md-12">
                                <label class="form-label">Empleado</label>
                                <select name="empleado_id" class="form-select" required>
                                    <option value="">Seleccione</option>
                                    @foreach($empleados as $emp)
                                        <option value="{{ $emp->id }}"
                                            {{ $comunicacion->empleado_id == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->persona->nombres }} {{ $emp->persona->apellidos }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Asunto</label>
                                <input type="text" name="asunto" class="form-control"
                                       value="{{ $comunicacion->asunto }}" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Mensaje</label>
                                <textarea name="mensaje" class="form-control" rows="4">{{ $comunicacion->mensaje }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Fecha</label>
                                <input type="date" name="fecha" class="form-control"
                                       value="{{ $comunicacion->fecha }}" required>
                            </div>

                            {{-- ARCHIVOS --}}
                            <div class="col-12 mt-4">

                                <h5 class="fw-bold mb-3" style="color: var(--text-main);">
                                    <i class="fas fa-folder-open text-primary me-2"></i> Archivos Adjuntos
                                </h5>

                                {{-- ARCHIVOS ACTUALES --}}
                                <div class="mb-3">
                                    @forelse($comunicacion->documentos as $doc)

                                        <div id="doc-{{ $doc->id }}"
                                             class="d-flex align-items-center justify-content-between mb-2 p-2 border rounded"
                                             style="max-width: 450px;">

                                            <a href="{{ asset('storage/' . $doc->ruta) }}"
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

                        </div>

                        {{-- BOTONES --}}
                        <div class="d-flex justify-content-end gap-3 mt-5 border-top pt-4">
                            <a href="{{ route('admin.comunicaciones.index') }}" class="btn btn-light-custom px-4">
                                Cancelar
                            </a>

                            <button type="submit" class="btn btn-orange px-5">
                                <i class="fas fa-save me-2"></i> Actualizar Comunicación
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

<script>
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