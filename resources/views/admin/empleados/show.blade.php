@extends('adminlte::page')

@section('title', 'Perfil del Empleado')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">Expediente del Empleado</h2>
        <p class="text-muted mb-0">Información integral y documentos asociados.</p>
    </div>
    <a href="{{ route('admin.empleados.index') }}" class="btn btn-light-custom px-4 border shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Volver al listado
    </a>
</div>
@stop

@section('content')
<div class="container-fluid px-2 pb-5">

    <div class="row g-4">
        {{-- COLUMNA INFO BÁSICA --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 rounded-lg">
                <div class="card-body text-center pt-5">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px; background-color: rgba(19, 182, 236, 0.1); color: var(--primary-blue); font-size: 2.5rem;">
                        <span class="fw-bold">{{ strtoupper(substr($empleado->persona->nombres ?? 'X', 0, 1)) }}</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $empleado->persona->nombres }} {{ $empleado->persona->apellidos }}</h3>
                    <p class="text-muted mb-4">{{ $empleado->cargo }} - {{ $empleado->area->nombre ?? 'Sin Área' }}</p>
                    
                    <div class="text-start px-3">
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="text-uppercase text-muted fw-bold small mb-3">Datos Personales</h6>
                            <div class="d-flex mb-2">
                                <span class="text-muted" style="width: 120px;">Documento:</span>
                                <span class="fw-semibold">{{ $empleado->persona->tipo_documento }} {{ $empleado->persona->numero_documento }}</span>
                            </div>
                            <div class="d-flex mb-2">
                                <span class="text-muted" style="width: 120px;">Teléfono:</span>
                                <span>{{ $empleado->persona->telefono ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex mb-2">
                                <span class="text-muted" style="width: 120px;">Correo:</span>
                                <span class="text-primary">{{ $empleado->persona->correo ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="mb-2">
                            <h6 class="text-uppercase text-muted fw-bold small mb-3">Datos Laborales</h6>
                            <div class="d-flex mb-2">
                                <span class="text-muted" style="width: 120px;">Sede:</span>
                                <span>{{ $empleado->sede->nombre ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex mb-2">
                                <span class="text-muted" style="width: 120px;">F. Ingreso:</span>
                                <span>{{ \Carbon\Carbon::parse($empleado->fecha_ingreso)->format('d/m/Y') }}</span>
                            </div>
                            <div class="d-flex mb-2">
                                <span class="text-muted" style="width: 120px;">Estado:</span>

                                @if($empleado->estado == 1)
                                    <span class="badge-soft-success">Activo</span>
                                @else
                                    <span class="badge-soft-danger">Inactivo</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUMNA TABS/CONTENIDO --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
                <div class="card-body p-4 bg-light-soft">
                    <h4 class="fw-bold mb-4 px-1" style="color: #1e293b; border-bottom: 2px solid var(--primary-blue); padding-bottom: 10px;">
                        <i class="fas fa-folder-open me-2 text-primary"></i> Expediente Digital de <span class="text-primary">{{ $employee_fullname = $empleado->persona->nombres . ' ' . $empleado->persona->apellidos }}</span>
                    </h4>

                    {{-- SECCIÓN: ETAPA PRECONTRACTUAL --}}
                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-weight: bold;">1</div>
                            <h5 class="fw-bold mb-0" style="color: #334155;">📌 Etapa Precontractual</h5>
                        </div>
                        
                        <div class="row g-3">
                            @forelse($empleado->persona->etapaPrecontractuales as $ep)
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm rounded-lg overflow-hidden border-start border-4 border-info">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-soft-info text-info px-3">Registro del {{ \Carbon\Carbon::parse($ep->fecha_registro)->format('d/m/Y') }}</span>
                                                <small class="text-muted">ID: #{{ $ep->id }}</small>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless align-middle mb-0">
                                                    <thead class="text-muted small text-uppercase">
                                                        <tr>
                                                            <th>Nombre del Documento</th>
                                                            <th class="text-end">Acción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($ep->documentos as $doc)
                                                            <tr class="border-top">
                                                                <td class="py-2">
                                                                    <i class="fas fa-file-pdf text-danger me-2"></i> {{ $doc->nombre_original }}
                                                                </td>
                                                                <td class="text-end py-2">
                                                                    <div class="d-flex justify-content-end gap-2">
                                                                        <a href="{{ Storage::url($doc->ruta) }}" target="_blank" class="btn btn-sm btn-light border shadow-sm" title="Ver archivo">
                                                                            <i class="fas fa-eye text-primary"></i>
                                                                        </a>
                                                                        <a href="{{ Storage::url($doc->ruta) }}" download="{{ $doc->nombre_original }}" class="btn btn-sm btn-light border shadow-sm" title="Descargar">
                                                                            <i class="fas fa-download text-success"></i>
                                                                        </a>
                                                                        
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-4 bg-white rounded-lg shadow-sm">
                                    <p class="text-muted mb-0 italic">No se encontraron archivos en la etapa precontractual.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- SECCIÓN: ETAPA CONTRACTUAL --}}
                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-weight: bold;">2</div>
                            <h5 class="fw-bold mb-0" style="color: #334155;">📌 Etapa Contractual</h5>
                        </div>
                        
                        <div class="row g-3">
                            @forelse($empleado->etapaContractuales as $c)
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm rounded-lg overflow-hidden border-start border-4 border-success">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="fw-bold text-success mb-0">{{ $c->tipo_contrato }}</h6>
                                                <span class="badge bg-light text-dark">Sueldo: ${{ number_format($c->salario) }}</span>
                                            </div>
                                            <small class="text-muted d-block mb-3">Vigencia: {{ \Carbon\Carbon::parse($c->fecha_inicio)->format('d/m/Y') }} @if($c->fecha_fin) al {{ \Carbon\Carbon::parse($c->fecha_fin)->format('d/m/Y') }} @else (Indefinido) @endif</small>
                                            
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless align-middle mb-0">
                                                    <thead class="text-muted small text-uppercase">
                                                        <tr>
                                                            <th>Archivo</th>
                                                            <th class="text-end">Acción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($c->documentos as $doc)
                                                            <tr class="border-top">
                                                                <td class="py-2">
                                                                    <i class="fas fa-file-contract text-success me-2"></i> {{ $doc->nombre_original }}
                                                                </td>
                                                                <td class="text-end py-2">
                                                                    <div class="d-flex justify-content-end gap-2">
                                                                        <a href="{{ Storage::url($doc->ruta) }}" target="_blank" class="btn btn-sm btn-light border shadow-sm" title="Ver archivo">
                                                                            <i class="fas fa-eye text-primary"></i>
                                                                        </a>
                                                                        <a href="{{ Storage::url($doc->ruta) }}" download="{{ $doc->nombre_original }}" class="btn btn-sm btn-light border shadow-sm" title="Descargar">
                                                                            <i class="fas fa-download text-success"></i>
                                                                        </a>
                                                                        
                                                                        <form action="{{ route('admin.documentos.destroy', $doc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar archivo permanentemente?');">
                                                                            @csrf @method('DELETE')
                                                                            <button type="submit" class="btn btn-sm btn-light border shadow-sm" title="Eliminar">
                                                                                <i class="fas fa-trash-alt text-danger"></i>
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-4 bg-white rounded-lg shadow-sm">
                                    <p class="text-muted mb-0">No hay contratos vinculados actualmente.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- SECCIÓN: SEGURIDAD Y SALUD --}}
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-orange text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-weight: bold;">3</div>
                            <h5 class="fw-bold mb-0" style="color: #334155;">📌 Seguridad y Salud en el Trabajo</h5>
                        </div>
                        
                        <div class="row g-3">
                            @forelse($empleado->seguridadSaludTrabajo as $sst)
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm rounded-lg overflow-hidden border-start border-4 border-warning">
                                        <div class="card-body p-3">
                                            <h6 class="fw-bold mb-1">{{ $sst->tipo_documento }}</h6>
                                            <small class="text-muted d-block mb-3">Emitido el: {{ \Carbon\Carbon::parse($sst->fecha)->format('d/m/Y') }}</small>
                                            
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless align-middle mb-0">
                                                    <tbody>
                                                        @foreach($sst->documentos as $doc)
                                                            <tr class="border-top">
                                                                <td class="py-2">
                                                                    <i class="fas fa-heartbeat text-danger me-2"></i> {{ $doc->nombre_original }}
                                                                </td>
                                                                <td class="text-end py-2">
                                                                    <div class="d-flex justify-content-end gap-2">
                                                                        <a href="{{ Storage::url($doc->ruta) }}" target="_blank" class="btn btn-sm btn-light border shadow-sm" title="Ver archivo">
                                                                            <i class="fas fa-eye text-primary"></i>
                                                                        </a>
                                                                        <a href="{{ Storage::url($doc->ruta) }}" download="{{ $doc->nombre_original }}" class="btn btn-sm btn-light border shadow-sm" title="Descargar">
                                                                            <i class="fas fa-download text-success"></i>
                                                                        </a>
                                                                        
                                                                        <form action="{{ route('admin.documentos.destroy', $doc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar archivo permanentemente?');">
                                                                            @csrf @method('DELETE')
                                                                            <button type="submit" class="btn btn-sm btn-light border shadow-sm" title="Eliminar">
                                                                                <i class="fas fa-trash-alt text-danger"></i>
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-4 bg-white rounded-lg shadow-sm">
                                    <p class="text-muted mb-0">No se han registrado documentos de SST.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- SECCIÓN: EVALUACIONES DE DESEMPEÑO --}}
                    <div class="mt-5">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-weight: bold;">4</div>
                            <h5 class="fw-bold mb-0" style="color: #334155;">📌 Evaluaciones de Desempeño</h5>
                        </div>
                        
                        <div class="row g-3">
                            @forelse($empleado->evaluacionesDesempeno->where('estado', 1) as $ev)
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm rounded-lg overflow-hidden border-start border-4 border-info">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="fw-bold text-info mb-0">Calificación: {{ $ev->calificacion }}/10</h6>
                                                <span class="badge bg-soft-secondary text-muted px-3">Fecha: {{ \Carbon\Carbon::parse($ev->fecha)->format('d/m/Y') }}</span>
                                            </div>
                                            @if($ev->observaciones)
                                                <p class="small text-muted mb-3 italic">"{{ $ev->observaciones }}"</p>
                                            @endif
                                            
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless align-middle mb-0">
                                                    <tbody>
                                                        @foreach($ev->documentos as $doc)
                                                            <tr class="border-top">
                                                                <td class="py-2">
                                                                    <i class="fas fa-chart-line text-info me-2"></i> {{ $doc->nombre_original }}
                                                                </td>
                                                                <td class="text-end py-2">
                                                                    <div class="d-flex justify-content-end gap-2">
                                                                        <a href="{{ Storage::url($doc->ruta) }}" target="_blank" class="btn btn-sm btn-light border shadow-sm" title="Ver archivo">
                                                                            <i class="fas fa-eye text-primary"></i>
                                                                        </a>
                                                                        <a href="{{ Storage::url($doc->ruta) }}" download="{{ $doc->nombre_original }}" class="btn btn-sm btn-light border shadow-sm" title="Descargar">
                                                                            <i class="fas fa-download text-success"></i>
                                                                        </a>
                                                                        
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-4 bg-white rounded-lg shadow-sm">
                                    <p class="text-muted mb-0">No se han registrado evaluaciones de desempeño.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- SECCIÓN: FORMACIÓN --}}
                    <div class="mt-5">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-weight: bold;">5</div>
                            <h5 class="fw-bold mb-0" style="color: #334155;">📌 Formación y Certificaciones</h5>
                        </div>
                        
                        <div class="row g-3">
                            @forelse($empleado->formaciones as $f)
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm rounded-lg overflow-hidden border-start border-4 border-secondary">
                                        <div class="card-body p-3">
                                            
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="fw-bold text-secondary mb-0">{{ $f->nombre_curso }}</h6>
                                                <span class="badge bg-light text-dark">{{ $f->institucion }}</span>
                                            </div>

                                            <small class="text-muted d-block mb-3">
                                                {{ $f->fecha_inicio ? \Carbon\Carbon::parse($f->fecha_inicio)->format('d/m/Y') : '' }}
                                                @if($f->fecha_fin)
                                                    - {{ \Carbon\Carbon::parse($f->fecha_fin)->format('d/m/Y') }}
                                                @endif
                                            </small>

                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless align-middle mb-0">
                                                    <tbody>
                                                        @forelse($f->documentos as $doc)
                                                            <tr class="border-top">
                                                                <td class="py-2">
                                                                    <i class="fas fa-graduation-cap text-secondary me-2"></i> {{ $doc->nombre_original }}
                                                                </td>
                                                                <td class="text-end py-2">
                                                                    <div class="d-flex justify-content-end gap-2">
                                                                        <a href="{{ Storage::url($doc->ruta) }}" target="_blank" class="btn btn-sm btn-light border shadow-sm">
                                                                            <i class="fas fa-eye text-primary"></i>
                                                                        </a>
                                                                        <a href="{{ Storage::url($doc->ruta) }}" download class="btn btn-sm btn-light border shadow-sm">
                                                                            <i class="fas fa-download text-success"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="2" class="text-muted text-center">Sin certificados</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-4 bg-white rounded-lg shadow-sm">
                                    <p class="text-muted mb-0">No hay formaciones registradas.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    @endsection

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    .rounded-lg { border-radius: 12px !important; }
    .bg-light-soft { background-color: #f8fafc; }
    .custom-tabs .nav-link {
        color: #64748b;
        font-weight: 600;
        border-radius: 0;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }
    .custom-tabs .nav-link.active {
        background-color: transparent !important;
        color: var(--primary-blue) !important;
        border-bottom-color: var(--primary-blue);
    }
    .badge-soft-success {
        background-color: #dcfce7;
        color: #166534;
        padding: 0.3rem 0.8rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .badge-soft-danger {
        background-color: #fee2e2;
        color: #991b1b;
        padding: 0.3rem 0.8rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .btn-white { background-color: white; }
</style>
@stop
