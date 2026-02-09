<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario'; // [cite: 152]

    protected $fillable = [
        'id_producto',
        'id_tipo_movimiento',
        'cantidad',
        'costo_unitario',
        'fecha'
    ]; // [cite: 158, 167, 170, 171]

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto'); // [cite: 158]
    }

    public function tipoMovimiento(): BelongsTo
    {
        return $this->belongsTo(TipoMovimiento::class, 'id_tipo_movimiento'); // [cite: 158]
    }
}