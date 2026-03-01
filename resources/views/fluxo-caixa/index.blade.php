@extends('layouts.app')

@section('content')
<div class="gap-1 px-6 flex flex-1 justify-center py-5">
    <div class="container-fluid p-0" style="max-width: 1200px;">
        <!-- Cabeçalho -->
        <div class="d-flex flex-wrap justify-content-between align-items-center p-4">
            <h1 class="text-dark m-0 fs-2 fw-bold" style="letter-spacing: -0.01em;">
                <i class="fa-solid fa-chart-line me-2"></i>Fluxo de Caixa
            </h1>
        </div>

        <!-- Filtro de Período -->
        <div class="px-4 pb-4">
            <div class="bg-white shadow-sm rounded-3 p-4 border border-light">
                <form method="GET" action="{{ route('fluxo-caixa.index') }}" class="row g-3 align-items-end">
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
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-warning bg-warning bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Total a Receber</p>
                    <p class="text-warning m-0 fs-4 fw-bold">R$ {{ number_format($totalAReceber, 2, ',', '.') }}</p>
                </div>
            </div>
            <div class="col-md-6 col-xl">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-success bg-success bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Total Recebido</p>
                    <p class="text-success m-0 fs-4 fw-bold">R$ {{ number_format($totalRecebido, 2, ',', '.') }}</p>
                </div>
            </div>
            <div class="col-md-6 col-xl">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-warning bg-warning bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Total a Pagar</p>
                    <p class="text-warning m-0 fs-4 fw-bold">R$ {{ number_format($totalAPagar, 2, ',', '.') }}</p>
                </div>
            </div>
            <div class="col-md-6 col-xl">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-danger bg-danger bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Total Pago</p>
                    <p class="text-danger m-0 fs-4 fw-bold">R$ {{ number_format($totalPago, 2, ',', '.') }}</p>
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

        <!-- Gráfico -->
        <div class="px-4 pb-4">
            <div class="bg-white shadow-sm rounded-3 p-4 border border-light">
                <h2 class="text-dark fs-5 fw-bold mb-3" style="letter-spacing: -0.015em;">Receitas vs Despesas por Mês</h2>
                <canvas id="fluxoCaixaChart" height="100"></canvas>
            </div>
        </div>

        <!-- Tabela de Movimentações Recentes -->
        <div class="px-4 pb-4">
            <div class="bg-white shadow-sm rounded-3 border border-light overflow-hidden">
                <div class="p-4 pb-3">
                    <h2 class="text-dark fs-5 fw-bold m-0" style="letter-spacing: -0.015em;">Movimentações Recentes</h2>
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
                                    <td colspan="6" class="text-center text-secondary py-4">Nenhuma movimentação encontrada no período.</td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var ctx = document.getElementById('fluxoCaixaChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($meses),
            datasets: [
                {
                    label: 'Receitas',
                    data: @json($receitasData),
                    backgroundColor: 'rgba(25, 135, 84, 0.7)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Despesas',
                    data: @json($despesasData),
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            var value = context.parsed.y || 0;
                            return context.dataset.label + ': R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 0 });
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
