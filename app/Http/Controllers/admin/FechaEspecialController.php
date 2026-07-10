<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FechaEspecial;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FechaEspecialController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-fechas_especiales', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear-fechas_especiales', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-fechas_especiales', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-fechas_especiales', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $buscar = $request->buscar;
        $tipo = $request->tipo;

        $fechas = FechaEspecial::with('empleado.persona')

            // Si es empleado, solo ve sus registros
            ->when(auth()->user()->hasRole('Empleado'), function ($query) {
                $query->whereHas('empleado.persona.user', function ($q) {
                    $q->where('id', auth()->id());
                });
            })

            ->when($buscar, function ($q) use ($buscar) {
                $q->whereHas('empleado.persona', function ($sq) use ($buscar) {
                    $sq->where('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellidos', 'like', "%{$buscar}%");
                });
            })

            ->when($tipo, function ($q) use ($tipo) {
                $q->where('tipo', 'like', "%{$tipo}%");
            })

            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.fechas_especiales.index', compact('fechas', 'buscar', 'tipo'));
    }

    public function create()
    {
        $empleados = Empleado::with('persona')->where('estado', 1)->get();
        return view('admin.fechas_especiales.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'tipo'        => 'required|string|max:255',
            'fecha'       => 'required|date',
            'archivo'     => 'nullable|file|mimes:pdf|max:10240',
            'descripcion' => 'nullable|string',
            'estado'      => 'required|integer'
        ]);

        $data = $request->except(['archivo']);

        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $nombre = time() . '_' . $archivo->getClientOriginalName(); // evita duplicados
            $data['archivo'] = $archivo->storeAs('fechas_especiales/archivos', $nombre, 'public');
        }

        FechaEspecial::create($data);

        return redirect()->route('admin.fechas_especiales.index')
            ->with('success', 'Fecha especial creada correctamente.');
    }

    public function show(FechaEspecial $fechaEspecial)
    {
        return view('admin.fechas_especiales.show', compact('fechaEspecial'));
    }

    public function edit($id)
    {
        $fechaEspecial = FechaEspecial::findOrFail($id);
        $empleados = Empleado::with('persona')->get();
        return view('admin.fechas_especiales.edit', compact('fechaEspecial', 'empleados'));
    }

    public function update(Request $request, $id)
    {
        $fechaEspecial = FechaEspecial::findOrFail($id);

        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'tipo'        => 'required|string|max:255',
            'fecha'       => 'required|date',
            'archivo'     => 'nullable|file|mimes:pdf|max:10240',
            'descripcion' => 'nullable|string',
            'estado'      => 'required|integer'
        ]);

        $data = $request->except(['archivo']);

        if ($request->hasFile('archivo')) {
            if ($fechaEspecial->archivo && Storage::disk('public')->exists($fechaEspecial->archivo)) {
                Storage::disk('public')->delete($fechaEspecial->archivo);
            }
            $archivo = $request->file('archivo');
            $nombre = time() . '_' . $archivo->getClientOriginalName();
            $data['archivo'] = $archivo->storeAs('fechas_especiales/archivos', $nombre, 'public');
        }

        $fechaEspecial->update($data);

        return redirect()->route('admin.fechas_especiales.index')
            ->with('success', 'Fecha especial actualizada correctamente.');
    }

    public function destroy($id)
    {
        $fechaEspecial = FechaEspecial::findOrFail($id);
        $fechaEspecial->delete();

        return redirect()->route('admin.fechas_especiales.index')
            ->with('success', 'Fecha especial eliminada correctamente.');
    }

    public function toggleStatus($id)
    {
        $fechaEspecial = FechaEspecial::findOrFail($id);
        $fechaEspecial->estado = $fechaEspecial->estado == 1 ? 0 : 1;
        $fechaEspecial->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'nuevo_estado' => $fechaEspecial->estado
        ]);
    }
}
