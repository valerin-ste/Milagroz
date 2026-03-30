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
    }

    public function index(Request $request)
    {
        $busqueda = $request->buscar;
        $tipo     = $request->tipo_documento;
        $estado   = $request->estado;

        $documentos = SeguridadSaludTrabajo::with(['empleado.persona', 'documentos'])
            ->when($busqueda, function ($query, $busqueda) {
                $query->whereHas('empleado.persona', function ($q) use ($busqueda) {
                    $q->where('nombres', 'like', "%$busqueda%")
                      ->orWhere('apellidos', 'like', "%$busqueda%");
                });
            })
            ->when($tipo, function ($query, $tipo) {
                $query->where('tipo_documento', 'like', "%$tipo%");
            })
            ->when($estado !== null && $estado !== '', function ($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->orderByDesc('fecha')
            ->paginate(10)
            ->withQueryString();

        return view('admin.seguridad_salud_trabajo.index', compact('documentos', 'busqueda', 'tipo', 'estado'));
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
            'tipo_documento' => 'required|string|max:100',
            'documentos'     => 'required|array',
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
            'tipo_documento' => 'required|string|max:100',
            'documentos'     => 'nullable|array',
            'documentos.*'   => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'fecha'          => 'required|date',
        ]);

        $this->service->update($seguridad_salud_trabajo, $request->all(), $request->file('documentos'));

        return redirect()->route('admin.seguridad_salud_trabajo.index')
            ->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(SeguridadSaludTrabajo $seguridad_salud_trabajo)
    {
        $seguridad_salud_trabajo->update(['estado' => 0]);
        return back()->with('success', 'Registro desactivado correctamente.');
    }

    public function toggleStatus($id)
    {
        $registro = SeguridadSaludTrabajo::findOrFail($id);
        $nuevoEstado = $registro->estado == 1 ? 0 : 1;
        $registro->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado == 1 ? 'activado' : 'desactivado';
        return back()->with('success', "Registro $mensaje correctamente.");
    }
}
