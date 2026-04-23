@extends('adminlte::page')

@section('content_header')
<div class="page-header">
    <div>
        <h2 class="page-title">
            <i class="fas fa-users"></i>
            Gestión de Empleados
        </h2>
        <p class="page-subtitle">
            Administración y control del personal médico y administrativo
        </p>
    </div>

    <div class="page-actions">
        <div class="dropdown">
            <button class="btn btn-light btn-sm shadow-sm dropdown-toggle" data-toggle="dropdown">
                <i class="fas fa-file-download"></i> Reportes
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="{{ route('admin.empleados.reporte.pdf', request()->all()) }}">
                    <i class="fas fa-file-pdf text-danger"></i> Exportar PDF
                </a>
            </div>
        </div>

        {{-- 🔶 BOTÓN NARANJA --}}
        <a href="{{ route('admin.empleados.create') }}" class="btn btn-orange btn-sm">
            <i class="fas fa-plus"></i> Nuevo Empleado
        </a>
    </div>
</div>
@stop

@section('content')

<div class="container-fluid">

    @if(session('success'))
        <div class="alert-success-modern">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- FILTROS --}}
    <form method="GET" action="{{ route('admin.empleados.index') }}" class="filter-card">
        <div class="filter-grid">

            <input type="text" name="buscar" class="form-control"
                   placeholder="Buscar empleado..."
                   value="{{ request('buscar') }}">

            <select name="estado" class="form-control">
                <option value="">Estado</option>
                <option value="1" {{ request('estado')=='1'?'selected':'' }}>Activo</option>
                <option value="0" {{ request('estado')=='0'?'selected':'' }}>Inactivo</option>
            </select>

            <select name="area_id" class="form-control">
                <option value="">Área</option>
                @foreach($areas as $area)
                    <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                @endforeach
            </select>

            <select name="sede_id" class="form-control">
                <option value="">Sede</option>
                @foreach($sedes as $sede)
                    <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                @endforeach
            </select>

            <div class="filter-actions">

                {{-- 🔶 BOTÓN NARANJA FILTRAR --}}
                <button class="btn btn-orange btn-sm">
                    <i class="fas fa-filter"></i>
                </button>

                <a href="{{ route('admin.empleados.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-undo"></i>
                </a>

            </div>

        </div>
    </form>

    {{-- TABLA --}}
    <div class="table-card">

        <div class="table-header">
            <span>{{ $empleados->total() }} empleados encontrados</span>
            <span>Página {{ $empleados->currentPage() }}</span>
        </div>

        <div class="table-responsive">
            <table class="table modern-table mb-0">

                <thead>
                    <tr>
                        <th>Empleado</th>
                        <th>Área</th>
                        <th>Sede</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($empleados as $e)
                    <tr>

                        <td>
                            <div class="user-cell">

                                {{-- ⚪ AVATAR GRIS --}}
                                <div class="avatar-gray">
                                    {{ strtoupper(substr($e->persona->nombres ?? 'U',0,1)) }}
                                </div>

                                <div>
                                    <div class="name">
                                        {{ $e->persona->nombres }} {{ $e->persona->apellidos }}
                                    </div>
                                    <div class="doc">
                                        {{ $e->persona->numero_documento }}
                                    </div>
                                </div>

                            </div>
                        </td>

                        <td>{{ $e->area->nombre ?? 'Sin área' }}</td>
                        <td>{{ $e->sede->nombre ?? 'Sin sede' }}</td>

                        <td>
                            <span class="badge-soft">
                                {{ $e->rol->nombre ?? 'Sin rol' }}
                            </span>
                        </td>

                        <td>
                            @if($e->estado == 1)
                                <span class="badge-success">Activo</span>
                            @else
                                <span class="badge-danger">Inactivo</span>
                            @endif
                        </td>

                        <td>
                            <div class="actions">

                                <a href="{{ route('admin.empleados.show', $e) }}" class="icon-btn">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($e->estado == 1)
                                    <a href="{{ route('admin.empleados.edit', $e) }}" class="icon-btn">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    <form method="POST" action="{{ route('admin.empleados.destroy', $e) }}">
                                        @csrf @method('DELETE')
                                        <button class="icon-btn danger"
                                                onclick="return confirm('¿Desactivar empleado?')">
                                            <i class="fas fa-user-slash"></i>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.empleados.toggle', $e->id) }}">
                                        @csrf
                                        <button class="icon-btn success">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif

                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty">No hay empleados registrados</td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $empleados->links() }}
    </div>

</div>
@stop

{{-- ================= CSS ================= --}}
@section('css')
<style>

/* BOTÓN NARANJA GLOBAL (MEJORADO) */
.btn-orange{
    background:#f97316;
    color:#fff;
    border:none;
    height:38px;
    padding:0 14px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    font-weight:500;
    border-radius:8px;
    transition:all .2s ease;
    box-shadow:0 2px 6px rgba(249,115,22,0.25);
}

/* HOVER */
.btn-orange:hover{
    background:#ea580c;
    color:#fff;
    transform:translateY(-1px);
    box-shadow:0 4px 10px rgba(234,88,12,0.35);
}

/* CLICK (EFECTO PRESIONADO) */
.btn-orange:active{
    transform:scale(0.98);
    box-shadow:0 2px 5px rgba(234,88,12,0.25);
}

/* FOCUS (ACCESIBILIDAD) */
.btn-orange:focus{
    outline:none;
    box-shadow:0 0 0 3px rgba(249,115,22,0.25);
}

/* HEADER */
.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
}

.page-title{
    font-size:1.6rem;
    font-weight:700;
}

.page-title i{
    color:#3b82f6;
    margin-right:8px;
}

.page-subtitle{
    font-size:0.85rem;
    color:#6b7280;
}

.page-actions{
    display:flex;
    gap:10px;
}

/* ALERTA */
.alert-success-modern{
    background:#ecfdf5;
    color:#047857;
    padding:10px 15px;
    border-radius:10px;
    display:flex;
    gap:10px;
    align-items:center;
    margin-bottom:15px;
}

/* FILTROS */
.filter-card{
    background:#fff;
    padding:15px;
    border-radius:12px;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
    margin-bottom:15px;
}

.filter-grid{
    display:grid;
    grid-template-columns:2fr 1fr 1fr 1fr auto;
    gap:10px;
}

.filter-actions{
    display:flex;
    gap:6px;
}

/* TABLA */
.table-card{
    background:#fff;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
}

.table-header{
    display:flex;
    justify-content:space-between;
    padding:12px 15px;
    font-size:0.8rem;
    color:#64748b;
    border-bottom:1px solid #eee;
}

/* AVATAR GRIS */
.avatar-gray{
    width:38px;
    height:38px;
    border-radius:50%;
    background:#9ca3af;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:600;
}

/* INFO USER */
.user-cell{
    display:flex;
    align-items:center;
    gap:10px;
}

.name{ font-weight:600; }
.doc{ font-size:0.75rem; color:#94a3b8; }

/* ACCIONES */
.actions{
    display:flex;
    justify-content:center;
    gap:6px;
}

.icon-btn{
    width:32px;
    height:32px;
    border-radius:8px;
    border:1px solid #e2e8f0;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#fff;
    transition:.2s;
}

.icon-btn:hover{
    transform:scale(1.05);
    background:#f1f5f9;
}

.icon-btn.danger{ color:#ef4444; }
.icon-btn.success{ color:#22c55e; }

/* BADGES */
.badge-success{
    background:#dcfce7;
    color:#166534;
    padding:3px 8px;
    border-radius:6px;
    font-size:0.75rem;
}

.badge-danger{
    background:#fee2e2;
    color:#991b1b;
    padding:3px 8px;
    border-radius:6px;
    font-size:0.75rem;
}

</style>
@stop