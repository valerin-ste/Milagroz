@php
    $totalAlerts = 0;
    if(isset($alertasCount)) {
        $totalAlerts = ($alertasCount['contratos_vencidos'] ?? 0) + 
                      ($alertasCount['sst_vencidos'] ?? 0) + 
                      ($alertasCount['contratos_criticos'] ?? 0) + 
                      ($alertasCount['sst_criticos'] ?? 0);
    }
@endphp

<li class="nav-item dropdown mr-2">
    <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
        <i class="far fa-bell" style="font-size: 1.25rem; color: var(--text-muted);"></i>
        @if($totalAlerts > 0)
            <span class="badge badge-danger navbar-badge" style="top: 5px; right: 2px;">{{ $totalAlerts }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right border-0 shadow-lg" style="border-radius: var(--radius-lg); overflow: hidden; margin-top: 10px !important;">
        <span class="dropdown-item dropdown-header bg-light-soft font-weight-bold py-3" style="font-size: 1rem; color: var(--text-main);">
            <i class="fas fa-bell mr-2"></i> {{ $totalAlerts }} Notificaciones
        </span>
        
        <div class="dropdown-divider m-0"></div>
        
        <div style="max-height: 350px; overflow-y: auto;">
            @if(($alertasCount['contratos_vencidos'] ?? 0) > 0)
                <a href="{{ route('admin.etapa_contractual.index') }}" class="dropdown-item py-3">
                    <div class="d-flex align-items-start">
                        <div class="bg-soft-red rounded-circle p-2 mr-3">
                            <i class="fas fa-file-contract text-soft-red" style="width: 15px; text-align: center;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 text-sm font-weight-bold" style="color: var(--text-main);">Contratos Vencidos</p>
                            <p class="mb-0 text-xs text-muted">Hay {{ $alertasCount['contratos_vencidos'] }} contratos que requieren acción inmediata.</p>
                        </div>
                    </div>
                </a>
                <div class="dropdown-divider m-0"></div>
            @endif

            @if(($alertasCount['sst_vencidos'] ?? 0) > 0)
                <a href="{{ route('admin.seguridad_salud_trabajo.index') }}" class="dropdown-item py-3">
                    <div class="d-flex align-items-start">
                        <div class="bg-soft-red rounded-circle p-2 mr-3">
                            <i class="fas fa-heartbeat text-soft-red" style="width: 15px; text-align: center;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 text-sm font-weight-bold" style="color: var(--text-main);">Docs SST Expirados</p>
                            <p class="mb-0 text-xs text-muted">{{ $alertasCount['sst_vencidos'] }} documentos de seguridad vencidos.</p>
                        </div>
                    </div>
                </a>
                <div class="dropdown-divider m-0"></div>
            @endif

            @if(($alertasCount['contratos_criticos'] ?? 0) > 0)
                <a href="{{ route('admin.etapa_contractual.index') }}" class="dropdown-item py-3">
                    <div class="d-flex align-items-start">
                        <div class="bg-soft-orange rounded-circle p-2 mr-3">
                            <i class="fas fa-exclamation-circle text-soft-orange" style="width: 15px; text-align: center;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 text-sm font-weight-bold" style="color: var(--text-main);">Vencimiento Cercano (8d)</p>
                            <p class="mb-0 text-xs text-muted">{{ $alertasCount['contratos_criticos'] }} contratos por vencer en 8 días.</p>
                        </div>
                    </div>
                </a>
                <div class="dropdown-divider m-0"></div>
            @endif

            @if(($alertasCount['sst_criticos'] ?? 0) > 0)
                <a href="{{ route('admin.seguridad_salud_trabajo.index') }}" class="dropdown-item py-3">
                    <div class="d-flex align-items-start">
                        <div class="bg-soft-orange rounded-circle p-2 mr-3">
                            <i class="fas fa-clock text-soft-orange" style="width: 15px; text-align: center;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 text-sm font-weight-bold" style="color: var(--text-main);">SST Críticos (8d)</p>
                            <p class="mb-0 text-xs text-muted">{{ $alertasCount['sst_criticos'] }} documentos SST por expirar.</p>
                        </div>
                    </div>
                </a>
                <div class="dropdown-divider m-0"></div>
            @endif

            @if($totalAlerts == 0)
                <div class="dropdown-item text-center py-4 text-muted small italic">
                    <i class="fas fa-check-circle text-soft-green fa-2x mb-2 d-block"></i>
                    No hay alertas pendientes
                </div>
            @endif
        </div>
        
        <div class="dropdown-footer text-center py-3 bg-light-soft">
            <a href="{{ route('admin.dashboard') }}" class="small font-weight-bold" style="color: var(--primary-blue);">
                Ir al Panel de Control <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
</li>
