<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FechaEspecial extends Model
{
    use SoftDeletes;

    protected $table = 'fechas_especiales';

    protected $fillable = [
        'empleado_id',
        'titulo',
        'fecha',
        'tipo',
        'descripcion',
        'archivo',
        'estado'
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
