@extends('adminlte::page')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h3 class="fw-bold">Editar Empleado</h3>
        <small class="text-muted">Modificar información</small>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="{{ route('admin.personas.update', $persona) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>Tipo Documento</label>
                        <select name="tipo_documento" class="form-control">
                            <option value="CC" {{ $persona->tipo_documento=='CC'?'selected':'' }}>Cédula</option>
                            <option value="TI" {{ $persona->tipo_documento=='TI'?'selected':'' }}>TI</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Número Documento</label>
                        <input type="text" name="numero_documento" class="form-control"
                               value="{{ $persona->numero_documento }}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Nombres</label>
                        <input type="text" name="nombres" class="form-control"
                               value="{{ $persona->nombres }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Apellidos</label>
                        <input type="text" name="apellidos" class="form-control"
                               value="{{ $persona->apellidos }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" class="form-control"
                               value="{{ $persona->telefono }}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Correo</label>
                        <input type="email" name="correo" class="form-control"
                               value="{{ $persona->correo }}">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Dirección</label>
                        <input type="text" name="direccion" class="form-control"
                               value="{{ $persona->direccion }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Fecha Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control"
                               value="{{ $persona->fecha_nacimiento }}">
                    </div>

                </div>

                <div class="d-flex justify-content-end mt-4 me-3">
                    <a href="{{ route('admin.personas.index') }}" class="btn btn-light me-2">
                        Cancelar
                    </a>

                    <button class="btn text-white px-4"
                            style="background:#f97316;">
                        Actualizar
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection