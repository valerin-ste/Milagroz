@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">
            Actualizar Etapa Precontractual
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">
            Candidato: {{ $etapa_precontractual->persona->nombres }} {{ $etapa_precontractual->persona->apellidos }}
        </p>
    </div>
    <a href="{{ route('admin.etapa_precontractual.index') }}" class="btn btn-light-custom px-4">
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
                    <h5 class="card-title" style="color: var(--primary-blue);">
                        <div class="d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); border-radius: 10px;">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        Detalles de la Etapa
                    </h5>
                </div>

                <div class="card-body px-4 pb-4 pt-2">
                    <form action="{{ route('admin.etapa_precontractual.update', $etapa_precontractual->id) }}"
                          method="POST"
                          enctype="multipart/form-data">

                        @csrf
                        @method('PUT')

                        <div class="row g-4">

                            {{-- CANDIDATO (readonly) --}}
                            <div class="col-md-12">
                                <label class="form-label">Candidato</label>
                                <input type="text" class="form-control bg-light" readonly disabled
                                    value="{{ $etapa_precontractual->persona->nombres }} {{ $etapa_precontractual->persona->apellidos }} - {{ $etapa_precontractual->persona->numero_documento }}">
                            </div>

                            {{-- ESTADO --}}
                            <div class="col-md-6">
                                <label class="form-label">Estado <span class="text-danger">*</span></label>
                                <select name="estado" class="form-select" required>
                                    <option value="0" {{ $etapa_precontractual->estado == 0 ? 'selected' : '' }}>En Proceso</option>
                                    <option value="1" {{ $etapa_precontractual->estado == 1 ? 'selected' : '' }}>Aprobado</option>
                                    <option value="2" {{ $etapa_precontractual->estado == 2 ? 'selected' : '' }}>Rechazado</option>
                                </select>
                            </div>

                            {{-- FECHA REGISTRO (readonly) --}}
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Registro</label>
                                <input type="date"
                                    name="fecha_registro"
                                    class="form-control"
                                    value="{{ old('fecha_registro', $etapa_precontractual->fecha_registro) }}">
                            </div>

                            {{-- DOCUMENTOS --}}
                            <div class="col-12 mt-5">
                                <h5 class="fw-bold mb-3" style="color: var(--text-main);">
                                    <i class="fas fa-folder-open text-primary me-2"></i> Documentos Adjuntos
                                </h5>

                                {{-- ARCHIVOS EXISTENTES --}}
                                @if($etapa_precontractual->documentos->count() > 0)
                                    <h6 class="text-muted fw-bold mb-2 small text-uppercase">Archivos Existentes</h6>
                                    <div class="file-list mb-4" id="existingFilesList">
                                        @foreach($etapa_precontractual->documentos as $doc)
                                            <div class="file-card" id="existing-doc-{{ $doc->id }}">
                                                <div class="file-details">
                                                    @php
                                                        $ext = strtolower(pathinfo($doc->ruta, PATHINFO_EXTENSION));
                                                        $iconClass = 'fa-file-alt text-secondary';
                                                        if($ext == 'pdf') $iconClass = 'fa-file-pdf text-danger';
                                                        elseif(in_array($ext, ['jpg','jpeg','png'])) $iconClass = 'fa-file-image text-primary';
                                                        elseif(in_array($ext, ['doc','docx'])) $iconClass = 'fa-file-word text-info';
                                                    @endphp
                                                    <i class="fas {{ $iconClass }} file-icon"></i>
                                                    <div class="file-info">
                                                        <a href="{{ Storage::url($doc->ruta) }}" target="_blank" class="file-name text-decoration-none" title="{{ $doc->nombre_original }}">{{ $doc->nombre_original }}</a>
                                                        <span class="file-size text-primary"><i class="fas fa-external-link-alt"></i> Ver archivo</span>
                                                    </div>
                                                </div>
                                                <button type="button" class="file-remove" onclick="removeExistingDoc({{ $doc->id }})" title="Eliminar archivo">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div id="hiddenDeleteInputs"></div>
                                @endif

                                {{-- NUEVOS ARCHIVOS --}}
                                <h6 class="text-muted fw-bold mb-2 mt-4 small text-uppercase">Subir Nuevos Archivos</h6>
                                <p class="text-muted mb-3 small">
                                    Formatos aceptados: PDF, Word, JPG, PNG. Max: 10MB por archivo.
                                </p>

                                <div class="file-drop-area" id="dropArea">
                                    <i class="fas fa-cloud-upload-alt file-drop-area-icon"></i>
                                    <span class="file-drop-area-text">Arrastra y suelta nuevos archivos aquí</span>
                                    <span class="file-drop-area-hint">o haz clic para explorar en tu computadora</span>
                                    <input type="file" name="documentos[]" id="fileInput" class="file-input-hidden" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                </div>

                                <div class="file-list" id="fileList"></div>
                            </div>

                        </div>

                        {{-- BOTONES --}}
                        <div class="d-flex justify-content-end gap-3 mt-5 border-top pt-4">
                            <a href="{{ route('admin.etapa_precontractual.index') }}" class="btn btn-light-custom px-4">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-orange px-5">
                                <i class="fas fa-save me-2 pb-1"></i> Confirmar Actualización
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
        const dropArea = document.getElementById("dropArea");
        const fileInput = document.getElementById("fileInput");
        const fileList = document.getElementById("fileList");
        let selectedFiles = new DataTransfer();

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.add('dragover'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragover'), false);
        });

        dropArea.addEventListener('drop', e => handleFiles(e.dataTransfer.files), false);
        dropArea.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', function() { handleFiles(this.files); });

        function handleFiles(files) {
            [...files].forEach(file => {
                if(file.size > 10 * 1024 * 1024) {
                    alert('El archivo ' + file.name + ' supera los 10MB permitidos.');
                    return;
                }
                selectedFiles.items.add(file);
            });
            updateDOM();
        }

        function updateDOM() {
            fileInput.files = selectedFiles.files;
            fileList.innerHTML = '';

            [...selectedFiles.files].forEach((file, index) => {
                const size = (file.size / 1024 / 1024).toFixed(2);
                const fileExt = file.name.split('.').pop().toLowerCase();

                let iconClass = 'fa-file-alt text-secondary';
                if (fileExt === 'pdf') iconClass = 'fa-file-pdf text-danger';
                else if (['jpg', 'jpeg', 'png'].includes(fileExt)) iconClass = 'fa-file-image text-primary';
                else if (['doc', 'docx'].includes(fileExt)) iconClass = 'fa-file-word text-info';

                const fileCard = document.createElement('div');
                fileCard.className = 'file-card';
                fileCard.innerHTML = `
                    <div class="file-details">
                        <i class="fas ${iconClass} file-icon"></i>
                        <div class="file-info">
                            <span class="file-name" title="${file.name}">${file.name}</span>
                            <span class="file-size">${size} MB</span>
                        </div>
                    </div>
                    <button type="button" class="file-remove" onclick="removeFile(${index})" title="Eliminar archivo">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                fileList.appendChild(fileCard);
            });
        }

        window.removeFile = function (index) {
            let newSelectedFiles = new DataTransfer();
            let filesArray = Array.from(selectedFiles.files);
            filesArray.splice(index, 1);
            filesArray.forEach(file => newSelectedFiles.items.add(file));
            selectedFiles = newSelectedFiles;
            updateDOM();
        };

        window.removeExistingDoc = function(id) {
            if (confirm('¿Está seguro de eliminar este archivo guardado?')) {
                document.getElementById('existing-doc-' + id).style.display = 'none';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'eliminar_documentos[]';
                input.value = id;
                document.getElementById('hiddenDeleteInputs').appendChild(input);
            }
        };
    });
</script>
@endsection

@push('css')
<style>
.file-drop-area {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    cursor: pointer;
    background: #f8fafc;
    transition: border-color 0.2s ease, background 0.2s ease;
}

.file-drop-area:hover,
.file-drop-area.dragover {
    border-color: var(--primary-blue, #13b6ec);
    background: #f0f9ff;
}

.file-drop-area-icon {
    font-size: 42px;
    color: #13b6ec;
    display: block;
    margin-bottom: 10px;
}

.file-drop-area-text {
    display: block;
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 4px;
}

.file-drop-area-hint {
    display: block;
    color: #94a3b8;
    font-size: 0.875rem;
}

.file-input-hidden {
    display: none;
}

.file-list {
    margin-top: 12px;
}

.file-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 8px;
    background: #fff;
    transition: box-shadow 0.2s;
}

.file-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
}

.file-details {
    display: flex;
    align-items: center;
    gap: 12px;
    overflow: hidden;
}

.file-icon {
    font-size: 1.4rem;
    flex-shrink: 0;
}

.file-info {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.file-name {
    font-size: 0.875rem;
    font-weight: 500;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 300px;
}

.file-size {
    font-size: 0.78rem;
    color: #64748b;
}

.file-remove {
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 6px;
    border-radius: 6px;
    transition: color 0.2s, background 0.2s;
    flex-shrink: 0;
}

.file-remove:hover {
    color: #ef4444;
    background: #fef2f2;
}
</style>
@endpush