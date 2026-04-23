<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Area;
use App\Models\Sede;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        // El index ahora sirve como un lanzador de reportes especializados 
        // para evitar duplicar las estadísticas del Panel de Control.
        return view('admin.reports.index');
    }

    public function employeeReport(Request $request)
    {
        $areas = Area::all();
        $sedes = Sede::all();

        $query = Empleado::with(['persona', 'area', 'sede', 'etapaContractuales' => function($q) {
            $q->orderBy('id', 'desc');
        }]);

        if ($request->has('buscar') && $request->buscar != '') {
            $buscar = $request->buscar;
            $query->whereHas('persona', function ($q) use ($buscar) {
                $q->where('nombres', 'like', "%$buscar%")
                  ->orWhere('apellidos', 'like', "%$buscar%")
                  ->orWhere('numero_documento', 'like', "%$buscar%");
            });
        }

        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        if ($request->has('area_id') && $request->area_id != '') {
            $query->where('area_id', $request->area_id);
        }

        if ($request->has('sede_id') && $request->sede_id != '') {
            $query->where('sede_id', $request->sede_id);
        }

        $empleados = $query->paginate(20)->withQueryString();

        return view('admin.reports.empleados', compact('empleados', 'areas', 'sedes'));
    }

    public function exportEmployeesPdf(Request $request)
    {
        $query = Empleado::with(['persona', 'area', 'sede', 'etapaContractuales' => function($q) {
            $q->orderBy('id', 'desc');
        }]);

        // REPETIR FILTROS
        if ($request->has('buscar') && $request->buscar != '') {
            $buscar = $request->buscar;
            $query->whereHas('persona', function ($q) use ($buscar) {
                $q->where('nombres', 'like', "%$buscar%")
                  ->orWhere('apellidos', 'like', "%$buscar%")
                  ->orWhere('numero_documento', 'like', "%$buscar%");
            });
        }
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }
        if ($request->has('area_id') && $request->area_id != '') {
            $query->where('area_id', $request->area_id);
        }
        if ($request->has('sede_id') && $request->sede_id != '') {
            $query->where('sede_id', $request->sede_id);
        }

        $empleados = $query->get();

        $pdf = Pdf::loadView('admin.reports.pdf.empleados', compact('empleados'));
        $pdf->setPaper('letter', 'landscape');
        
        return $pdf->download('reporte_empleados_' . date('Ymd_His') . '.pdf');
    }


}
