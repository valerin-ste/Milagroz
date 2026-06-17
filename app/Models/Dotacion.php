<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dotacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dotaciones';

    protected $fillable = [
        'empleado_id',
        'tipo_dotacion',
        'talla',
        'cantidad',
        'observaciones',
        'archivo',
        'fecha',
        'estado'
    ];

    protected $casts = [
        'fecha' => 'date',
        'estado' => 'boolean',
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
