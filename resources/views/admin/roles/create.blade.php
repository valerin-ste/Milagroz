@extends('adminlte::page')

@section('content')
<div class="container-fluid">

    {{-- HEADER CON VOLVER --}}
    <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
        <div>
            <div class="mb-1 text-muted small">
                Inicio / Roles / <span class="fw-semibold text-dark">Crear Rol</span>
            </div>
            <h3 class="fw-bold mb-0">Crear Nuevo Rol</h3>
            <small class="text-muted">
                Defina las responsabilidades del nuevo rol en el sistema
            </small>
        </div>

        <a href="{{ route('admin.roles.index') }}" class="btn btn-light px-4">
            <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
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

    {{-- CARD FORM --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                <div class="row">

                    {{-- NOMBRE --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Nombre del rol</label>
                        <input type="text"
                               name="nombre"
                               class="form-control"
                               placeholder="Ej: Administrador"
                               value="{{ old('nombre') }}"
                               required>
                    </div>

                    {{-- ESTADO --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Estado</label>
                        <select name="estado" class="form-control">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>

                    {{-- DESCRIPCIÓN --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion"
                                  rows="4"
                                  class="form-control"
                                  placeholder="Describa las funciones del rol...">{{ old('descripcion') }}</textarea>
                    </div>

                </div>

                {{-- FOOTER FORM --}}
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3"
                     style="border-top:1px solid #f1f5f9;">

                    <small class="text-muted fst-italic">
                        * Complete los campos obligatorios
                    </small>

                    <div class="d-flex gap-2">

                        {{-- CANCELAR --}}
                        <a href="{{ route('admin.roles.index') }}"
                           class="btn"
                           style="background:#f1f5f9; color:#475569;">
                            Cancelar
                        </a>

                        {{-- GUARDAR --}}
                        <button type="submit"
                                class="btn text-white px-4"
                                style="background-color:#f97316;">
                            <i class="fas fa-save me-1"></i>
                            Guardar Rol
                        </button>

                    </div>

                </div>

            </form>

        </div>
    </div>

</div>
@stop