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
            $table->float('valor_total')->nullable(false);
            $table->string('nota_fiscal')->nullable(true);
            $table->date('data_emissao')->nullable(false);
            $table->date('data_entrega')->nullable(false);
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
