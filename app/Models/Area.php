<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'sede_id',
        'estado' // 🔥 IMPORTANTE
    ];

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }
}