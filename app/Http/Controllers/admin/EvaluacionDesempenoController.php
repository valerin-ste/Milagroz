<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluacionDesempeno;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvaluacionDesempenoController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->buscar;
        $evaluaciones = EvaluacionDesempeno::with(['empleado.persona', 'documentos'])
            ->when($buscar, function($q) use ($buscar) {
                $q->whereHas('empleado.persona', function($sq) use ($buscar) {
                    $sq->where('nombres', 'like', "%$buscar%")
                       ->orWhere('apellidos', 'like', "%$buscar%");
                });
            })
            ->orderByDesc('fecha')
            ->paginate(10)
            ->withQueryString();

        return view('admin.evaluaciones_desempeno.index', compact('evaluaciones', 'buscar'));
    }

    public function create()
    {
        $empleados = Empleado::with('persona')->where('estado', 1)->get();
        return view('admin.evaluaciones_desempeno.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'fecha' => 'required|date',
            'archivos.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $evaluacion = EvaluacionDesempeno::create($request->only([
            'empleado_id', 'observaciones', 'fecha'
        ]));

        if ($request->hasFile('archivos')) {
            $files = $request->file('archivos');
            if (!is_array($files)) { $files = [$files]; }
            foreach ($files as $file) {
                if ($file->isValid()) {
                    $evaluacion->documentos()->create([
                        'nombre_original' => $file->getClientOriginalName(),
                        'ruta' => $file->store('evaluaciones', 'public'),
                        'tipo_documento' => 'Evaluación desempeño',
                    ]);
                }
            }
        }

        return redirect()->route('admin.evaluaciones_desempeno.index')
            ->with('success', 'Evaluación creada correctamente.');
    }

    public function edit($id)
    {
        $evaluacion = EvaluacionDesempeno::findOrFail($id);
        $empleados = Empleado::with('persona')->get();
        return view('admin.evaluaciones_desempeno.edit', compact('evaluacion', 'empleados'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fecha' => 'required|date',
            'archivos.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $evaluacion->update($request->only(['observaciones', 'fecha']));

        if ($request->hasFile('archivos')) {
            $files = $request->file('archivos');
            if (!is_array($files)) { $files = [$files]; }
            foreach ($files as $file) {
                if ($file->isValid()) {
                    $evaluacion->documentos()->create([
                        'nombre_original' => $file->getClientOriginalName(),
                        'ruta' => $file->store('evaluaciones', 'public'),
                        'tipo_documento' => 'Evaluación desempeño',
                    ]);
                }
            }
        }

        // 🔥 ARCHIVOS A ELIMINAR (SISTEMA ESTANDARIZADO)
        if ($request->eliminar_documentos) {
            foreach ($request->eliminar_documentos as $docId) {
                $doc = \App\Models\Documento::find($docId);
                if ($doc && $doc->documentable_id == $evaluacion->id) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($doc->ruta)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->ruta);
                    }
                    $doc->delete();
                }
            }
        }

        return redirect()->route('admin.evaluaciones_desempeno.index')
            ->with('success', 'Evaluación actualizada correctamente.');
    }

    public function destroy($id)
    {
        $evaluacion = EvaluacionDesempeno::findOrFail($id);
        // Aunque el usuario pide ocultar el estado, mantenemos el registro pero eliminamos la lógica de desactivación si prefiere eliminación real
        // O simplemente lo dejamos como hard delete si el estado ya no importa.
        // Dado el sistema, aplicaremos el soft delete si el modelo lo soporta, o simplemente delete.
        $evaluacion->delete();
        return redirect()->route('admin.evaluaciones_desempeno.index')
            ->with('success', 'Evaluación eliminada correctamente.');
    }
}
