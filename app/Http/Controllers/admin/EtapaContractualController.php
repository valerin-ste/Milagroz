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

    public function index()
    {
        $contratos = EtapaContractual::with(['empleado.persona', 'documentos'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.etapa_contractual.index', compact('contratos'));
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
