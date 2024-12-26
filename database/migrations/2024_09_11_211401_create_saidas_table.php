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
        Schema::create('saidas', function (Blueprint $table) {
            $table->id();
            $table->integer('qtde')->nullable(false);
            $table->date('data_inicio')->nullable(true);
            $table->date('data_termino')->nullable(true);
            $table->foreignId('componente_servico_id')->constrained('componente_servicos')->nullable(false);
            $table->foreignId('ordem_servico_id')->constrained('ordem_servicos')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saidas');
    }
};
