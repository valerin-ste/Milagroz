<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtapaPrecontractual extends Model
{
    use HasFactory;

    protected $table = 'etapa_precontractual';
    public $timestamps = false;

    protected $fillable = [
        'persona_id',
        'archivo',
        'estado',
        'fecha_registro',
    ];

    /**
     * Get the persona that owns the etapa precontractual.
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
}
