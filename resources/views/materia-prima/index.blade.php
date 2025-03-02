@extends('layouts.app')

@section('title')
Matéria Prima
@endsection()

@section('content')
<div>
    <h1><span style="font-size: 48px; color: Dodgerblue;"><i class="fa-solid fa-clipboard-user"></i></span> Matéria Prima</h1>
    <a class="btn btn-sm btn-primary mr-5" style="float:right" href="{{ route('materia-prima.inserir') }}"><i class="fa-solid fa-user-plus"></i></a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Custo médio</th>
                <th>Estoque atual</th>
                <th>Estoque mínimo</th>
                <th>Aviso de estoque</th>
                <th>Tipo de matéria-prima</th>
                <th class="text-center">Ações</th>
            </tr>
        </thead>
        <tbody>
            @if($materiasPrimas->isEmpty())
            <tr>
                <td colspan="7" class="text-center">Nenhuma matéria prima cadastrada</td>
            </tr>
            @else
            @foreach ($materiasPrimas as $materiaPrima)
            <tr>
                <td>{{ $materiaPrima->descricao }}</td>
                <td>{{ $materiaPrima->pessoa->cpf_cnpj }}</td>
                <td>{{ $materiaPrima->pessoa->login }}</td>
                <td class="text-center">
                    <div class="col">
                        <a class="btn btn-sm btn-warning" href="{{ route('materia-prima.editar', $materiaPrima->id) }}"><span style="font-size: 12px"><i class="fa-solid fa-user-pen"></i></span></a>
                        <a class="btn btn-sm btn-danger" onclick="event.preventDefault();
                            document.getElementById('excluir-form').submit();">
                            <span style="font-size: 12px"><i class="fa-solid fa-trash"></i></span>
                        </a>

                        <form id="excluir-form" action="{{ route('materia-prima.excluir', $materiaPrima->id) }}" method="POST" class="d-none">
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
