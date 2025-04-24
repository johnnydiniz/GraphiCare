@extends('layouts.app')

@section('title')
Pessoas
@endsection()

@section('content')
@error('db_error')
<span>{{ $message }}</span>
@enderror
<div>
    <h1><span style="font-size: 48px; color: Dodgerblue;"><i class="fa-solid fa-clipboard-user"></i></span> Pessoas</h1>
    <a class="btn btn-sm btn-primary mr-5" style="float:right" href="{{ route('pessoa.inserir') }}"><i class="fa-solid fa-user-plus"></i></a>
    <table class="table table-striped">
        <thead>
            <tr class="text-center">
                <th style="width: 3%">Cód.</th>
                <th style="width: 11%">Nome</th>
                <th style="width: 13%">CPF/CNPJ</th>
                <th style="width: 10%">Usuário</th>
                <th style="width: 10%">Tipo de fornecedor</th>
                <th style="width: 10%">Tipo de cliente</th>
                <th style="width: 10%">Limite de crédito</th>
                <th style="width: 3%">Taxa de desconto</th>
                <th style="width: 10%">Cargo</th>
                <th style="width: 8%">Salário</th>
                <th style="width: 3%">Ativo</th>
                <th style="width: 10%">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pessoas as $pessoa)
            <tr class="text-center align-middle">
                <td>{{ $pessoa->id }}</td>
                <td>{{ $pessoa->nome_social ?? $pessoa->nome_registro }}</td>
                <td>{{ $pessoa->cpf_cnpj }}</td>
                <td>{{ $pessoa->login }}</td>
                <td>{{ !is_null($pessoa->fornecedor) ?  $pessoa->fornecedor->tipo_fornecedor  : '-' }}</td>
                <td>{{ !is_null($pessoa->cliente) ? $pessoa->cliente->tipo : '-' }}</td>
                <td>{{ !is_null($pessoa->cliente) ? $pessoa->cliente->limite_credito : '-' }}</td>
                <td>{{ !is_null($pessoa->cliente) ? $pessoa->cliente->taxa_desconto : '-' }}</td>
                <td>{{ !is_null($pessoa->funcionario) ? $pessoa->funcionario->cargo : '-' }}</td>
                <td>{{ !is_null($pessoa->funcionario) ? $pessoa->funcionario->salario : '-' }}</td>
                <td>{{ $pessoa->ativo ? 'Sim' : 'Não' }}</td>
                <td>
                    <div class="col">
                        <a class="btn btn-sm btn-warning" href="{{ route('pessoa.editar', $pessoa->id) }}"><span style="font-size: 12px"><i class="fa-solid fa-user-pen"></i></span></a>
                        <a class="btn btn-sm btn-danger" onclick="event.preventDefault();
                            document.getElementById('excluir-form-{{ $pessoa->id }}').submit();">
                            <span style="font-size: 12px"><i class="fa-solid fa-trash"></span></i>
                        </a>

                        <form id="excluir-form-{{ $pessoa->id }}" action="{{ route('pessoa.excluir', $pessoa->id) }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection()
