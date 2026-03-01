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
        Schema::create('boletos', function (Blueprint $table) {
            $table->id();
            $table->date('data_emissao')->nullable(false);
            $table->date('data_vencimento')->nullable(false);
            $table->float('valor')->nullable(false);
            $table->string('status')->default('pendente');
            $table->text('observacoes')->nullable();
            $table->foreignId('ordem_compra_id')->constrained('ordem_compras');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boletos');
    }
};
