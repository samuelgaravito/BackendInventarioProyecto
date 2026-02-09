<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id(); // [cite: 59]
            $table->string('nombre'); // [cite: 71]
            $table->string('apellido'); // [cite: 146]
            $table->string('cedula')->unique(); // [cite: 147]
            
            // Relación con cargos (Nivel 1)
            $table->foreignId('cargo_id') // [cite: 148]
                  ->constrained('cargos')
                  ->onDelete('restrict'); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};