@extends('layouts.app')

@section('title')
Clientes
@endsection()

@section('content')
<div>
    <h1><span style="font-size: 48px; color: Dodgerblue;"><i class="fa-solid fa-id-card"></i></span> Clientes</h1>
    <a class="btn btn-sm btn-primary mr-5" style="float:right" href="{{ route('cliente.inserir') }}"><i class="fa-solid fa-user-plus"></i></a>
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
            @if($clientes->isEmpty())
            <tr>
                <td colspan="4" class="text-center">Nenhum cliente cadastrado</td>
            </tr>
            @else
            @foreach ($clientes as $cliente)
            <tr>
                <td>{{ $cliente->pessoa->nome_social ?? $cliente->pessoa->nome_registro }}</td>
                <td>{{ $cliente->pessoa->cpf_cnpj }}</td>
                <td>{{ $cliente->pessoa->login }}</td>
                <td class="text-center">
                    <div class="col">
                        <a class="btn btn-sm btn-warning" href="{{ route('cliente.editar', $cliente->id) }}"><span style="font-size: 12px"><i class="fa-solid fa-user-pen"></i></span></a>
                        <a class="btn btn-sm btn-danger" onclick="event.preventDefault();
                            document.getElementById('excluir-form-{{ $cliente->id }}').submit();">
                            <span style="font-size: 12px"><i class="fa-solid fa-trash"></span></i>
                        </a>

                        <form id="excluir-form-{{ $cliente->id }}" action="{{ route('cliente.excluir', $funcionario->id) }}" method="POST" class="d-none">
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
