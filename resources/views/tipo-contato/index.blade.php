@extends('layouts.app')

@section('title')
Tipo de Contato
@endsection()

@section('content')
<div>
    <h1><span style="font-size: 48px; color: Dodgerblue;"><i class="fa-solid fa-clipboard-user"></i></span> Tipo de Contato</h1>
    <a class="btn btn-sm btn-primary mr-5" style="float:right" href="{{ route('tipo-contato.inserir') }}"><i class="fa-solid fa-user-plus"></i></a>
    <table class="table table-striped">
        <thead>
            <tr class="text-center">
                <th>Descrição</th>
                <th>Ativo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @if($tipoContatos->isEmpty())
            <tr>
                <td colspan="3" class="text-center">Nenhum tipo de contato cadastrado</td>
            </tr>
            @else
            @foreach ($tipoContatos as $tipoContato)
            <tr class="text-center">
                <td>{{ $tipoContato->descricao }}</td>
                <td>{{ $tipoContato->ativo ? 'Sim' : 'Não' }}</td>
                <td class="text-center">
                    <div class="col">
                        <a class="btn btn-sm btn-warning" href="{{ route('tipo-contato.editar', $tipoContato->id) }}"><span style="font-size: 12px"><i class="fa-solid fa-user-pen"></i></span></a>
                        <a class="btn btn-sm btn-danger" onclick="event.preventDefault();
                            document.getElementById('excluir-form-{{ $tipoContato->id }}').submit();">
                            <span style="font-size: 12px"><i class="fa-solid fa-trash"></i></span>
                        </a>

                        <form id="excluir-form-{{ $tipoContato->id }}" action="{{ route('tipo-contato.excluir', $tipoContato->id) }}" method="POST" class="d-none">
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
