<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $fillable = [
    'persona_id',
    'area_id',
    'sede_id',
    'rol_id',
    'cargo',
    'fecha_ingreso',
    'estado'
];

    // RELACIONES
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function rol()
    {
        return $this->belongsTo(Role::class); 
    }
}