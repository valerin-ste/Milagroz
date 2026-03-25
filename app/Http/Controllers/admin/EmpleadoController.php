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

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::with(['persona', 'area', 'sede', 'rol'])
            ->latest()
            ->paginate(10);

        return view('admin.empleados.index', compact('empleados'));
    }

    public function show(Empleado $empleado)
    {
        $empleado->load([
        'persona',
        'area',
        'sede',
        'rol',
        'etapaPrecontractuales.documentos',
        'etapaContractuales.documentos',    
        'seguridadSaludTrabajo.documentos',
        'evaluacionesDesempeno.documentos',
        'formaciones.documentos' // 🔥 FALTA ESTO
    ]);

        return view('admin.empleados.show', compact('empleado'));
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
}