<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Solicitud;
use App\Models\Comunicacion;
use App\Models\Formacion;
use App\Models\Area;
use App\Models\EtapaContractual;
use App\Models\EvaluacionDesempeno;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 🔹 1. CONTADORES (KPIs)
        $kpi = [
            'total_empleados'    => Empleado::count(),
            'empleados_activos'  => Empleado::where('estado', 1)->count(),
            'empleados_inactivos' => Empleado::where('estado', 0)->count(),
            'total_solicitudes'  => Solicitud::count(),
            'total_comunicaciones' => Comunicacion::count(),
            'total_formaciones'  => Formacion::count(),
        ];

        // 🔹 2. SISTEMA DE ALERTAS
        $alertas = [
            'contratos_por_vencer' => EtapaContractual::with('empleado.persona')
                ->where('fecha_fin', '>=', now())
                ->where('fecha_fin', '<=', now()->addDays(30))
                ->get(),
            'solicitudes_pendientes' => Solicitud::where('estado', 'Pendiente')->count(),
            'evaluaciones_pendientes' => EvaluacionDesempeno::where('estado', 0)->count(),
        ];

        // 🔹 3. DATOS PARA GRÁFICAS (CHART.JS)
        
        // Empleados por Área (Donut Chart)
        $empleadosPorArea = Area::withCount('empleados')->get();
        $chartAreasLabels = $empleadosPorArea->pluck('nombre');
        $chartAreasData   = $empleadosPorArea->pluck('empleados_count');

        // Solicitudes por Mes (Bar Chart) - Últimos 6 meses
        $solicitudesPorMes = Solicitud::select(
            DB::raw('count(*) as total'),
            DB::raw("DATE_FORMAT(fecha, '%Y-%m') as mes")
        )
        ->groupBy('mes')
        ->orderBy('mes', 'desc')
        ->limit(6)
        ->get()
        ->reverse();

        $chartMesesLabels = $solicitudesPorMes->pluck('mes')->map(function($m) {
            return Carbon::parse($m)->translatedFormat('M Y');
        });
        $chartMesesData = $solicitudesPorMes->pluck('total');

        // 🔹 4. ÚLTIMOS REGISTROS (TABLAS)
        $ultimasSolicitudes = Solicitud::with('empleado.persona')
            ->latest()
            ->limit(5)
            ->get();

        $ultimosEmpleados = Empleado::with(['persona', 'area'])
            ->latest()
            ->limit(5)
            ->get();

        // 🔹 5. HISTORIAL DE ACTIVIDAD (TIMELINE SIMULADO)
        // Combinamos los últimos registros de diferentes modelos para crear un timeline
        $actividad = collect();

        Empleado::latest()->limit(3)->get()->each(function($e) use ($actividad) {
            $actividad->push([
                'tipo' => 'empleado',
                'titulo' => 'Nuevo Empleado Registrado',
                'descripcion' => "{$e->persona->nombres} {$e->persona->apellidos} se unió como {$e->cargo}",
                'fecha' => $e->created_at,
                'color' => 'bg-info',
                'icono' => 'fas fa-user-plus'
            ]);
        });

        Solicitud::latest()->limit(3)->get()->each(function($s) use ($actividad) {
            $actividad->push([
                'tipo' => 'solicitud',
                'titulo' => 'Nueva Solicitud Recibida',
                'descripcion' => "Tipo: {$s->tipo} por {$s->empleado->persona->nombres}",
                'fecha' => $s->created_at,
                'color' => 'bg-warning',
                'icono' => 'fas fa-envelope'
            ]);
        });

        $actividad = $actividad->sortByDesc('fecha');

        return view('admin.dashboard', compact(
            'kpi', 
            'alertas', 
            'chartAreasLabels', 
            'chartAreasData', 
            'chartMesesLabels', 
            'chartMesesData',
            'ultimasSolicitudes',
            'ultimosEmpleados',
            'actividad'
        ));
    }
}
