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
        'qtde',
        'materia_prima_id',
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

        $options = [];
        $tipos = MateriaPrima::with('tipoMateriaPrima')->get();
        foreach ($tipos as $tipo) {
            $options[$tipo->id] = $tipo->tipoMateriaPrima->descricao . " " .  $tipo->descricao;
        }

        $fields = [
            ['name' => 'ativo', 'label' => 'Ativo', 'type' => 'checkbox', 'checked' => $this->ativo ?? true, 'hidden' => $function == 'create' ? 'hidden' : false],
            ['name' => 'tipo', 'label' => 'Tipo', 'type' => 'select', 'options' => ['material' => 'Material', 'servico' => 'Serviço'], 'selected' => $this->tipo ?? 'material', 'hidden' => false],
            ['name' => 'descricao', 'label' => 'Descrição', 'type' => 'text', 'value' => $this->descricao ?? '', 'hidden' => false],
            ['name' => 'qtde', 'label' => 'Quantidade', 'type' => 'number', 'value' => $this->qtde ?? '', 'hidden' => false],
            ['name' => 'materia_prima_id', 'label' => 'Matéria-prima', 'type' => 'select', 'options' => $options, 'selected' => $this->materia_prima_id ?? '', 'hidden' => false],
            ['name' => 'custo_operacional', 'label' => 'Custo operacional', 'type' => 'number', 'value' => $this->custo_operacional ?? '', 'hidden' => false, 'step' => '0.01'],
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
     * The relationship with Servico
     */
    public function servico()
    {
        return $this->belongsToMany(Servico::class, 'servicos_componente_servicos');
    }

}
