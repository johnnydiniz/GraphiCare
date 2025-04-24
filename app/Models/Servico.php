<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ativo',
        'descricao',
        'qtde',
        'valor',
        'custo',
        'tempo_realizacao',
    ];

    /**
     * The form fields to be generated
     *
     * @var list<string>
     */
    public function generateFields(String $function){
        $fields = [
            ['name' => 'ativo', 'label' => 'Ativo', 'type' => 'checkbox', 'checked' => $this->ativo ?? true, 'hidden' => $function == 'create' ? 'hidden' : false],
            ['name' => 'descricao', 'label' => 'Descrição', 'type' => 'text', 'value' => $this->descricao ?? '', 'required' => true],
            ['name' => 'qtde', 'label' => 'Quantidade', 'type' => 'number', 'value' => $this->qtde ?? 0, 'required' => true],
            ['name' => 'valor', 'label' => 'Valor', 'type' => 'number', 'value' => $this->valor ?? 0, 'required' => true, 'step' => '0.01'],
            ['name' => 'custo', 'label' => 'Custo', 'type' => 'number', 'value' => $this->custo ?? 0, 'required' => true, 'step' => '0.01'],
            ['name' => 'tempo_realizacao', 'label' => 'Tempo de Realização', 'type' => 'time', 'value' => $this->tempo_realizacao ?? '', 'required' => true],
        ];
        return $fields;
    }

    /**
     * The relationship with ComponenteServico
     */
    public function componenteServico(){
        return $this->belongsToMany(ComponenteServico::class, 'servicos_componente_servicos');
    }

    /**
     * The relationship with OrdemServico
     */
    public function ordemServico(){
        return $this->belongsToMany(OrdemServico::class, 'servicos_ordem_servicos');
    }

    /**
     * The relationship with Orcamento
     */
    public function orcamento(){
        return $this->belongsToMany(Orcamento::class, 'servicos_orcamentos');
    }
}
