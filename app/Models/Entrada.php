<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;

    protected $fillable = [
        'qtde',
        'valor_unitario',
        'materia_prima_id',
        'ordem_compra_id',
    ];

    public function materiaPrima()
    {
        return $this->belongsTo(MateriaPrima::class);
    }

    public function ordemCompra()
    {
        return $this->belongsTo(OrdemCompra::class);
    }

    public function estoque()
    {
        return $this->hasOne(Estoque::class);
    }
}
