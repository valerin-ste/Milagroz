@extends('adminlte::page')

@section('title', 'Nuevo Documento de Calidad')

@section('content_header')
<div class="page-header d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="page-title fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Nuevo Documento de Calidad
        </h2>
        <p class="page-subtitle text-muted mb-0">
            Registrar un nuevo documento en el sistema de calidad.
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.calidad_documentos.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver al listado
        </a>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    @if($errors->any())
        <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-circle me-2"></i>Por favor corrige los siguientes errores:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-9 mx-auto">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold" style="color:#334155;">
                        <i class="fas fa-file-alt text-primary me-2"></i> Datos del Documento
                    </h5>
                </div>

                <form action="{{ route('admin.calidad_documentos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body p-4">
                        <div class="row g-4">

                            {{-- EMPLEADO --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold text-muted small">
                                    Empleado <span class="text-danger">*</span>
                                </label>
                                <select name="empleado_id"
                                        class="form-select border-light shadow-sm bg-light select2" required>
                                    <option value="" disabled selected>-- Selecciona un empleado --</option>
                                    @foreach($empleados as $emp)
                                        <option value="{{ $emp->id }}"
                                                {{ old('empleado_id') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->persona->nombres }} {{ $emp->persona->apellidos }}
                                            — {{ $emp->persona->numero_documento ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- NOMBRE DOCUMENTO --}}
                            <div class="col-md-8">
                                <label class="form-label fw-bold text-muted small">
                                    Nombre del Documento <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nombre_documento"
                                       class="form-control border-light shadow-sm bg-light"
                                       placeholder="Ej: Manual de Calidad ISO 9001"
                                       value="{{ old('nombre_documento') }}"
                                       required maxlength="150">
                            </div>

                            {{-- CATEGORÍA --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small">
                                    Categoría <span class="text-danger">*</span>
                                </label>
                                <select name="categoria"
                                        class="form-select border-light shadow-sm bg-light select2" required>
                                    <option value="" disabled selected>-- Selecciona --</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat }}"
                                                {{ old('categoria') == $cat ? 'selected' : '' }}>
                                            {{ $cat }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- CÓDIGO --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small">
                                    Código <span class="text-muted">(Opcional)</span>
                                </label>
                                <input type="text" name="codigo"
                                       class="form-control border-light shadow-sm bg-light"
                                       placeholder="Ej: DOC-CAL-001"
                                       value="{{ old('codigo') }}" maxlength="50">
                            </div>

                            {{-- VERSIÓN --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small">
                                    Versión <span class="text-muted">(Opcional)</span>
                                </label>
                                <input type="text" name="version"
                                       class="form-control border-light shadow-sm bg-light"
                                       placeholder="Ej: v1.0"
                                       value="{{ old('version') }}" maxlength="20">
                            </div>

                            {{-- ARCHIVO --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small">
                                    Archivo <span class="text-muted">(Opcional)</span>
                                </label>
                                <input type="file" name="archivo" id="archivoInput"
                                       class="form-control border-light shadow-sm bg-light"
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                <small class="text-muted mt-1 d-block">
                                    PDF, Word, Excel o imágenes. Máx. 10MB.
                                </small>
                                {{-- Preview nombre --}}
                                <div id="archivoPreview" class="mt-2 d-none">
                                    <div class="d-flex align-items-center gap-2 p-2 bg-white border rounded-3">
                                        <i class="fas fa-file-alt text-primary"></i>
                                        <span id="archivoNombre" class="small text-truncate"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- FECHA EMISIÓN --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small">
                                    Fecha de Emisión <span class="text-muted">(Opcional)</span>
                                </label>
                                <input type="date" name="fecha_emision"
                                       class="form-control border-light shadow-sm bg-light"
                                       value="{{ old('fecha_emision') }}">
                            </div>

                            {{-- FECHA VENCIMIENTO --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small">
                                    Fecha de Vencimiento <span class="text-muted">(Opcional)</span>
                                </label>
                                <input type="date" name="fecha_vencimiento"
                                       class="form-control border-light shadow-sm bg-light"
                                       value="{{ old('fecha_vencimiento') }}">
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Dejar en blanco si no vence.
                                </small>
                            </div>

                        </div>
                    </div>

                    <div class="card-footer bg-white border-top-0 pt-0 pb-4 px-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.calidad_documentos.index') }}"
                        class="btn btn-light border px-4 rounded-pill">
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-orange px-4 rounded-pill fw-bold">
                            <i class="fas fa-save me-1"></i> Guardar Documento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
.form-control, .form-select {
    padding: 0.6rem 1rem;
    border-radius: 0.5rem;
}
.btn-orange {
    background-color: #ff6a00;
    border: none;
    color: #fff;
}
.btn-orange:hover {
    background-color: #e65c00;
    color: #fff;
}
.select2-container .select2-selection--single {
    height: 42px !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 0.5rem !important;
    background-color: #f8fafc !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 42px !important;
    padding-left: 15px !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px !important;
}
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'default',
        width: '100%',
        placeholder: '-- Selecciona --'
    });

    // Preview nombre de archivo
    $('#archivoInput').on('change', function() {
        const file = this.files[0];
        if (file) {
            $('#archivoNombre').text(file.name);
            $('#archivoPreview').removeClass('d-none');
        } else {
            $('#archivoPreview').addClass('d-none');
        }
    });
});
</script>
@endsection
