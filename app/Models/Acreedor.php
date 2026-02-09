<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Acreedor extends Model
{
    protected $table = 'acreedores';

    protected $fillable = [
        'rif',
        'nombre'
    ];

    // Relación: Un acreedor tiene muchas compras (Nivel 3)
    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class, 'id_acreedor');
    }

    // Relación: Un acreedor tiene muchas cuentas por pagar (Nivel 4)
    public function cuentasPagar(): HasMany
    {
        return $this->hasMany(CuentaPagar::class, 'id_acreedor');
    }
}