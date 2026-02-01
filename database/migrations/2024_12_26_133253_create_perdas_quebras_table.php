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
            $table->foreignId('componente_servico_id')->nullable(true)->constrained('componente_servicos');
            $table->foreignId('ordem_servico_id')->nullable(true)->constrained('ordem_servicos');
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
