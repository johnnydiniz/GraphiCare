<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa um serviço oferecido pela empresa.
 *
 * @package App\Models
 * @property int $id
 * @property bool $ativo Indica se o serviço está ativo
 * @property string $descricao Descrição do serviço
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read float $custo_estimado Custo estimado calculado a partir dos componentes
 */
class Servico extends Model
{
    use HasFactory;

    protected $fillable = [
        'ativo',
        'descricao',
    ];

    /**
     * Obtém os componentes de serviço associados a este serviço.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function componenteServico()
    {
        return $this->belongsToMany(ComponenteServico::class, 'servicos_componente_servicos')
            ->withPivot('ordem', 'qtde', 'custo_operacional')
            ->orderBy('pivot_ordem');
    }

    /**
     * Calcula o custo estimado total com base nos componentes.
     *
     * Custo Base (do componente) * quantidade + Custo Operacional Adicional (da pivot).
     *
     * @return float
     */
    public function getCustoEstimadoAttribute()
    {
        $total = 0;
        foreach ($this->componenteServico as $componente) {
            $qtde = $componente->pivot->qtde ?? 1;
            $custoBase = $componente->custo_operacional ?? 0; // Custo base do componente
            $custoOperacionalAdicional = $componente->pivot->custo_operacional ?? 0; // Custo adicional por serviço
            $total += ($custoBase * $qtde) + $custoOperacionalAdicional;
        }
        return $total;
    }

    /**
     * Obtém as ordens de serviço associadas a este serviço.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function ordemServico()
    {
        return $this->belongsToMany(OrdemServico::class, 'servicos_ordem_servicos');
    }

    /**
     * Obtém os orçamentos associados a este serviço.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orcamentos()
    {
        return $this->belongsToMany(Orcamento::class, 'servicos_orcamentos')
            ->withPivot('qtde')
            ->withTimestamps();
    }
}
