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
            $table->string('cpf_cnpj')->nullable(false);
            $table->string('nome_registro')->nullable(false);
            $table->string('nome_social')->nullable(false);
            $table->date('data_nascimento')->nullable(true);
            $table->enum('escolaridade', ['fundamental', 'medio', 'superior', 'pos_graduacao', 'mestrado', 'doutorado'])->nullable(true);
            $table->boolean('ativo')->default(true)->nullable(false);
            $table->boolean('bloqueado')->default(false)->nullable(false);
            $table->boolean('aceite_termos')->default(false)->nullable(false);
            $table->date('data_aceite')->nullable(true);
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
            $table->foreignId('pessoa_id')->nullable(true)->index();
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
