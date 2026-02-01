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
        Schema::create('servicos_componente_servicos', function (Blueprint $table) {
            $table->id();
            $table->integer('ordem')->nullable(true);
            $table->integer('qtde')->default(1)->nullable(false);
            $table->float('custo_operacional')->default(0)->nullable(false);
            $table->foreignId('servico_id')->constrained('servicos')->nullable(false);
            $table->foreignId('componente_servico_id')->constrained('componente_servicos')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servico_componente_servico');
    }
};
