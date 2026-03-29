<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa uma entrada de matéria-prima no estoque.
 *
 * @package App\Models
 * @property int $id
 * @property float $qtde Quantidade de itens na entrada
 * @property float $valor_unitario Valor unitário do item
 * @property int $materia_prima_id ID da matéria-prima associada
 * @property int $ordem_compra_id ID da ordem de compra associada
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Entrada extends Model
{
    use HasFactory;

    protected $fillable = [
        'qtde',
        'valor_unitario',
        'materia_prima_id',
        'ordem_compra_id',
    ];

    /**
     * Obtém a matéria-prima associada a esta entrada.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function materiaPrima()
    {
        return $this->belongsTo(MateriaPrima::class);
    }

    /**
     * Obtém a ordem de compra associada a esta entrada.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ordemCompra()
    {
        return $this->belongsTo(OrdemCompra::class);
    }

    /**
     * Obtém o registro de estoque gerado por esta entrada.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function estoque()
    {
        return $this->hasOne(Estoque::class);
    }
}
