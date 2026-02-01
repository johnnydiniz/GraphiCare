<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponenteServico extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ativo',
        'tipo',
        'descricao',
        'materia_prima_id',
        'equipamento_operacional_id',
        'custo_operacional',
    ];

    protected $table = 'componente_servicos';

    /**
     * The form fields to be generated
     *
     * @var list<string>
     */
    public function generateFields(String $function)
    {
        $materiaPrimaOptions = [];
        $materiasPrimas = MateriaPrima::with('tipoMateriaPrima')->get();
        foreach ($materiasPrimas as $mp) {
            $materiaPrimaOptions[$mp->id] = $mp->tipoMateriaPrima->descricao . " " . $mp->descricao;
        }

        $equipamentoOptions = [];
        $equipamentos = EquipamentoOperacional::all();
        foreach ($equipamentos as $ts) {
            $equipamentoOptions[$ts->id] = $ts->descricao;
        }

        $fields = [
            ['name' => 'ativo', 'label' => 'Ativo', 'type' => 'checkbox', 'checked' => $this->ativo ?? true, 'hidden' => $function == 'create' ? 'hidden' : false],
            ['name' => 'tipo', 'label' => 'Tipo', 'type' => 'select', 'options' => ['material' => 'Material', 'servico' => 'Serviço'], 'selected' => $this->tipo ?? 'material', 'hidden' => false],
            ['name' => 'descricao', 'label' => 'Descrição', 'type' => 'text', 'value' => $this->descricao ?? '', 'hidden' => false],
            ['name' => 'materia_prima_id', 'label' => 'Matéria-prima', 'type' => 'select', 'options' => $materiaPrimaOptions, 'selected' => $this->materia_prima_id ?? '', 'hidden' => false],
            ['name' => 'equipamento_operacional_id', 'label' => 'Tipo de Serviço Operacional', 'type' => 'select', 'options' => $equipamentoOptions, 'selected' => $this->equipamento_operacional_id ?? '', 'hidden' => false]
        ];
        return $fields;
    }

    /**
     * Get the MateriaPrima that owns the ComponenteServico
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function materiaprima()
    {
        return $this->belongsTo(MateriaPrima::class, 'materia_prima_id');
    }

    /**
     * Get the EquipamentoOperacional that owns the ComponenteServico
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function equipamentoOperacional()
    {
        return $this->belongsTo(EquipamentoOperacional::class, 'equipamento_operacional_id');
    }

    /**
     * The relationship with Servico
     */
    public function servico()
    {
        return $this->belongsToMany(Servico::class, 'servicos_componente_servicos')
            ->withPivot('ordem', 'qtde', 'custo_operacional');
    }

}
