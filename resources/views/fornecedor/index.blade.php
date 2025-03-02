@extends('layouts.app')

@section('title')
Clientes
@endsection()

@section('content')
@error('db_error')
<span>{{ $message }}</span>
@enderror
<div>
    <h1><span style="font-size: 48px; color: Dodgerblue;"><i class="fa-solid fa-user-tie"></i></span> Fornecedores</h1>
    <a class="btn btn-sm btn-primary mr-5" style="float:right" href="{{ route('fornecedor.inserir') }}"><i class="fa-solid fa-user-plus"></i></a>
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
            @if($fornecedores->isEmpty())
            <tr>
                <td colspan="4" class="text-center">Nenhum fornecedor cadastrado</td>
            </tr>
            @else
            @foreach ($fornecedores as $fornecedor)
            <tr>
                <td>{{ $fornecedor->pessoa->nome_social ?? $fornecedor->pessoa->nome_registro }}</td>
                <td>{{ $fornecedor->pessoa->cpf_cnpj }}</td>
                <td>{{ $fornecedor->pessoa->login }}</td>
                <td class="text-center">
                    <div class="col">
                        <a class="btn btn-sm btn-warning" href="{{ route('fornecedor.editar', $fornecedor->id) }}"><span style="font-size: 12px"><i class="fa-solid fa-user-pen"></i></span></a>
                        <a class="btn btn-sm btn-danger" onclick="event.preventDefault();
                                                        document.getElementById('excluir-form').submit();">
                            <span style="font-size: 12px"><i class="fa-solid fa-trash"></span></i>
                        </a>

                        <form id="excluir-form" action="{{ route('fornecedor.excluir', $fornecedor->id) }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection()
