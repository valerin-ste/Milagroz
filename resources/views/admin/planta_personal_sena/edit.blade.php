@extends('adminlte::page')

@section('title', 'Editar - Planta Personal SENA')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="font-size:1.75rem;">Editar Registro Planta Personal SENA</h2>
        <p class="text-muted mb-0">Actualice los datos del registro seleccionado.</p>
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
                <form action="{{ route('admin.planta_personal_sena.update', $registro->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

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
                        <input type="hidden" name="empleado_id" id="empleado_id"
                               value="{{ old('empleado_id', $registro->empleado_id) }}">
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
                               value="{{ old('fecha_reporte', \Carbon\Carbon::parse($registro->fecha_reporte)->format('Y-m-d')) }}"
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
                                  style="border-radius:15px;">{{ old('observaciones', $registro->observaciones) }}</textarea>
                    </div>

                    {{-- ARCHIVOS --}}
                    <div class="col-12 mt-4 text-start">
                        <div class="card border-0 shadow-sm" style="border-radius:15px; background-color:#f8fafc;">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-4" style="color:#1e293b;">
                                    <i class="fas fa-folder-open text-primary me-2"></i> Archivos Adjuntos
                                </h5>

                                {{-- ARCHIVOS EXISTENTES --}}
                                @if(isset($registro) && $registro->documentos && $registro->documentos->count() > 0)
                                    <h6 class="text-muted fw-bold mb-2 small text-uppercase" style="letter-spacing:0.5px;">Archivos Existentes</h6>
                                    <div class="row g-3 mb-4">
                                        @foreach($registro->documentos as $doc)
                                            <div class="col-md-6 col-xl-4" id="doc_card_{{ $doc->id }}">
                                                <div class="file-card position-relative">
                                                    <div class="file-details">
                                                        @php
                                                            $ext = strtolower(pathinfo($doc->nombre_original, PATHINFO_EXTENSION));
                                                            $icon = 'fa-file-alt text-secondary';
                                                            if($ext == 'pdf') $icon = 'fa-file-pdf text-danger';
                                                            elseif(in_array($ext, ['jpg','jpeg','png'])) $icon = 'fa-file-image text-primary';
                                                            elseif(in_array($ext, ['doc','docx'])) $icon = 'fa-file-word text-info';
                                                        @endphp
                                                        <i class="fas {{ $icon }} file-icon"></i>
                                                        <div class="file-info">
                                                            <span class="file-name" title="{{ $doc->nombre_original }}">{{ $doc->nombre_original }}</span>
                                                            <a href="{{ route('admin.documentos.view', $doc->id) }}" target="_blank" class="text-decoration-none small text-primary fw-bold mt-1">Ver Archivo <i class="fas fa-external-link-alt ms-1"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="form-check form-switch position-absolute top-50 end-0 translate-middle-y me-3">
                                                        <input class="form-check-input switch-delete" type="checkbox" role="switch" id="delete_doc_{{ $doc->id }}" name="eliminar_documentos[]" value="{{ $doc->id }}" onchange="toggleDeleteCard({{ $doc->id }})">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <h6 class="text-muted fw-bold mb-2 mt-4 small text-uppercase" style="letter-spacing:0.5px;">Subir Nuevos Archivos</h6>
                                
                                <div class="file-drop-area" id="dropArea">
                                    <i class="fas fa-cloud-upload-alt file-drop-area-icon"></i>
                                    <span class="file-drop-area-text">Arrastra y suelta nuevos archivos aquí</span>
                                    <span class="file-drop-area-hint">o haz clic para explorar en tu computadora</span>
                                    <small class="text-muted d-block mt-1">PDF, Word, JPG, PNG - Máx 10MB</small>
                                    <input type="file" name="archivos[]" id="fileInput" class="file-input-hidden" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                </div>

                                <div class="file-list" id="fileList"></div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-5">
                        <a href="{{ route('admin.planta_personal_sena.index') }}"
                        class="btn btn-light border px-4 fw-bold shadow-sm"
                        style="border-radius:15px; font-size:1.1rem; letter-spacing:.5px;">
                            <i class="fas fa-times me-2"></i> CANCELAR
                        </a>

                        <button type="submit"
                                class="btn btn-orange text-white px-4 fw-bold shadow-sm"
                                style="border-radius:15px; font-size:1.1rem; letter-spacing:.5px;">
                            <i class="fas fa-save me-2"></i> ACTUALIZAR REGISTRO
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

    // Pre-cargar empleado actual
    if (hidden.value) {
        const emp = empleados.find(e => e.id == hidden.value);
        if (emp) {
            input.value = emp.persona.nombres + ' ' + emp.persona.apellidos + ' - ' + (emp.persona.numero_documento || '');
        } else {
            input.value = "{{ $registro->empleado->persona->nombres ?? '' }} {{ $registro->empleado->persona->apellidos ?? '' }} - {{ $registro->empleado->persona->numero_documento ?? '' }}";
        }
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

    // 🗑️ ELIMINAR ARCHIVOS EXISTENTES
    window.toggleDeleteCard = function(id) {
        const card = document.getElementById('doc_card_' + id);
        const checkbox = document.getElementById('delete_doc_' + id);
        if (checkbox.checked) {
            card.classList.add('opacity-50');
            card.querySelector('.file-card').style.borderColor = '#ef4444';
            card.querySelector('.file-card').style.backgroundColor = '#fef2f2';
        } else {
            card.classList.remove('opacity-50');
            card.querySelector('.file-card').style.borderColor = '#e2e8f0';
            card.querySelector('.file-card').style.backgroundColor = '#fff';
        }
    };

    // ==================================
    // 📎 DRAG & DROP ARCHIVOS
    // ==================================
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
@stop

@section('css')
<style>
.btn-orange { background-color:#ff6a00; border:none; color:#fff; transition:all .3s; }
.btn-orange:hover { background-color:#e65c00; color:#fff; transform:translateY(-2px); box-shadow:0 5px 15px rgba(255,106,0,.3); }
.list-group-item-action:hover { background-color:#fdf2f0 !important; color:#ff6a00 !important; }

/* FILE UPLOAD STYLES */
.file-drop-area {
    border: 2px dashed #cbd5e1;
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    background-color: #f8fafc;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}
.file-drop-area.dragover, .file-drop-area:hover {
    background-color: #f1f5f9;
    border-color: #ff6a00;
}
.file-drop-area-icon {
    font-size: 2.5rem;
    color: #94a3b8;
    margin-bottom: 10px;
    transition: color 0.3s;
}
.file-drop-area:hover .file-drop-area-icon { color: #ff6a00; }
.file-drop-area-text {
    display: block;
    font-size: 1.1rem;
    font-weight: 600;
    color: #475569;
}
.file-drop-area-hint {
    display: block;
    font-size: 0.9rem;
    color: #64748b;
    margin-top: 5px;
}
.file-input-hidden {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}
.file-list {
    margin-top: 15px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.file-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 15px;
    background-color: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.file-details {
    display: flex;
    align-items: center;
    overflow: hidden;
}
.file-icon {
    font-size: 1.5rem;
    margin-right: 15px;
}
.file-info {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.file-name {
    font-weight: 600;
    color: #334155;
    font-size: 0.95rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 250px;
}
.file-size {
    font-size: 0.8rem;
    color: #64748b;
}
.file-remove {
    background: none;
    border: none;
    color: #ef4444;
    cursor: pointer;
    font-size: 1.2rem;
    padding: 5px;
    transition: color 0.2s;
}
.file-remove:hover { color: #dc2626; }
.switch-delete:checked { background-color: #ef4444; border-color: #ef4444; }
</style>
@endsection
