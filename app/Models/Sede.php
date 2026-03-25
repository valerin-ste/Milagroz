<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    protected $fillable = [
        'nombre',
        'direccion',
        'ciudad',
        'telefono',
        'estado'
    ];

    // Relación: una sede tiene muchas áreas
    public function areas()
    {
        return $this->hasMany(Area::class); // busca areas.sede_id
    }
}