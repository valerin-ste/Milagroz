@extends('adminlte::page')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Editar Etapa Precontractual</h3>
            <small class="text-muted">Candidato: {{ $etapa_precontractual->persona->nombres }} {{ $etapa_precontractual->persona->apellidos }}</small>
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
            <form action="{{ route('admin.etapa_precontractual.update', $etapa_precontractual) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Candidato (Persona)</label>
                        <input type="text" class="form-control" value="{{ $etapa_precontractual->persona->nombres }} {{ $etapa_precontractual->persona->apellidos }} - {{ $etapa_precontractual->persona->numero_documento }}" readonly disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Registro</label>
                        <input type="date" class="form-control" value="{{ $etapa_precontractual->fecha_registro }}" readonly disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select name="estado" id="estado" class="form-control" required>
                            <option value="en_proceso" {{ old('estado', $etapa_precontractual->estado) == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="aprobado" {{ old('estado', $etapa_precontractual->estado) == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                            <option value="rechazado" {{ old('estado', $etapa_precontractual->estado) == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="archivo" class="form-label">Actualizar Archivo (Opcional)</label>
                        <input type="file" name="archivo" id="archivo" class="form-control" accept=".pdf,.doc,.docx">
                        @if($etapa_precontractual->archivo)
                            <div class="mt-2">
                                <a href="{{ Storage::url($etapa_precontractual->archivo) }}" target="_blank" class="text-sm">
                                    <i class="fas fa-file-pdf text-danger"></i> Ver archivo actual
                                </a>
                            </div>
                        @else
                            <small class="text-muted mt-1 d-block">Sin archivo subido actualmente.</small>
                        @endif
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn text-white px-4" style="background:#f97316;">
                        <i class="fas fa-save me-1"></i> Actualizar Registro
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
