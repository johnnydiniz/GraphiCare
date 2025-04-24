@extends('layouts.app')

@section('title')
Funcionários
@endsection()

@section('content')
<div>
    <h1><span style="font-size: 48px; color: Dodgerblue;"><i class="fa-solid fa-clipboard-user"></i></span> Funcionários</h1>
    <a class="btn btn-sm btn-primary mr-5" style="float:right" href="{{ route('funcionario.inserir') }}"><i class="fa-solid fa-user-plus"></i></a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>CPF/CNPJ</th>
                <th>Usuário</th>
                <th class="text-center">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($funcionarios as $funcionario)
            <tr>
                <td>{{ $funcionario->pessoa->nome_social ?? $funcionario->pessoa->nome_registro }}</td>
                <td>{{ $funcionario->pessoa->cpf_cnpj }}</td>
                <td>{{ $funcionario->pessoa->login }}</td>
                <td class="text-center">
                    <div class="col">
                        <a class="btn btn-sm btn-warning" href="{{ route('funcionario.editar', $funcionario->id) }}"><span style="font-size: 12px"><i class="fa-solid fa-user-pen"></i></span></a>
                        <a class="btn btn-sm btn-danger" onclick="event.preventDefault();
                            document.getElementById('excluir-form-{{ $funcionario->id }}').submit();">
                            <span style="font-size: 12px"><i class="fa-solid fa-trash"></span></i>
                        </a>

                        <form id="excluir-form-{{ $funcionario->id }}" action="{{ route('funcionario.excluir', $funcionario->id) }}" method="POST" class="d-none">
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
