<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
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
        'cargo',
        'salario',
    ];

    /**
     * The form fields to be generated
     *
     * @var list<string>
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
     * Get the pessoa that owns the Funcionario
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }
}
