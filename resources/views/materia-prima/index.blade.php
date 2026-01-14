@extends('layouts.app')

@section('title')
    {{ __($title) }}
@endsection()

@section('content')
    <div class="container-fluid py-5 px-4 px-md-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <!-- Cabeçalho -->
                <div class="d-flex flex-wrap justify-content-between p-4">
                    <h1 class="text-dark m-0 fs-2 fw-bold" style="min-width: 288px; letter-spacing: -0.01em;">
                        {{ __($title) }} </h1>
                    <a class="btn bg-light text-dark d-flex align-items-center justify-content-center overflow-hidden rounded-3 py-1 px-4 text-truncate small fw-medium"
                        style="min-width: 84px; max-width: 480px; height: 32px;" href="{{ route('{{ $title }}.inserir') }}"> +
                        {{ __($title) }} </a>
                </div>

                <!-- Barra de Pesquisa -->
                <div class="p-4 py-3">
                    <div class="input-group" style="width:100%">
                        <span class="input-group-text bg-light border-0 rounded-start-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#49739c"
                                viewBox="0 0 256 256">
                                <path
                                    d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z">
                                </path>
                            </svg>
                        </span>
                        <input type="text" class="form-control form-control-custom border-0 rounded-end-3 flex-1"
                            placeholder="{{ __('Search') }} {{ strtolower(__($title)) }}">
                    </div>
                </div>

                <!-- Filtros -->
                <div class="d-flex flex-wrap gap-3 p-3 pe-4">
                    <button class="btn btn-light d-flex align-items-center gap-2 py-1 px-3 rounded-3" style="height: 32px;">
                        <span class="small fw-medium">{{ __('Status') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#0d141c"
                            viewBox="0 0 256 256">
                            <path
                                d="M213.66,101.66l-80,80a8,8,0,0,1-11.32,0l-80-80A8,8,0,0,1,53.66,90.34L128,164.69l74.34-74.35a8,8,0,0,1,11.32,11.32Z">
                            </path>
                        </svg>
                    </button>
                    <button class="btn btn-light d-flex align-items-center gap-2 py-1 px-3 rounded-3" style="height: 32px;">
                        <span class="small fw-medium">{{ __('Date') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#0d141c"
                            viewBox="0 0 256 256">
                            <path
                                d="M213.66,101.66l-80,80a8,8,0,0,1-11.32,0l-80-80A8,8,0,0,1,53.66,90.34L128,164.69l74.34-74.35a8,8,0,0,1,11.32,11.32Z">
                            </path>
                        </svg>
                    </button>
                    <button class="btn btn-light d-flex align-items-center gap-2 py-1 px-3 rounded-3" style="height: 32px;">
                        <span class="small fw-medium">{{ __('Amount') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#0d141c"
                            viewBox="0 0 256 256">
                            <path
                                d="M213.66,101.66l-80,80a8,8,0,0,1-11.32,0l-80-80A8,8,0,0,1,53.66,90.34L128,164.69l74.34-74.35a8,8,0,0,1,11.32,11.32Z">
                            </path>
                        </svg>
                    </button>
                </div>

                <!-- Tabela -->
                <div class="p-4 py-3">
                    <div class="table-responsive rounded-3 border border-light bg-light">
                        <table class="table table-borderless mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-dark small fw-medium py-3 ps-4" style="width: 400px;">ID</th>
                                    <th class="text-dark small fw-medium py-3 ps-4" style="width: 400px;">
                                        {{ __('Customer') }}</th>
                                    <th class="text-dark small fw-medium py-3 ps-4" style="width: 240px;">
                                        {{ __('Status') }}</th>
                                    <th class="text-dark small fw-medium py-3 ps-4" style="width: 400px;">
                                        {{ __('Date') }}</th>
                                    <th class="text-dark small fw-medium py-3 ps-4" style="width: 400px;">
                                        {{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($orcamentos->isEmpty())
                                    <tr>
                                        <td colspan="5" class="border-top border-light align-middle text-center">Nenhum
                                            orçamento
                                            cadastrado</td>
                                    </tr>
                                @else
                                    @foreach ($orcamentos as $orcamento)
                                        <tr class="border-top border-light align-middle">
                                            <td class="text-dark small py-2 ps-4" style="height: 72px;">
                                                #{{ $orcamento->id }}</td>
                                            <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                                {{ $orcamento->cliente->nome }}
                                            </td>
                                            <td class="small py-2 ps-4" style="height: 72px;">
                                                <button class="btn btn-light w-100 rounded-3 py-1" style="height: 32px;">
                                                    <span class="text-truncate fw-medium">Completed</span>
                                                </button>
                                            </td>
                                            <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                                {{ $orcamento->created_at }}</td>
                                            <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                                {{ $formatter->formatCurrency($orcamento->valor_final, 'BRL') }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection()




@extends('layouts.app')

@section('title')
    Matéria Prima
@endsection()

@section('content')
    <div>
        <h1><span style="font-size: 48px; color: Dodgerblue;"><i class="fa-solid fa-clipboard-user"></i></span> Matéria
            Prima</h1>
        <a class="btn btn-sm btn-primary mr-5" style="float:right" href="{{ route('materia-prima.inserir') }}"><i
                class="fa-solid fa-user-plus"></i></a>
        <table class="table table-striped">
            <thead>
                <tr class="text-center">
                    <th>Tipo</th>
                    <th>Descrição</th>
                    <th>Custo médio</th>
                    <th>Estoque atual</th>
                    <th>Estoque mínimo</th>
                    <th>Aviso de estoque</th>
                    <th>Ativo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @if ($materiasPrimas->isEmpty())
                    <tr>
                        <td colspan="8" class="text-center">Nenhuma matéria prima cadastrada</td>
                    </tr>
                @else
                    @foreach ($materiasPrimas as $materiaPrima)
                        <tr class="text-center">
                            <td>{{ $materiaPrima->tipoMateriaPrima->descricao }}</td>
                            <td>{{ $materiaPrima->descricao }}</td>
                            <td>{{ $formatter->formatCurrency($materiaPrima->custo_medio, 'BRL') }}</td>
                            <td>{{ $materiaPrima->estoque_atual }}</td>
                            <td>{{ $materiaPrima->estoque_minimo }}</td>
                            <td>{{ $materiaPrima->aviso_estoque ? 'Sim' : 'Não' }}</td>
                            <td>{{ $materiaPrima->ativo ? 'Sim' : 'Não' }}</td>
                            <td class="text-center">
                                <div class="col">
                                    <a class="btn btn-sm btn-warning"
                                        href="{{ route('materia-prima.editar', $materiaPrima->id) }}"><span
                                            style="font-size: 12px"><i class="fa-solid fa-user-pen"></i></span></a>
                                    <a class="btn btn-sm btn-danger"
                                        onclick="event.preventDefault();
                            document.getElementById('excluir-form-{{ $materiaPrima->id }}').submit();">
                                        <span style="font-size: 12px"><i class="fa-solid fa-trash"></i></span>
                                    </a>

                                    <form id="excluir-form-{{ $materiaPrima->id }}"
                                        action="{{ route('materia-prima.excluir', $materiaPrima->id) }}" method="POST"
                                        class="d-none">
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
