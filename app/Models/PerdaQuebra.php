<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa um registro de perda ou quebra de matéria-prima.
 *
 * @package App\Models
 * @property int $id
 * @property float $qtde Quantidade perdida ou quebrada
 * @property \Illuminate\Support\Carbon $data Data da ocorrência
 * @property string|null $motivo Motivo da perda ou quebra
 * @property string|null $observacoes Observações adicionais
 * @property int $materia_prima_id ID da matéria-prima relacionada
 * @property int|null $componente_servico_id ID do componente de serviço relacionado
 * @property int|null $ordem_servico_id ID da ordem de serviço relacionada
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class PerdaQuebra extends Model
{
    use HasFactory;

    protected $table = 'perdas_quebras';

    protected $fillable = [
        'qtde',
        'data',
        'motivo',
        'observacoes',
        'materia_prima_id',
        'componente_servico_id',
        'ordem_servico_id',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    /**
     * Obtém a matéria-prima associada à perda/quebra.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function materiaPrima()
    {
        return $this->belongsTo(MateriaPrima::class);
    }

    /**
     * Obtém o componente de serviço associado à perda/quebra.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function componenteServico()
    {
        return $this->belongsTo(ComponenteServico::class);
    }

    /**
     * Obtém a ordem de serviço associada à perda/quebra.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ordemServico()
    {
        return $this->belongsTo(OrdemServico::class);
    }

    /**
     * Obtém o registro de estoque associado à perda/quebra.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function estoque()
    {
        return $this->hasOne(Estoque::class);
    }
}
