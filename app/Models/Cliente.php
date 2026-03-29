<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa um cliente do sistema.
 *
 * @package App\Models
 * @property int $id
 * @property bool $ativo Indica se o cliente está ativo
 * @property int $pessoa_id ID da pessoa associada ao cliente
 * @property string $tipo Tipo do cliente (final, representante, nao_informado)
 * @property float|null $limite_credito Limite de crédito do cliente
 * @property float|null $taxa_desconto Taxa de desconto aplicável ao cliente
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Cliente extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
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
     * Gera os campos do formulário para o recurso.
     *
     * @param string $function Tipo de operação do formulário
     * @return array<int, array<string, mixed>> Lista de campos do formulário
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
     * Obtém a pessoa associada ao cliente.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }
}
