@extends('adminlte::page')

@section('content')
<div class="container-fluid">

    {{-- TITULO --}}
    <div class="mb-4">
        <h3 class="fw-bold">Editar Rol</h3>
        <small class="text-muted">
            Modifique la información del rol seleccionado.
        </small>
    </div>

    {{-- ERRORES --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CARD --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    {{-- NOMBRE --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nombre del Rol</label>
                        <input type="text" name="nombre" class="form-control"
                               placeholder="Ej: Administrador"
                               value="{{ old('nombre', $role->nombre) }}" required>
                    </div>

                    {{-- ESTADO --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Estado</label>
                        <select name="estado" class="form-control">
                            <option value="1" {{ old('estado', $role->estado ?? 1) == 1 ? 'selected' : '' }}>
                                Activo
                            </option>
                            <option value="0" {{ old('estado', $role->estado ?? 1) == 0 ? 'selected' : '' }}>
                                Inactivo
                            </option>
                        </select>
                    </div>

                    {{-- DESCRIPCION --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion" rows="4" class="form-control"
                                  placeholder="Descripción del rol...">{{ old('descripcion', $role->descripcion) }}</textarea>
                    </div>

                </div>

                {{-- BOTONES --}}
                <div class="d-flex justify-content-end mt-3 me-2 mb-2">

                    <a href="{{ route('admin.roles.index') }}" 
                       class="btn btn-light me-2 px-4">
                        Cancelar
                    </a>

                    <button type="submit" 
                        class="btn text-white px-4"
                        style="background-color: #f97316; border: none;">
                        Actualizar Rol
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>
@stop