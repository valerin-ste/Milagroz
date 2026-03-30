@extends('adminlte::page')

@section('content')
<div class="container-fluid">

    {{-- HEADER CON VOLVER --}}
    <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
        <div>
            <h3 class="fw-bold mb-1">Editar Sede</h3>
            <small class="text-muted">Modifique la información de la sede</small>
        </div>

        <a href="{{ route('admin.sedes.index') }}" class="btn btn-light px-4">
            <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="{{ route('admin.sedes.update', $sede) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control"
                               value="{{ $sede->nombre }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Ciudad</label>
                        <input type="text" name="ciudad" class="form-control"
                               value="{{ $sede->ciudad }}">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Dirección</label>
                        <input type="text" name="direccion" class="form-control"
                               value="{{ $sede->direccion }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" class="form-control"
                               value="{{ $sede->telefono }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Áreas</label>
                        <select name="areas[]" class="form-control" multiple>
                            @foreach(\App\Models\Area::all() as $area)
                                <option value="{{ $area->id }}"
                                    {{ $sede->areas->contains($area->id) ? 'selected' : '' }}>
                                    {{ $area->nombre }}
                                </option>
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
                        Actualizar Sede
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>
@stop