<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa um contato de uma pessoa (telefone, email, etc.).
 *
 * @package App\Models
 * @property int $id
 * @property int $tipo_contato_id ID do tipo de contato
 * @property string $contato Valor do contato (número, email, etc.)
 * @property int $pessoa_id ID da pessoa associada ao contato
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Contato extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_contato_id',
        'contato',
        'pessoa_id',
    ];

    /**
     * Gera os campos do formulário para o recurso.
     *
     * @param string $function Tipo de operação do formulário
     * @return array<int, array<string, mixed>> Lista de campos do formulário
     */
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

    /**
     * Obtém a pessoa associada a este contato.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }

    /**
     * Obtém o tipo de contato associado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipoContato()
    {
        return $this->belongsTo(TipoContato::class);
    }
}
