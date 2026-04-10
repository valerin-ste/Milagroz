<?php

namespace App\Exports;

use App\Models\Empleado;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Empleado::with(['persona', 'area', 'sede', 'etapaContractuales' => function($q) {
            $q->orderBy('id', 'desc');
        }]);

        if (isset($this->filters['buscar'])) {
            $buscar = $this->filters['buscar'];
            $query->whereHas('persona', function ($q) use ($buscar) {
                $q->where('nombres', 'like', "%$buscar%")
                  ->orWhere('apellidos', 'like', "%$buscar%")
                  ->orWhere('numero_documento', 'like', "%$buscar%");
            });
        }

        if (isset($this->filters['estado'])) {
            $query->where('estado', $this->filters['estado']);
        }

        if (isset($this->filters['area_id'])) {
            $query->where('area_id', $this->filters['area_id']);
        }

        if (isset($this->filters['sede_id'])) {
            $query->where('sede_id', $this->filters['sede_id']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nombre Completo',
            'Documento',
            'Cargo',
            'Área',
            'Sede',
            'Tipo de Contrato',
            'Estado'
        ];
    }

    public function map($empleado): array
    {
        $latestContrato = $empleado->etapaContractuales->first();
        
        return [
            ($empleado->persona->nombres ?? '') . ' ' . ($empleado->persona->apellidos ?? ''),
            $empleado->persona->numero_documento ?? 'N/A',
            $empleado->cargo,
            $empleado->area->nombre ?? 'N/A',
            $empleado->sede->nombre ?? 'N/A',
            $latestContrato->tipo_contrato ?? 'No registrado',
            $empleado->estado == 1 ? 'Activo' : 'Inactivo'
        ];
    }
}
