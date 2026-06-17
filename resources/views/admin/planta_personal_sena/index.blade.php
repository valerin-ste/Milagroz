@extends('adminlte::page')

@section('title', 'Planta Personal SENA')

@section('content_header')
<div class="page-header d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="page-title fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Módulo Planta Personal SENA
        </h2>
        <p class="page-subtitle text-muted mb-0">
            Listado de registros de planta de personal vinculados al SENA.
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.planta_personal_sena.create') }}" class="btn btn-orange">
            <i class="fas fa-plus me-2"></i> Nuevo Registro
        </a>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center border-0 shadow-sm mb-3"
             style="background-color:#ecfdf5; color:#047857; border-radius:12px;">
            <i class="fas fa-check-circle fa-lg me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center border-0 shadow-sm mb-3"
             style="background-color:#fef2f2; color:#b91c1c; border-radius:12px;">
            <i class="fas fa-exclamation-circle fa-lg me-3"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- FILTROS --}}
    <form method="GET" action="{{ route('admin.planta_personal_sena.index') }}" class="mb-4">
        <div class="card shadow-sm border-0 rounded-4" style="background-color:#f8fafc;">
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label class="text-muted small fw-bold mb-2 ps-1">Buscar Empleado</label>
                        <div class="d-flex align-items-center bg-white"
                             style="border-radius:30px; height:45px; padding:0 15px; box-shadow:0 2px 8px rgba(0,0,0,0.04); border:1px solid #e2e8f0;">
                            <i class="fas fa-search text-muted" style="font-size:14px;"></i>
                            <input type="text" name="buscar"
                                   class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                   placeholder="Nombre o Documento..." value="{{ request('buscar') }}"
                                   style="outline:none;">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="text-muted small fw-bold mb-2 ps-1">Estado</label>
                        <div class="d-flex align-items-center bg-white"
                             style="border-radius:30px; height:45px; padding:0 15px; box-shadow:0 2px 8px rgba(0,0,0,0.04); border:1px solid #e2e8f0;">
                            <i class="fas fa-filter text-muted" style="font-size:14px;"></i>
                            <select name="estado"
                                    class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                    style="outline:none;">
                                <option value="">-- Todos --</option>
                                <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                                <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="text-muted small fw-bold mb-2 ps-1">Fecha Reporte</label>
                        <div class="d-flex align-items-center bg-white"
                             style="border-radius:30px; height:45px; padding:0 15px; box-shadow:0 2px 8px rgba(0,0,0,0.04); border:1px solid #e2e8f0;">
                            <i class="far fa-calendar-alt text-muted" style="font-size:14px;"></i>
                            <input type="date" name="fecha"
                                   class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                   value="{{ request('fecha') }}" style="outline:none;">
                        </div>
                    </div>

                    <button type="submit"
                            class="btn btn-orange flex-grow-1 fw-bold d-flex justify-content-center align-items-center gap-2"
                            style="border-radius:30px; height:45px;">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>

                    <a href="{{ route('admin.planta_personal_sena.index') }}"
                       class="btn bg-white border flex-grow-1 fw-bold text-secondary d-flex justify-content-center align-items-center gap-2"
                       style="border-radius:30px; height:45px;">
                        <i class="fas fa-sync-alt"></i> Limpiar
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- TABLA --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color:#f8fafc; border-bottom:2px solid #e2e8f0;">
                        <tr>
                            <th class="ps-4 py-3 text-muted" style="font-weight:600; font-size:0.85rem; text-transform:uppercase;">Empleado</th>
                            <th class="py-3 text-muted" style="font-weight:600; font-size:0.85rem; text-transform:uppercase;">Fecha Reporte</th>
                            <th class="py-3 text-muted" style="font-weight:600; font-size:0.85rem; text-transform:uppercase;">Observaciones</th>
                            <th class="py-3 text-muted" style="font-weight:600; font-size:0.85rem; text-transform:uppercase;">Estado</th>
                            <th class="text-center pe-4 py-3 text-muted" style="font-weight:600; font-size:0.85rem; text-transform:uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registros as $reg)
                        <tr style="border-bottom:1px solid #f1f5f9; {{ $reg->estado == 0 ? 'background-color:#f8fafc; opacity:0.8;' : '' }}">

                            {{-- EMPLEADO --}}
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width:45px; height:45px; background-color:rgba(255,106,0,0.1); color:#ff6a00; flex-shrink:0;">
                                        <span class="fw-bold" style="font-size:1.1rem;">
                                            {{ strtoupper(substr($reg->empleado->persona->nombres ?? 'U', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size:0.95rem;">
                                            {{ $reg->empleado->persona->nombres ?? '' }} {{ $reg->empleado->persona->apellidos ?? '' }}
                                        </div>
                                        <div class="text-muted" style="font-size:0.83rem;">
                                            {{ $reg->empleado->cargo ?? 'Sin cargo' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- FECHA --}}
                            <td class="py-3" style="color:#475569;">
                                {{ \Carbon\Carbon::parse($reg->fecha_reporte)->format('d/m/Y') }}
                            </td>

                            {{-- OBSERVACIONES --}}
                            <td class="py-3" style="max-width:220px;">
                                <span class="text-muted" style="font-size:0.88rem;">
                                    {{ $reg->observaciones ? \Str::limit($reg->observaciones, 60, '…') : '—' }}
                                </span>
                            </td>

                            {{-- ESTADO --}}
                            <td class="py-3">
                                @if($reg->estado == 1)
                                    <span class="badge bg-success px-2 py-1 rounded-pill" style="font-size:0.8rem;">Activo</span>
                                @else
                                    <span class="badge bg-secondary px-2 py-1 rounded-pill" style="font-size:0.8rem;">Inactivo</span>
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td class="text-center pe-4 py-3">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.planta_personal_sena.show', $reg) }}"
                                       class="btn btn-sm btn-icon btn-outline-info"
                                       title="Ver detalle"><i class="fas fa-eye"></i></a>

                                    @if($reg->estado == 1)
                                        <a href="{{ route('admin.planta_personal_sena.edit', $reg) }}"
                                           class="btn btn-sm btn-icon btn-outline-primary"
                                           title="Editar"><i class="fas fa-pen"></i></a>

                                        <form action="{{ route('admin.planta_personal_sena.destroy', $reg) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-icon btn-outline-danger"
                                                    title="Desactivar"
                                                    onclick="return confirm('¿Desactivar este registro?');">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-icon border-0 text-muted opacity-50" disabled>
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <form action="{{ route('admin.planta_personal_sena.toggle', $reg->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-light border text-success"
                                                    style="border-radius:30px;" title="Reactivar">
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
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3"
                                         style="width:60px; height:60px; background-color:rgba(255,106,0,0.1);">
                                        <i class="fas fa-users fa-2x" style="color:#ff6a00;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1" style="color:#64748b;">Sin registros de Planta Personal SENA</h5>
                                    <p class="mb-0">Aún no se han creado registros en este módulo.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($registros->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4">
            {{ $registros->links() }}
        </div>
        @endif
    </div>
</div>
@stop

@section('js')
<script>
$(function () { $('[title]').tooltip({ placement: 'top', trigger: 'hover' }); });
</script>
@stop

@section('css')
<style>
.btn-icon { width:34px; height:34px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; }
.btn-icon:hover { transform:scale(1.12); transition:.2s; }
.btn-orange { background-color:#ff6a00; border:none; color:#fff; }
.btn-orange:hover { background-color:#e65c00; color:#fff; }
</style>
@endsection
