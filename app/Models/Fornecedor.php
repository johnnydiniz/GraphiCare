<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa um fornecedor de produtos e/ou serviços.
 *
 * @package App\Models
 * @property int $id
 * @property bool $ativo Indica se o fornecedor está ativo
 * @property int $pessoa_id ID da pessoa associada ao fornecedor
 * @property string $tipo Tipo de fornecedor (produtos, serviços, produtos e serviços)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Fornecedor extends Model
{
    use HasFactory;

    protected $table = 'fornecedores';

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ativo',
        'pessoa_id',
        'tipo',
    ];

    /**
     * Gera os campos do formulário para o recurso.
     *
     * @param string $function Tipo de operação do formulário
     * @return array<int, array<string, mixed>> Lista de campos do formulário
     */
    public function generateFields(String $function)
    {
        $fields = [
            ['name' => 'tipo_fornecedor', 'label' => 'Tipo de Fornecedor', 'type' => 'select', 'options' => ['nao_informado' => 'Não informado', 'produtos' => 'Produtos', 'servicos' => 'Serviços', 'produtos_servicos' => 'Produtos e Serviços'], 'selected' => $this->tipo ?? 'nao_informado'],
        ];

        return $fields;
    }

    /**
     * Obtém a pessoa associada ao fornecedor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }

    /**
     * Obtém as ordens de compra do fornecedor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ordensCompra()
    {
        return $this->hasMany(OrdemCompra::class);
    }
}
