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
        Schema::create('ordem_compras', function (Blueprint $table) {
            $table->id();
            $table->boolean('ativo')->default(true);
            $table->string('status')->default('pendente');
            $table->float('valor_total')->default(0);
            $table->string('nota_fiscal')->nullable(true);
            $table->date('data_emissao')->nullable(false);
            $table->date('data_entrega')->nullable(true);
            $table->text('observacoes')->nullable(true);
            $table->foreignId('fornecedor_id')->constrained('fornecedores')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordem_compras');
    }
};
