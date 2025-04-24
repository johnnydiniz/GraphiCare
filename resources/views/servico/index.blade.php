@extends('layouts.app')

@section('title')
Serviço
@endsection()

@section('content')
<div>
    <h1><span style="font-size: 48px; color: Dodgerblue;"><i class="fa-solid fa-clipboard-user"></i></span> Serviço</h1>
    <a class="btn btn-sm btn-primary mr-5" style="float:right" href="{{ route('servico.inserir') }}"><i class="fa-solid fa-user-plus"></i></a>
    <table class="table table-striped">
        <thead>
            <tr class="text-center">
                <th>Nome</th>
                <th>Valor</th>
                <th>Custo</th>
                <th>Tempo de Realização</th>
                <th>Ativo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @if($servicos->isEmpty())
            <tr>
                <td colspan="8" class="text-center">Nenhum serviço cadastrado</td>
            </tr>
            @else
            @foreach ($servicos as $servico)
            <tr class="text-center">
                <td>{{ $servico->descricao }}</td>
                <td>{{ $formatter->formatCurrency($servico->valor, 'BRL') }}</td>
                <td>{{ $servico->custo }}</td>
                <td>{{ $servico->tempo_realizacao }}</td>
                <td>{{ $servico->ativo ? 'Sim' : 'Não' }}</td>
                <td class="text-center">
                    <div class="col">
                        <a class="btn btn-sm btn-warning" href="{{ route('servico.editar', $servico->id) }}"><span style="font-size: 12px"><i class="fa-solid fa-user-pen"></i></span></a>
                        <a class="btn btn-sm btn-danger" onclick="event.preventDefault();
                            document.getElementById('excluir-form-{{ $servico->id }}').submit();">
                            <span style="font-size: 12px"><i class="fa-solid fa-trash"></i></span>
                        </a>

                        <form id="excluir-form-{{ $servico->id }}" action="{{ route('servico.excluir', $servico->id) }}" method="POST" class="d-none">
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
