@extends('adminlte::page')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h3 class="fw-bold">Crear Nueva Sede</h3>
        <small class="text-muted">Defina los parámetros de la sede</small>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="{{ route('admin.sedes.store') }}" method="POST">
                @csrf

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Ciudad</label>
                        <input type="text" name="ciudad" class="form-control">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Dirección</label>
                        <input type="text" name="direccion" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Áreas</label>
                        <select name="areas[]" class="form-control" multiple>
                            @foreach(\App\Models\Area::all() as $area)
                                <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- BOTONES --}}
                <div class="d-flex justify-content-end mt-3 mb-2 pr-3">

                    <a href="{{ route('admin.sedes.index') }}" 
                       class="btn btn-light mr-2">
                        Cancelar
                    </a>

                    <button type="submit" 
                        class="btn text-white px-4"
                        style="background-color:#f97316;">
                        Guardar Sede
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>
@stop