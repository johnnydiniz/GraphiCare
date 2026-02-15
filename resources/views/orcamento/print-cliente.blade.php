@extends('layouts.print')

@section('title')
{{ __('Quote No.') }} {{ $orcamento->id }}
@endsection

@section('doc-title')
{{ __('Quote No.') }} {{ $orcamento->id }}
<br><small class="text-muted">{{ __('Issue Date') }}: {{ $orcamento->created_at ? $orcamento->created_at->format('d/m/Y') : '-' }}</small>
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
        <div class="col-6">
            <small class="text-muted">{{ __('Name') }}</small>
            <p class="mb-1 fw-medium">{{ $pessoa->nome_social ?? $pessoa->nome_registro ?? '-' }}</p>
        </div>
        <div class="col-6">
            <small class="text-muted">{{ __('SSN/EIN') }}</small>
            <p class="mb-1 fw-medium">{{ $pessoa->cpf_cnpj ?? '-' }}</p>
        </div>
    </div>
</div>

{{-- Services --}}
<div class="mb-4">
    <h6 class="fw-bold border-bottom pb-2">{{ __('Services') }}</h6>
    @if($orcamento->servicos->count() > 0)
        @php
            $servicoCount = $orcamento->servicos->count();
            $totalQtde = $orcamento->servicos->sum(function($s) { return $s->pivot->qtde ?? 1; });
        @endphp
        <table class="table table-sm table-bordered mb-0">
            <thead class="table-light">
                <tr>
                    <th class="small">#</th>
                    <th class="small">{{ __('Description') }}</th>
                    <th class="small text-center">{{ __('Qty') }}</th>
                    <th class="small text-end">{{ __('Unit Value') }}</th>
                    <th class="small text-end">{{ __('Subtotal') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orcamento->servicos as $index => $servico)
                    @php
                        $qtde = $servico->pivot->qtde ?? 1;
                        // Distribute valor_final proportionally across services based on cost weight
                        $custoServico = $servico->custo_estimado * $qtde;
                        $proporcao = $orcamento->custo_final > 0 ? $custoServico / $orcamento->custo_final : 0;
                        $valorProporcional = $orcamento->valor_final * $proporcao;
                        $valorUnitarioProporcional = $qtde > 0 ? $valorProporcional / $qtde : 0;
                    @endphp
                    <tr>
                        <td class="small">{{ $index + 1 }}</td>
                        <td class="small">{{ $servico->descricao }}</td>
                        <td class="small text-center">{{ $qtde }}</td>
                        <td class="small text-end">{{ $formatCurrency($valorUnitarioProporcional) }}</td>
                        <td class="small text-end">{{ $formatCurrency($valorProporcional) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted">{{ __('No services registered') }}</p>
    @endif
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
            <p class="mb-1 fw-medium">{{ $orcamento->validade ? $orcamento->validade->format('d/m/Y') : '-' }}</p>
        </div>
    </div>
</div>

{{-- Final Value --}}
<div class="mb-4 p-3 border rounded bg-light">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">{{ __('Final Value') }}</h5>
        <h4 class="fw-bold mb-0 text-primary">{{ $formatCurrency($orcamento->valor_final) }}</h4>
    </div>
</div>

{{-- Notes --}}
@if($orcamento->observacoes)
<div class="mb-4">
    <h6 class="fw-bold border-bottom pb-2">{{ __('Notes') }}</h6>
    <p class="mb-0">{{ $orcamento->observacoes }}</p>
</div>
@endif

{{-- Signature --}}
<div class="d-flex justify-content-between mt-5 pt-4">
    <div class="signature-line">
        <small>{{ __('Customer') }}</small>
    </div>
    <div class="signature-line">
        <small>{{ __('Responsible') }}</small>
    </div>
</div>
@endsection
