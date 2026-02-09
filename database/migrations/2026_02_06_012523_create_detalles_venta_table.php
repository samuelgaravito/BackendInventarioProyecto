<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalles_venta', function (Blueprint $table) {
            $table->id(); // pk [cite: 105]
            $table->decimal('cantidad', 12, 2); // [cite: 126]
            $table->decimal('precio_unitario', 12, 2); // [cite: 142]
            $table->decimal('importe', 12, 2); // [cite: 142]
            
            // Relaciones
            $table->foreignId('id_venta')->constrained('ventas')->onDelete('cascade'); // [cite: 122]
            $table->foreignId('id_producto')->constrained('productos'); // [cite: 123]
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles_venta');
    }
};