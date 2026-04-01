@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">Etapa Contractual</h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Gestión y control de los contratos laborales firmados.</p>
    </div>
    <a href="{{ route('admin.etapa_contractual.create') }}" class="btn btn-orange">
        <i class="fas fa-plus me-2"></i> Nuevo Contrato
    </a>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    <form method="GET" action="{{ route('admin.etapa_contractual.index') }}" class="filter-card">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="filter-label">Buscar Empleado</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="buscar" class="form-control"
                           placeholder="Nombre o apellido..." value="{{ request('buscar') }}">
                </div>
            </div>

            <div class="col-md-2">
                <label class="filter-label">Estado</label>
                <select name="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="filter-label">Rango de Vigencia (Inicio – Fin)</label>
                <div class="input-group">
                    <input type="date" name="desde" value="{{ request('desde') }}" class="form-control">
                    <span class="input-group-text" style="background:transparent; border-left:0; border-right:0;">a</span>
                    <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control">
                </div>
            </div>

            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1" style="height:38px;">
                        <i class="fas fa-filter mr-1"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.etapa_contractual.index') }}" class="btn btn-light border btn-sm flex-grow-1" style="height:38px;">
                        <i class="fas fa-undo mr-1"></i> Limpiar
                    </a>
                </div>
            </div>
        </div>
    </form>

    <div class="card border-0">
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom" style="background:#fafbfc;">
            <span style="font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#94a3b8;">Contratos registrados</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Empleado Contratado</th>
                            <th>Tipo y Salario</th>
                            <th>Fechas de Vigencia</th>
                            <th>Estado Actual</th>
                            <th>Soporte Digital</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contratos as $c)
                        <tr>
                            <td style="padding-left:1.5rem;">
                                <div class="d-flex align-items-center" style="gap:0.85rem;">
                                    <div class="cell-avatar">
                                        {{ strtoupper(substr($c->empleado->persona->nombres ?? 'X', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="cell-primary">
                                            {{ $c->empleado->persona->nombres ?? '' }} {{ $c->empleado->persona->apellidos ?? '' }}
                                        </div>
                                        <div class="cell-secondary">
                                            <i class="fas fa-id-card mr-1"></i>{{ $c->empleado->persona->numero_documento ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="d-block" style="color: #334155; font-weight: 500;">
                                    <i class="fas fa-file-signature text-muted me-1"></i> {{ $c->tipo_contrato }}
                                </span>
                                <span class="badge" style="background-color: #f1f5f9; color: #0f172a; font-size: 0.85rem; font-weight: 500;">
                                    $ {{ number_format($c->salario, 2) }}
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
                                @php $badge = $c->getStatusBadge($c->fecha_fin); @endphp
                                
                                @if($c->estado == 0)
                                    <span class="badge-soft-danger"><i class="fas fa-ban"></i> Inactivo</span>
                                @else
                                    <span class="{{ $badge['class'] }}">
                                        <i class="{{ $badge['icon'] }}"></i> {{ $badge['label'] }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($c->documentos->count() > 0)
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($c->documentos as $doc)
                                            <a href="{{ Storage::url($doc->ruta) }}" target="_blank" class="btn btn-sm btn-light-custom text-start text-truncate" title="{{ $doc->nombre_original }}" style="border: 1px solid #e2e8f0; color: #b91c1c; max-width: 160px; font-size: 0.8rem;">
                                                <i class="fas fa-file-pdf"></i> {{ $doc->nombre_original }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic">Sin archivos</span>
                                @endif
                            </td>

                            <td class="text-center pe-4">
                                <div class="action-container">
                                    @if($c->estado == 1)
                                        <a href="{{ route('admin.etapa_contractual.edit', $c) }}"
                                           class="btn-table-action"
                                           data-toggle="tooltip" data-placement="top" title="Editar contrato">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <form action="{{ route('admin.etapa_contractual.destroy', $c) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-table-action"
                                                    data-toggle="tooltip" data-placement="top" title="Desactivar contrato"
                                                    onclick="return confirm('¿Confirma que desea DESACTIVAR este contrato?');">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn-table-action opacity-50"
                                                data-toggle="tooltip" data-placement="top" title="Edición no disponible (Inactivo)"
                                                style="cursor:not-allowed;" disabled>
                                            <i class="fas fa-pen"></i>
                                        </button>

                                        <form action="{{ route('admin.etapa_contractual.toggle', $c->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-table-action"
                                                    data-toggle="tooltip" data-placement="top" title="Reactivar contrato">
                                                <i class="fas fa-check-circle"></i>
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
                                    <h5 class="fw-bold mb-1" style="color: #64748b;">Aún no hay contratos registrados</h5>
                                    <p class="mb-0">Comience registrando el primer contrato.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($contratos->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4">
            {{ $contratos->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
