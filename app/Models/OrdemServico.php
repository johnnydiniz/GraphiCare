<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdemServico extends Model
{
    use HasFactory;

    /**
     * The relationship with Servico
     */
    public function servico()
    {
        return $this->belongsToMany(Servico::class, 'servicos_ordem_servicos');
    }


}
