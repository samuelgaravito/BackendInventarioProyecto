<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id(); // pk [cite: 156]
            $table->integer('cantidad'); // [cite: 167]
            $table->decimal('costo_unitario', 12, 2); // [cite: 170]
            $table->date('fecha'); // [cite: 171]
            
            // Relaciones
            $table->foreignId('id_producto')->constrained('productos'); // [cite: 158]
            $table->foreignId('id_tipo_movimiento')->constrained('tipos_movimiento'); // [cite: 158]
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};