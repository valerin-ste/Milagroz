<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Persona
            'tipo_documento' => 'required|string|max:10',
            'numero_documento' => 'required|string|max:50|unique:personas,numero_documento',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:150|unique:personas,correo',
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
