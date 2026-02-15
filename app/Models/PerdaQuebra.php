<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerdaQuebra extends Model
{
    use HasFactory;

    protected $table = 'perdas_quebras';

    protected $fillable = [
        'qtde',
        'data',
        'componente_servico_id',
        'ordem_servico_id',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public function componenteServico()
    {
        return $this->belongsTo(ComponenteServico::class);
    }

    public function ordemServico()
    {
        return $this->belongsTo(OrdemServico::class);
    }
}
