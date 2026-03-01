<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MateriaPrima extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ativo',
        'tipo_materia_prima_id',
        'descricao',
        'custo_medio',
        'estoque_atual',
        'aviso_estoque',
        'estoque_minimo',
    ];

    protected $table = 'materias_primas';

    /**
     * The form fields to be generated
     *
     * @var list<string>
     */
    public function generateFields(String $function)
    {
        $options = [];
        $tipos = TipoMateriaPrima::all();
        foreach ($tipos as $tipo) {
            $options[$tipo->id] = $tipo->descricao;
        }
        $fields = [
            ['name' => 'ativo', 'label' => 'Ativo', 'type' => 'checkbox', 'checked' => $this->ativo ?? true, 'hidden' => $function == 'create' ? 'hidden' : false, 'proportion' => 12],
            ['name' => 'tipo', 'label' => 'Tipo', 'type' => 'select', 'required' => true, 'options' => $options, 'selected' => $this->tipo_materia_prima_id ?? null, 'action' => 'TipoMateria', 'proportion' => 6],
            ['name' => 'descricao', 'label' => 'Descrição', 'type' => 'text', 'value' => $this->descricao ?? '', 'required' => true, 'proportion' => 6],
            ['name' => 'custo_medio', 'label' => 'Custo Médio', 'type' => 'number', 'value' => $this->custo_medio ?? 0, 'required' => true, 'step' => '0.01', 'proportion' => 4],
            ['name' => 'estoque_atual', 'label' => 'Estoque Atual', 'type' => 'number', 'value' => $this->estoque_atual ?? 0, 'required' => true, 'proportion' => 4],
            ['name' => 'estoque_minimo', 'label' => 'Estoque Mínimo', 'type' => 'number', 'value' => $this->estoque_minimo ?? null, 'proportion' => 4],
            ['name' => 'aviso_estoque', 'label' => 'Ativar aviso de estoque mínimo', 'type' => 'checkbox', 'checked' => $this->aviso_estoque ?? true, 'proportion' => 12],
        ];
        return $fields;
    }

    /**
     * Get the tipoMateriaPrima that owns the MateriaPrima
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipoMateriaPrima()
    {
        return $this->belongsTo(TipoMateriaPrima::class, 'tipo_materia_prima_id');
    }

    /**
     * Verifica se o estoque está abaixo do mínimo
     */
    public function estoqueAbaixoMinimo(): bool
    {
        if (!$this->aviso_estoque || $this->estoque_minimo === null) {
            return false;
        }
        return $this->estoque_atual < $this->estoque_minimo;
    }

    public function perdasQuebras()
    {
        return $this->hasMany(PerdaQuebra::class);
    }
}
