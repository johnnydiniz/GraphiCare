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
        'tipo',
        'login',
        'senha',
        'cpf_cnpj',
        'nome_registro',
        'nome_social',
        'endereco_id',
    ];

    protected $attributes = [
        'endereco_id' => null,
    ];

    public function generateFields(String $function)
    {
        $fields = [
            ['proportion' => '2', 'name' => 'tipo', 'label' => 'Tipo', 'type' => 'select', 'required' => true, 'options' => ['fisica' => 'Física', 'juridica' => 'Jurídica'], 'selected' => $this->tipo ?? 'fisica'],
            ['proportion' => '2', 'name' => 'cpf_cnpj', 'label' => 'CPF', 'type' => 'text', 'value' => $this->cpf_cnpj ?? '', 'required' => true],
            ['proportion' => '8', 'name' => 'nome_registro', 'label' => 'Nome completo', 'type' => 'text', 'value' => $this->nome_registro ?? null, 'required' => true],
            ['proportion' => '2', 'name' => 'data_nascimento', 'label' => 'Data de nascimento', 'type' => 'date', 'value' => $this->data_nascimento ?? null],
            ['proportion' => '2', 'name' => 'escolaridade', 'label' => 'Escolaridade', 'type' => 'select', 'options' => ['nao_informado' => 'Não informado', 'fundamental' => 'Fundamental', 'medio' => 'Médio', 'superior' => 'Superior', 'pos_graduacao' => 'Pós-graduação', 'mestrado' => 'Mestrado', 'doutorado' => 'Doutorado'], 'selected' => $this->escolaridade ?? 'nao_informado'],
            ['proportion' => '8', 'name' => 'nome_social', 'label' => 'Nome social', 'type' => 'text', 'value' => $this->nome_social ?? ''],
            ['proportion' => '4', 'name' => 'login', 'label' => 'Login', 'type' => 'text', 'value' => $this->login ?? '', 'required' => true],
            ['proportion' => '4', 'name' => 'senha', 'label' => 'Senha', 'type' => 'password', 'value' => null, 'required' => true],
            ['proportion' => '4', 'name' => 'senha_confirmacao', 'label' => 'Confirme a senha', 'type' => 'password', 'value' => null, 'required' => true],
        ];
        // $contatoFields = [];
        // if (! is_null($this->contatos) && $this->contatos->count() > 0) {
        //     foreach ($this->contatos as $contato) {
        //         $contatoFields = $contato->generateFields($function);
        //     }
        // } else {
        //     $contatoFields = (new Contato())->generateFields($function);
        // }

        // $enderecoFields = ! is_null($this->endereco) ? $this->endereco->generateFields($function) : (new Endereco())->generateFields($function);

        // $fornecedorFields = ! is_null($this->fornecedor) ? $this->fornecedor->generateFields($function) : (new Fornecedor())->generateFields($function);

        // $clienteFields = ! is_null($this->cliente) ? $this->cliente->generateFields($function) : (new Cliente())->generateFields($function);

        // $funcionarioFields = ! is_null($this->funcionario) ? $this->funcionario->generateFields($function) : (new Funcionario())->generateFields($function);

        // $fields = array_merge($fields, $contatoFields, $enderecoFields, $fornecedorFields, $clienteFields, $funcionarioFields);

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
