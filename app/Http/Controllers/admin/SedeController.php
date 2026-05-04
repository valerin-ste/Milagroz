<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sede;
use App\Models\Area;
use Illuminate\Http\Request;

class SedeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-sedes', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear-sedes', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-sedes', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-sedes', ['only' => ['destroy', 'toggleStatus']]);
    }
    /**
     * Mostrar listado de sedes con paginación
     */
    public function index()
    {
        // Trae las sedes con sus áreas relacionadas, paginación 10 por página
        $sedes = Sede::with('areas')->paginate(10);

        return view('admin.sedes.index', compact('sedes'));
    }

    /**
     * Formulario para crear nueva sede
     */
    public function create()
    {
        $areas = Area::all(); // Para selección múltiple
        return view('admin.sedes.create', compact('areas'));
    }

    /**
     * Guardar nueva sede
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'direccion' => 'nullable|string|max:150',
            'ciudad' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'areas' => 'nullable|array',
            'areas.*' => 'exists:areas,id'
        ]);

        // Crear la sede
        $sede = Sede::create($request->only(['nombre', 'direccion', 'ciudad', 'telefono']));

        // Asignar áreas (si se seleccionaron)
        if ($request->has('areas')) {
            foreach ($request->areas as $areaId) {
                $area = Area::find($areaId);
                if ($area) {
                    $area->sede_id = $sede->id;
                    $area->save();
                }
            }
        }

        return redirect()->route('admin.sedes.index')
                         ->with('success'
                         
                         );
    }

    /**
     * Formulario para editar sede
     */
    public function edit($id)
    {
        $sede = Sede::with('areas')->findOrFail($id);
        
        if ($sede->estado != 1) {
            return redirect()->route('admin.sedes.index')->with('error', 'No se puede editar una sede que está inactiva.');
        }

        $areas = Area::all();
        return view('admin.sedes.edit', compact('sede', 'areas'));
    }

    /**
     * Actualizar sede
     */
    public function update(Request $request, $id)
    {
        $sede = Sede::findOrFail($id);

        if ($sede->estado != 1) {
            return redirect()->route('admin.sedes.index')->with('error', 'No se puede actualizar una sede que está inactiva.');
        }

        $request->validate([
            'nombre' => 'required|string|max:100',
            'direccion' => 'nullable|string|max:150',
            'ciudad' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'areas' => 'nullable|array',
            'areas.*' => 'exists:areas,id'
        ]);

        // Actualizar datos de la sede
        $sede->update($request->only(['nombre', 'direccion', 'ciudad', 'telefono']));

        // Primero desvincular todas las áreas que pertenecen a esta sede
        Area::where('sede_id', $sede->id)->update(['sede_id' => null]);

        // Asignar las nuevas áreas seleccionadas
        if ($request->has('areas')) {
            foreach ($request->areas as $areaId) {
                $area = Area::find($areaId);
                if ($area) {
                    $area->sede_id = $sede->id;
                    $area->save();
                }
            }
        }

        return redirect()->route('admin.sedes.index')
                         ->with('success', 'Sede actualizada correctamente.');
    }

    /**
     * Eliminar sede
     */
    public function destroy($id)
    {
        $sede = Sede::findOrFail($id);
        $sede->update(['estado' => 0]);

        return redirect()->route('admin.sedes.index')
                         ->with('success', 'La sede ha sido desactivada correctamente.');
    }

    public function toggleStatus($id)
    {
        $sede = Sede::findOrFail($id);
        $nuevoEstado = $sede->estado == 1 ? 0 : 1;
        $sede->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado == 1 ? 'activada' : 'desactivada';
        return redirect()->route('admin.sedes.index')
                         ->with('success', "La sede ha sido $mensaje correctamente.");
    }
}