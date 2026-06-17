@extends('adminlte::page')

@section('title', 'Nueva Productividad')

@section('content_header')
<div class="page-header d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="page-title fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Nuevo Registro
        </h2>
        <p class="page-subtitle text-muted mb-0">
            Añadir seguimiento o productividad para un empleado.
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.productividades.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver al listado
        </a>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    @if($errors->any())
        <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-circle me-2"></i> Por favor corrige los siguientes errores:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold" style="color: #334155;"><i class="fas fa-clipboard-list text-primary me-2"></i> Detalles del Registro</h5>
                </div>
                
                <form action="{{ route('admin.productividades.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="card-body p-4">
                        <div class="row g-4">
                            
                            {{-- EMPLEADO --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold text-muted small">Empleado <span class="text-danger">*</span></label>
                                <select name="empleado_id" class="form-select border-light shadow-sm bg-light select2" required>
                                    <option value="" disabled selected>-- Selecciona un empleado --</option>
                                    @foreach($empleados as $emp)
                                        <option value="{{ $emp->id }}" {{ old('empleado_id') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->persona->nombres }} {{ $emp->persona->apellidos }} - {{ $emp->persona->numero_documento }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            

                            {{-- TÍTULO --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Título de la Actividad <span class="text-danger">*</span></label>
                                <input type="text" name="titulo" class="form-control border-light shadow-sm bg-light" 
                                       placeholder="Ej: Seguimiento Mensual" value="{{ old('titulo') }}" required maxlength="150">
                            </div>

                            {{-- TIPO --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Tipo <span class="text-muted">(Opcional)</span></label>
                                <input type="text" name="tipo" class="form-control border-light shadow-sm bg-light" 
                                       placeholder="Ej: Observación, Meta, General" value="{{ old('tipo') }}" maxlength="100">
                            </div>

                            {{-- FECHA --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" class="form-control border-light shadow-sm bg-light" 
                                       value="{{ old('fecha', date('Y-m-d')) }}" required>
                            </div>

                            {{-- ARCHIVO --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Archivo de Soporte <span class="text-muted">(Opcional)</span></label>
                                <input type="file" name="archivo" class="form-control border-light shadow-sm bg-light">
                                <small class="text-muted mt-1 d-block">Tamaño máximo 5MB.</small>
                            </div>

                            {{-- DESCRIPCIÓN --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold text-muted small">Descripción o Observaciones <span class="text-danger">*</span></label>
                                <textarea name="descripcion" rows="4" class="form-control border-light shadow-sm bg-light" 
                                          placeholder="Describe las actividades, seguimiento u observaciones..." required>{{ old('descripcion') }}</textarea>
                            </div>

                        </div>
                    </div>
                    
                    <div class="card-footer bg-white border-top-0 pt-0 pb-4 px-4 text-end">
                        <a href="{{ route('admin.productividades.index') }}" class="btn btn-light border px-4 me-2 rounded-pill">Cancelar</a>
                        <button type="submit" class="btn btn-orange px-4 rounded-pill fw-bold">
                            <i class="fas fa-save me-1"></i> Guardar Registro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
            placeholder: "-- Selecciona un empleado --"
        });
    });
</script>
@endsection
