<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * The relationship with Cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * The relationship with Orcamento
     */
    public function orcamento()
    {
        return $this->belongsTo(Orcamento::class);
    }

    /**
     * The relationship with Servico
     */
    public function servicos()
    {
        return $this->belongsToMany(Servico::class, 'servicos_ordem_servicos')
            ->withPivot('qtde')
            ->withTimestamps();
    }

    /**
     * The relationship with Saida
     */
    public function saidas()
    {
        return $this->hasMany(Saida::class);
    }

    /**
     * The relationship with PerdaQuebra
     */
    public function perdasQuebras()
    {
        return $this->hasMany(PerdaQuebra::class);
    }

    public function faturas()
    {
        return $this->hasMany(Fatura::class);
    }

    /**
     * Calculate the total cost based on services and their quantities
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
     * Calculate the final value (cost + profit - discount)
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
