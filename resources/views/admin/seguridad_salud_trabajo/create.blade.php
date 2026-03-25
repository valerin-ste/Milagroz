@extends('adminlte::page')

@section('title', 'Registrar Documento SST')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">Registrar Documento</h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Asocie un documento de Seguridad y Salud a un empleado.</p>
    </div>
    <a href="{{ route('admin.seguridad_salud_trabajo.index') }}" class="btn btn-light-custom px-4">
        <i class="fas fa-arrow-left me-2"></i> Volver al listado
    </a>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0 mb-4" style="border-radius: var(--radius-md); background-color: #fef2f2; color: #991b1b;">
            <div class="d-flex align-items-center border-bottom pb-2 mb-2" style="border-color: #fecaca !important;">
                <i class="fas fa-exclamation-circle fa-lg me-2"></i> 
                <strong>Revise los siguientes errores:</strong>
            </div>
            <ul class="mb-0 mt-2 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.seguridad_salud_trabajo.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card h-100 shadow-sm border-0 rounded-lg">
                    <div class="card-header pt-4 px-4 pb-3 bg-white border-0">
                        <h5 class="card-title fw-bold" style="color: var(--primary-blue);">
                            <div class="d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); border-radius: 10px;">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            Detalles del Documento SST
                        </h5>
                    </div>

                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Empleado <span class="text-danger">*</span></label>
                                <select name="empleado_id" class="form-select form-control" required>
                                    <option value="" disabled selected>Seleccionar Empleado</option>
                                    @foreach($empleados as $e)
                                        <option value="{{ $e->id }}" {{ old('empleado_id') == $e->id ? 'selected' : '' }}>
                                            {{ $e->persona->nombres }} {{ $e->persona->apellidos }} - {{ $e->persona->numero_documento }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Tipo de Documento <span class="text-danger">*</span></label>
                                <input type="text" name="tipo_documento" class="form-control" placeholder="Ej. Examen Médico, Formación, Brigada..." required value="{{ old('tipo_documento') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" class="form-control" required value="{{ old('fecha', now()->toDateString()) }}">
                            </div>

                            <div class="col-12 mt-4">
                                <h5 class="fw-bold mb-3" style="color: var(--text-main);">
                                     <i class="fas fa-folder-open text-primary me-2"></i> Anexar Documentos de Soporte
                                 </h5>
                                 <p class="text-muted mb-4 small">
                                     Puede seleccionar o arrastrar múltiples archivos a la vez.
                                     <br><strong>Formatos aceptados:</strong> PDF, Word, JPG, PNG. Max: 10MB por archivo.
                                 </p>

                                 <div class="file-drop-area" id="dropArea">
                                     <i class="fas fa-cloud-upload-alt file-drop-area-icon"></i>
                                     <span class="file-drop-area-text">Arrastra y suelta tus archivos aquí</span>
                                     <span class="file-drop-area-hint">o haz clic para explorar en tu computadora</span>
                                     <input type="file" name="documentos[]" id="fileInput" class="file-input-hidden" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                 </div>

                                 <div class="file-list" id="fileList"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BOTONES ACCIÓN --}}
        <div class="d-flex justify-content-center gap-3 mt-4 mb-5 pb-4 px-2">
            <a href="{{ route('admin.seguridad_salud_trabajo.index') }}" class="btn btn-light-custom px-4 shadow-sm border">
                Cancelar
            </a>
            <button type="submit" class="btn btn-orange px-5 shadow-sm">
                <i class="fas fa-save me-2"></i> Guardar Registro y Documentos
            </button>
        </div>

    </form>
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
    });
</script>
@endsection

@section('css')
<style>
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.2rem rgba(19, 182, 236, 0.1);
    }
    .btn-orange {
        background-color: #f97316;
        color: white;
        border: none;
        font-weight: 500;
        transition: transform 0.2s;
    }
    .btn-orange:hover {
        background-color: #ea580c;
        color: white;
        transform: translateY(-2px);
    }
    .btn-light-custom {
        background-color: white;
        color: #475569;
        font-weight: 500;
        transition: all 0.2s;
    }
    .btn-light-custom:hover {
        background-color: #f1f5f9;
        color: #1e293b;
    }
</style>
@stop
