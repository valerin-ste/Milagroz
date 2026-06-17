<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlantaPersonalSena extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'planta_personal_sena';

    protected $fillable = [
        'empleado_id',
        'observaciones',
        'fecha_reporte',
        'estado',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
