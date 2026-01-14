@extends('layouts.list')

@section('table')
    <!-- Tabela -->
    <div class="p-4 py-3">
        <div class="table-responsive rounded-3 border border-light bg-light">
            <div>
                <div class="d-flex border-bottom border-light px-4 gap-4 gap-md-8">
                    <a class="d-flex flex-column align-items-center justify-content-center border-bottom border-primary border-3 pb-3 pt-4 text-decoration-none"
                        href="#">
                        <span class="small fw-bold text-dark" style="letter-spacing: 0.015em;">{{ __('General') }}</span>
                    </a>
                    <a class="d-flex flex-column align-items-center justify-content-center border-bottom border-3 pb-3 pt-4 text-decoration-none"
                        href="#" style="border-bottom-color: transparent !important; color: #49739c;">
                        <span class="small fw-bold" style="letter-spacing: 0.015em;">{{ __('Customer') }}</span>
                    </a>
                    <a class="d-flex flex-column align-items-center justify-content-center border-bottom border-3 pb-3 pt-4 text-decoration-none"
                        href="#" style="border-bottom-color: transparent !important; color: #49739c;">
                        <span class="small fw-bold" style="letter-spacing: 0.015em;">{{ __('Provider') }}</span>
                    </a>
                    <a class="d-flex flex-column align-items-center justify-content-center border-bottom border-3 pb-3 pt-4 text-decoration-none"
                        href="#" style="border-bottom-color: transparent !important; color: #49739c;">
                        <span class="small fw-bold" style="letter-spacing: 0.015em;">{{ __('Employee') }}</span>
                    </a>
                </div>
            </div>
            <table class="table table-borderless mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="text-dark small fw-medium py-3 ps-4" style="width: 10%;">ID</th>
                        <th class="text-dark small fw-medium py-3 ps-4" style="width: 25%;">
                            {{ __('Name') }}</th>
                        <th class="text-dark small fw-medium py-3 ps-4" style="width: 25%;">
                            {{ __('SSN/EIN') }}</th>
                        <th class="text-dark small fw-medium py-3 ps-4" style="width: 25%;">
                            {{ __('Login') }}</th>
                        <th class="text-dark small fw-medium py-3 ps-4" style="width: 15%;">
                            {{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($pessoas->isEmpty())
                        <tr>
                            <td colspan="5" class="border-top border-light align-middle text-center">Nenhum
                                pessoa
                                cadastrada</td>
                        </tr>
                    @else
                        @foreach ($pessoas as $pessoa)
                            <tr class="border-top border-light align-middle">
                                <td class="text-dark small py-2 ps-4" style="height: 72px;">
                                    #{{ $pessoa->id }}</td>
                                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                    {{ $pessoa->nome_social ?? $pessoa->nome_registro }}
                                </td>
                                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                    {{ $pessoa->cpf_cnpj }}
                                </td>
                                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                    {{ $pessoa->login }}</td>
                                <td class="text-dark small py-2 ps-4 align-right">
                                    <div class="col">
                                        <a class="btn btn-sm btn-warning"
                                            href="{{ route('pessoa.editar', $pessoa->id) }}"><span
                                                style="font-size: 12px"><i class="fa-solid fa-user-pen"></i></span></a>
                                        <a class="btn btn-sm btn-danger"
                                            onclick="event.preventDefault();
                            document.getElementById('excluir-form-{{ $pessoa->id }}').submit();">
                                            <span style="font-size: 12px"><i class="fa-solid fa-trash"></span></i>
                                        </a>

                                        <form id="excluir-form-{{ $pessoa->id }}"
                                            action="{{ route('pessoa.excluir', $pessoa->id) }}" method="POST"
                                            class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        {{-- <td>{{ $pessoa->id }}</td>
                        <td>{{ $pessoa->nome_social ?? $pessoa->nome_registro }}</td>
                        <td>{{ $pessoa->cpf_cnpj }}</td>
                        <td>{{ $pessoa->login }}</td>
                        <td>{{ !is_null($pessoa->fornecedor) ? $pessoa->fornecedor->tipo_fornecedor : '-' }}</td>
                        <td>{{ !is_null($pessoa->cliente) ? $pessoa->cliente->tipo : '-' }}</td>
                        <td>{{ !is_null($pessoa->cliente) ? $pessoa->cliente->limite_credito : '-' }}</td>
                        <td>{{ !is_null($pessoa->cliente) ? $pessoa->cliente->taxa_desconto : '-' }}</td>
                        <td>{{ !is_null($pessoa->funcionario) ? $pessoa->funcionario->cargo : '-' }}</td>
                        <td>{{ !is_null($pessoa->funcionario) ? $pessoa->funcionario->salario : '-' }}</td>
                        <td>{{ $pessoa->ativo ? 'Sim' : 'Não' }}</td>
                        <td>
                            <div class="col">
                                <a class="btn btn-sm btn-warning" href="{{ route('pessoa.editar', $pessoa->id) }}"><span
                                        style="font-size: 12px"><i class="fa-solid fa-user-pen"></i></span></a>
                                <a class="btn btn-sm btn-danger"
                                    onclick="event.preventDefault();
                            document.getElementById('excluir-form-{{ $pessoa->id }}').submit();">
                                    <span style="font-size: 12px"><i class="fa-solid fa-trash"></span></i>
                                </a>

                                <form id="excluir-form-{{ $pessoa->id }}"
                                    action="{{ route('pessoa.excluir', $pessoa->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td> --}}
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection()


{{-- @extends('layouts.app')

@section('title')
    Pessoas
@endsection()

@section('content')
    @error('db_error')
        <span>{{ $message }}</span>
    @enderror
    <div>
        <h1><span style="font-size: 48px; color: Dodgerblue;"><i class="fa-solid fa-clipboard-user"></i></span> Pessoas</h1>
        <a class="btn btn-sm btn-primary mr-5" style="float:right" href="{{ route('pessoa.inserir') }}"><i
                class="fa-solid fa-user-plus"></i></a>
        <table class="table table-striped">
            <thead>
                <tr class="text-center">
                    <th style="width: 3%">Cód.</th>
                    <th style="width: 11%">Nome</th>
                    <th style="width: 13%">CPF/CNPJ</th>
                    <th style="width: 10%">Usuário</th>
                    <th style="width: 10%">Tipo de fornecedor</th>
                    <th style="width: 10%">Tipo de cliente</th>
                    <th style="width: 10%">Limite de crédito</th>
                    <th style="width: 3%">Taxa de desconto</th>
                    <th style="width: 10%">Cargo</th>
                    <th style="width: 8%">Salário</th>
                    <th style="width: 3%">Ativo</th>
                    <th style="width: 10%">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pessoas as $pessoa)
                    <tr class="text-center align-middle">

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection() --}}
