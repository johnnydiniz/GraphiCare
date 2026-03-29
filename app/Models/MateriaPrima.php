<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa uma matéria-prima utilizada na produção.
 *
 * @package App\Models
 * @property int $id
 * @property bool $ativo Indica se a matéria-prima está ativa
 * @property int $tipo_materia_prima_id ID do tipo de matéria-prima
 * @property string $descricao Descrição da matéria-prima
 * @property float $custo_medio Custo médio unitário
 * @property float $estoque_atual Quantidade atual em estoque
 * @property bool $aviso_estoque Indica se o aviso de estoque mínimo está ativado
 * @property float|null $estoque_minimo Quantidade mínima de estoque
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class MateriaPrima extends Model
{
    use HasFactory;
    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ativo',
        'tipo_materia_prima_id',
        'descricao',
        'custo_medio',
        'estoque_atual',
        'aviso_estoque',
        'estoque_minimo',
    ];

    protected $table = 'materias_primas';

    /**
     * Gera os campos do formulário para o recurso.
     *
     * @param string $function Tipo de operação do formulário
     * @return array<int, array<string, mixed>> Lista de campos do formulário
     */
    public function generateFields(String $function)
    {
        $options = [];
        $tipos = TipoMateriaPrima::all();
        foreach ($tipos as $tipo) {
            $options[$tipo->id] = $tipo->descricao;
        }
        $fields = [
            ['name' => 'ativo', 'label' => 'Ativo', 'type' => 'checkbox', 'checked' => $this->ativo ?? true, 'hidden' => $function == 'create' ? 'hidden' : false, 'proportion' => 12],
            ['name' => 'tipo', 'label' => 'Tipo', 'type' => 'select', 'required' => true, 'options' => $options, 'selected' => $this->tipo_materia_prima_id ?? null, 'action' => 'TipoMateria', 'proportion' => 6],
            ['name' => 'descricao', 'label' => 'Descrição', 'type' => 'text', 'value' => $this->descricao ?? '', 'required' => true, 'proportion' => 6],
            ['name' => 'custo_medio', 'label' => 'Custo Médio', 'type' => 'number', 'value' => $this->custo_medio ?? 0, 'required' => true, 'step' => '0.01', 'proportion' => 4],
            ['name' => 'estoque_atual', 'label' => 'Estoque Atual', 'type' => 'number', 'value' => $this->estoque_atual ?? 0, 'required' => true, 'proportion' => 4],
            ['name' => 'estoque_minimo', 'label' => 'Estoque Mínimo', 'type' => 'number', 'value' => $this->estoque_minimo ?? null, 'proportion' => 4],
            ['name' => 'aviso_estoque', 'label' => 'Ativar aviso de estoque mínimo', 'type' => 'checkbox', 'checked' => $this->aviso_estoque ?? true, 'proportion' => 12],
        ];
        return $fields;
    }

    /**
     * Obtém o tipo de matéria-prima associado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipoMateriaPrima()
    {
        return $this->belongsTo(TipoMateriaPrima::class, 'tipo_materia_prima_id');
    }

    /**
     * Verifica se o estoque está abaixo do mínimo.
     *
     * @return bool
     */
    public function estoqueAbaixoMinimo(): bool
    {
        if (!$this->aviso_estoque || $this->estoque_minimo === null) {
            return false;
        }
        return $this->estoque_atual < $this->estoque_minimo;
    }

    /**
     * Obtém as perdas e quebras associadas à matéria-prima.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function perdasQuebras()
    {
        return $this->hasMany(PerdaQuebra::class);
    }
}
