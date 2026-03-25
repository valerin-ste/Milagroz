@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">Nueva Etapa Precontractual</h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Registra y anexa múltiples documentos al candidato.</p>
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

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.etapa_precontractual.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-4">
                    <div class="col-md-4">
                        <label for="persona_id" class="form-label">Candidato (Persona) <span class="text-danger">*</span></label>
                        <select name="persona_id" id="persona_id" class="form-select" required>
                            <option value="" disabled selected>Seleccione un candidato</option>
                            @foreach($personas as $persona)
                                <option value="{{ $persona->id }}" {{ old('persona_id') == $persona->id ? 'selected' : '' }}>
                                    {{ $persona->nombres }} {{ $persona->apellidos }} - {{ $persona->numero_documento }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="fecha_registro" class="form-label">Fecha de Registro</label>
                        <input type="date" name="fecha_registro" id="fecha_registro" class="form-control" value="{{ old('fecha_registro', now()->toDateString()) }}">
                    </div>

                    <div class="col-md-4">
                        <label for="estado" class="form-label">Estado de la Revisión <span class="text-danger">*</span></label>
                        <select name="estado" id="estado" class="form-select" required>
                            <option value="0">En Proceso</option>
                            <option value="1">Aprobado</option>
                            <option value="2">Rechazado</option>
                        </select>
                    </div>

                    {{-- FILE UPLOAD DRAG & DROP --}}
                    <div class="col-12 mt-5">
                        <h5 class="fw-bold mb-3" style="color: var(--text-main);">
                            <i class="fas fa-folder-open text-primary me-2"></i> Anexar Documentos de Soporte
                        </h5>
                        <p class="text-muted mb-4 small">
                            Puede seleccionar o arrastrar múltiples archivos a la vez (por ejemplo: Cédula, Hoja de vida, Certificados).
                            <br><strong>Formatos aceptados:</strong> PDF, Word, JPG, PNG. Max: 10MB por archivo.
                        </p>

                        <div class="file-drop-area" id="dropArea">
                            <i class="fas fa-cloud-upload-alt file-drop-area-icon"></i>
                            <span class="file-drop-area-text">Arrastra y suelta tus archivos aquí</span>
                            <span class="file-drop-area-hint">o haz clic para explorar en tu computadora</span>
                            <!-- Input array -->
                            <input type="file" name="documentos[]" id="fileInput" class="file-input-hidden" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        </div>

                        <!-- Contenedor donde se listarán los archivos seleccionados -->
                        <div class="file-list" id="fileList"></div>
                    </div>
                </div>

                <div class="mt-5 text-end border-top pt-4">
                    <button type="submit" class="btn btn-orange px-5" onclick="document.querySelector('form').submit();">
                        <i class="fas fa-save me-2"></i> Guardar Registro y Archivos
                    </button>
                </div>
            </form>
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

        // Evitar comportamientos por defecto
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Efectos visuales de drag & drop
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => {
                dropArea.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => {
                dropArea.classList.remove('dragover');
            }, false);
        });

        // Manejar cuando se sueltan los archivos
        dropArea.addEventListener('drop', function(e) {
            let dt = e.dataTransfer;
            let files = dt.files;
            handleFiles(files);
        }, false);

        // Manejar cuando se hace clic y se seleccionan archivos por ventana
        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });

        function handleFiles(files) {
            [...files].forEach(file => {
                // Validación básica de archivos
                if(file.size > 10 * 1024 * 1024) {
                    alert('El archivo ' + file.name + ' supera los 10MB permitidos.');
                    return;
                }
                selectedFiles.items.add(file);
            });
            
            // Actualizar el DOM y el input oculto
            updateDOM();
        }

        function updateDOM() {
            fileInput.files = selectedFiles.files;
            fileList.innerHTML = '';
            
            [...selectedFiles.files].forEach((file, index) => {
                const size = (file.size / 1024 / 1024).toFixed(2);
                const fileExt = file.name.split('.').pop().toLowerCase();
                
                let iconClass = 'fa-file-alt text-secondary';
                if (fileExt === 'pdf') {
                    iconClass = 'fa-file-pdf text-danger';
                } else if (['jpg', 'jpeg', 'png'].includes(fileExt)) {
                    iconClass = 'fa-file-image text-primary';
                } else if (['doc', 'docx'].includes(fileExt)) {
                    iconClass = 'fa-file-word text-info';
                }

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

        // Función que se expone globalmente para eliminar archivos
        window.removeFile = function (index) {
            let newSelectedFiles = new DataTransfer();
            let filesArray = Array.from(selectedFiles.files);
            
            // Eliminar el archivo seleccionado en ese índice
            filesArray.splice(index, 1);
            
            // Re-agregar los restantes
            filesArray.forEach(file => newSelectedFiles.items.add(file));
            
            selectedFiles = newSelectedFiles;
            updateDOM();
        };
    });
</script>
@endsection
