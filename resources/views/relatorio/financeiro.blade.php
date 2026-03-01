@extends('layouts.app')

@section('content')
<div class="gap-1 px-6 flex flex-1 justify-center py-5">
    <div class="container-fluid p-0" style="max-width: 1200px;">
        <!-- Cabeçalho -->
        <div class="d-flex flex-wrap justify-content-between align-items-center p-4">
            <h1 class="text-dark m-0 fs-2 fw-bold" style="letter-spacing: -0.01em;">
                <i class="fa-solid fa-coins me-2"></i>Relatório Financeiro
            </h1>
        </div>

        <!-- Filtro de Período -->
        <div class="px-4 pb-4">
            <div class="bg-white shadow-sm rounded-3 p-4 border border-light">
                <form method="GET" action="{{ route('relatorio.financeiro') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="data_inicio" class="form-label small fw-medium text-secondary">Data Início</label>
                        <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="{{ $dataInicio }}">
                    </div>
                    <div class="col-md-4">
                        <label for="data_fim" class="form-label small fw-medium text-secondary">Data Fim</label>
                        <input type="date" class="form-control" id="data_fim" name="data_fim" value="{{ $dataFim }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-dark w-100">
                            <i class="fa-solid fa-filter me-2"></i>Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cards de KPIs -->
        <div class="row g-4 px-4 pb-4">
            <div class="col-md-6 col-xl">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-primary bg-primary bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Total Faturas</p>
                    <p class="text-primary m-0 fs-4 fw-bold">{{ $totalFaturas }}</p>
                </div>
            </div>
            <div class="col-md-6 col-xl">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-secondary bg-secondary bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Total Boletos</p>
                    <p class="text-secondary m-0 fs-4 fw-bold">{{ $totalBoletos }}</p>
                </div>
            </div>
            <div class="col-md-6 col-xl">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-warning bg-warning bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Total a Receber</p>
                    <p class="text-warning m-0 fs-4 fw-bold">R$ {{ number_format($totalAReceber, 2, ',', '.') }}</p>
                </div>
            </div>
            <div class="col-md-6 col-xl">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-danger bg-danger bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Total a Pagar</p>
                    <p class="text-danger m-0 fs-4 fw-bold">R$ {{ number_format($totalAPagar, 2, ',', '.') }}</p>
                </div>
            </div>
            <div class="col-md-12 col-xl">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border {{ $saldo >= 0 ? 'border-success bg-success' : 'border-danger bg-danger' }} bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Saldo</p>
                    <p class="{{ $saldo >= 0 ? 'text-success' : 'text-danger' }} m-0 fs-4 fw-bold">
                        R$ {{ number_format($saldo, 2, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Tabela -->
        <div class="px-4 pb-4">
            <div class="bg-white shadow-sm rounded-3 border border-light overflow-hidden">
                <div class="p-4 pb-3">
                    <h2 class="text-dark fs-5 fw-bold m-0" style="letter-spacing: -0.015em;">Movimentações no Período</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-secondary small fw-medium py-3 ps-4">Tipo</th>
                                <th class="text-secondary small fw-medium py-3">Referência</th>
                                <th class="text-secondary small fw-medium py-3">Entidade</th>
                                <th class="text-secondary small fw-medium py-3">Valor</th>
                                <th class="text-secondary small fw-medium py-3">Status</th>
                                <th class="text-secondary small fw-medium py-3 pe-4">Vencimento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($movimentacoes as $mov)
                                <tr class="border-top border-light align-middle">
                                    <td class="small py-3 ps-4" style="height: 60px;">
                                        @if ($mov['tipo'] === 'Fatura')
                                            <span class="badge bg-primary">Fatura</span>
                                        @else
                                            <span class="badge bg-secondary">Boleto</span>
                                        @endif
                                    </td>
                                    <td class="text-dark small py-3">{{ $mov['referencia'] }}</td>
                                    <td class="text-secondary small py-3">{{ $mov['entidade'] }}</td>
                                    <td class="text-dark small py-3 fw-medium">R$ {{ number_format($mov['valor'], 2, ',', '.') }}</td>
                                    <td class="small py-3">
                                        @if (in_array($mov['status'], ['paga', 'pago']))
                                            <span class="badge bg-success">{{ ucfirst($mov['status']) }}</span>
                                        @elseif (in_array($mov['status'], ['vencida', 'vencido']))
                                            <span class="badge bg-danger">{{ ucfirst($mov['status']) }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pendente</span>
                                        @endif
                                    </td>
                                    <td class="text-secondary small py-3 pe-4">{{ $mov['data_vencimento']->format('d/m/Y') }}</td>
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
