<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    use HasFactory;

    protected $table = 'estoque';

    protected $fillable = [
        'qtde',
        'materia_prima_id',
        'entrada_id',
        'saida_id',
        'data_movimentacao',
    ];

    protected $casts = [
        'data_movimentacao' => 'date',
    ];

    public function materiaPrima()
    {
        return $this->belongsTo(MateriaPrima::class);
    }

    public function saida()
    {
        return $this->belongsTo(Saida::class);
    }

    public function entrada()
    {
        return $this->belongsTo(Entrada::class);
    }
}
