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
        Schema::create('materias_primas', function (Blueprint $table) {
            $table->id();
            $table->string('descricao')->nullable(false)->unique();
            $table->float('custo_medio')->nullable(false);
            $table->float('estoque_atual')->nullable(false);
            $table->float('estoque_minimo')->nullable(true);
            $table->boolean('aviso_estoque')->nullable(true);
            $table->boolean('ativo')->nullable(false);
            $table->foreignId('tipo_materia_prima_id')->constrained('tipo_materias_primas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materias_primas');
    }
};
