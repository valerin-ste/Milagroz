@extends('adminlte::page')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Etapa Precontractual</h3>
            <small class="text-muted">Gestión de documentos precontractuales</small>
        </div>

        <a href="{{ route('admin.etapa_precontractual.create') }}" 
           class="btn text-white px-4"
           style="background:#f97316;">
            <i class="fas fa-plus me-1"></i> Nuevo Registro
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
                            <th class="px-4 py-3">Candidato</th>
                            <th class="px-4 py-3">Documento ID</th>
                            <th class="px-4 py-3">Fecha Registro</th>
                            <th class="px-4 py-3">Archivo</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($etapas as $etapa)
                        <tr class="border-top">

                            {{-- NOMBRE --}}
                            <td class="px-4 py-3 fw-semibold">
                                {{ $etapa->persona->nombres ?? '' }} {{ $etapa->persona->apellidos ?? '' }}
                            </td>

                            {{-- DOCUMENTO ID --}}
                            <td class="px-4 py-3 text-muted">
                                {{ $etapa->persona->numero_documento ?? '' }}
                            </td>

                            {{-- FECHA --}}
                            <td class="px-4 py-3 text-muted">
                                {{ $etapa->fecha_registro ? \Carbon\Carbon::parse($etapa->fecha_registro)->format('d/m/Y') : 'N/A' }}
                            </td>

                            {{-- ARCHIVO --}}
                            <td class="px-4 py-3">
                                @if($etapa->archivo)
                                    <a href="{{ Storage::url($etapa->archivo) }}" target="_blank" class="text-decoration-none">
                                        <i class="fas fa-file-pdf text-danger fa-lg"></i> Ver Archivo
                                    </a>
                                @else
                                    <span class="text-muted">Sin Archivo</span>
                                @endif
                            </td>

                            {{-- ESTADO --}}
                            <td class="px-4 py-3">
                                @if($etapa->estado == 'aprobado')
                                    <span class="badge px-3 py-2" style="background:#dcfce7; color:#15803d;">
                                        ● Aprobado
                                    </span>
                                @elseif($etapa->estado == 'rechazado')
                                    <span class="badge px-3 py-2" style="background:#fee2e2; color:#dc2626;">
                                        ● Rechazado
                                    </span>
                                @else
                                    <span class="badge px-3 py-2" style="background:#fef3c7; color:#b45309;">
                                        ● En Proceso
                                    </span>
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td class="px-4 py-3 text-center">
                                <div class="d-flex justify-content-center gap-2">

                                    <a href="{{ route('admin.etapa_precontractual.edit', $etapa) }}"
                                       class="btn btn-sm"
                                       style="background:#eef2ff; color:#4338ca;" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.etapa_precontractual.destroy', $etapa) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm"
                                                style="background:#fee2e2; color:#dc2626;"
                                                onclick="return confirm('¿Eliminar este registro?');" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No hay registros de etapa precontractual
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer bg-white border-0">
            {{ $etapas->links() }}
        </div>

    </div>

</div>
@endsection
