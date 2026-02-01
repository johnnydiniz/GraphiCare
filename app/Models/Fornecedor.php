<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    use HasFactory;

    protected $table = 'fornecedores';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ativo',
        'pessoa_id',
        'tipo',
    ];

    /**
     * The form fields to be generated
     *
     * @var list<string>
     */
    public function generateFields(String $function)
    {
        $fields = [
            ['name' => 'tipo_fornecedor', 'label' => 'Tipo de Fornecedor', 'type' => 'select', 'options' => ['nao_informado' => 'Não informado', 'produtos' => 'Produtos', 'servicos' => 'Serviços', 'produtos_servicos' => 'Produtos e Serviços'], 'selected' => $this->tipo ?? 'nao_informado'],
        ];

        return $fields;
    }

    /**
     * Get the pessoa that owns the Fornecedor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }
}
