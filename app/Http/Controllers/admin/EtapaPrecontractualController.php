<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EtapaPrecontractual;
use App\Models\Persona;
use App\Services\EtapaPrecontractualService;
use App\Http\Requests\Admin\StoreEtapaPrecontractualRequest;
use App\Http\Requests\Admin\UpdateEtapaPrecontractualRequest;

class EtapaPrecontractualController extends Controller
{
    protected $service;

    public function __construct(EtapaPrecontractualService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $etapas = EtapaPrecontractual::with('persona')
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.etapa_precontractual.index', compact('etapas'));
    }

    public function create()
    {
        $personas = Persona::all();
        return view('admin.etapa_precontractual.create', compact('personas'));
    }

    public function store(StoreEtapaPrecontractualRequest $request)
    {
        $this->service->store($request->validated(), $request->file('archivo'));

        return redirect()->route('admin.etapa_precontractual.index')
            ->with('success', 'Registro creado correctamente.');
    }

    public function edit(EtapaPrecontractual $etapa_precontractual)
    {
        $etapa_precontractual->load('persona');
        return view('admin.etapa_precontractual.edit', compact('etapa_precontractual'));
    }

    public function update(UpdateEtapaPrecontractualRequest $request, EtapaPrecontractual $etapa_precontractual)
    {
        $this->service->update($etapa_precontractual, $request->validated(), $request->file('archivo'));

        return redirect()->route('admin.etapa_precontractual.index')
            ->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(EtapaPrecontractual $etapa_precontractual)
    {
        $this->service->delete($etapa_precontractual);

        return back()->with('success', 'Registro eliminado correctamente.');
    }
}
