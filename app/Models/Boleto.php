<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa um boleto de pagamento vinculado a uma ordem de compra.
 *
 * @package App\Models
 * @property int $id
 * @property string $data_emissao Data de emissão do boleto
 * @property string $data_vencimento Data de vencimento do boleto
 * @property float $valor Valor do boleto
 * @property string $status Status atual do boleto
 * @property string|null $observacoes Observações adicionais sobre o boleto
 * @property int $ordem_compra_id ID da ordem de compra associada
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Boleto extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_emissao',
        'data_vencimento',
        'valor',
        'status',
        'observacoes',
        'ordem_compra_id',
    ];

    protected $casts = [
        'data_emissao' => 'date',
        'data_vencimento' => 'date',
    ];

    /**
     * Obtém a ordem de compra associada ao boleto.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ordemCompra()
    {
        return $this->belongsTo(OrdemCompra::class);
    }
}
