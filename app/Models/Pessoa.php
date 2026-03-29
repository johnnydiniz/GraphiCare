<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Representa uma pessoa (física ou jurídica) que pode ser funcionário, cliente ou fornecedor.
 *
 * @package App\Models
 * @property int $id
 * @property bool $ativo Indica se a pessoa está ativa
 * @property string $tipo Tipo de pessoa (física ou jurídica)
 * @property string $login Login de acesso ao sistema
 * @property string $senha Senha de acesso ao sistema
 * @property string $cpf_cnpj CPF ou CNPJ da pessoa
 * @property string $nome_registro Nome de registro (razão social ou nome completo)
 * @property string|null $nome_social Nome social ou nome fantasia
 * @property int|null $endereco_id ID do endereço associado
 * @property string|null $escolaridade Nível de escolaridade
 * @property string|null $data_nascimento Data de nascimento
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Pessoa extends Authenticatable
{
    use HasFactory;
    /**
     * Os atributos que podem ser atribuídos em massa.
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

    /**
     * Gera os campos do formulário para o recurso.
     *
     * @param string $function Tipo de operação do formulário
     * @return array<int, array<string, mixed>> Lista de campos do formulário
     */
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
     * Os atributos que devem ser ocultados na serialização.
     *
     * @var list<string>
     */
    protected $hidden = [
        'senha',
        'remember_token',
    ];

    /**
     * Obtém os atributos que devem ser convertidos.
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

    /**
     * Obtém a senha para autenticação.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->senha;
    }

    /**
     * Obtém o funcionário associado à pessoa.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function funcionario()
    {
        return $this->hasOne(Funcionario::class);
    }

    /**
     * Obtém o cliente associado à pessoa.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cliente()
    {
        return $this->hasOne(Cliente::class);
    }

    /**
     * Obtém o fornecedor associado à pessoa.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function fornecedor()
    {
        return $this->hasOne(Fornecedor::class);
    }

    /**
     * Obtém o endereço associado à pessoa.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function endereco()
    {
        return $this->belongsTo(Endereco::class);
    }

    /**
     * Obtém os contatos da pessoa.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contatos()
    {
        return $this->hasMany(Contato::class);
    }
}
