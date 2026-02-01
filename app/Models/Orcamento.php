<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * The relationship with Cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * The relationship with Servico
     */
    public function servicos()
    {
        return $this->belongsToMany(Servico::class, 'servicos_orcamentos')
            ->withPivot('qtde')
            ->withTimestamps();
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

        // valor_final = custo_final * (1 + taxa_lucro/100) * (1 - desconto/100)
        $valorComLucro = $custoFinal * (1 + ($taxaLucro / 100));
        $valorFinal = $valorComLucro * (1 - ($desconto / 100));

        return $valorFinal;
    }
}
