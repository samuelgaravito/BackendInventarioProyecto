<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Empleado extends Model
{
    protected $table = 'empleados';

    protected $fillable = [
        'nombre',
        'apellido',
        'cedula',
        'cargo_id'
    ];

    // Relación: Un empleado pertenece a un cargo (Nivel 1)
    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }

    // Relación: Un empleado puede tener una cuenta de usuario
    public function usuario(): HasOne
    {
        return $this->hasOne(User::class, 'id_empleado');
    }

    // Relación: Un empleado tiene muchos registros de nómina (Nivel 3)
    public function nominas(): HasMany
    {
        return $this->hasMany(Nomina::class, 'id_empleado');
    }
}