<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'personas';

    protected $fillable = [
    'tipo_documento',
    'numero_documento',
    'nombres',
    'apellidos',
    'telefono',
    'correo',
    'direccion',
    'fecha_nacimiento'
    ];

    /**
     * Get the abbreviated name (First Name + First Surname)
     */
    public function getShortNameAttribute()
    {
        $nombres = explode(' ', trim($this->nombres));
        $apellidos = explode(' ', trim($this->apellidos));
        return ($nombres[0] ?? '') . ' ' . ($apellidos[0] ?? '');
    }

    /**
     * Get the full name
     */
    public function getFullNameAttribute()
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    /**
     * Get the user account for the persona.
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * Get the employee record for the persona.
     */
    public function empleado()
    {
        return $this->hasOne(Empleado::class);
    }

    /**
     * Get the etapa precontractual records for the persona.
     */
    public function etapaPrecontractuales()
    {
        return $this->hasMany(EtapaPrecontractual::class);
    }
}