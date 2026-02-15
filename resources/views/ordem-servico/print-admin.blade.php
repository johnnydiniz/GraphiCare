@extends('layouts.print')

@section('title')
{{ __('Order No.') }} {{ $ordemServico->id }} - {{ __('Administrative View') }}
@endsection

@section('doc-title')
{{ __('Order No.') }} {{ $ordemServico->id }}
<br><small class="text-muted">{{ __('Administrative View') }}</small>
@endsection

@section('content')
@php
    $formatCurrency = function($value) {
        return 'R$ ' . number_format($value, 2, ',', '.');
    };
    $pessoa = $ordemServico->cliente->pessoa;
@endphp

{{-- General Info --}}
<div class="mb-4">
    <h6 class="fw-bold border-bottom pb-2">{{ __('Order Information') }}</h6>
    <div class="row">
        <div class="col-4">
            <small class="text-muted">{{ __('Customer') }}</small>
            <p class="mb-1 fw-medium">{{ $pessoa->nome_social ?? $pessoa->nome_registro ?? '-' }}</p>
        </div>
        <div class="col-4">
            <small class="text-muted">{{ __('SSN/EIN') }}</small>
            <p class="mb-1 fw-medium">{{ $pessoa->cpf_cnpj ?? '-' }}</p>
        </div>
        <div class="col-2">
            <small class="text-muted">{{ __('Status') }}</small>
            <p class="mb-1">
                @if($ordemServico->status === 'finalizada')
                    <span class="badge bg-success">{{ __('Finished') }}</span>
                @elseif($ordemServico->status === 'em_andamento')
                    <span class="badge bg-warning text-dark">{{ __('In Progress') }}</span>
                @else
                    <span class="badge bg-secondary">{{ __('Pending') }}</span>
                @endif
            </p>
        </div>
        <div class="col-2">
            <small class="text-muted">{{ __('Active') }}</small>
            <p class="mb-1">
                <span class="badge {{ $ordemServico->ativo ? 'bg-success' : 'bg-secondary' }}">
                    {{ $ordemServico->ativo ? __('Yes') : __('No') }}
                </span>
            </p>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-3">
            <small class="text-muted">{{ __('Delivery Type') }}</small>
            <p class="mb-1 fw-medium">{{ $ordemServico->tipo_entrega === 'entrega' ? __('Delivery') : __('Pickup') }}</p>
        </div>
        <div class="col-3">
            <small class="text-muted">{{ __('Start Date') }}</small>
            <p class="mb-1 fw-medium">{{ $ordemServico->data_inicio ? $ordemServico->data_inicio->format('d/m/Y') : '-' }}</p>
        </div>
        <div class="col-3">
            <small class="text-muted">{{ __('End Date') }}</small>
            <p class="mb-1 fw-medium">{{ $ordemServico->data_fim ? $ordemServico->data_fim->format('d/m/Y') : '-' }}</p>
        </div>
        <div class="col-3">
            <small class="text-muted">{{ __('Delivery Date') }}</small>
            <p class="mb-1 fw-medium">{{ $ordemServico->data_entrega ? $ordemServico->data_entrega->format('d/m/Y') : '-' }}</p>
        </div>
    </div>
    @if($ordemServico->orcamento_id)
    <div class="row mt-2">
        <div class="col-12">
            <small class="text-muted">{{ __('Quote') }}</small>
            <p class="mb-1 fw-medium">#{{ $ordemServico->orcamento_id }}</p>
        </div>
    </div>
    @endif
</div>

{{-- Services with Components --}}
<div class="mb-4">
    <h6 class="fw-bold border-bottom pb-2">{{ __('Services') }}</h6>
    @if($ordemServico->servicos->count() > 0)
        <table class="table table-sm table-bordered mb-0">
            <thead class="table-light">
                <tr>
                    <th class="small">#</th>
                    <th class="small">{{ __('Description') }}</th>
                    <th class="small text-center">{{ __('Qty') }}</th>
                    <th class="small text-end">{{ __('Unit Cost') }}</th>
                    <th class="small text-end">{{ __('Subtotal') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ordemServico->servicos as $index => $servico)
                    @php
                        $qtde = $servico->pivot->qtde ?? 1;
                        $custoUnitario = $servico->custo_estimado;
                        $subtotal = $custoUnitario * $qtde;
                    @endphp
                    <tr>
                        <td class="small">{{ $index + 1 }}</td>
                        <td class="small">{{ $servico->descricao }}</td>
                        <td class="small text-center">{{ $qtde }}</td>
                        <td class="small text-end">{{ $formatCurrency($custoUnitario) }}</td>
                        <td class="small text-end">{{ $formatCurrency($subtotal) }}</td>
                    </tr>
                    {{-- Components --}}
                    @if($servico->componenteServico->count() > 0)
                        <tr>
                            <td colspan="5" class="p-0 ps-4">
                                <table class="table table-sm mb-0 w-100" style="background-color: #f9f9f9; table-layout: auto;">
                                    <thead>
                                        <tr>
                                            <th class="small text-muted">{{ __('Type') }}</th>
                                            <th class="small text-muted">{{ __('Description') }}</th>
                                            <th class="small text-muted text-center">{{ __('Qty') }}</th>
                                            <th class="small text-muted text-end">{{ __('Cost') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($servico->componenteServico as $componente)
                                            <tr>
                                                <td class="small text-muted">
                                                    {{ $componente->tipo === 'material' ? __('Material') : __('Equipment') }}
                                                </td>
                                                <td class="small text-muted">
                                                    @if($componente->tipo === 'material' && $componente->materiaprima)
                                                        {{ $componente->materiaprima->tipoMateriaPrima->descricao ?? '' }} - {{ $componente->materiaprima->descricao }}
                                                    @elseif($componente->equipamentoOperacional)
                                                        {{ $componente->equipamentoOperacional->descricao }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="small text-muted text-center">{{ $componente->pivot->qtde ?? 1 }}</td>
                                                <td class="small text-muted text-end text-nowrap">{{ $formatCurrency($componente->custo_operacional ?? 0) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted">{{ __('No services registered') }}</p>
    @endif
</div>

{{-- Financial Summary --}}
<div class="mb-4">
    <h6 class="fw-bold border-bottom pb-2">{{ __('Summary') }}</h6>
    <div class="row g-3">
        <div class="col-4">
            <div class="border rounded p-2">
                <small class="text-muted d-block">{{ __('Total Cost') }}</small>
                <span class="fw-bold">{{ $formatCurrency($ordemServico->custo_final) }}</span>
            </div>
        </div>
        <div class="col-4">
            <div class="border rounded p-2">
                <small class="text-muted d-block">{{ __('Profit Rate') }}</small>
                <span class="fw-bold text-success">
                    {{ number_format($ordemServico->taxa_lucro ?? 0, 2, ',', '.') }}%
                    ({{ $formatCurrency($ordemServico->custo_final * (($ordemServico->taxa_lucro ?? 0) / 100)) }})
                </span>
            </div>
        </div>
        <div class="col-4">
            <div class="border rounded p-2">
                <small class="text-muted d-block">{{ __('Discount') }}</small>
                @php
                    $valorComLucro = $ordemServico->custo_final * (1 + (($ordemServico->taxa_lucro ?? 0) / 100));
                    $descontoValor = $valorComLucro * (($ordemServico->desconto ?? 0) / 100);
                @endphp
                <span class="fw-bold text-danger">
                    {{ number_format($ordemServico->desconto ?? 0, 2, ',', '.') }}%
                    ({{ $formatCurrency($descontoValor) }})
                </span>
            </div>
        </div>
    </div>
    <div class="mt-3 p-3 border rounded bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">{{ __('Final Value') }}</h5>
            <h4 class="fw-bold mb-0 text-primary">{{ $formatCurrency($ordemServico->valor_final) }}</h4>
        </div>
    </div>
</div>

{{-- Notes --}}
@if($ordemServico->observacoes)
<div class="mb-4">
    <h6 class="fw-bold border-bottom pb-2">{{ __('Notes') }}</h6>
    <p class="mb-0">{{ $ordemServico->observacoes }}</p>
</div>
@endif
@endsection
