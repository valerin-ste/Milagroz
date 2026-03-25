<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeguridadSaludTrabajo extends Model
{
    protected $table = 'seguridad_salud_trabajo';
    public $timestamps = false;

    protected $fillable = [
        'empleado_id',
        'tipo_documento',
        'archivo', 
        'fecha',
        'estado'
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    /**
     * Relación polimórfica para múltiples archivos.
     */
    public function documentos()
    {
        return $this->morphMany(Documento::class, 'documentable');
    }

    /**
     * Relación: un registro pertenece a un empleado.
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
