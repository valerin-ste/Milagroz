@extends('adminlte::page')

@section('title', 'Calidad de Documentos')

@section('content_header')
<div class="page-header d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="page-title fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Calidad de Documentos
        </h2>
        <p class="page-subtitle text-muted mb-0">
            Gestión y control de documentos de calidad del personal.
        </p>
    </div>

    <div class="page-actions">
        <a href="{{ route('admin.calidad_documentos.create') }}" class="btn btn-orange">
            <i class="fas fa-plus me-2"></i> Nuevo Documento
        </a>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center border-0 shadow-sm"
             style="background-color: #ecfdf5; color: #047857; border-radius: 12px;">
            <i class="fas fa-check-circle fa-lg me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center border-0 shadow-sm"
             style="border-radius: 12px;">
            <i class="fas fa-exclamation-circle fa-lg me-3"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- FILTROS --}}
    <form method="GET" action="{{ route('admin.calidad_documentos.index') }}" class="mb-4">
        <div class="card shadow-sm border-0 rounded-4" style="background-color: #f8fafc;">
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">

                    {{-- BUSCAR --}}
                    <div class="col-md-3">
                        <label class="text-muted small fw-bold mb-2 ps-1">Buscar empleado / documento</label>
                        <div class="d-flex align-items-center bg-white"
                             style="border-radius: 30px; height: 45px; padding: 0 15px;
                                    box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="fas fa-search text-muted" style="font-size: 14px;"></i>
                            <input type="text" name="buscar"
                                   class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                   placeholder="Nombre, código..." value="{{ request('buscar') }}"
                                   style="outline: none;">
                        </div>
                    </div>

                    {{-- CATEGORÍA --}}
                    <div class="col-md-2">
                        <label class="text-muted small fw-bold mb-2 ps-1">Categoría</label>
                        <div class="d-flex align-items-center bg-white"
                             style="border-radius: 30px; height: 45px; padding: 0 15px;
                                    box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="fas fa-tags text-muted" style="font-size: 14px;"></i>
                            <select name="categoria"
                                    class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                    style="outline: none;">
                                <option value="">-- Todas --</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat }}" {{ request('categoria') == $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- ESTADO --}}
                    <div class="col-md-2">
                        <label class="text-muted small fw-bold mb-2 ps-1">Estado</label>
                        <div class="d-flex align-items-center bg-white"
                             style="border-radius: 30px; height: 45px; padding: 0 15px;
                                    box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="fas fa-filter text-muted" style="font-size: 14px;"></i>
                            <select name="estado"
                                    class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                    style="outline: none;">
                                <option value="">-- Todos --</option>
                                <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                                <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                    </div>

                    {{-- VENCIMIENTO --}}
                    <div class="col-md-2">
                        <label class="text-muted small fw-bold mb-2 ps-1">Vencimiento</label>
                        <div class="d-flex align-items-center bg-white"
                             style="border-radius: 30px; height: 45px; padding: 0 15px;
                                    box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                            <i class="far fa-calendar-alt text-muted" style="font-size: 14px;"></i>
                            <select name="vencimiento"
                                    class="form-control border-0 shadow-none w-100 bg-transparent ms-2 px-0"
                                    style="outline: none;">
                                <option value="">-- Todos --</option>
                                <option value="vigente"  {{ request('vencimiento') == 'vigente'  ? 'selected' : '' }}>Vigente</option>
                                <option value="proximo"  {{ request('vencimiento') == 'proximo'  ? 'selected' : '' }}>Próx. a vencer</option>
                                <option value="vencido"  {{ request('vencimiento') == 'vencido'  ? 'selected' : '' }}>Vencido</option>
                            </select>
                        </div>
                    </div>

                    {{-- BOTONES --}}
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit"
                                class="btn btn-orange fw-bold d-flex justify-content-center align-items-center gap-2 flex-grow-1"
                                style="border-radius: 30px; height: 45px;">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.calidad_documentos.index') }}"
                           class="btn bg-white border fw-bold text-secondary d-flex justify-content-center align-items-center gap-2 flex-grow-1"
                           style="border-radius: 30px; height: 45px;">
                            <i class="fas fa-sync-alt"></i> Limpiar
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </form>

    {{-- TABLA --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th class="ps-4 py-3 text-muted" style="font-weight:600;font-size:0.82rem;text-transform:uppercase;">Empleado</th>
                            <th class="py-3 text-muted" style="font-weight:600;font-size:0.82rem;text-transform:uppercase;">Categoría</th>
                            <th class="py-3 text-muted" style="font-weight:600;font-size:0.82rem;text-transform:uppercase;">Documento</th>
                            <th class="py-3 text-muted" style="font-weight:600;font-size:0.82rem;text-transform:uppercase;">Código / Versión</th>
                            <th class="py-3 text-muted" style="font-weight:600;font-size:0.82rem;text-transform:uppercase;">Emisión</th>
                            <th class="py-3 text-muted" style="font-weight:600;font-size:0.82rem;text-transform:uppercase;">Vencimiento</th>
                            <th class="py-3 text-muted" style="font-weight:600;font-size:0.82rem;text-transform:uppercase;">Archivo</th>
                            <th class="py-3 text-muted" style="font-weight:600;font-size:0.82rem;text-transform:uppercase;">Estado</th>
                            <th class="text-center pe-4 py-3 text-muted" style="font-weight:600;font-size:0.82rem;text-transform:uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($calidad_documentos as $doc)
                        <tr style="border-bottom: 1px solid #f1f5f9; {{ $doc->estado == 0 ? 'background-color:#f8fafc; opacity:0.8;' : '' }}">

                            {{-- EMPLEADO --}}
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center" style="gap: 0.75rem;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width:42px;height:42px;background-color:rgba(19,182,236,0.1);color:var(--primary-blue,#13b6ec);flex-shrink:0;">
                                        <span class="fw-bold" style="font-size:1rem;">
                                            {{ strtoupper(substr($doc->empleado->persona->nombres ?? 'U', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size:0.9rem;line-height:1.2;">
                                            {{ $doc->empleado->persona->nombres ?? '—' }}
                                            {{ $doc->empleado->persona->apellidos ?? '' }}
                                        </div>
                                        <div class="text-muted" style="font-size:0.8rem;">
                                            {{ $doc->empleado->cargo ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- CATEGORÍA --}}
                            <td class="py-3">
                                <span class="badge rounded-pill px-3 py-1"
                                      style="background-color:#e0f2fe; color:#0369a1; font-size:0.78rem; font-weight:600;">
                                    {{ $doc->categoria ?? '—' }}
                                </span>
                            </td>

                            {{-- NOMBRE DOCUMENTO --}}
                            <td class="py-3" style="max-width:200px;">
                                <span class="d-block fw-semibold text-dark text-truncate" style="font-size:0.9rem;" title="{{ $doc->nombre_documento }}">
                                    {{ $doc->nombre_documento }}
                                </span>
                            </td>

                            {{-- CÓDIGO / VERSIÓN --}}
                            <td class="py-3">
                                <span class="d-block text-dark" style="font-size:0.88rem;">
                                    <i class="fas fa-hashtag text-muted me-1" style="font-size:0.75rem;"></i>{{ $doc->codigo ?? '—' }}
                                </span>
                                <span class="d-block text-muted" style="font-size:0.8rem;">
                                    <i class="fas fa-code-branch me-1" style="font-size:0.75rem;"></i>{{ $doc->version ?? '—' }}
                                </span>
                            </td>

                            {{-- FECHA EMISIÓN --}}
                            <td class="py-3" style="color:#475569; font-size:0.88rem;">
                                @if($doc->fecha_emision)
                                    <i class="far fa-calendar me-1 text-muted"></i>
                                    {{ \Carbon\Carbon::parse($doc->fecha_emision)->format('d/m/Y') }}
                                @else
                                    <span class="text-muted fst-italic">—</span>
                                @endif
                            </td>

                            {{-- FECHA VENCIMIENTO --}}
                            <td class="py-3">
                                @if($doc->fecha_vencimiento)
                                    @php
                                        $ev = $doc->estado_vencimiento;
                                        $colorMap = ['vigente'=>'success','proximo'=>'warning','vencido'=>'danger'];
                                        $labelMap = ['vigente'=>'Vigente','proximo'=>'Próx. vencer','vencido'=>'Vencido'];
                                        $iconMap  = ['vigente'=>'check-circle','proximo'=>'exclamation-triangle','vencido'=>'times-circle'];
                                        $bgMap    = ['vigente'=>'#ecfdf5','proximo'=>'#fefce8','vencido'=>'#fef2f2'];
                                        $textMap  = ['vigente'=>'#065f46','proximo'=>'#92400e','vencido'=>'#991b1b'];
                                    @endphp
                                    <div>
                                        <span class="badge rounded-pill px-2 py-1 mb-1"
                                              style="background-color:{{ $bgMap[$ev] }};color:{{ $textMap[$ev] }};font-size:0.75rem;">
                                            <i class="fas fa-{{ $iconMap[$ev] }} me-1"></i>{{ $labelMap[$ev] }}
                                        </span>
                                        <div class="text-muted" style="font-size:0.8rem;">
                                            {{ \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic small">Sin fecha</span>
                                @endif
                            </td>

                            {{-- ARCHIVO --}}
                            <td class="py-3">
                                @if($doc->archivo)
                                    <button type="button"
                                            class="btn btn-sm btn-light border"
                                            data-toggle="modal"
                                            data-target="#docsModal{{ $doc->id }}"
                                            style="border-radius: 8px;">
                                        <i class="fas fa-folder text-warning me-1"></i>
                                        Ver documentos (1)
                                    </button>
                                @else
                                    <span class="text-muted small fst-italic">
                                        <i class="fas fa-file-slash me-1"></i> Sin soporte
                                    </span>
                                @endif
                            </td>

                            {{-- ESTADO --}}
                            <td class="py-3">
                                @if($doc->estado == 1)
                                    <span class="badge bg-success rounded-pill px-2 py-1" style="font-size:0.8rem;">Activo</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-2 py-1" style="font-size:0.8rem;">Inactivo</span>
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td class="text-center pe-4 py-3">
                                <div class="d-flex justify-content-center gap-2">
                                    @if($doc->estado == 1)
                                        <a href="{{ route('admin.calidad_documentos.edit', $doc) }}"
                                           class="btn btn-sm btn-icon btn-outline-primary"
                                           data-toggle="tooltip" data-placement="top" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <form action="{{ route('admin.calidad_documentos.destroy', $doc) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-icon btn-outline-danger"
                                                    data-toggle="tooltip" data-placement="top" title="Desactivar"
                                                    onclick="return confirm('¿Desactivar este documento?');">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-icon border-0 text-muted opacity-50"
                                                data-toggle="tooltip" data-placement="top" title="Edición no disponible"
                                                disabled>
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <form action="{{ route('admin.calidad_documentos.toggle', $doc->id) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light border text-success"
                                                    data-toggle="tooltip" data-placement="top" title="Reactivar"
                                                    style="border-radius: 30px;">
                                                <i class="fas fa-check-circle"></i> Activar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3"
                                         style="width:60px;height:60px;background-color:rgba(19,182,236,0.1);">
                                        <i class="fas fa-folder-open fa-2x" style="color:var(--primary-blue,#13b6ec);"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1" style="color:#64748b;">Sin documentos registrados</h5>
                                    <p class="mb-0">Registra los documentos de calidad del personal.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($calidad_documentos->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4 rounded-bottom-4">
            {{ $calidad_documentos->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     MODALES DE DOCUMENTOS
═══════════════════════════════════════════════════════════ --}}
@foreach($calidad_documentos as $doc)
    @if($doc->archivo)
    <div class="modal fade" id="docsModal{{ $doc->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">

                {{-- HEADER --}}
                <div class="modal-header bg-light border-0 py-3" style="border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fas fa-folder-open text-warning me-2"></i>
                        {{ $doc->nombre_documento }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                {{-- BODY --}}
                <div class="modal-body p-4">
                    @php
                        $ext = $doc->extension_archivo;
                        if ($ext === 'pdf')                          $icon = 'fas fa-file-pdf text-danger';
                        elseif (in_array($ext,['jpg','jpeg','png'])) $icon = 'fas fa-file-image text-primary';
                        elseif (in_array($ext,['doc','docx']))       $icon = 'fas fa-file-word text-info';
                        elseif (in_array($ext,['xls','xlsx']))       $icon = 'fas fa-file-excel text-success';
                        else                                         $icon = 'fas fa-file text-secondary';
                    @endphp

                    {{-- INFO DEL DOCUMENTO --}}
                    <div class="mb-3 p-3 rounded-3" style="background:#f8fafc;">
                        <div class="row g-2 small text-muted">
                            <div class="col-6">
                                <i class="fas fa-tags me-1"></i>
                                <strong>Categoría:</strong> {{ $doc->categoria ?? '—' }}
                            </div>
                            <div class="col-6">
                                <i class="fas fa-hashtag me-1"></i>
                                <strong>Código:</strong> {{ $doc->codigo ?? '—' }}
                            </div>
                            <div class="col-6">
                                <i class="fas fa-code-branch me-1"></i>
                                <strong>Versión:</strong> {{ $doc->version ?? '—' }}
                            </div>
                            <div class="col-6">
                                @if($doc->fecha_vencimiento)
                                    @php $ev = $doc->estado_vencimiento; @endphp
                                    <i class="far fa-calendar-alt me-1"></i>
                                    <strong>Vence:</strong>
                                    <span class="text-{{ $doc->color_vencimiento }}">
                                        {{ \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') }}
                                    </span>
                                @else
                                    <i class="far fa-calendar-alt me-1"></i><strong>Vence:</strong> Sin fecha
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ARCHIVO --}}
                    <div class="border rounded p-3 d-flex justify-content-between align-items-center"
                         style="border-color:#e2e8f0 !important; min-height:85px;">

                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center"
                                 style="width:48px;height:48px;border-radius:10px;background-color:#f8fafc;">
                                <i class="{{ $icon }}" style="font-size:1.5rem;"></i>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-muted" style="font-size:0.78rem;">Archivo adjunto</span>
                                <span class="fw-bold text-dark text-truncate" style="font-size:0.92rem;max-width:200px;">
                                    {{ $doc->nombre_archivo }}
                                </span>
                                <span class="text-muted" style="font-size:0.75rem;">
                                    {{ strtoupper($ext) }}
                                </span>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.calidad_documentos.archivo.view', $doc->id) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary"
                               data-toggle="tooltip" title="Ver archivo">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.calidad_documentos.archivo.download', $doc->id) }}"
                               class="btn btn-sm btn-outline-success"
                               data-toggle="tooltip" title="Descargar">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border px-4 rounded-pill" data-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach

@stop

@section('js')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip({
            placement: 'top',
            trigger: 'hover',
            boundary: 'window'
        });
    });
</script>
@stop

@section('css')
<style>
.btn-icon:hover {
    transform: scale(1.1);
    transition: 0.2s;
}
.btn-orange {
    background-color: #ff6a00;
    border: none;
    color: #fff;
}
.btn-orange:hover {
    background-color: #e65c00;
    color: #fff;
}
</style>
@endsection
