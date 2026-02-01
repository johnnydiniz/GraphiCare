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
            $table->boolean('ativo')->default(true)->nullable(false);
            $table->enum('tipo', ['material', 'equipamento'])->nullable(false);
            $table->string('descricao')->nullable(false);
            $table->foreignId('materia_prima_id')->nullable()->constrained('materias_primas');
            $table->foreignId('equipamento_operacional_id')->nullable()->constrained('equipamentos_operacionais');
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
