<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compras'; // Aseguramos el plural según tu migración

    protected $fillable = [
        'num_compra',
        'fecha',
        'monto_total',
        'id_acreedor',
        'id_forma_pago' // Agregamos este campo que faltaba
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'id_compra');
    }

    public function acreedor()
    {
        return $this->belongsTo(Acreedor::class, 'id_acreedor');
    }
}