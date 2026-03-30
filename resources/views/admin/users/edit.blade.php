@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
    <h1>Editar Usuario: {{ $user->name }}</h1>
@stop

@section('content')
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">Datos del Usuario</h3>
        </div>
        
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="name">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="email">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-12"><small class="text-muted mb-2 d-block">Dejar en blanco si no desea cambiar la contraseña</small></div>
                    <div class="form-group col-md-6">
                        <label for="password">Nueva Contraseña</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password">
                        @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation">
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label>Roles de Sistema</label>
                    <div class="row">
                        @foreach($roles as $role)
                            <div class="col-md-3">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="role_{{ $role->id }}" name="roles[]" value="{{ $role->name }}" 
                                    {{ (is_array(old('roles')) && in_array($role->name, old('roles'))) || (isset($user) && $user->roles->contains($role->id)) ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}" class="custom-control-label">{{ $role->name }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card-footer text-right">
                <a href="{{ route('admin.users.index') }}" class="btn btn-default mr-2">Cancelar</a>
                <button type="submit" class="btn btn-warning">Actualizar Usuario</button>
            </div>
        </form>
    </div>
@stop
