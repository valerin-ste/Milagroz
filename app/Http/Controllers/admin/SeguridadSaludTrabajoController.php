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
            ->orderByDesc('fecha')
            ->paginate(10)
            ->withQueryString();

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
            'tipo_documento' => 'required|string|in:Ingreso,Periódico',
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
            'tipo_documento' => 'required|string|in:Ingreso,Periódico',
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
        // En lugar de desactivar, eliminamos el registro si el usuario lo solicita (o mantenemos inactivación lógica si se prefiere ocultar)
        // Por consistencia con la solicitud de "eliminar campo estado", trataremos los registros como siempre vigentes
        // pero permitiremos el borrado físico si se desea limpiar datos. 
        // El usuario pidió "Eliminar el campo estado", así que quitamos la lógica de toggles.
        $seguridad_salud_trabajo->delete();
        return back()->with('success', 'Registro eliminado correctamente.');
    }
}
