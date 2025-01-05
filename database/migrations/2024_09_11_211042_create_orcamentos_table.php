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
            $table->float('desconto')->nullable(true);
            $table->float('custo_final')->nullable(false);
            $table->float('valor_final')->nullable(false);
            $table->date('previsao_inicio')->nullable(false);
            $table->date('previsao_entrega')->nullable(false);
            $table->date('validade')->nullable(false);
            $table->foreignId('cliente_id')->constrained('clientes');
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
