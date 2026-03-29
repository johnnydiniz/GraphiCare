<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa uma movimentação de estoque de matéria-prima.
 *
 * @package App\Models
 * @property int $id
 * @property float $qtde Quantidade movimentada
 * @property int $materia_prima_id ID da matéria-prima associada
 * @property int|null $entrada_id ID da entrada associada
 * @property int|null $saida_id ID da saída associada
 * @property int|null $perda_quebra_id ID da perda/quebra associada
 * @property string $data_movimentacao Data da movimentação no estoque
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Estoque extends Model
{
    use HasFactory;

    protected $table = 'estoque';

    protected $fillable = [
        'qtde',
        'materia_prima_id',
        'entrada_id',
        'saida_id',
        'perda_quebra_id',
        'data_movimentacao',
    ];

    protected $casts = [
        'data_movimentacao' => 'date',
    ];

    /**
     * Obtém a matéria-prima associada a esta movimentação de estoque.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function materiaPrima()
    {
        return $this->belongsTo(MateriaPrima::class);
    }

    /**
     * Obtém a saída associada a esta movimentação de estoque.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function saida()
    {
        return $this->belongsTo(Saida::class);
    }

    /**
     * Obtém a entrada associada a esta movimentação de estoque.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entrada()
    {
        return $this->belongsTo(Entrada::class);
    }

    /**
     * Obtém a perda/quebra associada a esta movimentação de estoque.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function perdaQuebra()
    {
        return $this->belongsTo(PerdaQuebra::class);
    }
}
