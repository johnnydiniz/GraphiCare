<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function ordemCompra()
    {
        return $this->belongsTo(OrdemCompra::class);
    }
}
