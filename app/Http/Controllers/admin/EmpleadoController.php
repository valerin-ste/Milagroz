<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Persona;
use App\Models\Area;
use App\Models\Sede;
use App\Models\Role;
use App\Http\Requests\Admin\StoreEmpleadoRequest;
use App\Http\Requests\Admin\UpdateEmpleadoRequest;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class EmpleadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-empleados', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear-empleados', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-empleados', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-empleados', ['only' => ['destroy', 'toggleStatus']]);
        $this->middleware('permission:exportar-empleados', ['only' => ['exportPdf']]);
    }
    public function index(Request $request)
    {
        $busqueda = $request->buscar;
        $estado   = $request->estado;
        $area_id  = $request->area_id;
        $sede_id  = $request->sede_id;

        $empleados = Empleado::with(['persona', 'area', 'sede'])
            ->when($busqueda, function ($query, $busqueda) {
                $query->whereHas('persona', function ($q) use ($busqueda) {
                    $q->where('nombres', 'like', "%$busqueda%")
                      ->orWhere('apellidos', 'like', "%$busqueda%")
                      ->orWhere('numero_documento', 'like', "%$busqueda%");
                });
            })
            ->when($estado !== null && $estado !== '', function ($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->when($area_id, function ($query, $area_id) {
                $query->where('area_id', $area_id);
            })
            ->when($sede_id, function ($query, $sede_id) {
                $query->where('sede_id', $sede_id);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $areas = Area::select('id', 'nombre')->get();
        $sedes = Sede::select('id', 'nombre')->get();

        return view('admin.empleados.index', compact('empleados', 'busqueda', 'estado', 'area_id', 'sede_id', 'areas', 'sedes'));
    }

    public function show(Empleado $empleado)
    {
        $empleado->load([
            'persona:id,tipo_documento,numero_documento,nombres,apellidos,telefono,correo,direccion,fecha_nacimiento',
            'area:id,nombre',
            'sede:id,nombre,ciudad',
            'rol:id,nombre',
            'etapaPrecontractuales.documentos',
            'etapaContractuales.documentos',
            'seguridadSaludTrabajo.documentos',
            'evaluacionesDesempeno.documentos',
            'formaciones.documentos',
            'comunicaciones.documentos',
            'solicitudes.documentos',
            'dotaciones.documentos',
            'productividades',
            'calidadDocumentos'
        ]);

        return view('admin.empleados.show', compact('empleado'));
    }

    /**
     * Escanea recursivamente carpetas de tipo (arl/retiro) -> Año -> Archivos
     */
    private function scanEmployeeFolders($empleadoId, $type)
    {
        $basePath = "empleados/{$empleadoId}/{$type}";
        $structure = [];

        if (\Storage::disk('public')->exists($basePath)) {
            $years = \Storage::disk('public')->directories($basePath);
            foreach ($years as $yearPath) {
                $year = basename($yearPath);
                $files = \Storage::disk('public')->files($yearPath);
                
                $fileData = [];
                foreach ($files as $file) {
                    $fileData[] = [
                        'name' => basename($file),
                        'path' => base64_encode($file), // Ofuscamos la ruta para la URL
                    ];
                }
                $structure[$year] = $fileData;
            }
            krsort($structure); // Años más recientes primero
        }
        return $structure;
    }

    /**
     * Ver archivo del filesystem (ARL/Retiro)
     */
    public function viewFile($empleadoId, $pathEncoded)
    {
        $path = base64_decode($pathEncoded);
        // Validación básica de seguridad: asegurar que la ruta pertenece al empleado
        if (!str_starts_with($path, "empleados/{$empleadoId}/")) {
            abort(403, 'Acceso no autorizado.');
        }

        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) abort(404);

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        if (in_array($ext, ['xls', 'xlsx', 'doc', 'docx'])) {
            $publicUrl = Storage::disk('public')->url($path);
            $viewerUrl = 'https://view.officeapps.live.com/op/view.aspx?src=' . urlencode($publicUrl);
            return redirect($viewerUrl);
        }

        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'svg'  => 'image/svg+xml',
        ];

        $headers = [
            'Content-Disposition' => 'inline; filename="' . str_replace('"', '\\"', basename($path)) . '"'
        ];

        if (array_key_exists($ext, $mimeTypes)) {
            $headers['Content-Type'] = $mimeTypes[$ext];
        }

        while (ob_get_level() > 0) ob_end_clean();

        return response()->file($fullPath, $headers);
    }

    /**
     * Descargar archivo del filesystem (ARL/Retiro)
     */
    public function downloadFile($empleadoId, $pathEncoded)
    {
        $path = base64_decode($pathEncoded);
        if (!str_starts_with($path, "empleados/{$empleadoId}/")) {
            abort(403, 'Acceso no autorizado.');
        }

        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) abort(404);

        return response()->download($fullPath, basename($path));
    }

    public function create()
    {
        // Optimizando memoria extrayendo solo columnas necesarias
        $personas = Persona::select('id', 'nombres', 'apellidos', 'numero_documento')->get();
        $areas = Area::select('id', 'nombre')->get();
        $sedes = Sede::select('id', 'nombre')->get();
        $roles = Role::select('id', 'nombre')->get();

        return view('admin.empleados.create', compact('personas', 'areas', 'sedes', 'roles'));
    }
 

    public function store(StoreEmpleadoRequest $request)
    {
        $validated = $request->validated();

        // 1. Crear persona
        $persona = Persona::create([
            'tipo_documento' => $validated['tipo_documento'],
            'numero_documento' => $validated['numero_documento'],
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'telefono' => $validated['telefono'] ?? null,
            'correo' => $validated['correo'] ?? null,
            'direccion' => $validated['direccion'] ?? null,
            'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
        ]);

        // 2. Crear empleado
        Empleado::create([
            'persona_id' => $persona->id,
            'area_id' => $validated['area_id'],
            'sede_id' => $validated['sede_id'],
            'rol_id' => $validated['rol_id'],
            'cargo' => $validated['cargo'],
            'tipo_contrato' => $validated['tipo_contrato'],
            'fecha_ingreso' => $validated['fecha_ingreso'],
            'estado' => $validated['estado'],
        ]);

        return redirect()->route('admin.empleados.index')
            ->with('success', 'Empleado registrado correctamente.');
    }

    public function edit(Empleado $empleado)
    {
        $empleado->load('persona');

        $areas = Area::select('id', 'nombre')->get();
        $sedes = Sede::select('id', 'nombre')->get();
        $roles = Role::select('id', 'nombre')->get();

        return view('admin.empleados.edit', compact('empleado', 'areas', 'sedes', 'roles'));
    }

    public function update(UpdateEmpleadoRequest $request, Empleado $empleado)
    {
        $validated = $request->validated();

        // 🔹 ACTUALIZAR PERSONA
        $empleado->persona->update([
            'tipo_documento' => $validated['tipo_documento'],
            'numero_documento' => $validated['numero_documento'],
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'telefono' => $validated['telefono'] ?? null,
            'correo' => $validated['correo'] ?? null,
            'direccion' => $validated['direccion'] ?? null,
            'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
        ]);

        // 🔹 ACTUALIZAR EMPLEADO
        $empleado->update([
            'area_id' => $validated['area_id'],
            'sede_id' => $validated['sede_id'],
            'rol_id' => $validated['rol_id'],
            'cargo' => $validated['cargo'],
            'tipo_contrato' => $validated['tipo_contrato'],
            'fecha_ingreso' => $validated['fecha_ingreso'],
            'estado' => $validated['estado'],
        ]);

        return redirect()->route('admin.empleados.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Empleado $empleado)
    {
        $empleado->update(['estado' => 0]);
        return back()->with('success', 'El empleado ha sido desactivado correctamente.');
    }

    public function toggleStatus($id)
    {
        $empleado = Empleado::findOrFail($id);
        $nuevoEstado = $empleado->estado == 1 ? 0 : 1;
        $empleado->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado == 1 ? 'activado' : 'desactivado';
        return back()->with('success', "El empleado ha sido $mensaje correctamente.");
    }

    public function exportPdf(Request $request)
    {
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

        $empleados = $query->get();

        $pdf = Pdf::loadView('admin.reports.pdf.empleados', compact('empleados'));
        $pdf->setPaper('letter', 'landscape');
        
        return $pdf->download('reporte_empleados_' . date('Ymd_His') . '.pdf');
    }
}