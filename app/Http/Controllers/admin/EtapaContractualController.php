<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EtapaContractual;
use App\Models\Empleado;
use App\Services\EtapaContractualService;
use App\Http\Requests\Admin\StoreEtapaContractualRequest;
use App\Http\Requests\Admin\UpdateEtapaContractualRequest;

class EtapaContractualController extends Controller
{
    protected $service;

    public function __construct(EtapaContractualService $service)
    {
        $this->service = $service;
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $busqueda  = $request->buscar;
        $documento = $request->documento;
        $tipo      = $request->tipo_contrato;

        // Lista fija de tipos de contrato
        $tiposContrato = [
            'Contrato fijo',
            'Contrato indefinido',
            'Obra o labor',
            'Temporal',
            'Prestación de servicios'
        ];

        $contratos = EtapaContractual::with(['empleado.persona', 'documentos'])
            ->when($busqueda, function ($query, $busqueda) {
                $query->whereHas('empleado.persona', function ($q) use ($busqueda) {
                    $q->where('nombres', 'like', "%$busqueda%")
                      ->orWhere('apellidos', 'like', "%$busqueda%");
                });
            })
            ->when($documento, function ($query, $documento) {
                $query->whereHas('empleado.persona', function ($q) use ($documento) {
                    $q->where('numero_documento', 'like', "%$documento%");
                });
            })
            ->when($tipo, function ($query, $tipo) {
                $query->where('tipo_contrato', $tipo);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.etapa_contractual.index', compact('contratos', 'busqueda', 'documento', 'tipo', 'tiposContrato'));
    }

    public function create()
    {
        // Optimización N+1
        $empleados = Empleado::with('persona:id,nombres,apellidos,numero_documento')->get();
        return view('admin.etapa_contractual.create', compact('empleados'));
    }

    public function store(StoreEtapaContractualRequest $request)
    {
        $this->service->store($request->validated(), $request->file('documentos'));

        return redirect()->route('admin.etapa_contractual.index')
            ->with('success', 'Registro contractual creado correctamente.');
    }

    public function edit(EtapaContractual $etapa_contractual)
    {
        $etapa_contractual->load('empleado.persona');
        return view('admin.etapa_contractual.edit', compact('etapa_contractual'));
    }

    public function update(UpdateEtapaContractualRequest $request, EtapaContractual $etapa_contractual)
    {
        $this->service->update($etapa_contractual, $request->validated(), $request->file('documentos'));

        return redirect()->route('admin.etapa_contractual.index')
            ->with('success', 'Registro contractual actualizado correctamente.');
    }

    public function destroy(EtapaContractual $etapa_contractual)
    {
        $etapa_contractual->update(['estado' => 0]);
        return back()->with('success', 'Registro contractual desactivado correctamente.');
    }

    public function toggleStatus($id)
    {
        $registro = EtapaContractual::findOrFail($id);
        $nuevoEstado = $registro->estado == 1 ? 0 : 1;
        $registro->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado == 1 ? 'activado' : 'desactivado';
        return back()->with('success', "Registro $mensaje correctamente.");
    }
}
