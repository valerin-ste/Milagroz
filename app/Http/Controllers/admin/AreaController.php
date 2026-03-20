<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Sede;
use Illuminate\Http\Request;

class AreaController extends Controller
{

public function index(Request $request)
{
    $buscar = $request->buscar;

    $areas = Area::with('sede')
        ->when($buscar, function ($query, $buscar) {
            $query->where('nombre', 'like', '%' . $buscar . '%');
        })
        ->paginate(10)
        ->withQueryString();

    // 🔥 CONTADORES REALES
    $totalAreas = Area::count();
    $areasActivas = Area::where('estado', 1)->count();
    $areasInactivas = Area::where('estado', 0)->count();

    return view('admin.areas.index', compact(
        'areas',
        'totalAreas',
        'areasActivas',
        'areasInactivas'
    ));
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
    $sedes = Sede::all();
    return view('admin.areas.edit', compact('area', 'sedes'));
}

public function update(Request $request, Area $area)
{
    $area->update([
        'nombre' => $request->nombre,
        'descripcion' => $request->descripcion,
        'sede_id' => $request->sede_id,
        'estado' => $request->estado ?? 0 // 🔥
    ]);

    return redirect()->route('admin.areas.index')
        ->with('success');
}
}