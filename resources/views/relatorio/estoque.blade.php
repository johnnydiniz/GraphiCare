@extends('layouts.app')

@section('content')
<div class="gap-1 px-6 flex flex-1 justify-center py-5">
    <div class="container-fluid p-0" style="max-width: 1200px;">
        <!-- Cabeçalho -->
        <div class="d-flex flex-wrap justify-content-between align-items-center p-4">
            <h1 class="text-dark m-0 fs-2 fw-bold" style="letter-spacing: -0.01em;">
                <i class="fa-solid fa-warehouse me-2"></i>Relatório de Estoque
            </h1>
        </div>

        <!-- Filtro de Período -->
        <div class="px-4 pb-4">
            <div class="bg-white shadow-sm rounded-3 p-4 border border-light">
                <form method="GET" action="{{ route('relatorio.estoque') }}" class="row g-3 align-items-end">
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
                    <p class="text-dark mb-0 fw-medium small">Materiais Ativos</p>
                    <p class="text-primary m-0 fs-4 fw-bold">{{ $totalMateriais }}</p>
                </div>
            </div>
            <div class="col-md-6 col-xl">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-danger bg-danger bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Estoque Crítico</p>
                    <p class="text-danger m-0 fs-4 fw-bold">{{ $materiaisCriticos }}</p>
                </div>
            </div>
            <div class="col-md-4 col-xl">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-success bg-success bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Total Entradas</p>
                    <p class="text-success m-0 fs-4 fw-bold">{{ $totalEntradas }}</p>
                </div>
            </div>
            <div class="col-md-4 col-xl">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-warning bg-warning bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Total Saídas</p>
                    <p class="text-warning m-0 fs-4 fw-bold">{{ $totalSaidas }}</p>
                </div>
            </div>
            <div class="col-md-4 col-xl">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-danger bg-danger bg-opacity-10">
                    <p class="text-dark mb-0 fw-medium small">Total Perdas</p>
                    <p class="text-danger m-0 fs-4 fw-bold">{{ $totalPerdas }}</p>
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
                                <th class="text-secondary small fw-medium py-3">Material</th>
                                <th class="text-secondary small fw-medium py-3">Qtde</th>
                                <th class="text-secondary small fw-medium py-3 pe-4">Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($movimentacoes as $mov)
                                <tr class="border-top border-light align-middle">
                                    <td class="small py-3 ps-4" style="height: 60px;">
                                        @if ($mov['tipo'] === 'Entrada')
                                            <span class="badge bg-success">Entrada</span>
                                        @elseif ($mov['tipo'] === 'Saída')
                                            <span class="badge bg-warning text-dark">Saída</span>
                                        @else
                                            <span class="badge bg-danger">Perda</span>
                                        @endif
                                    </td>
                                    <td class="text-dark small py-3">{{ $mov['material'] }}</td>
                                    <td class="text-dark small py-3 fw-medium">{{ $mov['qtde'] }}</td>
                                    <td class="text-secondary small py-3 pe-4">{{ $mov['data']->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-secondary py-4">Nenhum registro encontrado no período.</td>
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
