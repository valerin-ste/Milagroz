<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReporteNovedadNomina;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ReporteNovedadNominaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-reportes_novedades_nomina', ['only' => ['index', 'show', 'viewArchivo', 'downloadArchivo']]);
        $this->middleware('permission:crear-reportes_novedades_nomina', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-reportes_novedades_nomina', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-reportes_novedades_nomina', ['only' => ['destroy', 'toggleStatus']]);
    }

    public function index(Request $request)
    {
        $buscar = $request->buscar;
        $estado = $request->estado;
        $fecha = $request->fecha;

        $reportes = ReporteNovedadNomina::with(['empleado.persona:id,nombres,apellidos,numero_documento'])
            ->when($buscar, function($query) use ($buscar) {
                $query->whereHas('empleado.persona', function($q) use ($buscar) {
                    $q->where('nombres', 'LIKE', "%$buscar%")
                      ->orWhere('apellidos', 'LIKE', "%$buscar%")
                      ->orWhere('numero_documento', 'LIKE', "%$buscar%");
                })->orWhere('tipo_novedad', 'LIKE', "%$buscar%");
            })
            ->when($estado !== null, function($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->when($fecha, function($query) use ($fecha) {
                $query->where('fecha', $fecha);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.reportes_novedades_nomina.index', compact('reportes', 'buscar', 'estado', 'fecha'));
    }

    public function create()
    {
        $empleados = Empleado::with('persona')->where('estado', 1)->get();
        return view('admin.reportes_novedades_nomina.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'tipo_novedad' => 'required|string|max:150',
            'cantidad' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string',
            'archivo' => 'nullable|file|max:5120',
        ]);

        $data = $request->only([
            'empleado_id',
            'tipo_novedad',
            'cantidad',
            'fecha',
            'observaciones'
        ]);

        $data['estado'] = 1;

        $reporte = ReporteNovedadNomina::create($data);

        // ✅ GUARDAR DOCUMENTO
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $nombreOriginal = $archivo->getClientOriginalName();
            
            $ruta = $archivo->storeAs(
                'novedades_nomina',
                $nombreOriginal,
                'public'
            );

            $reporte->update([
                'archivo' => $ruta
            ]);

            $reporte->documentos()->create([
                'ruta' => $ruta,
                'nombre_original' => $nombreOriginal,
                'tipo_documento' => $archivo->getClientMimeType()
            ]);
        }

        return redirect()
            ->route('admin.reportes-novedades-nomina.index')
            ->with('success', 'Registro de novedad de nómina creado correctamente.');
    }

    public function show($id)
    {
        $reporte = ReporteNovedadNomina::with('empleado.persona')->findOrFail($id);
        return view('admin.reportes_novedades_nomina.show', compact('reporte'));
    }

    public function edit($id)
    {
        $reporte = ReporteNovedadNomina::with('empleado.persona')->findOrFail($id);
        
        if ($reporte->estado == 0) {
            return redirect()->route('admin.reportes-novedades-nomina.index')->with('error', 'No se puede editar un registro inactivo.');
        }

        $empleados = Empleado::with('persona')->where('estado', 1)->get();
        return view('admin.reportes_novedades_nomina.edit', compact('reporte', 'empleados'));
    }

    public function update(Request $request, $id)
    {
        $reporte = ReporteNovedadNomina::findOrFail($id);

        if ($reporte->estado == 0) return back()->with('error', 'Edición bloqueada.');

        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'tipo_novedad' => 'required|string|max:150',
            'cantidad' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string',
            'archivo' => 'nullable|file|max:5120',
        ]);

        $data = $request->only([
            'empleado_id',
            'tipo_novedad',
            'cantidad',
            'fecha',
            'observaciones'
        ]);

        if ($request->hasFile('archivo')) {
            // Eliminar archivo anterior si existe
            if ($reporte->archivo && Storage::disk('public')->exists($reporte->archivo)) {
                Storage::disk('public')->delete($reporte->archivo);
                $reporte->documentos()->delete();
            }
            
            $archivo = $request->file('archivo');
            $nombreOriginal = $archivo->getClientOriginalName();
            
            $data['archivo'] = $archivo->storeAs('novedades_nomina', $nombreOriginal, 'public');
            
            $reporte->documentos()->create([
                'ruta' => $data['archivo'],
                'nombre_original' => $nombreOriginal,
                'tipo_documento' => $archivo->getClientMimeType()
            ]);
        } elseif ($request->eliminar_archivo) {
            if ($reporte->archivo && Storage::disk('public')->exists($reporte->archivo)) {
                Storage::disk('public')->delete($reporte->archivo);
                $reporte->documentos()->delete();
            }
            $data['archivo'] = null;
        }

        $reporte->update($data);

        return redirect()->route('admin.reportes-novedades-nomina.index')->with('success', 'Registro de novedad de nómina actualizado correctamente.');
    }

    public function destroy($id)
    {
        $reporte = ReporteNovedadNomina::findOrFail($id);
        $reporte->update(['estado' => 0]);
        return back()->with('success', 'Registro desactivado correctamente.');
    }

    public function toggleStatus($id)
    {
        $reporte = ReporteNovedadNomina::findOrFail($id);
        $nuevoEstado = $reporte->estado == 1 ? 0 : 1;
        $reporte->update(['estado' => $nuevoEstado]);

        $texto = $nuevoEstado == 1 ? 'reactivado' : 'desactivado';
        return back()->with('success', "Registro $texto correctamente.");
    }

    public function viewArchivo($id)
    {
        $reporte = ReporteNovedadNomina::findOrFail($id);
        if (!$reporte->archivo || !Storage::disk('public')->exists($reporte->archivo)) {
            abort(404, 'Archivo no encontrado');
        }
        return response()->file(storage_path('app/public/' . $reporte->archivo));
    }

    public function downloadArchivo($id)
    {
        $reporte = ReporteNovedadNomina::findOrFail($id);
        if (!$reporte->archivo || !Storage::disk('public')->exists($reporte->archivo)) {
            abort(404, 'Archivo no encontrado');
        }
        return Storage::disk('public')->download($reporte->archivo);
    }
}
