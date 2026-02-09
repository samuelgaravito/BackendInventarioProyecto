<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id(); 
            $table->string('referencia')->unique(); // [cite: 99]
            $table->string('descripcion'); // [cite: 104]
            
            // Costos y Precios con precisión para PostgreSQL
            $table->decimal('costo_unitario', 12, 2)->default(0); // [cite: 108]
            $table->decimal('precio_venta', 12, 2)->default(0);  // [cite: 116]
            
            // Inventario
            $table->integer('existencia')->default(0); // [cite: 125]
            $table->integer('stock_minimo')->default(5); // <-- Faltante útil
            
            // Campos de contabilidad (según tu lógica de debe/haber/saldo)
            $table->decimal('debe', 12, 2)->default(0);  // [cite: 131]
            $table->decimal('haber', 12, 2)->default(0); // [cite: 136]
            $table->decimal('saldo', 12, 2)->default(0); // [cite: 149]
            
            $table->boolean('activo')->default(true); // Para "eliminar" lógicamente
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};