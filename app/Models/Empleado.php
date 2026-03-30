<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empleado extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'persona_id',
        'area_id',
        'sede_id',
        'rol_id',
        'cargo',
        'fecha_ingreso',
        'estado'
    ];

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
        return $this->belongsTo(Role::class); // ✅ CORREGIDO
    }

    public function etapaPrecontractuales()
    {
        return $this->hasMany(
            EtapaPrecontractual::class,
            'persona_id',     // FK en la tabla etapa_precontractual
            'persona_id'      // FK en empleados
        );
    }

    public function etapaContractuales()
    {
        return $this->hasMany(EtapaContractual::class, 'empleado_id');
    }

    public function seguridadSaludTrabajo()
    {
        return $this->hasMany(SeguridadSaludTrabajo::class, 'empleado_id');
    }

    public function evaluacionesDesempeno()
    {
        return $this->hasMany(EvaluacionDesempeno::class, 'empleado_id');
    }

    public function formaciones()
    {
        return $this->hasMany(Formacion::class);
    }

    public function comunicaciones()
    {
        return $this->hasMany(Comunicacion::class);
    }   

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }

   
}