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
        Schema::create('orcamentos', function (Blueprint $table) {
            $table->id();
            $table->float('desconto')->nullable(true)->default(0);
            $table->float('taxa_lucro')->nullable(true)->default(0);
            $table->float('custo_final')->nullable(false)->default(0);
            $table->float('valor_final')->nullable(false)->default(0);
            $table->date('previsao_inicio')->nullable(true);
            $table->date('previsao_entrega')->nullable(true);
            $table->date('validade')->nullable(true);
            $table->text('observacoes')->nullable(true);
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->boolean('ativo')->default(true)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orcamentos');
    }
};
