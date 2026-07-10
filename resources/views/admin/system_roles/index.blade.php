@extends('adminlte::page')

@section('title', 'Roles de Sistema')

@section('content_header')
<div class="page-header d-flex justify-content-between align-items-center mb-2 px-2">
    <div>
        <h2 class="page-title fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem;">
            Roles y Accesos
        </h2>
        <p class="page-subtitle text-muted mb-0">
            Configuración de roles del sistema y sus permisos.
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.system_roles.create') }}" class="btn btn-orange">
            <i class="fas fa-plus me-2"></i> Nuevo Rol
        </a>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-2">

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center" style="background-color: #ecfdf5; color: #047857; border: none;">
            <i class="fas fa-check-circle fa-lg me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center" style="background-color: #fef2f2; color: #b91c1c; border: none;">
            <i class="fas fa-exclamation-circle fa-lg me-3"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th class="ps-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">#</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Nombre del Rol</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Permisos Asignados</th>
                            <th class="py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Estado</th>
                            <th class="text-center pe-4 py-3 text-muted" style="font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr style="border-bottom: 1px solid #f1f5f9; @if($role->estado == 0) opacity: 0.6; background-color: #fcfcfc; @endif">
                            <td class="ps-4 py-3 text-muted" style="font-size: 0.9rem;">{{ $loop->iteration }}</td>

                            <td class="py-3">
                                <div class="d-flex align-items-center" style="gap: 0.75rem;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px; background-color: rgba(19,182,236,0.1); color: var(--primary-blue);">
                                        <i class="fas fa-key"></i>
                                    </div>
                                    <span class="fw-bold text-dark {{ $role->name === 'Admin' ? 'text-primary' : '' }}" style="font-size: 0.95rem;">
                                        {{ $role->name }}
                                    </span>
                                </div>
                            </td>

                            <td class="py-3">
                                @if($role->permissions->count() > 0)
                                    @foreach($role->permissions->take(5) as $permission)
                                        <span class="badge bg-secondary me-1">{{ $permission->name }}</span>
                                    @endforeach
                                    @if($role->permissions->count() > 5)
                                        <span class="badge bg-light text-secondary">+{{ $role->permissions->count() - 5 }} más</span>
                                    @endif
                                @else
                                    @if($role->name === 'Admin')
                                        <span class="badge bg-success">Acceso Total (Implícito)</span>
                                    @else
                                        <span class="text-muted" style="font-size: 0.85rem;">Sin permisos específicos</span>
                                    @endif
                                @endif
                            </td>

                            <td class="py-3">
                                @if($role->estado == 1)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>

                            <td class="text-center pe-4 py-3">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    {{-- BOTÓN EDITAR (Solo si está activo) --}}
                                    @if($role->estado == 1)
                                        <a href="{{ route('admin.system_roles.edit', $role->id) }}"
                                           class="btn btn-sm btn-icon btn-outline-primary"
                                           data-toggle="tooltip" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    @endif

                                    {{-- BOTÓN ACTIVAR / INACTIVAR (Visible para todos, validado en backend) --}}
                                    <form action="{{ route('admin.system_roles.toggle', $role->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        @if($role->estado == 1)
                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-danger"
                                                    data-toggle="tooltip" title="Inactivar"
                                                    onclick="return confirm('¿Confirma que desea inactivar este registro?');">
                                                <i class="fas fa-toggle-off"></i>
                                            </button> 
                                        @else
                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-success"
                                                    data-toggle="tooltip" title="Activar"
                                                    onclick="return confirm('¿Confirma que desea activar este registro?');">
                                                <i class="fas fa-toggle-on"></i>
                                            </button>
                                        @endif
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
@stop

@push('scripts')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush

@push('css')
<style>
/* ── Botones ── */
.btn-orange { background-color: #ff6a00; border: none; color: #fff; border-radius: 8px; transition: all 0.2s; font-weight: 600; }
.btn-orange:hover { background-color: #e65c00; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(255,106,0,0.25); }
.btn-icon { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s; }
.btn-icon:hover { transform: scale(1.1); }
/* ── Tabla ── */
.table thead th { letter-spacing: 0.04em; }
.table tbody tr:hover { background-color: #f8fafc; }
/* ── Badge ── */
.badge { font-size: 0.78rem; padding: 0.35em 0.7em; border-radius: 6px; font-weight: 600; }
</style>
@endpush
