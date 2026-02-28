<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function pessoa()
    {
        return $this->hasOne(Pessoa::class, 'endereco_id');
    }
}
