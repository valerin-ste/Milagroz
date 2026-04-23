@php
    $path = public_path('images/logo_ips.jpg');
    if (file_exists($path)) {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    } else {
        $base64 = null;
    }
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Empleados - Milagroz</title>
    <style>
        /* Modern PDF Design for DomPDF - Brand Optimized */
        @page { margin: 0.5cm 1cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 9pt; color: #1e293b; margin: 0; padding: 0.5cm; }
        
        /* Brand Header */
        .header-table { width: 100%; border-bottom: 2px solid #f97316; margin-bottom: 20px; padding-bottom: 15px; }
        .brand-logo-img { height: 60px; width: auto; margin-bottom: 10px; }
        .brand-logo-text { font-size: 28pt; font-weight: 800; color: #f97316; letter-spacing: -1.5px; margin: 0; }
        .brand-subtitle { font-size: 10pt; color: #64748b; font-weight: 400; margin-top: -5px; }
        .meta-container { text-align: right; font-size: 8pt; color: #64748b; vertical-align: top; }
        
        .report-label { background: #f97316; color: white; padding: 5px 12px; border-radius: 4px; font-weight: bold; display: inline-block; margin-top: 10px; text-transform: uppercase; letter-spacing: 0.5px; }

        /* KPI Info */
        .summary-stats { margin-bottom: 30px; width: 100%; }
        .stat-card { background: #fff7ed; border: 1px solid #ffedd5; border-radius: 8px; padding: 10px; width: 23%; display: inline-block; text-align: center; margin-right: 2%; }
        .stat-value { font-size: 14pt; font-weight: 700; color: #c2410c; display: block; }
        .stat-label { font-size: 7pt; color: #9a3412; text-transform: uppercase; font-weight: 600; }

        /* Dynamic Table Style */
        table.main-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table.main-table th { background-color: #f8fafc; color: #f97316; font-weight: 700; font-size: 7.5pt; text-transform: uppercase; text-align: left; padding: 12px 10px; border-bottom: 2px solid #f97316; }
        table.main-table td { padding: 10px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        
        .row-even { background-color: #ffffff; }
        .row-odd { background-color: #fffaf5; }

        .emp-name { font-weight: 700; color: #0f172a; font-size: 9.5pt; display: block; }
        .emp-doc { color: #64748b; font-size: 8pt; }
        
        .badge-pill { padding: 4px 10px; border-radius: 50px; font-size: 7.5pt; font-weight: 700; text-transform: uppercase; display: inline-block; }
        .bg-active { background: #ecfdf5; color: #059669; }
        .bg-inactive { background: #fef2f2; color: #dc2626; }
        
        .area-info { font-size: 8pt; color: #334155; }
        .sede-info { color: #0284c7; font-weight: 600; }

        .footer { position: fixed; bottom: -10px; left: 0; right: 0; height: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px; text-align: center; font-size: 8pt; color: #94a3b8; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="65%">
                @if($base64)
                    <img src="{{ $base64 }}" class="brand-logo-img">
                @else
                    <h1 class="brand-logo-text">Milagroz</h1>
                @endif
                <p class="brand-subtitle">Institución Prestadora de Salud • Gestión de Talento Humano</p>
                <div class="report-label">Reporte General de Personal</div>
            </td>
            <td class="meta-container">
                <strong>Fecha de Generación:</strong><br>
                {{ date('d F, Y') }}<br>
                {{ date('h:i A') }}<br><br>
                <strong>Exportado por:</strong><br>
                Administrador del Sistema
            </td>
        </tr>
    </table>

    <table class="summary-stats">
        <tr>
            <td class="stat-card">
                <span class="stat-value">{{ $empleados->count() }}</span>
                <span class="stat-label">Total Personal</span>
            </td>
            <td class="stat-card">
                <span class="stat-value">{{ $empleados->where('estado', 1)->count() }}</span>
                <span class="stat-label">Colaboradores Activos</span>
            </td>
            <td class="stat-card">
                <span class="stat-value">{{ $empleados->where('estado', 0)->count() }}</span>
                <span class="stat-label">Inactivos</span>
            </td>
            <td class="stat-card" style="margin-right: 0;">
                <span class="stat-value">{{ date('Y') }}</span>
                <span class="stat-label">Ciclo Operativo</span>
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="30%">Funcionario</th>
                <th width="25%">Posición / Área</th>
                <th width="15%">Sede</th>
                <th width="20%">Tipo Contrato</th>
                <th width="10%" style="text-align: right;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($empleados as $index => $e)
            <tr class="{{ $index % 2 == 0 ? 'row-even' : 'row-odd' }}">
                <td>
                    <span class="emp-name">{{ $e->persona->full_name ?? 'N/A' }}</span>
                    <span class="emp-doc">{{ $e->persona->tipo_documento ?? '' }}: {{ $e->persona->numero_documento ?? '' }}</span>
                </td>
                <td>
                    <div class="area-info">
                        <strong>{{ $e->cargo }}</strong><br>
                        {{ $e->area->nombre ?? 'N/A' }}
                    </div>
                </td>
                <td class="sede-info">{{ $e->sede->nombre ?? 'N/A' }}</td>
                <td>
                    <span style="font-size: 8pt; color: #475569;">
                        {{ $e->etapaContractuales->first()->tipo_contrato ?? 'No Registrado' }}
                    </span>
                </td>
                <td style="text-align: right;">
                    @if($e->estado == 1)
                        <span class="badge-pill bg-active">Activo</span>
                    @else
                        <span class="badge-pill bg-inactive">Inactivo</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Milagroz IPS - Sistema de Gestión Administrativa &copy; {{ date('Y') }} — Confidencial
    </div>
</body>
</html>
