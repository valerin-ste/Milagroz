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

    public function index()
    {
        $documentos = SeguridadSaludTrabajo::with(['empleado.persona', 'documentos'])
            ->orderByDesc('fecha')
            ->paginate(10);

        return view('admin.seguridad_salud_trabajo.index', compact('documentos'));
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
