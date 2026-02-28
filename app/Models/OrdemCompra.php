<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class);
    }

    public function entradas()
    {
        return $this->hasMany(Entrada::class);
    }
}
