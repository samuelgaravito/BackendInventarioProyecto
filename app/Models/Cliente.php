<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'cedula',
        'telefono',
        'direccion'
    ];

    // Relación: Un cliente tiene muchas ventas (Nivel 3)
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'id_cliente');
    }

    // Relación: Un cliente tiene muchas cuentas por cobrar (Nivel 4)
    public function cuentasCobrar(): HasMany
    {
        return $this->hasMany(CuentaCobrar::class, 'id_cliente');
    }
}