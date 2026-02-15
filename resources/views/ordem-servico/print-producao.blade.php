@extends('layouts.print')

@section('title')
{{ __('Order No.') }} {{ $ordemServico->id }} - {{ __('Production View') }}
@endsection

@section('doc-title')
{{ __('Order No.') }} {{ $ordemServico->id }}
<br><small class="text-muted">{{ __('Production View') }}</small>
@endsection

@section('content')
@php
    $pessoa = $ordemServico->cliente->pessoa;
@endphp

{{-- General Info --}}
<div class="mb-4">
    <div class="row">
        <div class="col-4">
            <small class="text-muted">{{ __('Customer') }}</small>
            <p class="mb-1 fw-medium">{{ $pessoa->nome_social ?? $pessoa->nome_registro ?? '-' }}</p>
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
            <small class="text-muted">{{ __('Delivery Type') }}</small>
            <p class="mb-1 fw-medium">{{ $ordemServico->tipo_entrega === 'entrega' ? __('Delivery') : __('Pickup') }}</p>
        </div>
        <div class="col-2">
            <small class="text-muted">{{ __('Quote') }}</small>
            <p class="mb-1 fw-medium">{{ $ordemServico->orcamento_id ? '#' . $ordemServico->orcamento_id : '-' }}</p>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-4">
            <small class="text-muted">{{ __('Start Date') }}</small>
            <p class="mb-1 fw-medium">{{ $ordemServico->data_inicio ? $ordemServico->data_inicio->format('d/m/Y') : '-' }}</p>
        </div>
        <div class="col-4">
            <small class="text-muted">{{ __('End Date') }}</small>
            <p class="mb-1 fw-medium">{{ $ordemServico->data_fim ? $ordemServico->data_fim->format('d/m/Y') : '-' }}</p>
        </div>
        <div class="col-4">
            <small class="text-muted">{{ __('Delivery Date') }}</small>
            <p class="mb-1 fw-medium">{{ $ordemServico->data_entrega ? $ordemServico->data_entrega->format('d/m/Y') : '-' }}</p>
        </div>
    </div>
</div>

<hr>

{{-- Services with Materials and Equipment --}}
<div class="mb-4">
    <h6 class="fw-bold border-bottom pb-2">{{ __('Services') }}</h6>
    @foreach($ordemServico->servicos as $index => $servico)
        @php
            $qtdeServico = $servico->pivot->qtde ?? 1;
            $materiais = [];
            $equipamentos = [];

            foreach ($servico->componenteServico as $componente) {
                if ($componente->tipo === 'material' && $componente->materiaprima) {
                    $qtdeComponente = $componente->pivot->qtde ?? 1;
                    $qtdeNecessaria = $qtdeComponente * $qtdeServico;
                    $materiais[] = [
                        'tipo' => $componente->materiaprima->tipoMateriaPrima->descricao ?? '-',
                        'descricao' => $componente->materiaprima->descricao,
                        'componente' => $componente->descricao,
                        'qtde' => $qtdeNecessaria,
                    ];
                } elseif ($componente->equipamentoOperacional) {
                    $equipamentos[] = [
                        'descricao' => $componente->equipamentoOperacional->descricao,
                        'componente' => $componente->descricao,
                    ];
                }
            }
        @endphp

        <div class="mb-3 p-3 border rounded">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>{{ $index + 1 }}. {{ $servico->descricao }}</strong>
                <span class="badge bg-primary">{{ __('Qty') }}: {{ $qtdeServico }}</span>
            </div>

            @if(count($materiais) > 0)
                <div class="mb-2">
                    <small class="fw-bold text-muted">{{ __('Materials Required') }}:</small>
                    <table class="table table-sm table-bordered mb-0 mt-1">
                        <thead class="table-light">
                            <tr>
                                <th class="small">{{ __('Type') }}</th>
                                <th class="small">{{ __('Raw Material') }}</th>
                                <th class="small">{{ __('Component') }}</th>
                                <th class="small text-center">{{ __('Qty Required') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materiais as $mat)
                                <tr>
                                    <td class="small">{{ $mat['tipo'] }}</td>
                                    <td class="small">{{ $mat['descricao'] }}</td>
                                    <td class="small">{{ $mat['componente'] }}</td>
                                    <td class="small text-center fw-bold">{{ $mat['qtde'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if(count($equipamentos) > 0)
                <div>
                    <small class="fw-bold text-muted">{{ __('Equipment Required') }}:</small>
                    <ul class="mb-0 mt-1 small">
                        @foreach($equipamentos as $equip)
                            <li>{{ $equip['descricao'] }} ({{ $equip['componente'] }})</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(count($materiais) === 0 && count($equipamentos) === 0)
                <p class="text-muted small mb-0">{{ __('No components registered') }}</p>
            @endif
        </div>
    @endforeach
</div>

{{-- Notes --}}
@if($ordemServico->observacoes)
<div class="mb-4">
    <h6 class="fw-bold border-bottom pb-2">{{ __('Notes') }}</h6>
    <p class="mb-0">{{ $ordemServico->observacoes }}</p>
</div>
@endif

{{-- Production Notes --}}
<div class="mb-4">
    <h6 class="fw-bold border-bottom pb-2">{{ __('Production Notes') }}</h6>
    <div class="border rounded p-3" style="min-height: 100px;">
        &nbsp;
    </div>
</div>

{{-- Signature --}}
<div class="d-flex justify-content-end mt-5 pt-4">
    <div class="signature-line">
        <small>{{ __('Responsible') }}</small>
    </div>
</div>
@endsection
