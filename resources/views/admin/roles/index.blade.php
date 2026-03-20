@extends('adminlte::page')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Gestión de Roles</h3>
            <small class="text-muted">Administración de roles del sistema</small>
        </div>

        <a href="{{ route('admin.roles.create') }}" 
           class="btn text-white px-4"
           style="background-color:#f97316;">
            <i class="fas fa-plus me-1"></i> Nuevo Rol
        </a>
    </div>

    {{-- MENSAJE --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- CARD --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table align-middle mb-0">

                    {{-- HEADER TABLA --}}
                    <thead style="background:#f8fafc;">
                        <tr class="text-muted">
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Rol</th>
                            <th class="px-4 py-3">Descripción</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($roles as $role)
                        <tr class="border-top">

                            {{-- ID --}}
                            <td class="px-4 py-3 text-muted">
                                {{ $role->id }}
                            </td>

                          
                            {{-- NOMBRE --}}
                            <td class="px-4 py-3">
                                <span class="fw-semibold">
                                    {{ $role->nombre }}
                                </span>
                            </td>

                            {{-- DESCRIPCIÓN --}}
                            <td class="px-4 py-3 text-muted">
                                {{ $role->descripcion ?? 'Sin descripción' }}
                            </td>

                            {{-- ESTADO (PUEDES HACERLO DINÁMICO LUEGO) --}}
                            <td class="px-4 py-3">
                                <span class="badge px-3 py-2"
                                      style="background:#dcfce7; color:#15803d;">
                                    ● Activo
                                </span>
                            </td>

                            {{-- ACCIONES --}}
                            <td class="px-4 py-3 text-center">
                                <div class="d-flex justify-content-center gap-2">

                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                       class="btn btn-sm"
                                       style="background:#eef2ff; color:#4338ca;">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.roles.destroy', $role) }}" 
                                          method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm"
                                                style="background:#fee2e2; color:#dc2626;"
                                                onclick="return confirm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>

                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No hay roles registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        {{-- PAGINACIÓN --}}
        @if(method_exists($roles, 'links'))
        <div class="card-footer bg-white border-0">
            {{ $roles->links() }}
        </div>
        @endif

    </div>

</div>
@endsection