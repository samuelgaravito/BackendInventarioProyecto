<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'referencia',
        'descripcion',
        'costo_unitario',
        'precio_venta',
        'existencia',
        'stock_minimo', // Agregado para lógica de inventario
        'debe',
        'haber',
        'saldo'
    ];

    // Relación: Un producto aparece en muchos detalles de venta (Nivel 4)
    public function detallesVenta(): HasMany
    {
        return $this->hasMany(DetalleVenta::class, 'id_producto');
    }

    // Relación: Un producto aparece en muchos detalles de compra (Nivel 4)
    public function detallesCompra(): HasMany
    {
        return $this->hasMany(DetalleCompra::class, 'id_producto');
    }

    // Relación: Un producto tiene muchos movimientos de inventario (Nivel 3)
    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'id_producto');
    }
}