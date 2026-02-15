@extends('layouts.print')

@section('title')
{{ __('Quote No.') }} {{ $orcamento->id }} - {{ __('Administrative View') }}
@endsection

@section('doc-title')
{{ __('Quote No.') }} {{ $orcamento->id }}
<br><small class="text-muted">{{ __('Administrative View') }} | {{ __('Issue Date') }}: {{ $orcamento->created_at ? $orcamento->created_at->format('d/m/Y') : '-' }}</small>
@endsection

@section('content')
@php
    $formatCurrency = function($value) {
        return 'R$ ' . number_format($value, 2, ',', '.');
    };
    $pessoa = $orcamento->cliente->pessoa;
@endphp

{{-- Customer Info --}}
<div class="mb-4">
    <h6 class="fw-bold border-bottom pb-2">{{ __('Customer') }}</h6>
    <div class="row">
        <div class="col-4">
            <small class="text-muted">{{ __('Name') }}</small>
            <p class="mb-1 fw-medium">{{ $pessoa->nome_social ?? $pessoa->nome_registro ?? '-' }}</p>
        </div>
        <div class="col-4">
            <small class="text-muted">{{ __('SSN/EIN') }}</small>
            <p class="mb-1 fw-medium">{{ $pessoa->cpf_cnpj ?? '-' }}</p>
        </div>
        <div class="col-4">
            <small class="text-muted">{{ __('Status') }}</small>
            <p class="mb-1">
                <span class="badge {{ $orcamento->ativo ? 'bg-success' : 'bg-secondary' }}">
                    {{ $orcamento->ativo ? __('Active') : __('Inactive') }}
                </span>
            </p>
        </div>
    </div>
</div>

{{-- Services with Components --}}
<div class="mb-4">
    <h6 class="fw-bold border-bottom pb-2">{{ __('Services') }}</h6>
    @if($orcamento->servicos->count() > 0)
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
                @foreach($orcamento->servicos as $index => $servico)
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
                <span class="fw-bold">{{ $formatCurrency($orcamento->custo_final) }}</span>
            </div>
        </div>
        <div class="col-4">
            <div class="border rounded p-2">
                <small class="text-muted d-block">{{ __('Profit Rate') }}</small>
                <span class="fw-bold text-success">
                    {{ number_format($orcamento->taxa_lucro ?? 0, 2, ',', '.') }}%
                    ({{ $formatCurrency($orcamento->custo_final * (($orcamento->taxa_lucro ?? 0) / 100)) }})
                </span>
            </div>
        </div>
        <div class="col-4">
            <div class="border rounded p-2">
                <small class="text-muted d-block">{{ __('Discount') }}</small>
                @php
                    $valorComLucro = $orcamento->custo_final * (1 + (($orcamento->taxa_lucro ?? 0) / 100));
                    $descontoValor = $valorComLucro * (($orcamento->desconto ?? 0) / 100);
                @endphp
                <span class="fw-bold text-danger">
                    {{ number_format($orcamento->desconto ?? 0, 2, ',', '.') }}%
                    ({{ $formatCurrency($descontoValor) }})
                </span>
            </div>
        </div>
    </div>
    <div class="mt-3 p-3 border rounded bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">{{ __('Final Value') }}</h5>
            <h4 class="fw-bold mb-0 text-primary">{{ $formatCurrency($orcamento->valor_final) }}</h4>
        </div>
    </div>
</div>

{{-- Dates --}}
<div class="mb-4">
    <h6 class="fw-bold border-bottom pb-2">{{ __('Dates') }}</h6>
    <div class="row">
        <div class="col-4">
            <small class="text-muted">{{ __('Expected Start') }}</small>
            <p class="mb-1 fw-medium">{{ $orcamento->previsao_inicio ? $orcamento->previsao_inicio->format('d/m/Y') : '-' }}</p>
        </div>
        <div class="col-4">
            <small class="text-muted">{{ __('Expected Delivery') }}</small>
            <p class="mb-1 fw-medium">{{ $orcamento->previsao_entrega ? $orcamento->previsao_entrega->format('d/m/Y') : '-' }}</p>
        </div>
        <div class="col-4">
            <small class="text-muted">{{ __('Validity') }}</small>
            <p class="mb-1 fw-medium">
                {{ $orcamento->validade ? $orcamento->validade->format('d/m/Y') : '-' }}
                @if($orcamento->validade && $orcamento->validade->isPast())
                    <span class="badge bg-danger">{{ __('Expired') }}</span>
                @endif
            </p>
        </div>
    </div>
</div>

{{-- Orders --}}
@if($orcamento->ordensServico->count() > 0)
<div class="mb-4">
    <h6 class="fw-bold border-bottom pb-2">{{ __('Orders') }}</h6>
    <p class="mb-0">{{ $orcamento->ordensServico->count() }} {{ __('order(s) generated') }}</p>
</div>
@endif

{{-- Notes --}}
@if($orcamento->observacoes)
<div class="mb-4">
    <h6 class="fw-bold border-bottom pb-2">{{ __('Notes') }}</h6>
    <p class="mb-0">{{ $orcamento->observacoes }}</p>
</div>
@endif
@endsection
