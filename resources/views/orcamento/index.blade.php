@extends('layouts.app')

@section('title')
Orçamentos
@endsection()

@section('content')
<div>
    <h1><span style="font-size: 48px; color: Dodgerblue;"><i class="fa-solid fa-clipboard-user"></i></span> Orçamentos</h1>
    <a class="btn btn-sm btn-primary mr-5" style="float:right" href="{{ route('orcamento.inserir') }}"><i class="fa-solid fa-user-plus"></i></a>
    <table class="table table-striped">
        <thead>
            <tr class="text-center">
                <th>Desconto</th>
                <th>Custo Final</th>
                <th>Valor Final</th>
                <th>Previsão de Início</th>
                <th>Previsão de Entrega</th>
                <th>Validade</th>
                <th>Cliente</th>
            </tr>
        </thead>
        <tbody>
            @if($orcamentos->isEmpty())
            <tr>
                <td colspan="7" class="text-center">Nenhum orçamento cadastrado</td>
            </tr>
            @else
            @foreach ($orcamentos as $orcamento)
            <tr class="text-center">
                <td>{{ $formatter->formatCurrency($orcamento->desconto, 'BRL') }}</td>
                <td>{{ $formatter->formatCurrency($orcamento->custo_final, 'BRL') }}</td>
                <td>{{ $formatter->formatCurrency($orcamento->valor_final, 'BRL') }}</td>
                <td>{{ $formatter->formatDate($orcamento->previsao_inicio) }}</td>
                <td>{{ $formatter->formatDate($orcamento->previsao_entrega) }}</td>
                <td>{{ $formatter->formatDate($orcamento->validade) }}</td>
                <td>{{ $orcamento->cliente->nome }}</td>
                
                <td class="text-center">
                    <div class="col">
                        <a class="btn btn-sm btn-warning" href="{{ route('orcamento.editar', $orcamento->id) }}"><span style="font-size: 12px"><i class="fa-solid fa-user-pen"></i></span></a>
                        <a class="btn btn-sm btn-danger" onclick="event.preventDefault();
                            document.getElementById('excluir-form-{{ $orcamento->id }}').submit();">
                            <span style="font-size: 12px"><i class="fa-solid fa-trash"></i></span>
                        </a>

                        <form id="excluir-form-{{ $orcamento->id }}" action="{{ route('orcamento.excluir', $orcamento->id) }}" method="POST" class="d-none">
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
