@extends('adminlte::page')

@section('title', 'Seguridad y Salud en el Trabajo')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">Seguridad y Salud en el Trabajo</h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Gestión de documentos de seguridad y salud de los empleados.</p>
    </div>
    <a href="{{ route('admin.seguridad_salud_trabajo.create') }}" class="btn btn-orange">
        <i class="fas fa-plus me-2"></i> Nuevo Documento
    </a>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center" style="background-color: #ecfdf5; color: #047857; border: none; border-radius: var(--radius-md);">
            <i class="fas fa-check-circle fa-lg me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Empleado</th>
                            <th>Tipo de Documento</th>
                            <th>Fecha</th>
                            <th>Archivo</th>
                            <th>Estado</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $doc)
                        <tr @if($doc->estado == 0) style="background-color: #f8fafc; opacity: 0.8;" @endif>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); color: var(--primary-blue);">
                                        <span class="fw-bold">{{ strtoupper(substr($doc->empleado->persona->nombres ?? 'X', 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 1.1rem; color: #1e293b !important;">{{ $doc->empleado->persona->nombres ?? '' }} {{ $doc->empleado->persona->apellidos ?? '' }}</div>
                                        <div class="text-muted" style="font-size: 0.85rem;">Documentación de SST</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="badge" style="background-color: #f1f5f9; color: #334155; font-size: 0.9rem; font-weight: 500; padding: 0.5rem 0.8rem;">
                                    <i class="fas fa-file-alt text-muted me-1"></i> {{ $doc->tipo_documento }}
                                </span>
                            </td>

                            <td style="color: #475569;">
                                <i class="fas fa-calendar-alt text-muted me-1"></i>
                                {{ \Carbon\Carbon::parse($doc->fecha)->format('d/m/Y') }}
                            </td>

                            <td>
                                @if($doc->documentos->count() > 0)
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($doc->documentos as $archivo)
                                            <a href="{{ Storage::url($archivo->ruta) }}" target="_blank" class="btn btn-sm btn-light-custom text-start text-truncate" title="{{ $archivo->nombre_original }}" style="border: 1px solid #e2e8f0; color: #b91c1c; max-width: 150px;">
                                                <i class="fas fa-file-pdf"></i> {{ $archivo->nombre_original }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small italic">Sin archivos</span>
                                @endif
                            </td>

                            <td>
                                @if($doc->estado == 1)
                                    <span class="badge bg-success px-3 py-2 shadow-sm" style="font-size: 0.8rem;">
                                        <i class="fas fa-check-circle me-1"></i> Activo
                                    </span>
                                @else
                                    <span class="badge bg-danger px-3 py-2 shadow-sm" style="font-size: 0.8rem;">
                                        <i class="fas fa-times-circle me-1"></i> Inactivo
                                    </span>
                                @endif
                            </td>

                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    @if($doc->estado == 1)
                                        <a href="{{ route('admin.seguridad_salud_trabajo.edit', $doc) }}" class="btn btn-sm btn-light-custom px-3" title="Editar">
                                            <i class="fas fa-pen text-muted"></i>
                                        </a>

                                        <form action="{{ route('admin.seguridad_salud_trabajo.destroy', $doc) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-light-custom px-3" title="Desactivar" onclick="return confirm('¿Confirma que desea DESACTIVAR este registro?');">
                                                <i class="fas fa-ban text-danger"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-light-custom px-3 opacity-50" disabled title="Inactivo">
                                            <i class="fas fa-pen text-muted"></i>
                                        </button>

                                        <form action="{{ route('admin.seguridad_salud_trabajo.toggle', $doc->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light-custom px-3 text-success shadow-sm" title="Reactivar">
                                                <i class="fas fa-check-circle"></i> Activar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background-color: #f1f5f9;">
                                        <i class="fas fa-shield-alt fa-2x" style="color: #cbd5e1;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1" style="color: #64748b;">No hay registros de Seguridad y Salud</h5>
                                    <p class="mb-0">Comience registrando el primer documento.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($documentos->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4">
            {{ $documentos->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
