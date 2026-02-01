<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipamentoOperacional extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ativo',
        'descricao',
        'custo',
    ];

    protected $table = 'equipamentos_operacionais';

    /**
     * The form fields to be generated
     *
     * @var list<string>
     */
    public function generateFields(String $function)
    {
        $fields = [
            ['name' => 'ativo', 'label' => 'Ativo', 'type' => 'checkbox', 'checked' => $this->ativo ?? true, 'hidden' => $function == 'create' ? 'hidden' : false, 'proportion' => 12],
            ['name' => 'descricao', 'label' => 'Descrição', 'type' => 'text', 'value' => $this->descricao ?? '', 'required' => true, 'proportion' => 8],
            ['name' => 'custo', 'label' => 'Custo Base', 'type' => 'number', 'value' => $this->custo ?? 0, 'required' => true, 'step' => '0.01', 'min' => 0, 'proportion' => 4],
        ];
        return $fields;
    }

    /**
     * Get the component services for this operational service type
     */
    public function componenteServicos()
    {
        return $this->hasMany(ComponenteServico::class, 'equipamento_operacional_id');
    }
}
