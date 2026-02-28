<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contato extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_contato_id',
        'contato',
        'pessoa_id',
    ];

    public function generateFields(String $function)
    {
        $options = [];
        $tipos = TipoContato::all();
        foreach ($tipos as $tipo) {
            $options[$tipo->id] = $tipo->descricao;
        }

        $fields = [
            ['name' => 'tipo_contato', 'label' => 'Tipo de contato', 'type' => 'select', 'options' => $options, 'selected' => $this->tipo_contato_id ?? null, 'action' => 'TipoContato'],
            ['name' => 'contato', 'label' => 'Contato', 'type' => 'text', 'value' => $this->contato ?? null]
        ];

        return $fields;
    }

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function tipoContato()
    {
        return $this->belongsTo(TipoContato::class);
    }
}
