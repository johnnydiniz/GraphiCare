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
        Schema::create('componente_servicos', function (Blueprint $table) {
            $table->id();
            $table->integer('ordem')->nullable(true);
            $table->enum('tipo', ['material', 'servico'])->nullable(false);
            $table->string('descricao')->nullable(false);
            $table->integer('qtde')->nullable(false);
            $table->foreignId('materia_prima_id')->constrained('materias_primas')->nullable(true);
            $table->float('custo_operacional')->default(0)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('componente_servicos');
    }
};
