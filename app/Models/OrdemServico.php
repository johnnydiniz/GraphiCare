<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa uma ordem de serviço vinculada a um cliente e opcionalmente a um orçamento.
 *
 * @package App\Models
 * @property int $id
 * @property bool $ativo Indica se a ordem de serviço está ativa
 * @property string $status Status atual da ordem de serviço
 * @property float|null $desconto Percentual de desconto aplicado
 * @property float|null $taxa_lucro Percentual de taxa de lucro
 * @property float|null $custo_final Custo final calculado
 * @property float|null $valor_final Valor final calculado
 * @property \Illuminate\Support\Carbon|null $data_inicio Data de início do serviço
 * @property \Illuminate\Support\Carbon|null $data_fim Data de término do serviço
 * @property string|null $tipo_entrega Tipo de entrega
 * @property \Illuminate\Support\Carbon|null $data_entrega Data de entrega
 * @property string|null $observacoes Observações adicionais
 * @property int $cliente_id ID do cliente associado
 * @property int|null $orcamento_id ID do orçamento associado
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class OrdemServico extends Model
{
    use HasFactory;

    protected $fillable = [
        'ativo',
        'status',
        'desconto',
        'taxa_lucro',
        'custo_final',
        'valor_final',
        'data_inicio',
        'data_fim',
        'tipo_entrega',
        'data_entrega',
        'observacoes',
        'cliente_id',
        'orcamento_id',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'data_entrega' => 'date',
    ];

    /**
     * Obtém o cliente associado à ordem de serviço.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Obtém o orçamento associado à ordem de serviço.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orcamento()
    {
        return $this->belongsTo(Orcamento::class);
    }

    /**
     * Obtém os serviços vinculados à ordem de serviço.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function servicos()
    {
        return $this->belongsToMany(Servico::class, 'servicos_ordem_servicos')
            ->withPivot('qtde')
            ->withTimestamps();
    }

    /**
     * Obtém as saídas de matéria-prima da ordem de serviço.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function saidas()
    {
        return $this->hasMany(Saida::class);
    }

    /**
     * Obtém as perdas e quebras da ordem de serviço.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function perdasQuebras()
    {
        return $this->hasMany(PerdaQuebra::class);
    }

    /**
     * Obtém as faturas da ordem de serviço.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function faturas()
    {
        return $this->hasMany(Fatura::class);
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

        $valorComLucro = $custoFinal * (1 + ($taxaLucro / 100));
        $valorFinal = $valorComLucro * (1 - ($desconto / 100));

        return $valorFinal;
    }
}
