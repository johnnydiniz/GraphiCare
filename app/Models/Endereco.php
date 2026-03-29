<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa um endereço.
 *
 * @package App\Models
 * @property int $id
 * @property string|null $tipo Tipo do endereço
 * @property string|null $logradouro Logradouro do endereço
 * @property string|null $nome_via Nome da via
 * @property string|null $numero Número do endereço
 * @property string|null $complemento Complemento do endereço
 * @property string|null $bairro Bairro do endereço
 * @property string|null $cidade Cidade do endereço
 * @property string|null $estado Estado (UF) do endereço
 * @property string|null $cep CEP do endereço
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Endereco extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'logradouro',
        'nome_via',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
    ];

    /**
     * Gera os campos do formulário para o recurso.
     *
     * @param string $function Tipo de operação do formulário
     * @return array<int, array<string, mixed>> Lista de campos do formulário
     */
    public function generateFields(String $function)
    {
        $fields = [
            ['name' => 'cep', 'label' => 'CEP', 'type' => 'text', 'value' => $this->cep ?? '', 'mask' => 'cep'],
            ['name' => 'logradouro', 'label' => 'Logradouro', 'type' => 'text', 'value' => $this->logradouro ?? ''],
            ['name' => 'numero', 'label' => 'Número', 'type' => 'text', 'value' => $this->numero ?? ''],
            ['name' => 'complemento', 'label' => 'Complemento', 'type' => 'text', 'value' => $this->complemento ?? ''],
            ['name' => 'bairro', 'label' => 'Bairro', 'type' => 'text', 'value' => $this->bairro ?? ''],
            ['name' => 'cidade', 'label' => 'Cidade', 'type' => 'text', 'value' => $this->cidade ?? ''],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'text', 'value' => $this->estado ?? ''],
        ];

        return $fields;
    }

    /**
     * Obtém a pessoa associada a este endereço.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function pessoa()
    {
        return $this->hasOne(Pessoa::class, 'endereco_id');
    }
}
