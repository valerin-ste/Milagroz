@extends('adminlte::page')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Gestión de Empleados</h3>
            <small class="text-muted">Administración del personal</small>
        </div>

        <a href="{{ route('admin.personas.create') }}" 
           class="btn text-white px-4"
           style="background-color:#f97316;">
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
                            <th class="px-4 py-3">Documento</th>
                            <th class="px-4 py-3">Nombre</th>
                            <th class="px-4 py-3">Teléfono</th>
                            <th class="px-4 py-3">Correo</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($personas as $p)
                        <tr class="border-top">

                            <td class="px-4 py-3 text-muted">
                                {{ $p->tipo_documento }} - {{ $p->numero_documento }}
                            </td>

                            <td class="px-4 py-3 fw-semibold">
                                {{ $p->nombres }} {{ $p->apellidos }}
                            </td>

                            <td class="px-4 py-3 text-muted">
                                {{ $p->telefono }}
                            </td>

                            <td class="px-4 py-3 text-muted">
                                {{ $p->correo }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                <div class="d-flex justify-content-center gap-2">

                                    <a href="{{ route('admin.personas.edit', $p) }}"
                                       class="btn btn-sm"
                                       style="background:#eef2ff; color:#4338ca;">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.personas.destroy', $p) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm"
                                                style="background:#fee2e2; color:#dc2626;"
                                                onclick="return confirm('¿Eliminar empleado?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No hay empleados registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer bg-white border-0">
            {{ $personas->links() }}
        </div>

    </div>

</div>
@endsection