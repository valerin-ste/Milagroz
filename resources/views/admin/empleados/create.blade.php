@extends('adminlte::page')

@section('content')
<div class="container-fluid">

    <h3 class="fw-bold mb-4">Registrar Empleado</h3>

    <form action="{{ route('admin.empleados.store') }}" method="POST">
        @csrf

        {{-- ================== DATOS PERSONALES ================== --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <strong>Datos Personales</strong>
            </div>

            <div class="card-body row">

                <div class="col-md-6 mb-3">
                    <label>Nombres</label>
                    <input type="text" name="nombres" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Apellidos</label>
                    <input type="text" name="apellidos" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Tipo Documento</label>
                    <select name="tipo_documento" class="form-control">
                        <option value="CC">Cédula</option>
                        <option value="CE">Extranjería</option>
                        <option value="PP">Pasaporte</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Número Documento</label>
                    <input type="text" name="numero_documento" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Fecha Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Correo</label>
                    <input type="email" name="correo" class="form-control">
                </div>

                <div class="col-md-12 mb-3">
                    <label>Dirección</label>
                    <input type="text" name="direccion" class="form-control">
                </div>

            </div>
        </div>

        {{-- ================== DATOS LABORALES ================== --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <strong>Datos Laborales</strong>
            </div>

            <div class="card-body row">

                <div class="col-md-6 mb-3">
                    <label>Área</label>
                    <select name="area_id" class="form-control">
                        @foreach($areas as $a)
                            <option value="{{ $a->id }}">{{ $a->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Sede</label>
                    <select name="sede_id" class="form-control">
                        @foreach($sedes as $s)
                            <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Rol</label>
                    <select name="rol_id" class="form-control">
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Cargo</label>
                    <input type="text" name="cargo" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Fecha Ingreso</label>
                    <input type="date" name="fecha_ingreso" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>

            </div>
        </div>

        {{-- BOTONES --}}
        <div class="text-end">
            <a href="{{ route('admin.empleados.index') }}" class="btn btn-secondary">Cancelar</a>
            <button class="btn btn-success">Guardar</button>
        </div>

    </form>

</div>
@endsection