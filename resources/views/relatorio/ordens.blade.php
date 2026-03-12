@extends('layouts.app')

@section('content')
<div class="gap-1 px-6 flex flex-1 justify-center py-5">
    <div class="container-fluid p-0" style="max-width: 1200px;">
        <!-- Cabeçalho -->
        <div class="d-flex flex-wrap justify-content-between align-items-center p-4">
            <h1 class="text-dark m-0 fs-2 fw-bold" style="letter-spacing: -0.01em;">
                <i class="fa-solid fa-print me-2"></i>Relatório de Ordens de Serviço
            </h1>
        </div>

        <!-- Filtro de Período -->
        <div class="px-4 pb-4">
            <div class="bg-white shadow-sm rounded-3 p-4 border border-light">
                <form method="GET" action="{{ route('relatorio.ordens') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="data_inicio" class="form-label small fw-medium text-secondary">Data Início</label>
                        <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="{{ $dataInicio }}">
                    </div>
                    <div class="col-md-3">
                        <label for="data_fim" class="form-label small fw-medium text-secondary">Data Fim</label>
                        <input type="date" class="form-control" id="data_fim" name="data_fim" value="{{ $dataFim }}">
                    </div>
                    <div class="col-md-3">
                        <label for="cliente_id" class="form-label small fw-medium text-secondary">Cliente</label>
                        <select class="form-select" id="cliente_id" name="cliente_id">
                            <option value="">Todos os clientes</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ ($clienteId ?? '') == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->pessoa->nome_social ?? $cliente->pessoa->nome_registro }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-dark w-100">
                            <i class="fa-solid fa-filter me-2"></i>Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cards de KPIs -->
        <div class="row g-4 px-4 pb-4">
            <div class="col-md-6 col-xl-3">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-primary bg-primary bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Total de OS</p>
                    <p class="text-primary m-0 fs-4 fw-bold">{{ $totalOS }}</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-success bg-success bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Concluídas</p>
                    <p class="text-success m-0 fs-4 fw-bold">{{ $concluidas }}</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-warning bg-warning bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Pendentes</p>
                    <p class="text-warning m-0 fs-4 fw-bold">{{ $pendentes }}</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-info bg-info bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Receita Total</p>
                    <p class="text-info m-0 fs-4 fw-bold">R$ {{ number_format($receitaTotal, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Tabela -->
        <div class="px-4 pb-4">
            <div class="bg-white shadow-sm rounded-3 border border-light overflow-hidden">
                <div class="p-4 pb-3">
                    <h2 class="text-dark fs-5 fw-bold m-0" style="letter-spacing: -0.015em;">Ordens no Período</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-secondary small fw-medium py-3 ps-4">ID</th>
                                <th class="text-secondary small fw-medium py-3">Cliente</th>
                                <th class="text-secondary small fw-medium py-3">Valor Final</th>
                                <th class="text-secondary small fw-medium py-3">Status</th>
                                <th class="text-secondary small fw-medium py-3">Data Início</th>
                                <th class="text-secondary small fw-medium py-3 pe-4">Data Entrega</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ordens as $ordem)
                                <tr class="border-top border-light align-middle">
                                    <td class="small py-3 ps-4" style="height: 60px;">{{ $ordem->id }}</td>
                                    <td class="text-dark small py-3">
                                        {{ $ordem->cliente->pessoa->nome_social ?? $ordem->cliente->pessoa->nome_registro ?? '-' }}
                                    </td>
                                    <td class="text-dark small py-3 fw-medium">R$ {{ number_format($ordem->valor_final ?? 0, 2, ',', '.') }}</td>
                                    <td class="small py-3">
                                        @if ($ordem->status === 'finalizada')
                                            <span class="badge bg-success">Finalizada</span>
                                        @elseif ($ordem->status === 'em_andamento')
                                            <span class="badge bg-primary">Em Andamento</span>
                                        @elseif ($ordem->status === 'pendente')
                                            <span class="badge bg-warning text-dark">Pendente</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($ordem->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-secondary small py-3">{{ $ordem->data_inicio ? $ordem->data_inicio->format('d/m/Y') : '-' }}</td>
                                    <td class="text-secondary small py-3 pe-4">{{ $ordem->data_entrega ? $ordem->data_entrega->format('d/m/Y') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-secondary py-4">Nenhum registro encontrado no período.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
