<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa uma ordem de compra de matérias-primas junto a fornecedores.
 *
 * @package App\Models
 * @property int $id
 * @property bool $ativo Indica se a ordem de compra está ativa
 * @property string $status Status atual da ordem de compra
 * @property float $valor_total Valor total da ordem de compra
 * @property string|null $nota_fiscal Número da nota fiscal
 * @property \Illuminate\Support\Carbon|null $data_emissao Data de emissão da ordem
 * @property \Illuminate\Support\Carbon|null $data_entrega Data de entrega prevista ou realizada
 * @property string|null $observacoes Observações adicionais
 * @property int $fornecedor_id ID do fornecedor associado
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class OrdemCompra extends Model
{
    use HasFactory;

    protected $fillable = [
        'ativo',
        'status',
        'valor_total',
        'nota_fiscal',
        'data_emissao',
        'data_entrega',
        'observacoes',
        'fornecedor_id',
    ];

    protected $casts = [
        'data_emissao' => 'date',
        'data_entrega' => 'date',
    ];

    /**
     * Obtém o fornecedor associado à ordem de compra.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class);
    }

    /**
     * Obtém as entradas de matéria-prima da ordem de compra.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entradas()
    {
        return $this->hasMany(Entrada::class);
    }

    /**
     * Obtém os boletos associados à ordem de compra.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function boletos()
    {
        return $this->hasMany(Boleto::class);
    }
}
