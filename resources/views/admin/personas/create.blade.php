@extends('adminlte::page')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h3 class="fw-bold">Crear Empleado</h3>
        <small class="text-muted">Ingrese la información del empleado</small>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="{{ route('admin.personas.store') }}" method="POST">
                @csrf

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>Tipo Documento</label>
                        <select name="tipo_documento" class="form-control">
                            <option value="CC">Cédula</option>
                            <option value="TI">Tarjeta Identidad</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Número Documento</label>
                        <input type="text" name="numero_documento" class="form-control" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Nombres</label>
                        <input type="text" name="nombres" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Apellidos</label>
                        <input type="text" name="apellidos" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" class="form-control" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Correo</label>
                        <input type="email" name="correo" class="form-control">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Dirección</label>
                        <input type="text" name="direccion" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Fecha Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control">
                    </div>

                </div>

                <div class="d-flex justify-content-end mt-4 me-3">
                    <a href="{{ route('admin.personas.index') }}" class="btn btn-light me-2">
                        Cancelar
                    </a>

                    <button class="btn text-white px-4"
                            style="background:#f97316;">
                        Guardar
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection