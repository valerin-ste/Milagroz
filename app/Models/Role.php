<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class Role extends Model
{
    use Auditable;
    use HasFactory;


    protected $fillable = [
        'nombre',
        'descripcion',
        'estado'
    ];

    public function systemRoles()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Role::class, 'cargo_system_role', 'cargo_id', 'system_role_id');
    }
}