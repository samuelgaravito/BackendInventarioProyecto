<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormaPago extends Model
{
    protected $table = 'formas_pago';

    protected $fillable = ['descripcion'];

    // Relación: Una forma de pago está en muchas ventas (Nivel 3)
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'id_forma_pago');
    }

    // Relación: Una forma de pago está en muchas compras (Nivel 3)
    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class, 'id_forma_pago');
    }
}