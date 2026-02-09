<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acreedores', function (Blueprint $table) {
            $table->id(); // pk [cite: 12, 13]
            $table->string('rif')->unique(); // [cite: 29, 30]
            $table->string('nombre'); // [cite: 34]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acreedores');
    }
};