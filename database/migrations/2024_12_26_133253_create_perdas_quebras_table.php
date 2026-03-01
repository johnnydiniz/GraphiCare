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
        Schema::create('perdas_quebras', function (Blueprint $table) {
            $table->id();
            $table->integer('qtde')->nullable(false);
            $table->date('data')->nullable(false);
            $table->string('motivo')->nullable(false);
            $table->text('observacoes')->nullable();
            $table->foreignId('materia_prima_id')->constrained('materias_primas')->nullable(false);
            $table->foreignId('componente_servico_id')->nullable()->constrained('componente_servicos');
            $table->foreignId('ordem_servico_id')->nullable()->constrained('ordem_servicos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perdas_quebras');
    }
};
