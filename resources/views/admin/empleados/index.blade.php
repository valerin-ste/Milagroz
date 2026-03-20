@extends('adminlte::page')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Gestión de Empleados</h3>
            <small class="text-muted">Administración del personal</small>
        </div>

        <a href="{{ route('admin.empleados.create') }}" 
           class="btn text-white px-4"
           style="background:#f97316;">
            <i class="fas fa-plus me-1"></i> Nuevo Empleado
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

                    <thead style="background:#f8fafc;">
                        <tr class="text-muted">
                            <th class="px-4 py-3">Empleado</th>
                            <th class="px-4 py-3">Área</th>
                            <th class="px-4 py-3">Sede</th>
                            <th class="px-4 py-3">Rol</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($empleados as $e)
                        <tr class="border-top">

                            {{-- NOMBRE --}}
                            <td class="px-4 py-3 fw-semibold">
                                {{ $e->persona->nombres ?? '' }} {{ $e->persona->apellidos ?? '' }}
                                <br>
                                <small class="text-muted">
                                    {{ $e->persona->numero_documento ?? '' }}
                                </small>
                            </td>

                            {{-- AREA --}}
                            <td class="px-4 py-3 text-muted">
                                {{ $e->area->nombre ?? 'Sin área' }}
                            </td>

                            {{-- SEDE --}}
                            <td class="px-4 py-3 text-muted">
                                {{ $e->sede->nombre ?? 'Sin sede' }}
                            </td>

                            {{-- ROL --}}
                            <td class="px-4 py-3 text-muted">
                                {{ $e->rol->nombre ?? 'Sin rol' }}
                            </td>

                            {{-- ESTADO --}}
                            <td class="px-4 py-3">
                                @if($e->estado)
                                    <span class="badge px-3 py-2"
                                          style="background:#dcfce7; color:#15803d;">
                                        ● Activo
                                    </span>
                                @else
                                    <span class="badge px-3 py-2"
                                          style="background:#fee2e2; color:#dc2626;">
                                        ● Inactivo
                                    </span>
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td class="px-4 py-3 text-center">
                                <div class="d-flex justify-content-center gap-2">

                                    <a href="{{ route('admin.empleados.edit', $e) }}"
                                       class="btn btn-sm"
                                       style="background:#eef2ff; color:#4338ca;">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.empleados.destroy', $e) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm"
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
                            <td colspan="6" class="text-center py-4 text-muted">
                                No hay empleados registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer bg-white border-0">
            {{ $empleados->links() }}
        </div>

    </div>

</div>
@endsection