<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id(); // pk [cite: 40]
            $table->string('num_compra'); // [cite: 66]
            $table->date('fecha'); // [cite: 90, 91]
            $table->decimal('monto_total', 12, 2); // [cite: 82]
            
            // Relaciones
            $table->foreignId('id_acreedor')->constrained('acreedores'); // [cite: 56]
            $table->foreignId('id_forma_pago')->constrained('formas_pago'); // [cite: 75]
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};