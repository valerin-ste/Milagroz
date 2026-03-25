<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Empleado;

class UpdateEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtener el empleado desde la ruta (puede ser el modelo o el ID)
        $empleadoRoute = $this->route('empleado');
        
        $personaId = null;
        if ($empleadoRoute instanceof Empleado) {
            $personaId = $empleadoRoute->persona_id;
        } elseif (is_numeric($empleadoRoute)) {
            $empleado = Empleado::find($empleadoRoute);
            $personaId = $empleado ? $empleado->persona_id : null;
        }

        \Log::info('DEBUG UNIQUE RULE', ['route' => $empleadoRoute, 'personaId' => $personaId]);

        return [
            // Persona
            'tipo_documento' => 'required|string|max:10',
            'numero_documento' => [
                'required',
                'string',
                'max:50',
                Rule::unique('personas', 'numero_documento')->ignore($personaId)
            ],
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'correo' => [
                'nullable',
                'email',
                'max:150',
                Rule::unique('personas', 'correo')->ignore($personaId)
            ],
            'direccion' => 'nullable|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            // Empleado
            'area_id' => 'required|exists:areas,id',
            'sede_id' => 'required|exists:sedes,id',
            'rol_id' => 'required|exists:roles,id',
            'cargo' => 'required|string|max:150',
            'fecha_ingreso' => 'required|date',
            'estado' => 'required|boolean',
        ];
    }
}
