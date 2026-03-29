<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa um tipo de matéria-prima utilizada na produção.
 *
 * @package App\Models
 * @property int $id
 * @property string $descricao Descrição do tipo de matéria-prima
 * @property bool $ativo Indica se o tipo de matéria-prima está ativo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class TipoMateriaPrima extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'descricao',
        'ativo',
    ];

    protected $table = 'tipo_materias_primas';

    /**
     * Gera os campos do formulário para o recurso.
     *
     * @param string $function Tipo de operação do formulário
     * @return array<int, array<string, mixed>> Lista de campos do formulário
     */
    public function generateFields(String $function)
    {
        $fields = [
            ['name' => 'ativo', 'label' => 'Ativo', 'type' => 'checkbox', 'checked' => $this->ativo ?? true, 'hidden' => $function == 'create' ? 'hidden' : false],
            ['name' => 'descricao', 'label' => 'Descrição', 'type' => 'text', 'value' => $this->descricao ?? '', 'required' => true],
        ];
        return $fields;
    }
}
