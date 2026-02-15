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
        Schema::create('ordem_servicos', function (Blueprint $table) {
            $table->id();
            $table->boolean('ativo')->default(true)->nullable(false);
            $table->string('status', 20)->default('pendente');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->float('desconto')->nullable(true);
            $table->float('taxa_lucro')->nullable()->default(0);
            $table->float('custo_final')->nullable(false);
            $table->float('valor_final')->nullable(false);
            $table->date('data_inicio')->nullable(true);
            $table->date('data_fim')->nullable(true);
            $table->enum('tipo_entrega', ['retirada', 'entrega'])->nullable(false);
            $table->date('data_entrega')->nullable(true);
            $table->text('observacoes')->nullable();
            $table->foreignId('orcamento_id')->nullable()->constrained('orcamentos')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordem_servicos');
    }
};
