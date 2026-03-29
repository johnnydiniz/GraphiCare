<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa um equipamento operacional utilizado em serviços.
 *
 * @package App\Models
 * @property int $id
 * @property bool $ativo Indica se o equipamento está ativo
 * @property string $descricao Descrição do equipamento operacional
 * @property float $custo Custo base do equipamento
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class EquipamentoOperacional extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ativo',
        'descricao',
        'custo',
    ];

    protected $table = 'equipamentos_operacionais';

    /**
     * Gera os campos do formulário para o recurso.
     *
     * @param string $function Tipo de operação do formulário
     * @return array<int, array<string, mixed>> Lista de campos do formulário
     */
    public function generateFields(String $function)
    {
        $fields = [
            ['name' => 'ativo', 'label' => 'Ativo', 'type' => 'checkbox', 'checked' => $this->ativo ?? true, 'hidden' => $function == 'create' ? 'hidden' : false, 'proportion' => 12],
            ['name' => 'descricao', 'label' => 'Descrição', 'type' => 'text', 'value' => $this->descricao ?? '', 'required' => true, 'proportion' => 8],
            ['name' => 'custo', 'label' => 'Custo Base', 'type' => 'number', 'value' => $this->custo ?? 0, 'required' => true, 'step' => '0.01', 'min' => 0, 'proportion' => 4],
        ];
        return $fields;
    }

    /**
     * Obtém os componentes de serviço associados a este equipamento operacional.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function componenteServicos()
    {
        return $this->hasMany(ComponenteServico::class, 'equipamento_operacional_id');
    }
}
