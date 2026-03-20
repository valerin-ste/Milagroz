@extends('adminlte::page')

@section('content')
<div class="container-fluid">

    <h3 class="fw-bold mb-4">Editar Empleado</h3>

    <form action="{{ route('admin.empleados.update', $empleado) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- ================= DATOS PERSONALES ================= --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <strong>Datos Personales</strong>
            </div>

            <div class="card-body row">

                <div class="col-md-6 mb-3">
                    <label>Nombres</label>
                    <input type="text" name="nombres" class="form-control"
                           value="{{ $empleado->persona->nombres }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Apellidos</label>
                    <input type="text" name="apellidos" class="form-control"
                           value="{{ $empleado->persona->apellidos }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Tipo Documento</label>
                    <select name="tipo_documento" class="form-control">
                        <option value="CC" {{ $empleado->persona->tipo_documento == 'CC' ? 'selected' : '' }}>CC</option>
                        <option value="CE" {{ $empleado->persona->tipo_documento == 'CE' ? 'selected' : '' }}>CE</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Número Documento</label>
                    <input type="text" name="numero_documento" class="form-control"
                           value="{{ $empleado->persona->numero_documento }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Fecha Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control"
                           value="{{ $empleado->persona->fecha_nacimiento }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" class="form-control"
                           value="{{ $empleado->persona->telefono }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Correo</label>
                    <input type="email" name="correo" class="form-control"
                           value="{{ $empleado->persona->correo }}">
                </div>

                <div class="col-md-12 mb-3">
                    <label>Dirección</label>
                    <input type="text" name="direccion" class="form-control"
                           value="{{ $empleado->persona->direccion }}">
                </div>

            </div>
        </div>

        {{-- ================= DATOS LABORALES ================= --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <strong>Datos Laborales</strong>
            </div>

            <div class="card-body row">

                <div class="col-md-6 mb-3">
                    <label>Área</label>
                    <select name="area_id" class="form-control">
                        @foreach($areas as $a)
                            <option value="{{ $a->id }}"
                                {{ $empleado->area_id == $a->id ? 'selected' : '' }}>
                                {{ $a->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Sede</label>
                    <select name="sede_id" class="form-control">
                        @foreach($sedes as $s)
                            <option value="{{ $s->id }}"
                                {{ $empleado->sede_id == $s->id ? 'selected' : '' }}>
                                {{ $s->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Rol</label>
                    <select name="rol_id" class="form-control">
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}"
                                {{ $empleado->rol_id == $r->id ? 'selected' : '' }}>
                                {{ $r->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Cargo</label>
                    <input type="text" name="cargo" class="form-control"
                           value="{{ $empleado->cargo }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Fecha Ingreso</label>
                    <input type="date" name="fecha_ingreso" class="form-control"
                           value="{{ $empleado->fecha_ingreso }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="1" {{ $empleado->estado ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ !$empleado->estado ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>

            </div>
        </div>

        <div class="text-end">
            <a href="{{ route('admin.empleados.index') }}" class="btn btn-secondary">Cancelar</a>
            <button class="btn btn-success">Actualizar</button>
        </div>

    </form>

</div>
@endsection