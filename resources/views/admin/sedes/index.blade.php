@extends('adminlte::page')

@section('content')
<div class="container-fluid">

    {{-- TITULO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Gestión de Sedes</h3>
            <small class="text-muted">
                Administra las sedes registradas en el sistema
            </small>
        </div>

        <a href="{{ route('admin.sedes.create') }}" 
           class="btn text-white px-4"
           style="background-color:#0ea5e9;">
            + Nueva Sede
        </a>
    </div>

    {{-- ALERTA --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- TABLA --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            <table class="table table-hover align-middle mb-0">

                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Ciudad</th>
                        <th>Teléfono</th>
                        <th>Áreas</th>
                        <th>Estado</th>
                        <th class="text-end pr-4">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($sedes as $sede)
                    <tr>

                        <td>{{ $sede->id }}</td>

                        <td class="fw-semibold">
                            {{ $sede->nombre }}
                        </td>

                        <td>{{ $sede->ciudad }}</td>

                        <td>{{ $sede->telefono }}</td>

                        {{-- AREAS --}}
                        <td>
                            @php
                                $areas = $sede->areas->pluck('nombre');
                                $max = 2;
                            @endphp

                            @if($areas->isEmpty())
                                <span class="text-muted">Sin áreas</span>
                            @else
                                {{ $areas->take($max)->join(', ') }}
                                @if($areas->count() > $max)
                                    <span class="text-muted">
                                        +{{ $areas->count() - $max }}
                                    </span>
                                @endif
                            @endif
                        </td>

                        {{-- ESTADO --}}
                        <td>
                            @if($sede->estado ?? 1)
                                <span class="badge bg-success px-3 py-2">
                                    Activa
                                </span>
                            @else
                                <span class="badge bg-danger px-3 py-2">
                                    Inactiva
                                </span>
                            @endif
                        </td>

                        {{-- ACCIONES --}}
                        <td class="text-end pr-3">

                            <a href="{{ route('admin.sedes.edit', $sede) }}" 
                               class="btn btn-light btn-sm mr-1">
                                ✏️
                            </a>

                            <form action="{{ route('admin.sedes.destroy', $sede) }}" 
                                  method="POST" 
                                  class="d-inline">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-light btn-sm text-danger"
                                        onclick="return confirm('¿Eliminar sede?')">
                                    🗑
                                </button>
                            </form>

                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            No hay sedes registradas
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

    {{-- PAGINACIÓN --}}
    <div class="mt-3">
        {{ $sedes->links() }}
    </div>

</div>
@stop