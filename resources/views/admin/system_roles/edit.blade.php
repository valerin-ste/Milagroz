@extends('adminlte::page')

@section('title', 'Editar Rol de Sistema')

@section('content_header')
    <h1>Editar Rol de Sistema: {{ $role->name }}</h1>
@stop

@section('content')
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">Datos del Rol</h3>
        </div>
        
        <form action="{{ route('admin.system_roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Nombre del Rol <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', $role->name) }}" required {{ $role->name === 'Admin' ? 'readonly' : '' }}>
                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    @if($role->name === 'Admin')
                        <small class="text-muted text-warning">El rol Administrador principal no puede cambiar de nombre.</small>
                    @endif
                </div>

                <div class="form-group mt-4">
                    <label>Asignación de Permisos</label>
                    <p class="text-muted text-sm border-bottom pb-2">Seleccione a qué módulos y acciones tendrá acceso este rol.</p>
                    <div class="row">
                        @forelse($permissions as $permission)
                            <div class="col-md-4 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}" 
                                    {{ (is_array(old('permissions')) && in_array($permission->name, old('permissions'))) || (isset($role) && $role->hasPermissionTo($permission->name)) ? 'checked' : '' }}
                                    {{ $role->name === 'Admin' ? 'disabled checked' : '' }}>
                                    <label for="perm_{{ $permission->id }}" class="custom-control-label font-weight-normal">{{ $permission->name }}</label>
                                </div>
                            </div>
                        @empty
                            <div class="col-12"><div class="alert alert-info">No hay permisos definidos en el sistema todavía.</div></div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card-footer text-right">
                <a href="{{ route('admin.system_roles.index') }}" class="btn btn-default mr-2">Cancelar</a>
                <button type="submit" class="btn btn-warning">Actualizar Rol</button>
            </div>
        </form>
    </div>
@stop
