<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa um funcionário da empresa.
 *
 * @package App\Models
 * @property int $id
 * @property bool $ativo Indica se o funcionário está ativo
 * @property int $pessoa_id ID da pessoa associada ao funcionário
 * @property string|null $cargo Cargo do funcionário
 * @property float|null $salario Salário do funcionário
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Funcionario extends Model
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
        'cargo',
        'salario',
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
            ['name' => 'cargo', 'label' => 'Cargo', 'type' => 'text', 'value' => $this->cargo ?? ''],
            ['name' => 'salario', 'label' => 'Salário', 'type' => 'text', 'value' => $this->salario ?? null, 'mask' => 'currency']
        ];

        return $fields;
    }

    /**
     * Obtém a pessoa associada ao funcionário.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }
}
