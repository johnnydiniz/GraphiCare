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
        Schema::create('estoque', function (Blueprint $table) {
            $table->id();
            $table->float('qtde')->nullable(false);
            $table->foreignId('materia_prima_id')->constrained('materias_primas')->nullable(false);
            $table->foreignId('entrada_id')->nullable()->constrained('entradas');
            $table->foreignId('saida_id')->nullable()->constrained('saidas');
            $table->date('data_movimentacao')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estoques');
    }
};
