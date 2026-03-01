@extends('layouts.app')

@section('content')
<div class="gap-1 px-6 flex flex-1 justify-center py-5">
    <div class="container-fluid p-0" style="max-width: 1200px;">
        <!-- Cabeçalho -->
        <div class="d-flex flex-wrap justify-content-between align-items-center p-4">
            <h1 class="text-dark m-0 fs-2 fw-bold" style="letter-spacing: -0.01em;">
                <i class="fa-solid fa-house me-2"></i>Dashboard
            </h1>
        </div>

        <!-- Cards de Métricas -->
        <div class="row g-4 px-4 pb-4">
            <div class="col-md-6 col-xl-3">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-primary bg-primary bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Total de Ordens</p>
                    <p class="text-primary m-0 fs-3 fw-bold">{{ $totalOrdens }}</p>
                    <a href="{{ route('ordem-servico.index') }}" class="text-primary small text-decoration-none">Ver ordens <i class="fa-solid fa-arrow-right ms-1" style="font-size: 0.7rem;"></i></a>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-warning bg-warning bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Ordens Pendentes</p>
                    <p class="text-warning m-0 fs-3 fw-bold">{{ $ordensPendentes }}</p>
                    <a href="{{ route('ordem-servico.index') }}" class="text-warning small text-decoration-none">Ver pendentes <i class="fa-solid fa-arrow-right ms-1" style="font-size: 0.7rem;"></i></a>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-success bg-success bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Receita do Mês</p>
                    <p class="text-success m-0 fs-3 fw-bold">R$ {{ number_format($receitaMes, 2, ',', '.') }}</p>
                    <a href="{{ route('fluxo-caixa.index') }}" class="text-success small text-decoration-none">Ver fluxo <i class="fa-solid fa-arrow-right ms-1" style="font-size: 0.7rem;"></i></a>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border {{ $estoqueCritico > 0 ? 'border-danger bg-danger' : 'border-success bg-success' }} bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Estoque Crítico</p>
                    <p class="{{ $estoqueCritico > 0 ? 'text-danger' : 'text-success' }} m-0 fs-3 fw-bold">{{ $estoqueCritico }} {{ $estoqueCritico == 1 ? 'item' : 'itens' }}</p>
                    <a href="{{ route('materia-prima.index') }}" class="{{ $estoqueCritico > 0 ? 'text-danger' : 'text-success' }} small text-decoration-none">Ver estoque <i class="fa-solid fa-arrow-right ms-1" style="font-size: 0.7rem;"></i></a>
                </div>
            </div>
        </div>

        <!-- Resumo Financeiro -->
        <div class="px-4 pb-4">
            <div class="bg-white shadow-sm rounded-3 p-4 border border-light">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="text-dark fs-5 fw-bold m-0" style="letter-spacing: -0.015em;">
                        <i class="fa-solid fa-coins me-2"></i>Resumo Financeiro
                    </h2>
                    <a href="{{ route('fluxo-caixa.index') }}" class="btn btn-sm btn-outline-dark">
                        <i class="fa-solid fa-chart-line me-1"></i>Fluxo de Caixa
                    </a>
                </div>
                <div class="row g-3">
                    <div class="col-md-6 col-xl">
                        <div class="rounded-3 p-3 border border-warning bg-warning bg-opacity-10">
                            <p class="text-secondary mb-1 small">A Receber</p>
                            <p class="text-warning m-0 fs-5 fw-bold">R$ {{ number_format($totalAReceber, 2, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl">
                        <div class="rounded-3 p-3 border border-success bg-success bg-opacity-10">
                            <p class="text-secondary mb-1 small">Recebido</p>
                            <p class="text-success m-0 fs-5 fw-bold">R$ {{ number_format($totalRecebido, 2, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl">
                        <div class="rounded-3 p-3 border border-warning bg-warning bg-opacity-10">
                            <p class="text-secondary mb-1 small">A Pagar</p>
                            <p class="text-warning m-0 fs-5 fw-bold">R$ {{ number_format($totalAPagar, 2, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl">
                        <div class="rounded-3 p-3 border border-danger bg-danger bg-opacity-10">
                            <p class="text-secondary mb-1 small">Pago</p>
                            <p class="text-danger m-0 fs-5 fw-bold">R$ {{ number_format($totalPago, 2, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xl">
                        <div class="rounded-3 p-3 border {{ $saldo >= 0 ? 'border-success bg-success' : 'border-danger bg-danger' }} bg-opacity-10">
                            <p class="text-secondary mb-1 small">Saldo</p>
                            <p class="{{ $saldo >= 0 ? 'text-success' : 'text-danger' }} m-0 fs-5 fw-bold">R$ {{ number_format($saldo, 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row px-4 pb-4 g-4">
            <!-- Ordens de Serviço Recentes -->
            <div class="col-xl-7">
                <div class="bg-white shadow-sm rounded-3 border border-light overflow-hidden h-100">
                    <div class="p-4 pb-3 d-flex justify-content-between align-items-center">
                        <h2 class="text-dark fs-5 fw-bold m-0" style="letter-spacing: -0.015em;">
                            <i class="fa-solid fa-print me-2"></i>Ordens Recentes
                        </h2>
                        <a href="{{ route('ordem-servico.index') }}" class="btn btn-sm btn-outline-dark">Ver todas</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-secondary small fw-medium py-3 ps-4">#</th>
                                    <th class="text-secondary small fw-medium py-3">Cliente</th>
                                    <th class="text-secondary small fw-medium py-3">Valor</th>
                                    <th class="text-secondary small fw-medium py-3 pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ordensRecentes as $os)
                                    <tr class="border-top border-light align-middle">
                                        <td class="text-dark small py-3 ps-4 fw-medium">{{ $os->id }}</td>
                                        <td class="text-dark small py-3">
                                            {{ optional(optional($os->cliente)->pessoa)->nome_social ?? optional(optional($os->cliente)->pessoa)->nome_registro ?? '-' }}
                                        </td>
                                        <td class="text-dark small py-3 fw-medium">R$ {{ number_format($os->valor_final ?? 0, 2, ',', '.') }}</td>
                                        <td class="small py-3 pe-4">
                                            @if ($os->status === 'concluida')
                                                <span class="badge bg-success">Concluída</span>
                                            @elseif ($os->status === 'em_andamento')
                                                <span class="badge bg-primary">Em Andamento</span>
                                            @elseif ($os->status === 'cancelada')
                                                <span class="badge bg-danger">Cancelada</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pendente</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-secondary py-4">Nenhuma ordem de serviço encontrada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Alertas de Estoque -->
            <div class="col-xl-5">
                <div class="bg-white shadow-sm rounded-3 border border-light overflow-hidden h-100">
                    <div class="p-4 pb-3 d-flex justify-content-between align-items-center">
                        <h2 class="text-dark fs-5 fw-bold m-0" style="letter-spacing: -0.015em;">
                            <i class="fa-solid fa-triangle-exclamation me-2 text-danger"></i>Estoque Crítico
                        </h2>
                        <a href="{{ route('materia-prima.index') }}" class="btn btn-sm btn-outline-dark">Ver estoque</a>
                    </div>
                    @if($materiaisCriticos->count() > 0)
                        <div class="px-4 pb-4">
                            @foreach($materiaisCriticos as $mat)
                                @php
                                    $percentual = $mat->estoque_minimo > 0 ? round(($mat->estoque_atual / $mat->estoque_minimo) * 100) : 0;
                                    $corBarra = $percentual <= 25 ? 'bg-danger' : ($percentual <= 50 ? 'bg-warning' : 'bg-info');
                                @endphp
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-medium text-dark">{{ $mat->descricao }}</span>
                                        <span class="small text-secondary">{{ $mat->estoque_atual }} / {{ $mat->estoque_minimo }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar {{ $corBarra }}" role="progressbar" style="width: {{ min($percentual, 100) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-secondary py-5">
                            <i class="fa-solid fa-check-circle fs-1 text-success mb-3 d-block"></i>
                            <p class="small mb-0">Todos os materiais estão com estoque adequado.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Vencimentos Próximos -->
        <div class="px-4 pb-4">
            <div class="bg-white shadow-sm rounded-3 border border-light overflow-hidden">
                <div class="p-4 pb-3 d-flex justify-content-between align-items-center">
                    <h2 class="text-dark fs-5 fw-bold m-0" style="letter-spacing: -0.015em;">
                        <i class="fa-solid fa-bell me-2 text-warning"></i>Vencimentos Próximos
                    </h2>
                    <a href="{{ route('fluxo-caixa.index') }}" class="btn btn-sm btn-outline-dark">
                        <i class="fa-solid fa-chart-line me-1"></i>Fluxo de Caixa
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-secondary small fw-medium py-3 ps-4">Tipo</th>
                                <th class="text-secondary small fw-medium py-3">Referência</th>
                                <th class="text-secondary small fw-medium py-3">Entidade</th>
                                <th class="text-secondary small fw-medium py-3">Valor</th>
                                <th class="text-secondary small fw-medium py-3">Vencimento</th>
                                <th class="text-secondary small fw-medium py-3 pe-4">Situação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vencimentos as $venc)
                                <tr class="border-top border-light align-middle">
                                    <td class="small py-3 ps-4">
                                        <i class="fa-solid {{ $venc['icone'] }} me-1 {{ $venc['tipo'] === 'Fatura' ? 'text-primary' : 'text-secondary' }}"></i>
                                        {{ $venc['tipo'] }}
                                    </td>
                                    <td class="text-dark small py-3 fw-medium">{{ $venc['referencia'] }}</td>
                                    <td class="text-secondary small py-3">{{ $venc['entidade'] }}</td>
                                    <td class="text-dark small py-3 fw-medium">R$ {{ number_format($venc['valor'], 2, ',', '.') }}</td>
                                    <td class="text-secondary small py-3">{{ $venc['data_vencimento']->format('d/m/Y') }}</td>
                                    <td class="small py-3 pe-4">
                                        @if($venc['vencido'])
                                            <span class="badge bg-danger">Vencido</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ $venc['dias'] }} {{ $venc['dias'] == 1 ? 'dia' : 'dias' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-secondary py-4">
                                        <i class="fa-solid fa-check-circle text-success me-1"></i>
                                        Nenhum vencimento nos próximos 7 dias.
                                    </td>
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
