<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entradas', function (Blueprint $table) {
            $table->id();
            $table->integer('qtde')->nullable(false);
            $table->float('valor_unitario')->nullable(false);
            $table->foreignId('materia_prima_id')->constrained('materias_primas')->nullable(true);
            $table->foreignId('ordem_compra_id')->constrained('ordem_compras')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entradas');
    }
};
