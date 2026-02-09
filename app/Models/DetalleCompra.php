<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleCompra extends Model
{
    protected $table = 'detalles_compra'; 

    protected $fillable = [
        'id_compra',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'importe'
    ]; 
    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class, 'id_compra'); 
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto'); 
    }
}