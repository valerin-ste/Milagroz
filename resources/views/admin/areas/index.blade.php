@extends('adminlte::page')

@section('content')

<div class="mb-4">

    {{-- TITULO --}}
    <div class="mb-3">
        <h3 class="fw-bold mb-0">Gestión de Áreas</h3>
        <small class="text-muted">Organiza y administra las áreas</small>
    </div>

    {{-- FILA DE FILTRO --}}
    <div class="d-flex align-items-center">

        {{-- IZQUIERDA --}}
        <div class="d-flex align-items-center">

            <form method="GET" action="{{ route('admin.areas.index') }}" 
                  class="d-flex align-items-center">

                <input 
                    type="text" 
                    name="buscar" 
                    class="form-control"
                    placeholder="Buscar área..."
                    value="{{ request('buscar') }}"
                    style="width: 250px; margin-right: 10px;"
                >

                <button type="submit" 
                        class="btn btn-info text-white"
                        style="margin-right: 10px;">
                    Buscar
                </button>
            </form>

            <a href="{{ route('admin.areas.index') }}" 
               class="btn btn-outline-secondary" 
               style="margin-right: 10px;">
                Limpiar
            </a>

        </div>

        {{-- DERECHA --}}
        <div class="ml-auto">
            <a href="{{ route('admin.areas.create') }}" 
               class="btn btn-info text-white">
                + Nueva Área
            </a>
        </div>

    </div>

</div>

    {{-- ALERTA --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- TABLA --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Área</th>
                        <th>Sede</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($areas as $area)
                    <tr>
                        <td><strong>#{{ $area->id }}</strong></td>

                        <td class="fw-semibold">
                            {{ $area->nombre }}
                        </td>

                        <td>
                            {{ $area->sede->nombre ?? 'Sin sede' }}
                        </td>

                        <td class="text-muted">
                            {{ $area->descripcion ?? 'Sin descripción' }}
                        </td>

                        <td>
                            @if($area->estado == 1)
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
                        <td class="text-center">
                            <a href="{{ route('admin.areas.edit', $area) }}" 
                               class="btn btn-sm btn-light border">
                                ✏️
                            </a>

                            <form action="{{ route('admin.areas.destroy', $area) }}" 
                                  method="POST" 
                                  class="d-inline">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-light border"
                                    onclick="return confirm('¿Eliminar esta área?')">
                                    🗑️
                                </button>
                            </form>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No hay áreas registradas
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

    {{-- PAGINACIÓN --}}
    <div class="mt-3">
        {{ $areas->links() }}
    </div>

    {{-- CARDS DE RESUMEN --}}
    <div class="row mt-4">

        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center p-3">
                <h6 class="text-muted">TOTAL ÁREAS</h6>
                <h3 class="fw-bold">{{ $totalAreas }}</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center p-3">
                <h6 class="text-muted">ÁREAS ACTIVAS</h6>
                <h3 class="fw-bold text-success">{{ $areasActivas }}</h3>
            </div>
    
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center p-3">
        <h6 class="text-muted">ÁREAS INACTIVAS</h6>
        <h3 class="fw-bold text-danger">{{ $areasInactivas }}</h3>
    </div>
    </div>

    </div>

</div>

@stop