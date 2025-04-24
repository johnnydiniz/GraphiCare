@extends('layouts.app')

@section('title')
Tipo de Matéria Prima
@endsection()

@section('content')
<div>
    <h1><span style="font-size: 48px; color: Dodgerblue;"><i class="fa-solid fa-clipboard-user"></i></span> Tipo de Matéria Prima</h1>
    <a class="btn btn-sm btn-primary mr-5" style="float:right" href="{{ route('tipo-materia-prima.inserir') }}"><i class="fa-solid fa-user-plus"></i></a>
    <table class="table table-striped">
        <thead>
            <tr class="text-center">
                <th>Descrição</th>
                <th>Ativo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @if($tipoMateriaPrimas->isEmpty())
            <tr>
                <td colspan="3" class="text-center">Nenhum tipo de matéria prima cadastrado</td>
            </tr>
            @else
            @foreach ($tipoMateriaPrimas as $tipoMateriaPrima)
            <tr class="text-center">
                <td>{{ $tipoMateriaPrima->descricao }}</td>
                <td>{{ $tipoMateriaPrima->ativo ? 'Sim' : 'Não' }}</td>
                <td class="text-center">
                    <div class="col">
                        <a class="btn btn-sm btn-warning" href="{{ route('tipo-materia-prima.editar', $tipoMateriaPrima->id) }}"><span style="font-size: 12px"><i class="fa-solid fa-user-pen"></i></span></a>
                        <a class="btn btn-sm btn-danger" onclick="event.preventDefault();
                            document.getElementById('excluir-form-{{ $tipoMateriaPrima->id }}').submit();">
                            <span style="font-size: 12px"><i class="fa-solid fa-trash"></i></span>
                        </a>

                        <form id="excluir-form-{{ $tipoMateriaPrima->id }}" action="{{ route('tipo-materia-prima.excluir', $tipoMateriaPrima->id) }}" method="POST" class="d-none">
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
