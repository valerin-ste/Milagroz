@extends('adminlte::page')

@section('title', 'Editar Fecha Especial')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">
            Editar Fecha Especial
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">
            Modifique los detalles de la fecha especial registrada.
        </p>
    </div>

    <a href="{{ route('admin.fechas_especiales.index') }}" class="btn btn-light-custom px-4">
        <i class="fas fa-arrow-left me-2"></i> Volver al listado
    </a>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    {{-- ERRORES --}}
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0 mb-4"
             style="border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
            <div class="d-flex align-items-center border-bottom pb-2 mb-2"
                 style="border-color: #fecaca !important;">
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

    {{-- FORM --}}
    <form action="{{ route('admin.fechas_especiales.update', $fechaEspecial->id) }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm border-0 rounded-lg">

                    <div class="card-header bg-white border-0 pt-4 px-4 pb-3">
                        <h5 class="fw-bold" style="color: #ff6a00;">
                            <i class="fas fa-calendar-star me-2"></i>
                            Detalles de la Fecha Especial
                        </h5>
                    </div>

                    <div class="card-body px-4 pb-4 pt-2">

                        <div class="row g-4">

                            {{-- EMPLEADO AUTOCOMPLETE (DESHABILITADO EN EDICIÓN PARA EVITAR ERRORES DE ASIGNACIÓN) --}}
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Empleado</label>
                                <div class="form-control bg-light" style="height: auto;">
                                    <strong>{{ $fechaEspecial->empleado->persona->nombres }} {{ $fechaEspecial->empleado->persona->apellidos }}</strong>
                                    <br>
                                    <small class="text-muted">CC: {{ $fechaEspecial->empleado->persona->numero_documento }}</small>
                                </div>
                                <input type="hidden" name="empleado_id" value="{{ $fechaEspecial->empleado_id }}">
                            </div>

                            {{-- TIPO DE FECHA --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Tipo de Fecha Especial <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="tipo" class="form-control" 
                                       value="{{ old('tipo', $fechaEspecial->tipo) }}" required>
                            </div>

                            {{-- FECHA --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Fecha <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="fecha" class="form-control" 
                                       value="{{ old('fecha', $fechaEspecial->fecha->format('Y-m-d')) }}" required>
                            </div>

                            {{-- ARCHIVO PDF --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Documento Soporte <small class="text-muted">(PDF)</small>
                                </label>
                                @if($fechaEspecial->archivo)
                                    <div class="mb-2">
                                        <a href="{{ asset('storage/' . $fechaEspecial->archivo) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-file-pdf me-1"></i> Ver PDF actual
                                        </a>
                                    </div>
                                @endif
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-file-pdf text-muted"></i></span>
                                    <input type="file" name="archivo" class="form-control" accept=".pdf">
                                </div>
                            </div>

                            {{-- ESTADO --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Estado <span class="text-danger">*</span>
                                </label>
                                <select name="estado" class="form-control" required>
                                    <option value="1" {{ old('estado', $fechaEspecial->estado) == '1' ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ old('estado', $fechaEspecial->estado) == '0' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                            </div>

                            {{-- BOTONES --}}
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2 mt-4 mb-4">
                                    <a href="{{ route('admin.fechas_especiales.index') }}"
                                    class="btn btn-light border px-4 fw-bold shadow-sm"
                                    style="border-radius:15px; font-size:1.1rem; letter-spacing:0.5px;">
                                        <i class="fas fa-times me-2"></i> CANCELAR
                                    </a>

                                    <button type="submit"
                                            class="btn btn-orange text-white px-4 fw-bold shadow-sm"
                                            style="border-radius:15px; font-size:1.1rem; letter-spacing:0.5px;">
                                        <i class="fas fa-save me-2"></i> ACTUALIZAR REGISTRO
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection

@section('css')
<style>
.btn-orange {
    background-color: #ff6a00;
    border: none;
    color: #fff;
}
.btn-orange:hover {
    background-color: #e65c00;
    color: #fff;
}
.btn-light-custom {
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #475569;
}
</style>
@endsection
