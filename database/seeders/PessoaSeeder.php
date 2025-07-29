<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class PessoaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pessoas')->insert([
            [
                'tipo' => 'fisica',
                'login' => 'joao.silva',
                'senha' => Hash::make('senha123'),
                'cpf_cnpj' => '12345678901',
                'nome_registro' => 'João da Silva',
                'nome_social' => 'João Silva',
                'data_nascimento' => '1990-05-15',
                'escolaridade' => 'superior',
                'ativo' => true,
                'bloqueado' => false,
                'aceite_termos' => true,
                'data_aceite' => now(),
                'endereco_id' => null, // ou use um ID válido se já houver
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo' => 'juridica',
                'login' => 'empresa.teste',
                'senha' => Hash::make('empresa2024'),
                'cpf_cnpj' => '12345678000199',
                'nome_registro' => 'Empresa Teste LTDA',
                'nome_social' => null,
                'data_nascimento' => null,
                'escolaridade' => 'nao_informado',
                'ativo' => true,
                'bloqueado' => false,
                'aceite_termos' => false,
                'data_aceite' => null,
                'endereco_id' => null,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}