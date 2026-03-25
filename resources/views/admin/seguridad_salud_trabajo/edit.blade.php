@extends('adminlte::page')

@section('title', 'Editar Documento SST')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">Editar Documento</h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Modifique los datos del documento de Seguridad y Salud.</p>
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

    <form action="{{ route('admin.seguridad_salud_trabajo.update', $documento) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                                    @foreach($empleados as $e)
                                        <option value="{{ $e->id }}" {{ old('empleado_id', $documento->empleado_id) == $e->id ? 'selected' : '' }}>
                                            {{ $e->persona->nombres }} {{ $e->persona->apellidos }} - {{ $e->persona->numero_documento }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                                                            <div class="col-md-12">
                                    <label class="form-label fw-bold small text-uppercase" style="color: #64748b; letter-spacing: 0.5px;">
                                        Archivos / Soportes actuales
                                    </label>

                                    <div class="list-group mb-3">
                                        @forelse($documento->documentos as $archivo)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">

                                                {{-- NOMBRE DEL ARCHIVO (CLICKEABLE) --}}
                                                <a href="{{ Storage::url($archivo->ruta) }}" target="_blank" class="text-decoration-none fw-semibold text-dark">
                                                    📄 {{ $archivo->nombre_original }}
                                                </a>

                                                {{-- ELIMINAR --}}
                                                <form action="{{ route('admin.documentos.destroy', $archivo->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este archivo?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        @empty
                                            <div class="text-muted small">Sin archivos adjuntos.</div>
                                        @endforelse
                                    </div>

                                    {{-- INPUT MULTIPLE --}}
                                    <label for="documentos" class="form-label fw-bold small text-uppercase" style="color: #64748b;">
                                        Cargar más archivos
                                    </label>

                                    <input type="file" name="documentos[]" id="documentos" multiple
                                        class="form-control border-light bg-light py-2 px-3 shadow-none"
                                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">

                                    <small class="text-muted mt-1 d-block">
                                        Opcional: Suba más documentos para este registro.
                                    </small>
                                </div>
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
