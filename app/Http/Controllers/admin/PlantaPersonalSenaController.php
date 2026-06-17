<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlantaPersonalSena;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PlantaPersonalSenaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-planta_personal_sena', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear-planta_personal_sena', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-planta_personal_sena', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-planta_personal_sena', ['only' => ['destroy', 'toggleStatus']]);
    }

    public function index(Request $request)
    {
        $buscar = $request->buscar;
        $estado = $request->estado;
        $fecha = $request->fecha;

        $registros = PlantaPersonalSena::with(['empleado.persona:id,nombres,apellidos,numero_documento'])
            ->when($buscar, function($query) use ($buscar) {
                $query->whereHas('empleado.persona', function($q) use ($buscar) {
                    $q->where('nombres', 'LIKE', "%$buscar%")
                      ->orWhere('apellidos', 'LIKE', "%$buscar%")
                      ->orWhere('numero_documento', 'LIKE', "%$buscar%");
                });
            })
            ->when($estado !== null, function($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->when($fecha, function($query) use ($fecha) {
                $query->where('fecha_reporte', $fecha);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.planta_personal_sena.index', compact('registros', 'buscar', 'estado', 'fecha'));
    }

    public function create()
    {
        $empleados = Empleado::with('persona')->where('estado', 1)->get();
        return view('admin.planta_personal_sena.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'fecha_reporte' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        $data = $request->only([
            'empleado_id',
            'fecha_reporte',
            'observaciones'
        ]);

        $data['estado'] = 1;

        PlantaPersonalSena::create($data);

        return redirect()
            ->route('admin.planta_personal_sena.index')
            ->with('success', 'Registro de Planta Personal SENA creado correctamente.');
    }

    public function show($id)
    {
        $registro = PlantaPersonalSena::with('empleado.persona')->findOrFail($id);
        return view('admin.planta_personal_sena.show', compact('registro'));
    }

    public function edit($id)
    {
        $registro = PlantaPersonalSena::with('empleado.persona')->findOrFail($id);
        
        if ($registro->estado == 0) {
            return redirect()->route('admin.planta_personal_sena.index')->with('error', 'No se puede editar un registro inactivo.');
        }

        $empleados = Empleado::with('persona')->where('estado', 1)->get();
        return view('admin.planta_personal_sena.edit', compact('registro', 'empleados'));
    }

    public function update(Request $request, $id)
    {
        $registro = PlantaPersonalSena::findOrFail($id);

        if ($registro->estado == 0) return back()->with('error', 'Edición bloqueada.');

        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'fecha_reporte' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        $data = $request->only([
            'empleado_id',
            'fecha_reporte',
            'observaciones'
        ]);

        $registro->update($data);

        return redirect()->route('admin.planta_personal_sena.index')->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy($id)
    {
        $registro = PlantaPersonalSena::findOrFail($id);
        $registro->update(['estado' => 0]);
        return back()->with('success', 'Registro desactivado correctamente.');
    }

    public function toggleStatus($id)
    {
        $registro = PlantaPersonalSena::findOrFail($id);
        $nuevoEstado = $registro->estado == 1 ? 0 : 1;
        $registro->update(['estado' => $nuevoEstado]);

        $texto = $nuevoEstado == 1 ? 'reactivado' : 'desactivado';
        return back()->with('success', "Registro $texto correctamente.");
    }
}
