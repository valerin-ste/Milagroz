@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">Etapa Precontractual</h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Gestión y control de los registros precontractuales.</p>
    </div>
    <a href="{{ route('admin.etapa_precontractual.create') }}" class="btn btn-orange">
        <i class="fas fa-plus me-2"></i> Nuevo Registro
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
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Empleado</th>
                            <th>Tipo de Registro</th>
                            <th>Fechas</th>
                            <th>Estado</th>
                            <th>Documentos</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($etapas as $c)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); color: var(--primary-blue);">
                                        <span class="fw-bold">{{ strtoupper(substr($c->persona->nombres ?? 'X', 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $c->persona->nombres ?? '' }} {{ $c->persona->apellidos ?? '' }}</div>
                                        <div class="text-muted" style="font-size: 0.85rem;">CC: {{ $c->persona->numero_documento ?? '' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="d-block" style="color: #334155; font-weight: 500;">
                                    <i class="fas fa-file-signature text-muted me-1"></i> {{ $c->tipo ?? 'N/A' }}
                                </span>
                            </td>

                            <td style="color: #475569;">
                                <div class="mb-1">
                                    <i class="fas fa-calendar-check text-success me-1"></i>
                                    {{ \Carbon\Carbon::parse($c->fecha_inicio)->format('d M, Y') }}
                                </div>
                                <div class="small text-muted">
                                    <i class="fas fa-calendar-times text-danger me-1"></i>
                                    {{ $c->fecha_fin ? \Carbon\Carbon::parse($c->fecha_fin)->format('d M, Y') : 'Indefinido' }}
                                </div>
                            </td>

                            <td>
                                @php
                                    $vencido = false;
                                    if ($c->fecha_fin && \Carbon\Carbon::parse($c->fecha_fin)->isPast()) {
                                        $vencido = true;
                                    }
                                @endphp
                                
                                @if($c->estado == 0)
                                    <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>
                                @elseif($vencido)
                                    <span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Vencido</span>
                                @else
                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Vigente</span>
                                @endif
                            </td>

                            <td>
                                @if($c->documentos->count() > 0)
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($c->documentos as $doc)
                                            <a href="{{ Storage::url($doc->ruta) }}" target="_blank" class="btn btn-sm btn-outline-secondary text-truncate" style="max-width: 160px; font-size: 0.8rem;" title="{{ $doc->nombre_original }}">
                                                <i class="fas fa-file-pdf text-danger"></i> {{ $doc->nombre_original }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic">Sin archivos</span>
                                @endif
                            </td>

                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    @if($c->estado == 1)
                                        <a href="{{ route('admin.etapa_precontractual.edit', $c) }}" class="btn btn-sm btn-light-custom px-3" title="Editar">
                                            <i class="fas fa-pen text-muted"></i>
                                        </a>

                                        <form action="{{ route('admin.etapa_precontractual.destroy', $c) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-light-custom px-3" title="Desactivar" onclick="return confirm('¿Confirma que desea DESACTIVAR este registro?');">
                                                <i class="fas fa-ban text-danger"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-light-custom px-3 opacity-50" title="Registro inactivo" style="cursor:not-allowed;">
                                            <i class="fas fa-pen text-muted"></i>
                                        </button>

                                        <form action="{{ route('admin.etapa_precontractual.toggle', $c->id) }}" method="POST" class="d-inline">
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
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background-color: #f1f5f9;">
                                        <i class="fas fa-handshake fa-2x" style="color: #cbd5e1;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1" style="color: #64748b;">Aún no hay registros precontractuales</h5>
                                    <p class="mb-0">Comience registrando el primer registro.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($etapas->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4">
            {{ $etapas->links() }}
        </div>
        @endif
    </div>

</div>
@endsection