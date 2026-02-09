<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalles_compra', function (Blueprint $table) {
            $table->id(); // pk [cite: 106]
            $table->decimal('cantidad', 12, 2); // [cite: 126]
            $table->decimal('precio_unitario', 12, 2); // [cite: 141]
            $table->decimal('importe', 12, 2); // [cite: 141]
            
            // Relaciones
            $table->foreignId('id_compra')->constrained('compras')->onDelete('cascade'); // [cite: 114]
            $table->foreignId('id_producto')->constrained('productos'); // [cite: 130]
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles_compra');
    }
};