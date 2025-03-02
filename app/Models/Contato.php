<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contato extends Model
{
    use HasFactory;

    /**
     * The form fields to be generated
     *
     * @var list<string>
     */
    public function generateFields(String $function)
    {

        $options = [];
        $tipos = TipoContato::all();
        foreach ($tipos as $tipo) {
            $options[$tipo->id] = $tipo->descricao;
        }

        $fields = [
            ['name' => 'tipo_contato[]', 'label' => 'Tipo de contato', 'type' => 'select', 'options' => $options, 'selected' => $this->tipo_contato ?? null, 'action' => 'TipoContato'],
            ['name' => 'contato[]', 'label' => 'Contato', 'type' => 'text', 'value' => $this->contato ?? null]
        ];

        return $fields;
    }

}
