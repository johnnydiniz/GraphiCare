<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pessoa extends Authenticatable
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ativo',
        'tipo',
        'login',
        'senha',
        'cpf_cnpj',
        'nome_registro',
        'nome_social',
        'endereco_id',
        'escolaridade',
        'data_nascimento',
    ];

    protected $attributes = [
        'endereco_id' => null,
    ];

    public function generateFields(String $function)
    {
        $fields = [
            ['proportion' => '2', 'name' => 'tipo', 'label' => 'Tipo', 'type' => 'select', 'required' => true, 'options' => ['fisica' => 'Física', 'juridica' => 'Jurídica'], 'selected' => $this->tipo ?? 'fisica'],
            ['proportion' => '2', 'name' => 'cpf_cnpj', 'label' => 'CPF', 'type' => 'text', 'value' => $this->cpf_cnpj ?? '', 'required' => true, 'mask' => 'cpf_cnpj'],
            ['proportion' => '8', 'name' => 'nome_registro', 'label' => 'Nome completo', 'type' => 'text', 'value' => $this->nome_registro ?? null, 'required' => true],
            ['proportion' => '2', 'name' => 'data_nascimento', 'label' => 'Data de nascimento', 'type' => 'date', 'value' => $this->data_nascimento ?? null],
            ['proportion' => '2', 'name' => 'escolaridade', 'label' => 'Escolaridade', 'type' => 'select', 'options' => ['nao_informado' => 'Não informado', 'fundamental' => 'Fundamental', 'medio' => 'Médio', 'superior' => 'Superior', 'pos_graduacao' => 'Pós-graduação', 'mestrado' => 'Mestrado', 'doutorado' => 'Doutorado'], 'selected' => $this->escolaridade ?? 'nao_informado'],
            ['proportion' => '8', 'name' => 'nome_social', 'label' => 'Nome social', 'type' => 'text', 'value' => $this->nome_social ?? ''],
            ['proportion' => '4', 'name' => 'login', 'label' => 'Login', 'type' => 'text', 'value' => $this->login ?? '', 'required' => true],
            ['proportion' => '4', 'name' => 'senha', 'label' => 'Senha', 'type' => 'password', 'value' => null, 'required' => true],
            ['proportion' => '4', 'name' => 'senha_confirmacao', 'label' => 'Confirme a senha', 'type' => 'password', 'value' => null, 'required' => true],
        ];

        return $fields;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'senha',
        'remember_token',
    ];

/**
 * Get the attributes that should be cast.
 *
 * @return array<string, string>
 */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'senha'             => 'hashed',
        ];
    }

    public function getAuthPassword()
    {
        return $this->senha;
    }

    public function funcionario()
    {
        return $this->hasOne(Funcionario::class);
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class);
    }

    public function fornecedor()
    {
        return $this->hasOne(Fornecedor::class);
    }

    public function endereco()
    {
        return $this->belongsTo(Endereco::class);
    }

    public function contatos()
    {
        return $this->hasMany(Contato::class);
    }
}
