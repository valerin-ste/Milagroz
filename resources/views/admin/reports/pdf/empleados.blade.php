<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Empleados - Milagroz</title>
    <style>
        @page { margin: 2.5cm 1.5cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 11pt; color: #333; }
        .header { position: absolute; top: -1.5cm; left: 0; right: 0; text-align: center; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .header h1 { font-size: 18pt; margin: 0; color: #1e293b; }
        .header p { font-size: 9pt; color: #64748b; margin-top: 5px; }
        .footer { position: fixed; bottom: -1cm; left: 0; right: 0; text-align: center; font-size: 8pt; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f8fafc; color: #475569; font-weight: bold; font-size: 9pt; text-transform: uppercase; text-align: left; padding: 10px 8px; border-bottom: 2px solid #e2e8f0; }
        td { padding: 10px 8px; border-bottom: 1px solid #f1f5f9; font-size: 10pt; }
        .success { color: #10b981; font-weight: bold; }
        .danger { color: #ef4444; font-weight: bold; }
        .badge { display: inline-block; padding: 3px 6px; border-radius: 4px; background: #f1f5f9; color: #475569; font-size: 8pt; }
        .text-right { text-align: right; }
        .summary { margin-top: 30px; font-size: 9pt; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte General de Empleados</h1>
        <p>Sistema de Gestión de Talento Humano — Milagroz</p>
        <p>Generado el: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre Completo</th>
                <th>Documento</th>
                <th>Cargo / Área</th>
                <th>Sede</th>
                <th>Tipo de Contrato</th>
                <th class="text-right">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($empleados as $e)
            <tr>
                <td>{{ $e->persona->nombres ?? '' }} {{ $e->persona->apellidos ?? '' }}</td>
                <td>{{ $e->persona->numero_documento ?? 'N/A' }}</td>
                <td>
                    <strong>{{ $e->cargo }}</strong><br>
                    <small>{{ $e->area->nombre ?? 'N/A' }}</small>
                </td>
                <td>{{ $e->sede->nombre ?? 'N/A' }}</td>
                <td>
                    <span class="badge">
                        {{ $e->etapaContractuales->first()->tipo_contrato ?? 'No registrado' }}
                    </span>
                </td>
                <td class="text-right">
                    @if($e->estado == 1)
                        <span class="success">ACTIVO</span>
                    @else
                        <span class="danger">INACTIVO</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        * Total de registros exportados: {{ count($empleados) }}
    </div>

    <div class="footer">
        Página <script type="text/php">echo $PAGE_NUM . " de " . $PAGE_COUNT;</script> — Milagroz 2026
    </div>
</body>
</html>
