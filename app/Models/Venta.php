<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Venta extends Model
{
    protected $table = 'ventas'; //

    protected $fillable = [
        'id_cliente',
        'num_venta',
        'id_forma_pago',
        'monto_total',
        'fecha'
    ]; //

    // Relación: Una venta pertenece a un cliente (Nivel 1)
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente'); //
    }

    // Relación: Una venta tiene una forma de pago (Nivel 1)
    public function formaPago(): BelongsTo
    {
        return $this->belongsTo(FormaPago::class, 'id_forma_pago'); //
    }

    // Relación: Una venta tiene muchos detalles (Nivel 4)
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta'); //
    }

    // Relación: Una venta puede generar una cuenta por cobrar (Nivel 4)
    public function cuentaCobrar(): HasOne
    {
        return $this->hasOne(CuentaCobrar::class, 'id_venta'); //
    }
}