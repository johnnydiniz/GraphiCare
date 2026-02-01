<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use HasFactory;

    protected $fillable = [
        'ativo',
        'descricao',
    ];

    /**
     * The relationship with ComponenteServico
     */
    public function componenteServico()
    {
        return $this->belongsToMany(ComponenteServico::class, 'servicos_componente_servicos')
            ->withPivot('ordem', 'qtde', 'custo_operacional')
            ->orderBy('pivot_ordem');
    }

    /**
     * Calculate the total estimated cost based on components
     * Custo Base (do componente) * quantidade + Custo Operacional Adicional (da pivot)
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
     * The relationship with OrdemServico
     */
    public function ordemServico()
    {
        return $this->belongsToMany(OrdemServico::class, 'servicos_ordem_servicos');
    }

    /**
     * The relationship with Orcamento
     */
    public function orcamentos()
    {
        return $this->belongsToMany(Orcamento::class, 'servicos_orcamentos')
            ->withPivot('qtde')
            ->withTimestamps();
    }
}
