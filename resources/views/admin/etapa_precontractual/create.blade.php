@extends('adminlte::page')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Nueva Etapa Precontractual</h3>
            <small class="text-muted">Crear registro de precontratación</small>
        </div>
        <a href="{{ route('admin.etapa_precontractual.index') }}" class="btn btn-secondary px-4">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.etapa_precontractual.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="persona_id" class="form-label">Candidato (Persona) <span class="text-danger">*</span></label>
                        <select name="persona_id" id="persona_id" class="form-control" required>
                            <option value="">Seleccione un candidato</option>
                            @foreach($personas as $persona)
                                <option value="{{ $persona->id }}" {{ old('persona_id') == $persona->id ? 'selected' : '' }}>
                                    {{ $persona->nombres }} {{ $persona->apellidos }} - {{ $persona->numero_documento }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="fecha_registro" class="form-label">Fecha de Registro</label>
                        <input type="date" name="fecha_registro" id="fecha_registro" class="form-control" value="{{ old('fecha_registro', now()->toDateString()) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select name="estado" id="estado" class="form-control" required>
                            <option value="en_proceso" {{ old('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="aprobado" {{ old('estado') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                            <option value="rechazado" {{ old('estado') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="archivo" class="form-label">Archivo Adicional (Opcional, Max 5MB)</label>
                        <input type="file" name="archivo" id="archivo" class="form-control" accept=".pdf,.doc,.docx">
                        <small class="text-muted">Formatos permitidos: PDF, DOCX.</small>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn text-white px-4" style="background:#f97316;">
                        <i class="fas fa-save me-1"></i> Guardar Registro
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
