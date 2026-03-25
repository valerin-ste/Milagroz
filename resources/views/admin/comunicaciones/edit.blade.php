@extends('adminlte::page')

@section('content')
<div class="container-fluid px-2">

    <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
        <h2 class="fw-bold mb-0">Editar Comunicación</h2>
        <a href="{{ route('admin.comunicaciones.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Volver al listado
        </a>
    </div>

    <div class="card shadow-sm border-0 mt-3">
        <div class="card-body">

            {{-- 🔹 Formulario de actualización --}}
            <form action="{{ route('admin.comunicaciones.update', $comunicacion) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Empleado</label>
                    <select name="empleado_id" class="form-control" required>
                        <option value="">Seleccione</option>
                        @foreach($empleados as $emp)
                            <option value="{{ $emp->id }}" {{ $comunicacion->empleado_id == $emp->id ? 'selected' : '' }}>
                                {{ $emp->persona->nombres }} {{ $emp->persona->apellidos }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Asunto</label>
                    <input type="text" name="asunto" class="form-control" value="{{ $comunicacion->asunto }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mensaje</label>
                    <textarea name="mensaje" class="form-control" rows="4">{{ $comunicacion->mensaje }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha</label>
                    <input type="date" name="fecha" class="form-control" value="{{ $comunicacion->fecha }}" required>
                </div>

                {{-- 🔹 Archivos actuales --}}
                <div class="mb-3">
                    <label class="form-label">Archivos actuales</label>
                    @forelse($comunicacion->documentos as $doc)
                        <div class="d-flex align-items-center justify-content-between mb-2 p-2 border rounded" style="max-width: 450px;">
                            <a href="{{ asset('storage/' . $doc->ruta) }}" target="_blank" class="text-truncate" title="{{ $doc->nombre_original }}">
                                📎 {{ $doc->nombre_original }}
                            </a>
                            <form action="{{ route('admin.comunicaciones.deleteArchivo', $doc->id) }}" method="POST" onsubmit="return confirm('¿Desea eliminar este archivo?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger ms-2">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    @empty
                        <span class="text-muted">Sin archivos</span>
                    @endforelse
                </div>

                {{-- 🔹 Subir nuevos archivos --}}
                <div class="mb-3">
                    <label class="form-label">Anexar documentos</label>
                    <input type="file" name="archivos[]" class="form-control" multiple>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i> Actualizar Comunicación
                </button>
            </form>

        </div>
    </div>

</div>
@endsection