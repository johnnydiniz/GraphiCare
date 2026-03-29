<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa uma saída de material/componente vinculada a uma ordem de serviço.
 *
 * @package App\Models
 * @property int $id
 * @property int $qtde Quantidade de saída
 * @property \Illuminate\Support\Carbon|null $data_inicio Data de início da saída
 * @property \Illuminate\Support\Carbon|null $data_termino Data de término da saída
 * @property int $componente_servico_id ID do componente de serviço relacionado
 * @property int $ordem_servico_id ID da ordem de serviço relacionada
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Saida extends Model
{
    use HasFactory;

    protected $fillable = [
        'qtde',
        'data_inicio',
        'data_termino',
        'componente_servico_id',
        'ordem_servico_id',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_termino' => 'date',
    ];

    /**
     * Obtém o componente de serviço associado a esta saída.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function componenteServico()
    {
        return $this->belongsTo(ComponenteServico::class);
    }

    /**
     * Obtém a ordem de serviço associada a esta saída.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ordemServico()
    {
        return $this->belongsTo(OrdemServico::class);
    }

    /**
     * Obtém o registro de estoque associado a esta saída.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function estoque()
    {
        return $this->hasOne(Estoque::class);
    }
}
