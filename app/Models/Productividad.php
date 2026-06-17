<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Productividad extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productividades';

    protected $fillable = [
        'empleado_id',
        'tipo',
        'titulo',
        'descripcion',
        'archivo',
        'fecha',
        'mes',
        'anio',
        'estado',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function documentos()
    {
        return $this->morphMany(\App\Models\Documento::class, 'documentable');
    }
}
