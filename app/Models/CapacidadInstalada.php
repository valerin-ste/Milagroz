<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Documento;

class CapacidadInstalada extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'capacidad_instalada';

    protected $fillable = [
        'empleado_id',
        'proceso',
        'capacidad_disponible',
        'capacidad_utilizada',
        'observaciones',
        'fecha',
        'estado',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function documentos()
    {
        return $this->morphMany(Documento::class, 'documentable');
    }
}
