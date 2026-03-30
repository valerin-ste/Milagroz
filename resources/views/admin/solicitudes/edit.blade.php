@extends('adminlte::page')

@section('content')

<div class="container-fluid px-2">

    <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
        <h2 class="fw-bold">✏ Editar Solicitud</h2>

        <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="{{ route('admin.solicitudes.update', $solicitud->id) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- EMPLEADO --}}
                <div class="mb-3">
                    <label>Empleado</label>
                    <select name="empleado_id" class="form-control" required>
                        @foreach($empleados as $emp)
                            <option value="{{ $emp->id }}"
                                {{ $solicitud->empleado_id == $emp->id ? 'selected' : '' }}>
                                {{ $emp->persona->nombres ?? '' }}
                                {{ $emp->persona->apellidos ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- TIPO --}}
                <div class="mb-3">
                    <label>Tipo de Solicitud</label>
                    <input type="text"
                           name="tipo"
                           class="form-control"
                           value="{{ $solicitud->tipo }}"
                           required>
                </div>

                {{-- DESCRIPCIÓN --}}
                <div class="mb-3">
                    <label>Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="4">{{ $solicitud->descripcion }}</textarea>
                </div>

                {{-- ESTADO --}}
                <div class="mb-3">
                    <label>Estado</label>
                    <select name="estado" class="form-control" required>
                        <option value="pendiente" {{ $solicitud->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="aprobado" {{ $solicitud->estado == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                        <option value="rechazado" {{ $solicitud->estado == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                    </select>
                </div>

                {{-- FECHA --}}
                <div class="mb-3">
                    <label>Fecha</label>
                    <input type="date"
                           name="fecha"
                           class="form-control"
                           value="{{ $solicitud->fecha }}"
                           required>
                </div>

                {{-- ARCHIVO ACTUAL --}}
                <div class="mb-3">
                    <label>Archivo actual</label><br>

                    @if($solicitud->archivo)
                        <a href="{{ Storage::url($solicitud->archivo) }}" target="_blank">
                            {{ $solicitud->nombre_archivo }}
                        </a>
                    @else
                        <span class="text-muted">Sin archivo</span>
                    @endif
                </div>

                <div class="mb-3">
                    <label>Reemplazar archivo</label>
                    <input type="file" name="archivo" class="form-control">
                </div>

                <button class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar
                </button>

            </form>

        </div>
    </div>

</div>

{{-- DELETE AJAX --}}
<script>
function eliminarDoc(id) {

    if (!confirm('¿Eliminar este documento?')) return;

    fetch('/admin/documentos/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('doc-' + id).remove();
        }
    })
    .catch(err => console.log(err));
}
</script>
@endsection