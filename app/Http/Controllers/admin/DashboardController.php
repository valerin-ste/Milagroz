<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Solicitud;
use App\Models\Comunicacion;
use App\Models\Formacion;
use App\Models\Area;
use App\Models\EtapaContractual;
use App\Models\SeguridadSaludTrabajo;
use App\Models\EvaluacionDesempeno;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now   = Carbon::now();
        $hoy   = $now->clone()->startOfDay();
        $en8d  = $now->clone()->addDays(8)->endOfDay();
        $en15d = $now->clone()->addDays(15)->endOfDay();
        $en30d = $now->clone()->addDays(30)->endOfDay();
        $mesAnterior = $now->clone()->subMonth();

        // ── 1. KPIs ──────────────────────────────────────────────
        $totalEmpleados   = Empleado::count();
        $activos          = Empleado::where('estado', 1)->count();
        $inactivos        = Empleado::where('estado', 0)->count();
        $totalSolicitudes = Solicitud::count();
        $solPendientes    = Solicitud::where('estado', 'pendiente')->count();
        $solMesActual     = Solicitud::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();
        $solMesAnterior   = Solicitud::whereMonth('created_at', $mesAnterior->month)->whereYear('created_at', $mesAnterior->year)->count();
        $totalComunicaciones = Comunicacion::count();
        $comMesActual     = Comunicacion::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();
        $totalFormaciones = Formacion::count();

        // Nuevos empleados este mes vs mes anterior
        $empMesActual   = Empleado::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();
        $empMesAnterior = Empleado::whereMonth('created_at', $mesAnterior->month)->whereYear('created_at', $mesAnterior->year)->count();
        $empVariacion   = $empMesAnterior > 0 ? round((($empMesActual - $empMesAnterior) / $empMesAnterior) * 100) : 0;

        $kpi = compact(
            'totalEmpleados', 'activos', 'inactivos',
            'totalSolicitudes', 'solPendientes', 'solMesActual', 'solMesAnterior',
            'totalComunicaciones', 'comMesActual',
            'totalFormaciones', 'empMesActual', 'empMesAnterior', 'empVariacion'
        );

        // ── 2. ALERTAS DETALLADAS ─────────────────────────────────
        // Contratos vencidos (fecha_fin <= hoy)
        $contratosVencidos = EtapaContractual::with('empleado.persona')
            ->where('estado', 1)
            ->whereNotNull('fecha_fin')
            ->whereDate('fecha_fin', '<=', $hoy)
            ->orderBy('fecha_fin')
            ->get();

        // Contratos críticos (8 días)
        $contratosCriticos = EtapaContractual::with('empleado.persona')
            ->where('estado', 1)
            ->whereNotNull('fecha_fin')
            ->whereDate('fecha_fin', '>', $hoy)
            ->whereDate('fecha_fin', '<=', $en8d)
            ->orderBy('fecha_fin')
            ->get();

        // Contratos preventivos (8–30 días)
        $contratosPreventivos = EtapaContractual::with('empleado.persona')
            ->where('estado', 1)
            ->whereNotNull('fecha_fin')
            ->whereDate('fecha_fin', '>', $en8d)
            ->whereDate('fecha_fin', '<=', $en30d)
            ->orderBy('fecha_fin')
            ->get();

        // SST vencidos
        $sstVencidos = SeguridadSaludTrabajo::with('empleado.persona')
            ->where('estado', 1)
            ->whereNotNull('fecha')
            ->whereDate('fecha', '<=', $hoy)
            ->orderBy('fecha')
            ->get();

        // SST críticos (8 días)
        $sstCriticos = SeguridadSaludTrabajo::with('empleado.persona')
            ->where('estado', 1)
            ->whereNotNull('fecha')
            ->whereDate('fecha', '>', $hoy)
            ->whereDate('fecha', '<=', $en8d)
            ->orderBy('fecha')
            ->get();

        // Solicitudes pendientes detalladas
        $solicitudesPendientesDetalle = Solicitud::with('empleado.persona')
            ->where('estado', 'pendiente')
            ->latest()
            ->limit(5)
            ->get();

        $alertas = compact(
            'contratosVencidos', 'contratosCriticos', 'contratosPreventivos',
            'sstVencidos', 'sstCriticos', 'solicitudesPendientesDetalle'
        );

        $totalAlertasCriticas = $contratosVencidos->count() + $sstVencidos->count() + $contratosCriticos->count();

        // ── 3. GRÁFICAS ───────────────────────────────────────────
        // Empleados por Área
        $empleadosPorArea = Area::withCount('empleados')->get();
        $chartAreasLabels = $empleadosPorArea->pluck('nombre');
        $chartAreasData   = $empleadosPorArea->pluck('empleados_count');

        // Solicitudes últimos 6 meses
        $solicitudesPorMes = Solicitud::select(
            DB::raw('count(*) as total'),
            DB::raw("DATE_FORMAT(fecha, '%Y-%m') as mes")
        )
        ->groupBy('mes')
        ->orderBy('mes', 'desc')
        ->limit(6)
        ->get()
        ->reverse();

        $chartMesesLabels = $solicitudesPorMes->pluck('mes')->map(fn($m) => Carbon::parse($m)->translatedFormat('M Y'));
        $chartMesesData   = $solicitudesPorMes->pluck('total');

        // Empleados registrados por mes (últimos 6 meses)
        $empleadosPorMes = Empleado::select(
            DB::raw('count(*) as total'),
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mes")
        )
        ->groupBy('mes')
        ->orderBy('mes', 'desc')
        ->limit(6)
        ->get()
        ->reverse();

        $chartEmpLabels = $empleadosPorMes->pluck('mes')->map(fn($m) => Carbon::parse($m)->translatedFormat('M Y'));
        $chartEmpData   = $empleadosPorMes->pluck('total');

        // ── 4. ÚLTIMOS REGISTROS ──────────────────────────────────
        $ultimosEmpleados = Empleado::with(['persona', 'area', 'sede'])
            ->latest()->limit(6)->get();

        $ultimasSolicitudes = Solicitud::with('empleado.persona')
            ->latest()->limit(5)->get();

        // ── 5. ACTIVIDAD AGRUPADA POR FECHA ──────────────────────
        $actividad = collect();

        Empleado::with('persona')->latest()->limit(4)->get()->each(function($e) use ($actividad) {
            $actividad->push([
                'tipo'        => 'empleado',
                'titulo'      => 'Empleado registrado',
                'descripcion' => ($e->persona->nombres ?? '') . ' ' . ($e->persona->apellidos ?? '') . ' — ' . $e->cargo,
                'fecha'       => $e->created_at,
                'color'       => '#0ea5e9',
                'bg'          => 'rgba(14,165,233,0.1)',
                'icono'       => 'fas fa-user-plus',
                'link'        => route('admin.empleados.show', $e->id),
            ]);
        });

        Solicitud::with('empleado.persona')->latest()->limit(4)->get()->each(function($s) use ($actividad) {
            $actividad->push([
                'tipo'        => 'solicitud',
                'titulo'      => 'Solicitud ' . ucfirst($s->estado ?? 'recibida'),
                'descripcion' => 'Tipo: ' . ($s->tipo ?? 'N/A') . ' — ' . ($s->empleado->persona->nombres ?? ''),
                'fecha'       => $s->created_at,
                'color'       => $s->estado === 'pendiente' ? '#f59e0b' : ($s->estado === 'aprobado' ? '#10b981' : '#ef4444'),
                'bg'          => $s->estado === 'pendiente' ? 'rgba(245,158,11,0.1)' : 'rgba(16,185,129,0.1)',
                'icono'       => 'fas fa-envelope-open-text',
                'link'        => route('admin.solicitudes.index'),
            ]);
        });

        Comunicacion::with('empleado.persona')->latest()->limit(3)->get()->each(function($c) use ($actividad) {
            $actividad->push([
                'tipo'        => 'comunicacion',
                'titulo'      => 'Comunicación emitida',
                'descripcion' => ($c->asunto ?? 'Sin asunto') . ' — ' . ($c->empleado->persona->nombres ?? ''),
                'fecha'       => $c->created_at,
                'color'       => '#6366f1',
                'bg'          => 'rgba(99,102,241,0.1)',
                'icono'       => 'fas fa-bullhorn',
                'link'        => route('admin.comunicaciones.index'),
            ]);
        });

        $actividad = $actividad->sortByDesc('fecha');

        return view('admin.dashboard', compact(
            'kpi',
            'alertas',
            'totalAlertasCriticas',
            'chartAreasLabels', 'chartAreasData',
            'chartMesesLabels', 'chartMesesData',
            'chartEmpLabels',   'chartEmpData',
            'ultimosEmpleados',
            'ultimasSolicitudes',
            'actividad'
        ));
    }
}
