@extends('adminlte::page')

@section('content')
<div class="container-fluid px-2">

    <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
        <h2 class="fw-bold mb-0">Listado de Comunicaciones</h2>
        <a href="{{ route('admin.comunicaciones.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Nueva Comunicación
        </a>
    </div>

    <div class="card shadow-sm border-0 mt-3">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Empleado</th>
                        <th>Asunto</th>
                        <th>Fecha</th>
                        <th>Archivos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($comunicaciones as $c)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ $c->empleado->persona->nombres ?? '' }}
                                {{ $c->empleado->persona->apellidos ?? '' }}
                            </td>
                            <td>{{ $c->asunto }}</td>
                            <td>{{ $c->fecha }}</td>
                            <td>
                                @forelse($c->documentos as $doc)
                                    <a href="{{ asset('storage/' . $doc->ruta) }}" target="_blank" title="{{ $doc->nombre_original }}">
                                        📎 {{ $doc->nombre_original }}
                                    </a><br>
                                @empty
                                    <span class="text-muted">Sin archivos</span>
                                @endforelse
                            </td>
                            <td>
                                <a href="{{ route('admin.comunicaciones.edit', $c) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('admin.comunicaciones.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Desea eliminar esta comunicación?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if($comunicaciones->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center text-muted">No hay comunicaciones registradas</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection