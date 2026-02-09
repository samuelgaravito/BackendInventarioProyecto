<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cargo extends Model
{
    protected $table = 'cargos';

    protected $fillable = [
        'descripcion',
        'sueldo_base'
    ];

    // Relación: Un cargo pertenece a muchos empleados (Nivel 2)
    public function empleados(): HasMany
    {
        return $this->hasMany(Empleado::class, 'cargo_id');
    }
}