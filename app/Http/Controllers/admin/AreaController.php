<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Sede;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-areas', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear-areas', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-areas', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-areas', ['only' => ['destroy', 'toggleStatus']]);
    }

public function index(Request $request)
{
    $buscar = $request->buscar;

    $areas = Area::with('sede')
        ->when($buscar, function ($query, $buscar) {
            $query->where('nombre', 'like', '%' . $buscar . '%');
        })
        ->paginate(10)
        ->withQueryString();

    return view('admin.areas.index', compact('areas'));
}

public function create()
{
    $sedes = Sede::all();
    return view('admin.areas.create', compact('sedes'));
}

public function store(Request $request)
{
    Area::create([
        'nombre' => $request->nombre,
        'descripcion' => $request->descripcion,
        'sede_id' => $request->sede_id,
        'estado' => $request->estado ?? 0 // 🔥
    ]);

    return redirect()->route('admin.areas.index')
        ->with('success');
}

public function edit(Area $area)
{
    if ($area->estado != 1) {
        return redirect()->route('admin.areas.index')->with('error', 'No se puede editar un área que está inactiva.');
    }
    $sedes = Sede::all();
    return view('admin.areas.edit', compact('area', 'sedes'));
}

public function update(Request $request, Area $area)
{
    if ($area->estado != 1) {
        return redirect()->route('admin.areas.index')->with('error', 'No se puede actualizar un área que está inactiva.');
    }

    $request->validate([
        'nombre' => 'required|string|max:100',
        'sede_id' => 'required|exists:sedes,id',
    ]);

    $area->update([
        'nombre' => $request->nombre,
        'descripcion' => $request->descripcion,
        'sede_id' => $request->sede_id,
    ]);

    return redirect()->route('admin.areas.index')->with('success', 'Área actualizada correctamente.');
}

public function destroy(Area $area)
{
    $area->update(['estado' => 0]);
    return back()->with('success', 'El área ha sido desactivada correctamente.');
}

public function toggleStatus($id)
{
    $area = Area::findOrFail($id);
    $nuevoEstado = $area->estado == 1 ? 0 : 1;
    $area->update(['estado' => $nuevoEstado]);

    $mensaje = $nuevoEstado == 1 ? 'activada' : 'desactivada';
    return back()->with('success', "El área ha sido $mensaje correctamente.");
}
}