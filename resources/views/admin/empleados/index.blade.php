@extends('adminlte::page')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-main); font-size: 1.75rem; letter-spacing: -0.5px;">Gestión de Empleados</h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">Administración y control del personal médico y administrativo.</p>
    </div>
    <a href="{{ route('admin.empleados.create') }}" class="btn btn-orange">
        <i class="fas fa-plus me-2"></i> Nuevo Empleado
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

    <div class="card border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Empleado</th>
                            <th>Área / Departamento</th>
                            <th>Sede / Ubicación</th>
                            <th>Rol Sistema</th>
                            <th>Estado</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empleados as $e)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: rgba(19, 182, 236, 0.1); color: var(--primary-blue);">
                                        <span class="fw-bold">{{ strtoupper(substr($e->persona->nombres ?? 'U', 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $e->persona->nombres ?? '' }} {{ $e->persona->apellidos ?? '' }}</div>
                                        <div class="text-muted" style="font-size: 0.85rem;">CI: {{ $e->persona->numero_documento ?? '' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="d-block" style="color: #334155; font-weight: 500;">{{ $e->area->nombre ?? 'Sin área' }}</span>
                                <span class="text-muted" style="font-size: 0.85rem;">{{ $e->cargo }}</span>
                            </td>

                            <td style="color: #475569;">
                                <i class="fas fa-hospital-alt me-1 text-muted"></i> {{ $e->sede->nombre ?? 'Sin sede' }}
                            </td>

                            <td>
                                <span class="px-2 py-1 rounded" style="background-color: #f1f5f9; color: #475569; font-size: 0.85rem; font-weight: 500;">
                                    {{ $e->rol->nombre ?? 'Sin rol' }}
                                </span>
                            </td>

                            <td>
                                @if($e->estado == 1)
                                    <span class="badge-soft-success">● Activo</span>
                                @else
                                    <span class="badge-soft-danger">● Inactivo</span>
                                @endif
                            </td>

                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.empleados.show', $e) }}" class="btn btn-sm btn-light-custom px-3" title="Ver Perfil">
                                        <i class="fas fa-eye text-primary"></i>
                                    </a>

                                    @if($e->estado == 1)
                                        <a href="{{ route('admin.empleados.edit', $e) }}" class="btn btn-sm btn-light-custom px-3" title="Editar">
                                            <i class="fas fa-pen text-muted"></i>
                                        </a>

                                        <form action="{{ route('admin.empleados.destroy', $e) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-light-custom px-3" title="Desactivar" onclick="return confirm('¿Confirma que desea DESACTIVAR a este empleado?');">
                                                <i class="fas fa-user-slash text-danger"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-light-custom px-3 opacity-50" title="No editable (Inactivo)" style="cursor:not-allowed;">
                                            <i class="fas fa-pen text-muted"></i>
                                        </button>

                                        <form action="{{ route('admin.empleados.toggle', $e->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-light-custom px-3 text-success" title="Reactivar Empleado">
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
                                    <i class="fas fa-users fa-3x mb-3" style="color: #cbd5e1;"></i>
                                    <h5 class="fw-bold mb-1" style="color: #64748b;">No hay empleados registrados</h5>
                                    <p class="mb-0">Comience agregando uno nuevo.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($empleados->hasPages())
        <div class="card-footer bg-white border-top border-light py-3 px-4">
            {{ $empleados->links() }}
        </div>
        @endif
    </div>

</div>
@endsection