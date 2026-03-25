<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formacion;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormacionController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->buscar;
        $formaciones = Formacion::with(['empleado.persona', 'documentos'])
            ->when($buscar, function($query) use ($buscar) {
                $query->whereHas('empleado.persona', function($q) use ($buscar) {
                    $q->where('nombres', 'LIKE', "%$buscar%")
                      ->orWhere('apellidos', 'LIKE', "%$buscar%");
                })->orWhere('nombre_curso', 'LIKE', "%$buscar%");
            })
            ->latest()
            ->paginate(10);

        return view('admin.formaciones.index', compact('formaciones'));
    }

    public function create()
    {
        $empleados = Empleado::with('persona')->where('estado', 1)->get();
        return view('admin.formaciones.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'nombre_curso' => 'required|string|max:150',
            'institucion' => 'required|string|max:150',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date',
            'archivos.*' => 'nullable|file|max:10240',
        ]);

        $formacion = Formacion::create([
            'empleado_id' => $request->empleado_id,
            'nombre_curso' => $request->nombre_curso,
            'institucion' => $request->institucion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'estado' => 1
        ]);

        if ($request->hasFile('archivos')) {
            $files = $request->file('archivos');
            if (!is_array($files)) { $files = [$files]; }
            foreach ($files as $file) {
                if ($file->isValid()) {
                    $formacion->documentos()->create([
                        'nombre_original' => $file->getClientOriginalName(),
                        'ruta' => $file->store('formaciones', 'public'),
                        'tipo_documento' => 'Certificado formación',
                    ]);
                }
            }
        }

        return redirect()->route('admin.formaciones.index')->with('success', 'Formación registrada correctamente.');
    }

    public function edit($id)
    {
        $formacion = Formacion::with('empleado.persona', 'documentos')->findOrFail($id);
        
        if ($formacion->estado == 0) {
            return redirect()->route('admin.formaciones.index')->with('error', 'No se puede editar una formación inactiva.');
        }

        $empleados = Empleado::with('persona')->where('estado', 1)->get();
        return view('admin.formaciones.edit', compact('formacion', 'empleados'));
    }

    public function update(Request $request, $id)
    {
        $formacion = Formacion::findOrFail($id);

        if ($formacion->estado == 0) return back()->with('error', 'Edición bloqueada.');

        $request->validate([
            'nombre_curso' => 'required|string|max:150',
            'institucion' => 'required|string|max:150',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date',
            'archivos.*' => 'nullable|file|max:10240',
        ]);

        $formacion->update($request->only(['nombre_curso', 'institucion', 'fecha_inicio', 'fecha_fin']));

        if ($request->hasFile('archivos')) {
            $files = $request->file('archivos');
            if (!is_array($files)) { $files = [$files]; }
            foreach ($files as $file) {
                if ($file->isValid()) {
                    $formacion->documentos()->create([
                        'nombre_original' => $file->getClientOriginalName(),
                        'ruta' => $file->store('formaciones', 'public'),
                        'tipo_documento' => 'Certificado formación',
                    ]);
                }
            }
        }

        return redirect()->route('admin.formaciones.index')->with('success', 'Formación actualizada correctamente.');
    }

    public function destroy($id)
    {
        $formacion = Formacion::findOrFail($id);
        $formacion->update(['estado' => 0]);
        return back()->with('success', 'Formación desactivada correctamente.');
    }

    public function toggleStatus($id)
    {
        $formacion = Formacion::findOrFail($id);
        $nuevoEstado = $formacion->estado == 1 ? 0 : 1;
        $formacion->update(['estado' => $nuevoEstado]);

        $texto = $nuevoEstado == 1 ? 'reactivada' : 'desactivada';
        return back()->with('success', "Formación $texto correctamente.");
    }
}
