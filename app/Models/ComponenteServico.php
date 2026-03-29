<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa um componente de serviço, podendo ser material ou serviço.
 *
 * @package App\Models
 * @property int $id
 * @property bool $ativo Indica se o componente está ativo
 * @property string $tipo Tipo do componente (material ou serviço)
 * @property string $descricao Descrição do componente de serviço
 * @property int|null $materia_prima_id ID da matéria-prima associada
 * @property int|null $equipamento_operacional_id ID do equipamento operacional associado
 * @property float|null $custo_operacional Custo operacional do componente
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ComponenteServico extends Model
{
    use HasFactory;
    /**
     * Os atributos que podem ser atribuídos em massa.
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
     * Gera os campos do formulário para o recurso.
     *
     * @param string $function Tipo de operação do formulário
     * @return array<int, array<string, mixed>> Lista de campos do formulário
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
     * Obtém a matéria-prima associada ao componente de serviço.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function materiaprima()
    {
        return $this->belongsTo(MateriaPrima::class, 'materia_prima_id');
    }

    /**
     * Obtém o equipamento operacional associado ao componente de serviço.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function equipamentoOperacional()
    {
        return $this->belongsTo(EquipamentoOperacional::class, 'equipamento_operacional_id');
    }

    /**
     * Obtém os serviços associados a este componente de serviço.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function servico()
    {
        return $this->belongsToMany(Servico::class, 'servicos_componente_servicos')
            ->withPivot('ordem', 'qtde', 'custo_operacional');
    }

}
