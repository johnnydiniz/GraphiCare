<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa um orçamento de serviços para um cliente.
 *
 * @package App\Models
 * @property int $id
 * @property bool $ativo Indica se o orçamento está ativo
 * @property float|null $desconto Percentual de desconto aplicado
 * @property float|null $taxa_lucro Percentual de taxa de lucro
 * @property float|null $custo_final Custo final calculado
 * @property float|null $valor_final Valor final calculado
 * @property \Illuminate\Support\Carbon|null $previsao_inicio Data de previsão de início
 * @property \Illuminate\Support\Carbon|null $previsao_entrega Data de previsão de entrega
 * @property \Illuminate\Support\Carbon|null $validade Data de validade do orçamento
 * @property string|null $observacoes Observações adicionais
 * @property int $cliente_id ID do cliente associado
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Orcamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'ativo',
        'desconto',
        'taxa_lucro',
        'custo_final',
        'valor_final',
        'previsao_inicio',
        'previsao_entrega',
        'validade',
        'observacoes',
        'cliente_id',
    ];

    protected $casts = [
        'previsao_inicio' => 'date',
        'previsao_entrega' => 'date',
        'validade' => 'date',
    ];

    /**
     * Obtém o cliente associado ao orçamento.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Obtém as ordens de serviço geradas a partir do orçamento.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ordensServico()
    {
        return $this->hasMany(OrdemServico::class);
    }

    /**
     * Obtém os serviços vinculados ao orçamento.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function servicos()
    {
        return $this->belongsToMany(Servico::class, 'servicos_orcamentos')
            ->withPivot('qtde')
            ->withTimestamps();
    }

    /**
     * Calcula o custo final com base nos serviços e suas quantidades.
     *
     * @return float
     */
    public function calcularCustoFinal()
    {
        $total = 0;
        foreach ($this->servicos as $servico) {
            $qtde = $servico->pivot->qtde ?? 1;
            $custoServico = $servico->custo_estimado ?? 0;
            $total += $custoServico * $qtde;
        }
        return $total;
    }

    /**
     * Calcula o valor final (custo + lucro - desconto).
     *
     * @return float
     */
    public function calcularValorFinal()
    {
        $custoFinal = $this->custo_final ?? $this->calcularCustoFinal();
        $taxaLucro = $this->taxa_lucro ?? 0;
        $desconto = $this->desconto ?? 0;

        // valor_final = custo_final * (1 + taxa_lucro/100) * (1 - desconto/100)
        $valorComLucro = $custoFinal * (1 + ($taxaLucro / 100));
        $valorFinal = $valorComLucro * (1 - ($desconto / 100));

        return $valorFinal;
    }
}
