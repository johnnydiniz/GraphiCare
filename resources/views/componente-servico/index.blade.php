@extends('layouts.app')

@section('title')
Componentes de serviços
@endsection()

@section('content')
<div>
    <h1><span style="font-size: 48px; color: Dodgerblue;"><i class="fa-solid fa-clipboard-user"></i></span> Componentes de serviços</h1>
    <a class="btn btn-sm btn-primary mr-5" style="float:right" href="{{ route('componente-servico.inserir') }}"><i class="fa-solid fa-user-plus"></i></a>
    <table class="table table-striped">
        <thead>
            <tr class="text-center">
                <th>Tipo</th>
                <th>Descrição</th>
                <th>Qtde</th>
                <th>Matéria prima</th>
                <th>Custo operacional</th>
                {{-- <th>Ativo</th> --}}
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @if($componentesServico->isEmpty())
            <tr>
                <td colspan="7" class="text-center">Nenhum componente de serviço cadastrado</td>
            </tr>
            @else
            @foreach ($componentesServico as $componenteServico)
            <tr class="text-center">
                <td>{{ $componenteServico->tipo ? 'Material' : 'Serviço' }}</td>
                <td>{{ $componenteServico->descricao }}</td>
                <td>{{ $componenteServico->qtde }}</td>
                <td>{{ $componenteServico->materiaPrima->tipoMateriaPrima->descricao }} {{ $componenteServico->materiaPrima->descricao }}</td>
                <td>{{ $formatter->formatCurrency($componenteServico->custo_operacional, 'BRL') }}</td>
                {{-- <td>{{ $componenteServico->ativo ? 'Sim' : 'Não' }}</td> --}}
                <td class="text-center">
                    <div class="col">
                        <a class="btn btn-sm btn-warning" href="{{ route('componente-servico.editar', $componenteServico->id) }}"><span style="font-size: 12px"><i class="fa-solid fa-user-pen"></i></span></a>
                        <a class="btn btn-sm btn-danger" onclick="event.preventDefault();
                            document.getElementById('excluir-form-{{ $componenteServico->id }}').submit();">
                            <span style="font-size: 12px"><i class="fa-solid fa-trash"></i></span>
                        </a>

                        <form id="excluir-form-{{ $componenteServico->id }}" action="{{ route('componente-servico.excluir', $componenteServico->id) }}" method="POST" class="d-none">
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
