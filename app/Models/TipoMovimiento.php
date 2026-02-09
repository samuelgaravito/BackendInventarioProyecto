<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoMovimiento extends Model
{
    protected $table = 'tipos_movimiento';

    protected $fillable = ['descripcion'];

    // Relación: Un tipo de movimiento aparece en muchos registros de inventario (Nivel 3)
    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'id_tipo_movimiento');
    }
}