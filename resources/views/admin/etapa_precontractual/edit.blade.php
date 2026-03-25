@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <h2 class="fw-bold mb-1 text-center w-100" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">
        Actualizar Etapa Precontractual
    </h2>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <form action="{{ route('admin.etapa_precontractual.update', $etapa_precontractual->id) }}" 
                          method="POST" 
                          enctype="multipart/form-data">

                        @csrf
                        @method('PUT')

                        {{-- DETALLES --}}
                        <h5 class="fw-bold mb-4 text-primary">
                            <i class="fas fa-file-alt me-2"></i>Detalles de la Etapa
                        </h5>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Empleado</label>
                                <input type="text" class="form-control" readonly
                                    value="{{ $etapa_precontractual->persona->nombres }} {{ $etapa_precontractual->persona->apellidos }} - {{ $etapa_precontractual->persona->numero_documento }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Estado *</label>
                                <select name="estado" class="form-control" required>
                                    <option value="0" {{ $etapa_precontractual->estado == 0 ? 'selected' : '' }}>En Proceso</option>
                                    <option value="1" {{ $etapa_precontractual->estado == 1 ? 'selected' : '' }}>Aprobado</option>
                                    <option value="2" {{ $etapa_precontractual->estado == 2 ? 'selected' : '' }}>Rechazado</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Fecha</label>
                                <input type="date" class="form-control" readonly
                                    value="{{ $etapa_precontractual->fecha_registro }}">
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- DOCUMENTOS --}}
                        <h5 class="fw-bold mb-3 text-primary">
                            <i class="fas fa-folder-open me-2"></i>Documentos Adjuntos
                        </h5>

                        {{-- EXISTENTES --}}
                        @if($etapa_precontractual->documentos->count() > 0)
                            <h6 class="text-muted small text-uppercase mb-2">Archivos existentes</h6>

                            <div class="border rounded p-3 mb-3">
                                @foreach($etapa_precontractual->documentos as $doc)
                                    <div class="d-flex justify-content-between align-items-center border-bottom py-2" id="existing-doc-{{ $doc->id }}">
                                        <div>
                                            <i class="fas fa-file me-2 text-primary"></i>
                                            <a href="{{ Storage::url($doc->ruta) }}" target="_blank">
                                                {{ $doc->nombre_original }}
                                            </a>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-light text-danger" onclick="removeExistingDoc({{ $doc->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <div id="hiddenDeleteInputs"></div>
                        @endif

                        {{-- NUEVOS --}}
                        <h6 class="text-muted small text-uppercase mb-2">Subir nuevos archivos</h6>
                        <div class="border border-dashed rounded text-center p-4 mb-3">
                            <i class="fas fa-cloud-upload-alt fa-2x mb-2 text-muted"></i>
                            <p class="mb-1">Arrastra y suelta archivos aquí</p>
                            <input type="file" name="documentos[]" multiple class="form-control mt-2">
                        </div>

                        <hr class="my-4">

                        {{-- BOTONES --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.etapa_precontractual.index') }}" class="btn btn-light">
                                Cancelar
                            </a>
                            <button type="submit" class="btn text-white px-4" style="background-color: #f97316; border: none;">
                                <i class="fas fa-save me-2"></i>Confirmar Actualización
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
function removeExistingDoc(id) {
    if (confirm('¿Eliminar este archivo?')) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'eliminar_documentos[]';
        input.value = id;
        document.getElementById('hiddenDeleteInputs').appendChild(input);
        document.getElementById('existing-doc-' + id).remove();
    }
}
</script>
@endsection