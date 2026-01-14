<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orcamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'desconto',
        'custo_final',
        'valor_final',
        'previsao_inicio',
        'previsao_entrega',
        'validade',
        'cliente_id',
    ];

    public function generateFields(String $function){
        $fields = [
            ['name' => 'desconto', 'label' => 'Desconto', 'type' => 'number', 'value' => $this->desconto ?? 0, 'step' => '0.01'],
            ['name' => 'custo_final', 'label' => 'Custo Final', 'type' => 'number', 'value' => $this->custo_final ?? 0, 'step' => '0.01'],
            ['name' => 'valor_final', 'label' => 'Valor Final', 'type' => 'number', 'value' => $this->valor_final ?? 0, 'step' => '0.01'],
            ['name' => 'previsao_inicio', 'label' => 'Previsão de Início', 'type' => 'date', 'value' => $this->previsao_inicio ?? null],
            ['name' => 'previsao_entrega', 'label' => 'Previsão de Entrega', 'type' => 'date', 'value' => $this->previsao_entrega ?? null],
            ['name' => 'validade', 'label' => 'Validade', 'type' => 'date', 'value' => $this->validade ?? null],
            ['name' => 'cliente_id', 'label' => 'Cliente', 'type' => 'select', 'required' => true, 'options' => Cliente::all()->pluck('pessoa.nome_social', 'id'), 'selected' => $this->cliente_id ?? null, 'action' => 'Cliente'],
        ];
        return $fields;
    }

    /**
     * The relationship with Cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * The relationship with Servico
     */
    public function servico()
    {
        return $this->belongsToMany(Servico::class, 'servicos_orcamentos');
    }
}
