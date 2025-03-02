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
        Schema::create('pessoas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['fisica', 'juridica'])->nullable(false);
            $table->string('login')->nullable(false);
            $table->string('senha')->nullable(false);
            $table->string('cpf_cnpj')->nullable(true);
            $table->string('nome_registro')->nullable(false);
            $table->string('nome_social')->nullable(true);
            $table->date('data_nascimento')->nullable(true);
            $table->enum('escolaridade', ['nao_informado','fundamental', 'medio', 'superior', 'pos_graduacao', 'mestrado', 'doutorado'])->nullable(false);
            $table->boolean('ativo')->default(true)->nullable(false);
            $table->boolean('bloqueado')->default(false)->nullable(false);
            $table->boolean('aceite_termos')->default(false)->nullable(false);
            $table->date('data_aceite')->nullable(true);
            $table->foreignId('endereco_id')->nullable(true)->constrained('enderecos');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('login')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->constrained('pessoas')->nullable(true)->index();
            $table->string('ip_address', 45)->nullable(true);
            $table->text('user_agent')->nullable(true);
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoas');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
