@extends('adminlte::page')

@section('content')

<div class="container-fluid px-2">

    {{-- HEADER CON BOTON VOLVER --}}
    <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
        <div>
            <h3 class="fw-bold mb-1">
                {{ isset($area) ? 'Editar Área' : 'Crear Nueva Área' }}
            </h3>
            <small class="text-muted">
                {{ isset($area) 
                    ? 'Modifique la información del área.' 
                    : 'Defina los parámetros para la nueva área.' }}
            </small>
        </div>

        <a href="{{ route('admin.areas.index') }}" class="btn btn-light px-4 shadow-sm border">
            <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
    </div>

    {{-- ERRORES --}}
    @if($errors->any())
        <div class="alert alert-danger shadow-sm border-0 mb-4" 
             style="border-radius: 10px; background-color: #fef2f2; color: #991b1b;">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CARD --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="{{ isset($area) ? route('admin.areas.update', $area) : route('admin.areas.store') }}" method="POST">
                @csrf
                @if(isset($area))
                    @method('PUT')
                @endif

                <div class="row">

                    {{-- NOMBRE --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre del área</label>
                        <input type="text" name="nombre" class="form-control"
                               placeholder="Ej: Recursos Humanos"
                               value="{{ old('nombre', $area->nombre ?? '') }}" required>
                    </div>

                    {{-- SEDE --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sede</label>
                        <select name="sede_id" class="form-control">
                            <option value="">Seleccione una sede</option>
                            @foreach($sedes as $sede)
                                <option value="{{ $sede->id }}" 
                                    {{ isset($area) && $area->sede_id == $sede->id ? 'selected' : '' }}>
                                    {{ $sede->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- DESCRIPCIÓN --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" rows="4" class="form-control"
                                  placeholder="Breve descripción del área...">{{ old('descripcion', $area->descripcion ?? '') }}</textarea>
                    </div>

                    {{-- ESTADO --}}
                    <div class="col-md-12 mb-4">
                        <div class="p-3 border rounded">

                            <label class="form-label fw-bold">Estado del Área</label>
                            <small class="text-muted d-block mb-2">
                                Seleccione si el área estará activa o inactiva
                            </small>

                            <select name="estado" class="form-control">
                                <option value="1" 
                                    {{ old('estado', $area->estado ?? 1) == 1 ? 'selected' : '' }}>
                                    Activa
                                </option>

                                <option value="0" 
                                    {{ old('estado', $area->estado ?? 1) == 0 ? 'selected' : '' }}>
                                    Inactiva
                                </option>
                            </select>

                        </div>
                    </div>

                </div>

                {{-- BOTONES --}}
                <div class="d-flex justify-content-end mt-3 mb-2">

                    <a href="{{ route('admin.areas.index') }}" 
                       class="btn btn-light me-2 px-4 shadow-sm border">
                        Cancelar
                    </a>

                    <button type="submit" 
                        class="btn text-white px-4"
                        style="background-color: #f97316; border: none;">
                        {{ isset($area) ? 'Actualizar Área' : 'Guardar Área' }}
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

@stop