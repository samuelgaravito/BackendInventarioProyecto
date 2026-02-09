<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nominas', function (Blueprint $table) {
            $table->id(); // pk [cite: 43]
            $table->integer('dias_trabajados'); // [cite: 69]
            $table->date('fecha'); // [cite: 145]
            
            // Aportes y Deducciones
            $table->decimal('ivss', 12, 2); // [cite: 79]
            $table->decimal('faov', 12, 2); // [cite: 144]
            $table->decimal('paro_forzoso', 12, 2); // [cite: 145]
            $table->decimal('caja_ahorro', 12, 2); // [cite: 145]
            $table->decimal('cesta_ticket_dia', 12, 2); // [cite: 145]
            $table->decimal('cesta_ticket_recibir', 12, 2); // [cite: 145]
            $table->decimal('salario_quincenal', 12, 2); // [cite: 145]
            $table->decimal('salario_mensual', 12, 2); // [cite: 145]
            
            // Relación
            $table->foreignId('id_empleado')->constrained('empleados'); // [cite: 69]
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nominas');
    }
};