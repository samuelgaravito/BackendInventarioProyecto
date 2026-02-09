<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuentaCobrar extends Model
{
    protected $table = 'cuentas_cobrar'; 
    protected $fillable = [
        'id_cliente',
        'id_venta',
        'fecha_emision',
        'fecha_vencimiento',
        'plazo_pago',
        'monto',
        'estatus'
    ]; 

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente'); 
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'id_venta');
    }

    // Relación: Una cuenta por cobrar tiene muchos abonos (Nivel 5)
    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoCobrar::class, 'id_cuentas_cobrar');
    }
}