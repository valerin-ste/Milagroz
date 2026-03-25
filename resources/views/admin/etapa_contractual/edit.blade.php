@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">Actualizar Contrato</h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Empleado: {{ $etapa_contractual->empleado->persona->nombres }} {{ $etapa_contractual->empleado->persona->apellidos }}</p>
    </div>
    <a href="{{ route('admin.etapa_contractual.index') }}" class="btn btn-light-custom px-4">
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
                            <i class="fas fa-file-contract"></i>
                        </div>
                        Detalles del Contrato
                    </h5>
                </div>

                <div class="card-body px-4 pb-4 pt-2">
                    <form action="{{ route('admin.etapa_contractual.update', $etapa_contractual) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label">Empleado</label>
                                <input type="text" class="form-control bg-light" value="{{ $etapa_contractual->empleado->persona->nombres }} {{ $etapa_contractual->empleado->persona->apellidos }} - {{ $etapa_contractual->empleado->persona->numero_documento }}" readonly disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tipo de Contrato <span class="text-danger">*</span></label>
                                <select name="tipo_contrato" class="form-select" required>
                                    <option value="Término Indefinido" {{ old('tipo_contrato', $etapa_contractual->tipo_contrato) == 'Término Indefinido' ? 'selected' : '' }}>Término Indefinido</option>
                                    <option value="Término Fijo" {{ old('tipo_contrato', $etapa_contractual->tipo_contrato) == 'Término Fijo' ? 'selected' : '' }}>Término Fijo</option>
                                    <option value="Prestación de Servicios" {{ old('tipo_contrato', $etapa_contractual->tipo_contrato) == 'Prestación de Servicios' ? 'selected' : '' }}>Prestación de Servicios</option>
                                    <option value="Obra o Labor" {{ old('tipo_contrato', $etapa_contractual->tipo_contrato) == 'Obra o Labor' ? 'selected' : '' }}>Obra o Labor</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Salario Base <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    <input type="number" step="0.01" name="salario" class="form-control" value="{{ old('salario', $etapa_contractual->salario) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_inicio" class="form-control" value="{{ old('fecha_inicio', $etapa_contractual->fecha_inicio) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Fecha de Fin (Opcional)</label>
                                <input type="date" name="fecha_fin" class="form-control" value="{{ old('fecha_fin', $etapa_contractual->fecha_fin) }}">
                            </div>

                            <div class="col-12 mt-5">
                                <h5 class="fw-bold mb-3" style="color: var(--text-main);">
                                    <i class="fas fa-folder-open text-primary me-2"></i> Documentos Adjuntos
                                </h5>

                                {{-- EXISTING DOCUMENTS --}}
                                @if($etapa_contractual->documentos->count() > 0)
                                    <h6 class="text-muted fw-bold mb-2 small text-uppercase">Archivos Existentes</h6>
                                    <div class="file-list mb-4" id="existingFilesList">
                                        @foreach($etapa_contractual->documentos as $doc)
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

                                {{-- NEW UPLOADS --}}
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

                        {{-- BOTONES ACCION --}}
                        <div class="d-flex justify-content-end gap-3 mt-5 border-top pt-4">
                            <a href="{{ route('admin.etapa_contractual.index') }}" class="btn btn-light-custom px-4">
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
