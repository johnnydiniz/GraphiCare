<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ativo',
        'pessoa_id',
        'tipo',
        'limite_credito',
        'taxa_desconto',
    ];

    /**
     * The form fields to be generated
     *
     * @var list<string>
     */
    public function generateFields(String $function)
    {
        $fields = [
            ['name' => 'tipo_cliente', 'label' => 'Tipo', 'type' => 'select', 'options' => ['nao_informado' => 'Não informado', 'final' => 'Final', 'representante' => 'Representante'], 'selected' => $this->tipo ?? 'nao_informado'],
            ['name' => 'limite_credito', 'label' => 'Limite de Crédito', 'type' => 'text', 'value' => $this->limite_credito ?? null, 'mask' => 'currency'],
            ['name' => 'taxa_desconto', 'label' => 'Taxa de Desconto', 'type' => 'text', 'value' => $this->taxa_desconto ?? null, 'mask' => 'percent'],
        ];

        return $fields;
    }

    /**
     * Get the pessoa that owns the cliente
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }
}
