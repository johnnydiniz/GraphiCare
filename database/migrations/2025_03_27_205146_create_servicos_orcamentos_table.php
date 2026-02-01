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
        Schema::create('servicos_orcamentos', function (Blueprint $table) {
            $table->id();
            $table->integer('qtde')->default(1)->nullable(false);
            $table->foreignId('servico_id')->constrained('servicos')->nullable(false);
            $table->foreignId('orcamento_id')->constrained('orcamentos')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servico_orcamento');
    }
};
