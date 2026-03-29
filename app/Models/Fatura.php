<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa uma fatura vinculada a uma ordem de serviço.
 *
 * @package App\Models
 * @property int $id
 * @property \Illuminate\Support\Carbon $data_emissao Data de emissão da fatura
 * @property \Illuminate\Support\Carbon $data_vencimento Data de vencimento da fatura
 * @property float $valor Valor da fatura
 * @property string $status Status atual da fatura
 * @property string|null $observacoes Observações adicionais
 * @property int $ordem_servico_id ID da ordem de serviço relacionada
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Fatura extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_emissao',
        'data_vencimento',
        'valor',
        'status',
        'observacoes',
        'ordem_servico_id',
    ];

    protected $casts = [
        'data_emissao' => 'date',
        'data_vencimento' => 'date',
    ];

    /**
     * Obtém a ordem de serviço associada à fatura.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ordemServico()
    {
        return $this->belongsTo(OrdemServico::class);
    }
}
