<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeguridadSaludTrabajo;
use App\Models\Empleado;
use App\Services\SeguridadSaludTrabajoService;
use Illuminate\Http\Request;

class SeguridadSaludTrabajoController extends Controller
{
    protected $service;

    public function __construct(SeguridadSaludTrabajoService $service)
    {
        $this->service = $service;
        $this->middleware('permission:ver-seguridad_salud_trabajo', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear-seguridad_salud_trabajo', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-seguridad_salud_trabajo', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-seguridad_salud_trabajo', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $busqueda = $request->buscar;
        $tipo     = $request->tipo_documento;

        // 1. Obtener los IDs de los registros representativos con filtros aplicados
        $representativeIds = SeguridadSaludTrabajo::query()
            ->when($busqueda, function ($query, $busqueda) {
                $query->whereHas('empleado.persona', function ($q) use ($busqueda) {
                    $q->where('nombres', 'like', "%$busqueda%")
                      ->orWhere('apellidos', 'like', "%$busqueda%");
                });
            })
            ->when($tipo, function ($query, $tipo) {
                $query->where('tipo_documento', 'like', "%$tipo%");
            })
            ->selectRaw('MAX(id) as id')
            ->groupBy('empleado_id', 'tipo_documento', \DB::raw('YEAR(fecha)'))
            ->pluck('id');

        // 2. Cargar los registros representativos
        $documentos = SeguridadSaludTrabajo::with(['empleado.persona', 'documentos'])
            ->whereIn('id', $representativeIds)
            ->orderByDesc('fecha')
            ->paginate(10)
            ->withQueryString();

        // 3. Cargar todos los documentos para cada grupo
        foreach ($documentos as $doc) {
            $anio = $doc->fecha->format('Y');
            
            $allIdsInGroup = SeguridadSaludTrabajo::where('empleado_id', $doc->empleado_id)
                ->where('tipo_documento', $doc->tipo_documento)
                ->whereYear('fecha', $anio)
                ->pluck('id');

            $allDocs = \App\Models\Documento::where('documentable_type', SeguridadSaludTrabajo::class)
                ->whereIn('documentable_id', $allIdsInGroup)
                ->get();

            $doc->setRelation('documentos', $allDocs);
        }

        return view('admin.seguridad_salud_trabajo.index', compact('documentos', 'busqueda', 'tipo'));
    }

    public function create()
    {
        $empleados = Empleado::with('persona')->get();
        return view('admin.seguridad_salud_trabajo.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id'    => 'required|exists:empleados,id',
            'tipo_documento' => 'required|string|in:Ingresos,Periódicos,ARL,Retiros',
            'documentos'     => 'nullable|array',
            'documentos.*'   => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'fecha'          => 'required|date',
        ]);

        $this->service->store($request->all(), $request->file('documentos'));

        return redirect()->route('admin.seguridad_salud_trabajo.index')
            ->with('success', 'Registro y archivos de Seguridad y Salud guardados correctamente.');
    }

    public function edit(SeguridadSaludTrabajo $seguridad_salud_trabajo)
    {
        $seguridad_salud_trabajo->load('documentos');
        $empleados = Empleado::with('persona')->get();
        return view('admin.seguridad_salud_trabajo.edit', [
            'documento' => $seguridad_salud_trabajo,
            'empleados' => $empleados
        ]);
    }

    public function update(Request $request, SeguridadSaludTrabajo $seguridad_salud_trabajo)
    {
        $request->validate([
            'empleado_id'    => 'required|exists:empleados,id',
            'tipo_documento' => 'required|string|in:Ingresos,Periódicos,ARL,Retiros',
            'documentos'     => 'nullable|array',
            'documentos.*'   => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'fecha'          => 'required|date',
        ]);

        $this->service->update($seguridad_salud_trabajo, $request->all(), $request->file('documentos'));

        return redirect()->route('admin.seguridad_salud_trabajo.index')
            ->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy($id)
    {
        $registro = SeguridadSaludTrabajo::findOrFail($id);

        $registro->estado = $registro->estado == 1 ? 0 : 1;
        $registro->save();

        return redirect()->route('admin.seguridad_salud_trabajo.index')->with('success', 'Estado actualizado correctamente.');
    }
}
