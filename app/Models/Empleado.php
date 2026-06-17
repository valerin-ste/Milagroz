<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\Auditable;

class Empleado extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'persona_id',
        'area_id',
        'sede_id',
        'rol_id',
        'cargo',
        'tipo_contrato',
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

    public function certificaciones()
    {
        return $this->hasMany(Certificacion::class);
    }

    public function comunicaciones()
    {
        return $this->hasMany(Comunicacion::class);
    }   

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }

    public function fechasEspeciales()
    {
        return $this->hasMany(FechaEspecial::class);
    }

    public function dotaciones()
    {
        return $this->hasMany(Dotacion::class);
    }

    public function productividades()
    {
        return $this->hasMany(Productividad::class, 'empleado_id');
    }

    public function calidadDocumentos()
    {
        return $this->hasMany(\App\Models\CalidadDocumento::class);
    }

    public function capacidadInstaladas()
    {
        return $this->hasMany(CapacidadInstalada::class, 'empleado_id');
    }

    public function reportesNovedadesNomina()
    {
        return $this->hasMany(ReporteNovedadNomina::class, 'empleado_id');
    }

    public function plantaPersonalSena()
    {
        return $this->hasMany(PlantaPersonalSena::class, 'empleado_id');
    }
}