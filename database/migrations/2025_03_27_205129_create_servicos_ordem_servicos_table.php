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
        Schema::create('servicos_ordem_servicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servico_id')->constrained('servicos')->nullable(false);
            $table->foreignId('ordem_servico_id')->constrained('ordem_servicos')->nullable(false);
            $table->integer('qtde')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servico_ordem_servico');
    }
};
