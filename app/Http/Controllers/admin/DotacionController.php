<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dotacion;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DotacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-dotaciones', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear-dotaciones', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-dotaciones', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-dotaciones', ['only' => ['destroy', 'toggleStatus']]);
    }

    public function index(Request $request)
    {
        $buscar = $request->buscar;
        $estado = $request->estado;

        $dotaciones = Dotacion::with(['empleado.persona:id,nombres,apellidos', 'documentos'])

            // Si es empleado, solo ve sus dotaciones
            ->when(auth()->user()->hasRole('Empleado'), function ($query) {
                $query->whereHas('empleado', function ($q) {
                    $q->where('persona_id', auth()->user()->persona_id);
                });
            })

            ->when($buscar, function($query) use ($buscar) {
                $query->where(function($q) use ($buscar) {
                    $q->whereHas('empleado.persona', function($sq) use ($buscar) {
                        $sq->where('nombres', 'LIKE', "%{$buscar}%")
                        ->orWhere('apellidos', 'LIKE', "%{$buscar}%");
                    })
                    ->orWhere('tipo_dotacion', 'LIKE', "%{$buscar}%")
                    ->orWhere('talla', 'LIKE', "%{$buscar}%");
                });
            })

            ->when($estado !== null && $estado !== '', function($query) use ($estado) {
                $query->where('estado', $estado);
            })

            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.dotaciones.index', compact('dotaciones', 'buscar', 'estado'));
    }

    public function create()
    {
        $empleados = Empleado::with('persona')->where('estado', 1)->get();
        return view('admin.dotaciones.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'tipo_dotacion' => 'required|string|max:100',
            'talla' => 'nullable|string|max:50',
            'cantidad' => 'required|integer|min:1',
            'fecha' => 'required|date',
            'archivo' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:5120',
            'observaciones' => 'nullable|string'
        ]);

        $data = $request->only([
            'empleado_id', 'tipo_dotacion', 'talla', 'cantidad', 'fecha', 'observaciones'
        ]);
        $data['estado'] = 1;

        $dotacion = Dotacion::create($data);

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            if ($file->isValid()) {
                $ruta = $file->store('dotaciones', 'public');
                $dotacion->update(['archivo' => $ruta]);

                $dotacion->documentos()->create([
                    'nombre_original' => $file->getClientOriginalName(),
                    'ruta' => $ruta,
                    'tipo_documento' => 'Dotación',
                ]);
            }
        }

        return redirect()->route('admin.dotaciones.index')->with('success', 'Dotación registrada correctamente.');
    }

    public function edit($id)
    {
        $dotacion = Dotacion::with('empleado.persona')->findOrFail($id);
        $empleados = Empleado::with('persona')->where('estado', 1)->get();
        return view('admin.dotaciones.edit', compact('dotacion', 'empleados'));
    }

    public function update(Request $request, $id)
    {
        $dotacion = Dotacion::findOrFail($id);

        $request->validate([
            'tipo_dotacion' => 'required|string|max:100',
            'talla' => 'nullable|string|max:20',
            'cantidad' => 'required|integer|min:1',
            'fecha' => 'required|date',
            'archivo' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:5120',
            'observaciones' => 'nullable|string'
        ]);

        $data = $request->only([
            'tipo_dotacion', 'talla', 'cantidad', 'fecha', 'observaciones'
        ]);

        $dotacion->update($data);

        if ($request->hasFile('archivo')) {
            if ($dotacion->archivo && Storage::disk('public')->exists($dotacion->archivo)) {
                Storage::disk('public')->delete($dotacion->archivo);
            }

            $file = $request->file('archivo');
            if ($file->isValid()) {
                $ruta = $file->store('dotaciones', 'public');
                $dotacion->update(['archivo' => $ruta]);

                $dotacion->documentos()->create([
                    'nombre_original' => $file->getClientOriginalName(),
                    'ruta' => $ruta,
                    'tipo_documento' => 'Dotación',
                ]);
            }
        }

        if ($request->eliminar_documentos) {
            foreach ($request->eliminar_documentos as $docId) {
                $doc = \App\Models\Documento::find($docId);
                if ($doc && $doc->documentable_id == $dotacion->id) {
                    if (Storage::disk('public')->exists($doc->ruta)) {
                        Storage::disk('public')->delete($doc->ruta);
                    }
                    $doc->delete();
                }
            }
        }

        return redirect()->route('admin.dotaciones.index')->with('success', 'Dotación actualizada correctamente.');
    }

    public function destroy($id)
    {
        $dotacion = Dotacion::findOrFail($id);
        $dotacion->delete();
        return back()->with('success', 'Dotación eliminada correctamente.');
    }

    public function toggleStatus($id)
    {
        $dotacion = Dotacion::findOrFail($id);
        $nuevoEstado = $dotacion->estado == 1 ? 0 : 1;
        $dotacion->update(['estado' => $nuevoEstado]);

        $texto = $nuevoEstado == 1 ? 'activada' : 'desactivada';
        return back()->with('success', "Dotación $texto correctamente.");
    }
}
