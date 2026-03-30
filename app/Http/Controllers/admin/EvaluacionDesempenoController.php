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
        $estado = $request->estado;

        $evaluaciones = EvaluacionDesempeno::with(['empleado.persona', 'documentos'])
            ->when($buscar, function($q) use ($buscar) {
                $q->whereHas('empleado.persona', function($sq) use ($buscar) {
                    $sq->where('nombres', 'like', "%$buscar%")
                       ->orWhere('apellidos', 'like', "%$buscar%");
                });
            })
            ->when($estado !== null && $estado !== '', function($q) use ($estado) {
                $q->where('estado', $estado);
            })
            ->orderByDesc('fecha')
            ->paginate(10)
            ->withQueryString();

        return view('admin.evaluaciones_desempeno.index', compact('evaluaciones', 'buscar', 'estado'));
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
            'calificacion' => 'required|integer|min:1|max:10',
            'fecha' => 'required|date',
            'archivos.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $evaluacion = EvaluacionDesempeno::create($request->only([
            'empleado_id', 'calificacion', 'observaciones', 'fecha'
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
        
        if ($evaluacion->estado != 1) {
            return redirect()->route('admin.evaluaciones_desempeno.index')->with('error', 'No se puede editar una evaluación inactiva.');
        }

        $empleados = Empleado::with('persona')->get();
        return view('admin.evaluaciones_desempeno.edit', compact('evaluacion', 'empleados'));
    }

    public function update(Request $request, $id)
    {
        $evaluacion = EvaluacionDesempeno::findOrFail($id);

        if ($evaluacion->estado != 1) {
            return redirect()->route('admin.evaluaciones_desempeno.index')->with('error', 'No se puede actualizar una evaluación inactiva.');
        }

        $request->validate([
            'calificacion' => 'required|integer|min:1|max:10',
            'fecha' => 'required|date',
            'archivos.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $evaluacion->update($request->only(['calificacion', 'observaciones', 'fecha']));

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
            ->with('success', 'Evaluación actualizada correctamente.');
    }

    public function destroy($id)
    {
        $evaluacion = EvaluacionDesempeno::findOrFail($id);
        $evaluacion->update(['estado' => 0]);
        return back()->with('success', 'La evaluación ha sido desactivada correctamente.');
    }

    public function toggleStatus($id)
    {
        $evaluacion = EvaluacionDesempeno::findOrFail($id);
        $nuevoEstado = $evaluacion->estado == 1 ? 0 : 1;
        $evaluacion->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado == 1 ? 'activada' : 'desactivada';
        return back()->with('success', "La evaluación ha sido $mensaje correctamente.");
    }
}
