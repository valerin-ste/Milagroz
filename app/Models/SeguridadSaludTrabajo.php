<?php

namespace App\Models;

use App\Traits\HasStatusAlerts;
use Illuminate\Database\Eloquent\Model;

class SeguridadSaludTrabajo extends Model
{
    use HasStatusAlerts;
    protected $table = 'seguridad_salud_trabajo';
    // La tabla sí tiene columnas created_at / updated_at (según migración)
    // public $timestamps = false; <-- deshabilitado correctamente

    protected $fillable = [
        'empleado_id',
        'tipo_documento',
        // 'archivo' se omite: el módulo usa la relación polimórfica 'documentos'
        'fecha',
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
