<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Persona;
use App\Models\Area;
use App\Models\Sede;
use App\Models\Role; // ✅ CORRECTO
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::with(['persona','area','sede','rol'])
            ->latest()
            ->paginate(10);

        return view('admin.empleados.index', compact('empleados'));
    }

    public function create()
    {
        $personas = Persona::all();
        $areas = Area::all();
        $sedes = Sede::all();
        $roles = Role::all(); // ✅ CORRECTO

        return view('admin.empleados.create', compact('personas','areas','sedes','roles'));
    }

    public function store(Request $request)
{
    // 1. Crear persona
    $persona = Persona::create([
        'tipo_documento' => $request->tipo_documento,
        'numero_documento' => $request->numero_documento,
        'nombres' => $request->nombres,
        'apellidos' => $request->apellidos,
        'telefono' => $request->telefono,
        'correo' => $request->correo,
        'direccion' => $request->direccion,
        'fecha_nacimiento' => $request->fecha_nacimiento,
    ]);

    // 2. Crear empleado
    Empleado::create([
        'persona_id' => $persona->id,
        'area_id' => $request->area_id,
        'sede_id' => $request->sede_id,
        'rol_id' => $request->rol_id,
        'cargo' => $request->cargo,
        'fecha_ingreso' => $request->fecha_ingreso,
        'estado' => $request->estado,
    ]);

    return redirect()->route('admin.empleados.index')
        ->with('success');
}

    public function edit(Empleado $empleado)
{
    $empleado->load('persona'); // importante

    $areas = Area::all();
    $sedes = Sede::all();
    $roles = Role::all();

    return view('admin.empleados.edit', compact('empleado','areas','sedes','roles'));
}

    public function update(Request $request, Empleado $empleado)
{
    // 🔹 ACTUALIZAR PERSONA
    $empleado->persona->update([
        'tipo_documento' => $request->tipo_documento,
        'numero_documento' => $request->numero_documento,
        'nombres' => $request->nombres,
        'apellidos' => $request->apellidos,
        'telefono' => $request->telefono,
        'correo' => $request->correo,
        'direccion' => $request->direccion,
        'fecha_nacimiento' => $request->fecha_nacimiento,
    ]);

    // 🔹 ACTUALIZAR EMPLEADO
    $empleado->update([
        'area_id' => $request->area_id,
        'sede_id' => $request->sede_id,
        'rol_id' => $request->rol_id,
        'cargo' => $request->cargo,
        'fecha_ingreso' => $request->fecha_ingreso,
        'estado' => $request->estado,
    ]);

    return redirect()->route('admin.empleados.index')
        ->with('success');
}

    public function destroy(Empleado $empleado)
    {
        $empleado->delete();

        return back()->with('success');
    }
}