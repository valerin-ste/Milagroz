@extends('adminlte::page')

@section('title', 'Roles de Sistema')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Roles de Sistema y Accesos</h1>
        <a href="{{ route('admin.system_roles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Nuevo Rol
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado de Roles (Spati)</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Nombre del Rol</th>
                        <th>Permisos Asignados</th>
                        <th class="text-right" style="width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td><strong class="{{ $role->name === 'Admin' ? 'text-primary' : '' }}">{{ $role->name }}</strong></td>
                            <td>
                                @if($role->permissions->count() > 0)
                                    @foreach($role->permissions->take(5) as $permission)
                                        <span class="badge badge-secondary">{{ $permission->name }}</span>
                                    @endforeach
                                    @if($role->permissions->count() > 5)
                                        <span class="badge badge-light">+{{ $role->permissions->count() - 5 }} más</span>
                                    @endif
                                @else
                                    <span class="text-muted text-sm">Sin permisos específicos</span>
                                    @if($role->name === 'Admin')
                                        <span class="badge badge-success">Acceso Total (Implícito)</span>
                                    @endif
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.system_roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($role->name !== 'Admin')
                                    <form action="{{ route('admin.system_roles.destroy', $role) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar rol de sistema? Los usuarios asociados perderán este acceso.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
