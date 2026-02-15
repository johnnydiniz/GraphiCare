@extends('layouts.app')

@section('title')
{{ $title }}
@endsection

@section('content')
<div class="container-fluid py-5 px-4 px-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center gap-3">
                    <h1 class="text-dark m-0 fs-2 fw-bold">{{ $title }}</h1>
                    @if(isset($ordemServico) && $ordemServico->id)
                        @if($ordemServico->status === 'finalizada')
                            <span class="badge bg-success fs-6">{{ __('Finished') }}</span>
                        @elseif($ordemServico->status === 'em_andamento')
                            <span class="badge bg-warning text-dark fs-6">{{ __('In Progress') }}</span>
                        @else
                            <span class="badge bg-secondary fs-6">{{ __('Pending') }}</span>
                        @endif
                    @endif
                </div>
                @if(isset($ordemServico) && $ordemServico->id)
                    <div class="d-flex gap-2">
                        {{-- Print Dropdown --}}
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-print me-1"></i> {{ __('Print') }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('ordem-servico.print-producao', $ordemServico->id) }}" target="_blank"><i class="fa-solid fa-industry me-2"></i>{{ __('Production View') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('ordem-servico.print-admin', $ordemServico->id) }}" target="_blank"><i class="fa-solid fa-user-tie me-2"></i>{{ __('Administrative View') }}</a></li>
                            </ul>
                        </div>
                        @if($ordemServico->status === 'pendente')
                            <form action="{{ route('ordem-servico.iniciar', $ordemServico->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">
                                    <i class="fa-solid fa-play me-1"></i> {{ __('Start') }}
                                </button>
                            </form>
                        @elseif($ordemServico->status === 'em_andamento')
                            <button type="button" class="btn btn-danger" data-os-id="{{ $ordemServico->id }}" data-bs-toggle="modal" data-bs-target="#finalizarModal">
                                <i class="fa-solid fa-flag-checkered me-1"></i> {{ __('Finish') }}
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ is_array($route) ? route($route[0], $route[1]) : route($route) }}" id="ordem-servico-form">
                @csrf
                @if(is_array($route))
                    @method('PUT')
                @endif

                {{-- Order Information --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">{{ __('Order Information') }}</h5>

                        @if(isset($ordemServico) && $ordemServico->id)
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="ativo" name="ativo" value="1" {{ $ordemServico->ativo ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativo">{{ __('Active') }}</label>
                                </div>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="cliente_id" class="form-label fw-medium">{{ __('Customer') }} <span class="text-danger">*</span></label>
                                <select class="form-select py-2 @error('cliente_id') is-invalid @enderror" id="cliente_id" name="cliente_id" required>
                                    <option value="">{{ __('Select a customer...') }}</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('cliente_id', $ordemServico->cliente_id ?? ($orcamentoSelecionado->cliente_id ?? '')) == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->pessoa->nome_social ?? $cliente->pessoa->nome_registro }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cliente_id')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="orcamento_id" class="form-label fw-medium">{{ __('Quote') }}</label>
                                <select class="form-select py-2 @error('orcamento_id') is-invalid @enderror" id="orcamento_id" name="orcamento_id">
                                    <option value="">{{ __('None (independent order)') }}</option>
                                    @foreach($orcamentos as $orc)
                                        <option value="{{ $orc->id }}" {{ old('orcamento_id', $ordemServico->orcamento_id ?? ($orcamentoSelecionado->id ?? '')) == $orc->id ? 'selected' : '' }}>
                                            #{{ $orc->id }} - {{ $orc->cliente->pessoa->nome_social ?? $orc->cliente->pessoa->nome_registro }}{{ $orc->validade && $orc->validade->isPast() ? ' (' . __('Expired') . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('orcamento_id')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-medium">{{ __('Delivery Type') }} <span class="text-danger">*</span></label>
                                <div class="d-flex gap-4 mt-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipo_entrega" id="tipo_retirada" value="retirada" {{ old('tipo_entrega', $ordemServico->tipo_entrega ?? 'retirada') == 'retirada' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tipo_retirada">{{ __('Pickup') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipo_entrega" id="tipo_entrega_radio" value="entrega" {{ old('tipo_entrega', $ordemServico->tipo_entrega ?? '') == 'entrega' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tipo_entrega_radio">{{ __('Delivery') }}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-md-4">
                                <label for="taxa_lucro" class="form-label fw-medium">{{ __('Profit Rate') }} (%)</label>
                                <input type="number" class="form-control py-2 @error('taxa_lucro') is-invalid @enderror" id="taxa_lucro" name="taxa_lucro" value="{{ old('taxa_lucro', $ordemServico->taxa_lucro ?? ($orcamentoSelecionado->taxa_lucro ?? 0)) }}" step="0.01" min="0">
                                @error('taxa_lucro')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-6 col-md-4">
                                <label for="desconto" class="form-label fw-medium">{{ __('Discount') }} (%)</label>
                                <input type="number" class="form-control py-2 @error('desconto') is-invalid @enderror" id="desconto" name="desconto" value="{{ old('desconto', $ordemServico->desconto ?? ($orcamentoSelecionado->desconto ?? 0)) }}" step="0.01" min="0" max="100">
                                @error('desconto')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-12 col-md-4">
                                <label for="data_inicio" class="form-label fw-medium">{{ __('Start Date') }}</label>
                                <input type="date" class="form-control py-2 @error('data_inicio') is-invalid @enderror" id="data_inicio" name="data_inicio" value="{{ old('data_inicio', isset($ordemServico->data_inicio) ? $ordemServico->data_inicio->format('Y-m-d') : ($defaultDates['data_inicio'] ?? '')) }}">
                                @error('data_inicio')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="data_fim" class="form-label fw-medium">{{ __('End Date') }}</label>
                                <input type="date" class="form-control py-2 @error('data_fim') is-invalid @enderror" id="data_fim" name="data_fim" value="{{ old('data_fim', isset($ordemServico->data_fim) ? $ordemServico->data_fim->format('Y-m-d') : '') }}">
                                @error('data_fim')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="data_entrega" class="form-label fw-medium">{{ __('Delivery Date') }}</label>
                                <input type="date" class="form-control py-2 @error('data_entrega') is-invalid @enderror" id="data_entrega" name="data_entrega" value="{{ old('data_entrega', isset($ordemServico->data_entrega) ? $ordemServico->data_entrega->format('Y-m-d') : '') }}">
                                @error('data_entrega')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-12">
                                <label for="observacoes" class="form-label fw-medium">{{ __('Notes') }}</label>
                                <textarea class="form-control py-2 @error('observacoes') is-invalid @enderror" id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $ordemServico->observacoes ?? ($orcamentoSelecionado->observacoes ?? '')) }}</textarea>
                                @error('observacoes')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Services --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">{{ __('Services') }}</h5>

                        {{-- Orcamento services info --}}
                        <div id="orcamento-servicos-info" class="alert alert-info d-none mb-3">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            {{ __('Services loaded from quote. Check/uncheck to select which services to include in this order.') }}
                        </div>

                        {{-- Orcamento expired warning --}}
                        <div id="orcamento-expirado-alert" class="alert alert-warning d-none mb-3">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            <strong>{{ __('Expired quote!') }}</strong> <span id="orcamento-expirado-msg"></span>
                        </div>

                        {{-- Header row --}}
                        <div class="row g-2 mb-2 d-none d-md-flex" id="servicos-header">
                            <div class="col-md-5">
                                <span class="form-label fw-medium small">{{ __('Service') }}</span>
                            </div>
                            <div class="col-md-2">
                                <span class="form-label fw-medium small">{{ __('Unit Cost') }}</span>
                            </div>
                            <div class="col-md-2">
                                <span class="form-label fw-medium small">{{ __('Quantity') }}</span>
                            </div>
                            <div class="col-md-2">
                                <span class="form-label fw-medium small">{{ __('Subtotal') }}</span>
                            </div>
                            <div class="col-md-1"></div>
                        </div>

                        <div id="servicos-container">
                            @if(isset($ordemServico) && $ordemServico->servicos->count() > 0)
                                @foreach($ordemServico->servicos as $index => $servico)
                                    <div class="servico-row mb-2" data-index="{{ $index }}">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-12 col-md-5">
                                                <span class="form-label fw-medium small d-md-none">{{ __('Service') }}</span>
                                                <div class="d-flex align-items-center gap-2">
                                                    <select class="form-select py-2 servico-select flex-grow-1" name="servicos[{{ $index }}][id]" required>
                                                        <option value="{{ $servico->id }}" data-custo="{{ $servico->custo_estimado }}" selected>{{ $servico->descricao }}</option>
                                                    </select>
                                                    <span class="stock-alert text-warning d-none" title="{{ __('Insufficient stock for some materials') }}">
                                                        <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-4 col-md-2">
                                                <span class="form-label fw-medium small d-md-none">{{ __('Unit Cost') }}</span>
                                                <input type="text" class="form-control py-2 custo-unitario-display bg-light" value="R$ {{ number_format($servico->custo_estimado, 2, ',', '.') }}" readonly>
                                            </div>
                                            <div class="col-4 col-md-2">
                                                <span class="form-label fw-medium small d-md-none">{{ __('Quantity') }}</span>
                                                <input type="number" class="form-control py-2 qtde-input" name="servicos[{{ $index }}][qtde]" min="1" value="{{ $servico->pivot->qtde ?? 1 }}" required>
                                            </div>
                                            <div class="col-4 col-md-2">
                                                <span class="form-label fw-medium small d-md-none">{{ __('Subtotal') }}</span>
                                                <input type="text" class="form-control py-2 subtotal-display bg-light" value="R$ 0,00" readonly>
                                            </div>
                                            <div class="col-12 col-md-1">
                                                <button type="button" class="btn btn-danger w-100 py-2 remove-servico" {{ $index == 0 ? 'disabled' : '' }}>
                                                    <i class="fa-solid fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="stock-alert-details small text-warning mt-1 d-none"></div>
                                    </div>
                                @endforeach
                            @else
                                <div class="servico-row mb-2" data-index="0">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-12 col-md-5">
                                            <span class="form-label fw-medium small d-md-none">{{ __('Service') }}</span>
                                            <div class="d-flex align-items-center gap-2">
                                                <select class="form-select py-2 servico-select flex-grow-1" name="servicos[0][id]" required>
                                                    <option value="">{{ __('Select a service...') }}</option>
                                                </select>
                                                <span class="stock-alert text-warning d-none" title="{{ __('Insufficient stock for some materials') }}">
                                                    <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-4 col-md-2">
                                            <span class="form-label fw-medium small d-md-none">{{ __('Unit Cost') }}</span>
                                            <input type="text" class="form-control py-2 custo-unitario-display bg-light" value="R$ 0,00" readonly>
                                        </div>
                                        <div class="col-4 col-md-2">
                                            <span class="form-label fw-medium small d-md-none">{{ __('Quantity') }}</span>
                                            <input type="number" class="form-control py-2 qtde-input" name="servicos[0][qtde]" min="1" value="1" required>
                                        </div>
                                        <div class="col-4 col-md-2">
                                            <span class="form-label fw-medium small d-md-none">{{ __('Subtotal') }}</span>
                                            <input type="text" class="form-control py-2 subtotal-display bg-light" value="R$ 0,00" readonly>
                                        </div>
                                        <div class="col-12 col-md-1">
                                            <button type="button" class="btn btn-danger w-100 py-2 remove-servico" disabled>
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="stock-alert-details small text-warning mt-1 d-none"></div>
                                </div>
                            @endif
                        </div>

                        {{-- Orcamento services container (checkbox mode) --}}
                        <div id="orcamento-servicos-container" class="d-none"></div>

                        <div class="d-flex justify-content-end mb-3" id="add-servico-wrapper">
                            <button type="button" class="btn btn-success px-4" id="add-servico">
                                <i class="fa-solid fa-plus me-2"></i>{{ __('Add Service') }}
                            </button>
                        </div>

                        @error('servicos')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Summary --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">{{ __('Summary') }}</h5>

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <div class="border rounded-3 p-3 h-100 bg-light">
                                    <small class="text-muted d-block mb-1">{{ __('Total Cost') }}</small>
                                    <span class="fw-bold fs-5" id="custo-total">R$ 0,00</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="border rounded-3 p-3 h-100 bg-light">
                                    <small class="text-muted d-block mb-1">{{ __('Profit') }} (<span id="taxa-lucro-display">0</span>%)</small>
                                    <span class="fw-bold fs-5 text-success" id="lucro-valor">+ R$ 0,00</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="border rounded-3 p-3 h-100 bg-light">
                                    <small class="text-muted d-block mb-1">{{ __('Discount') }} (<span id="desconto-display">0</span>%)</small>
                                    <span class="fw-bold fs-5 text-danger" id="desconto-valor">- R$ 0,00</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">{{ __('Final Value') }}</h5>
                            <h3 class="text-primary fw-bold mb-0" id="valor-final">R$ 0,00</h3>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="{{ route('ordem-servico.index') }}" class="btn btn-light px-4 py-2">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary px-4 py-2">{{ $btn_label }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(isset($ordemServico) && $ordemServico->id && $ordemServico->status === 'em_andamento')
{{-- Finalization Modal --}}
<div class="modal fade" id="finalizarModal" tabindex="-1" aria-labelledby="finalizarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="finalizarForm" method="POST" action="{{ route('ordem-servico.finalizar', $ordemServico->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="finalizarModalLabel">{{ __('Finish Order') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        {{ __('Review the materials used in this order. If there was any loss or breakage, enter the quantity in the corresponding field.') }}
                    </div>

                    <div id="finalizarLoading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">{{ __('Loading...') }}</span>
                        </div>
                        <p class="mt-2 text-muted">{{ __('Loading...') }}</p>
                    </div>

                    <div id="finalizarContent" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="small">{{ __('Component') }}</th>
                                        <th class="small">{{ __('Raw Material') }}</th>
                                        <th class="small text-center">{{ __('Current Stock') }}</th>
                                        <th class="small text-center">{{ __('Qty Used') }}</th>
                                        <th class="small text-center" style="width: 130px;">{{ __('Loss/Breakage') }}</th>
                                        <th class="small text-center">{{ __('Total Deduction') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="finalizarComponentes">
                                </tbody>
                            </table>
                        </div>
                        <div id="finalizarSemMateriais" class="text-muted text-center py-3" style="display: none;">
                            {{ __('No material components in this order.') }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger" id="btnConfirmarFinalizar">
                        <i class="fa-solid fa-flag-checkered me-1"></i> {{ __('Finish Order') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
window.addEventListener('load', function() {
    var servicoIndex = {{ isset($ordemServico) ? $ordemServico->servicos->count() : 1 }};
    var servicosUrl = '{{ route("ordem-servico.servicos") }}';
    var orcamentoServicosBaseUrl = '{{ url("/ordem-servico/orcamento-servicos") }}';
    var servicosData = [];
    var isOrcamentoMode = false;

    function formatCurrency(value) {
        return 'R$ ' + parseFloat(value).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function parseCurrency(value) {
        if (typeof value === 'number') return value;
        return parseFloat(value.replace(/[R$\s.]/g, '').replace(',', '.')) || 0;
    }

    function loadServicos() {
        return fetch(servicosUrl)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                servicosData = data;
                return data;
            });
    }

    function populateServicoSelect(select, selectedId) {
        var options = '<option value="">{{ __("Select a service...") }}</option>';
        servicosData.forEach(function(item) {
            var isSelected = item.id == selectedId ? 'selected' : '';
            var materiaisJson = JSON.stringify(item.materiais || []).replace(/"/g, '&quot;');
            options += '<option value="' + item.id + '" data-custo="' + item.custo_estimado + '" data-materiais="' + materiaisJson + '" ' + isSelected + '>' + item.descricao + '</option>';
        });
        select.innerHTML = options;
    }

    function checkStockAvailability(row) {
        var servicoSelect = row.querySelector('.servico-select');
        var qtdeInput = row.querySelector('.qtde-input');
        var stockAlert = row.querySelector('.stock-alert');
        var stockAlertDetails = row.querySelector('.stock-alert-details');

        var selected = servicoSelect.options[servicoSelect.selectedIndex];
        var qtde = parseInt(qtdeInput.value) || 1;

        stockAlert.classList.add('d-none');
        stockAlertDetails.classList.add('d-none');
        stockAlertDetails.innerHTML = '';

        if (!selected || !selected.value) return;

        var materiaisJson = selected.getAttribute('data-materiais');
        if (!materiaisJson) return;

        try {
            var materiais = JSON.parse(materiaisJson.replace(/&quot;/g, '"'));
            var alertMessages = [];

            materiais.forEach(function(mat) {
                var qtdeNecessaria = mat.qtde_por_servico * qtde;
                if (qtdeNecessaria > mat.estoque_atual) {
                    alertMessages.push(mat.descricao + ': {{ __("needs") }} ' + qtdeNecessaria + ', {{ __("available") }} ' + mat.estoque_atual);
                }
            });

            if (alertMessages.length > 0) {
                stockAlert.classList.remove('d-none');
                stockAlertDetails.classList.remove('d-none');
                stockAlertDetails.innerHTML = '<i class="fa-solid fa-exclamation-circle me-1"></i>' + alertMessages.join('<br><i class="fa-solid fa-exclamation-circle me-1"></i>');
            }
        } catch (e) {
            console.error('Error parsing materiais:', e);
        }
    }

    function updateRowSubtotal(row) {
        var servicoSelect = row.querySelector('.servico-select');
        var qtdeInput = row.querySelector('.qtde-input');
        var custoUnitarioDisplay = row.querySelector('.custo-unitario-display');
        var subtotalDisplay = row.querySelector('.subtotal-display');

        var selected = servicoSelect.options[servicoSelect.selectedIndex];
        var custoUnitario = selected && selected.dataset.custo ? parseFloat(selected.dataset.custo) : 0;
        var qtde = parseInt(qtdeInput.value) || 1;

        custoUnitarioDisplay.value = formatCurrency(custoUnitario);

        var subtotal = custoUnitario * qtde;
        subtotalDisplay.value = formatCurrency(subtotal);
        subtotalDisplay.dataset.subtotal = subtotal;

        checkStockAvailability(row);
    }

    function updateTotals() {
        var custoTotal = 0;

        if (isOrcamentoMode) {
            // Sum from orcamento checkbox rows
            document.querySelectorAll('#orcamento-servicos-container .orcamento-servico-row').forEach(function(row) {
                var checkbox = row.querySelector('.orcamento-servico-check');
                if (checkbox && checkbox.checked) {
                    var subtotalDisplay = row.querySelector('.subtotal-display');
                    custoTotal += parseFloat(subtotalDisplay.dataset.subtotal) || 0;
                }
            });
        } else {
            // Sum from normal rows
            document.querySelectorAll('#servicos-container .servico-row').forEach(function(row) {
                var subtotalDisplay = row.querySelector('.subtotal-display');
                custoTotal += parseFloat(subtotalDisplay.dataset.subtotal) || 0;
            });
        }

        var taxaLucro = parseFloat(document.getElementById('taxa_lucro').value) || 0;
        var desconto = parseFloat(document.getElementById('desconto').value) || 0;

        var lucroValor = custoTotal * (taxaLucro / 100);
        var valorComLucro = custoTotal + lucroValor;
        var descontoValor = valorComLucro * (desconto / 100);
        var valorFinal = valorComLucro - descontoValor;

        document.getElementById('custo-total').textContent = formatCurrency(custoTotal);
        document.getElementById('taxa-lucro-display').textContent = taxaLucro.toFixed(2);
        document.getElementById('lucro-valor').textContent = '+ ' + formatCurrency(lucroValor);
        document.getElementById('desconto-display').textContent = desconto.toFixed(2);
        document.getElementById('desconto-valor').textContent = '- ' + formatCurrency(descontoValor);
        document.getElementById('valor-final').textContent = formatCurrency(valorFinal);
    }

    function bindRowEvents(row) {
        var servicoSelect = row.querySelector('.servico-select');
        var qtdeInput = row.querySelector('.qtde-input');
        var removeBtn = row.querySelector('.remove-servico');

        servicoSelect.addEventListener('change', function() {
            updateRowSubtotal(row);
            updateTotals();
        });

        qtdeInput.addEventListener('input', function() {
            if (this.value < 1) this.value = 1;
            updateRowSubtotal(row);
            updateTotals();
        });

        removeBtn.addEventListener('click', function() {
            row.remove();
            updateRemoveButtons();
            updateTotals();
        });
    }

    function updateRemoveButtons() {
        var rows = document.querySelectorAll('#servicos-container .servico-row');
        rows.forEach(function(row) {
            var removeBtn = row.querySelector('.remove-servico');
            removeBtn.disabled = rows.length === 1;
        });
    }

    function addServicoRow() {
        var container = document.getElementById('servicos-container');
        var newRow = document.createElement('div');
        newRow.className = 'servico-row mb-2';
        newRow.dataset.index = servicoIndex;

        newRow.innerHTML = `
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <span class="form-label fw-medium small d-md-none">{{ __('Service') }}</span>
                    <div class="d-flex align-items-center gap-2">
                        <select class="form-select py-2 servico-select flex-grow-1" name="servicos[${servicoIndex}][id]" required>
                            <option value="">{{ __('Select a service...') }}</option>
                        </select>
                        <span class="stock-alert text-warning d-none" title="{{ __('Insufficient stock for some materials') }}">
                            <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                        </span>
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <span class="form-label fw-medium small d-md-none">{{ __('Unit Cost') }}</span>
                    <input type="text" class="form-control py-2 custo-unitario-display bg-light" value="R$ 0,00" readonly>
                </div>
                <div class="col-4 col-md-2">
                    <span class="form-label fw-medium small d-md-none">{{ __('Quantity') }}</span>
                    <input type="number" class="form-control py-2 qtde-input" name="servicos[${servicoIndex}][qtde]" min="1" value="1" required>
                </div>
                <div class="col-4 col-md-2">
                    <span class="form-label fw-medium small d-md-none">{{ __('Subtotal') }}</span>
                    <input type="text" class="form-control py-2 subtotal-display bg-light" value="R$ 0,00" data-subtotal="0" readonly>
                </div>
                <div class="col-12 col-md-1">
                    <button type="button" class="btn btn-danger w-100 py-2 remove-servico">
                        <i class="fa-solid fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="stock-alert-details small text-warning mt-1 d-none"></div>
        `;

        container.appendChild(newRow);
        populateServicoSelect(newRow.querySelector('.servico-select'), null);
        bindRowEvents(newRow);
        updateRemoveButtons();
        servicoIndex++;
    }

    // Switch to orcamento mode (checkbox-based service selection)
    function switchToOrcamentoMode(orcamentoData) {
        isOrcamentoMode = true;

        // Hide normal services UI
        document.getElementById('servicos-container').classList.add('d-none');
        document.getElementById('servicos-header').classList.add('d-none');
        document.getElementById('add-servico-wrapper').classList.add('d-none');
        document.getElementById('orcamento-servicos-info').classList.remove('d-none');

        // Show/hide expired quote warning
        var expiradoAlert = document.getElementById('orcamento-expirado-alert');
        var expiradoMsg = document.getElementById('orcamento-expirado-msg');
        if (orcamentoData.expirado) {
            expiradoAlert.classList.remove('d-none');
            expiradoMsg.textContent = '{{ __("This quote expired on") }} ' + orcamentoData.validade + '.';
        } else {
            expiradoAlert.classList.add('d-none');
        }

        // Disable required on hidden normal selects
        document.querySelectorAll('#servicos-container .servico-select').forEach(function(sel) {
            sel.removeAttribute('required');
        });
        document.querySelectorAll('#servicos-container .qtde-input').forEach(function(inp) {
            inp.removeAttribute('required');
        });

        // Show orcamento services container
        var orcContainer = document.getElementById('orcamento-servicos-container');
        orcContainer.classList.remove('d-none');
        orcContainer.innerHTML = '';

        // Header
        var header = document.createElement('div');
        header.className = 'row g-2 mb-2 d-none d-md-flex';
        header.innerHTML = '<div class="col-md-1"><span class="form-label fw-medium small">{{ __("Select") }}</span></div>' +
            '<div class="col-md-3"><span class="form-label fw-medium small">{{ __("Service") }}</span></div>' +
            '<div class="col-md-2"><span class="form-label fw-medium small">{{ __("Unit Cost") }}</span></div>' +
            '<div class="col-md-2"><span class="form-label fw-medium small">{{ __("Quantity") }}</span></div>' +
            '<div class="col-md-2"><span class="form-label fw-medium small">{{ __("Subtotal") }}</span></div>' +
            '<div class="col-md-2"><span class="form-label fw-medium small">{{ __("Status") }}</span></div>';
        orcContainer.appendChild(header);

        // Build rows from orcamento services
        var enabledIndex = 0;
        orcamentoData.servicos.forEach(function(servico, index) {
            var row = document.createElement('div');
            row.className = 'orcamento-servico-row mb-2';
            row.dataset.index = index;

            var isEsgotado = servico.esgotado;
            var qtdeValue = isEsgotado ? 0 : servico.qtde;
            var subtotal = servico.custo_estimado * qtdeValue;

            var materiaisJson = JSON.stringify(servico.materiais || []).replace(/"/g, '&quot;');

            // Status badge
            var statusHtml = '';
            if (isEsgotado) {
                statusHtml = '<span class="badge bg-secondary">{{ __("Consumed") }}</span>';
            } else if (servico.qtde_consumida > 0) {
                statusHtml = '<span class="badge bg-info">{{ __("Remaining") }}: ' + servico.qtde_remanescente + '/' + servico.qtde_orcamento + '</span>';
            } else {
                statusHtml = '<span class="badge bg-success">{{ __("Available") }}</span>';
            }

            row.innerHTML = '<div class="row g-2 align-items-center">' +
                '<div class="col-12 col-md-1">' +
                    '<div class="form-check d-flex justify-content-center">' +
                        '<input class="form-check-input orcamento-servico-check" type="checkbox" ' + (isEsgotado ? 'disabled' : 'checked') + ' data-servico-id="' + servico.id + '">' +
                    '</div>' +
                '</div>' +
                '<div class="col-12 col-md-3">' +
                    '<span class="form-label fw-medium small d-md-none">{{ __("Service") }}</span>' +
                    '<div class="d-flex align-items-center gap-2">' +
                        '<input type="text" class="form-control py-2 bg-light" value="' + servico.descricao + '" readonly>' +
                        '<input type="hidden" class="servico-id-input" name="servicos[' + index + '][id]" value="' + servico.id + '"' + (isEsgotado ? ' disabled' : '') + '>' +
                        '<span class="stock-alert text-warning d-none" title="{{ __("Insufficient stock for some materials") }}">' +
                            '<i class="fa-solid fa-triangle-exclamation fs-5"></i>' +
                        '</span>' +
                    '</div>' +
                '</div>' +
                '<div class="col-4 col-md-2">' +
                    '<span class="form-label fw-medium small d-md-none">{{ __("Unit Cost") }}</span>' +
                    '<input type="text" class="form-control py-2 custo-unitario-display bg-light" value="' + formatCurrency(servico.custo_estimado) + '" readonly data-custo="' + servico.custo_estimado + '">' +
                '</div>' +
                '<div class="col-4 col-md-2">' +
                    '<span class="form-label fw-medium small d-md-none">{{ __("Quantity") }}</span>' +
                    '<input type="number" class="form-control py-2 qtde-input" name="servicos[' + index + '][qtde]" min="1" value="' + (qtdeValue || 1) + '"' + (isEsgotado ? ' disabled' : '') + '>' +
                '</div>' +
                '<div class="col-4 col-md-2">' +
                    '<span class="form-label fw-medium small d-md-none">{{ __("Subtotal") }}</span>' +
                    '<input type="text" class="form-control py-2 subtotal-display bg-light" value="' + formatCurrency(subtotal) + '" data-subtotal="' + subtotal + '" readonly>' +
                '</div>' +
                '<div class="col-12 col-md-2">' +
                    '<span class="form-label fw-medium small d-md-none">{{ __("Status") }}</span>' +
                    statusHtml +
                '</div>' +
            '</div>' +
            '<div class="stock-alert-details small text-warning mt-1 d-none" data-materiais="' + materiaisJson + '" data-qtde-por-servico=""></div>';

            if (isEsgotado) {
                row.style.opacity = '0.5';
            }

            orcContainer.appendChild(row);

            // Bind events for this row
            var checkbox = row.querySelector('.orcamento-servico-check');
            var qtdeInput = row.querySelector('.qtde-input');
            var idInput = row.querySelector('.servico-id-input');

            if (!isEsgotado) {
                checkbox.addEventListener('change', function() {
                    if (!this.checked) {
                        idInput.disabled = true;
                        qtdeInput.disabled = true;
                        row.style.opacity = '0.5';
                    } else {
                        idInput.disabled = false;
                        qtdeInput.disabled = false;
                        row.style.opacity = '1';
                    }
                    updateTotals();
                });

                qtdeInput.addEventListener('input', function() {
                    if (this.value < 1) this.value = 1;
                    var custo = servico.custo_estimado;
                    var qtde = parseInt(this.value) || 1;
                    var sub = custo * qtde;
                    var subtotalEl = row.querySelector('.subtotal-display');
                    subtotalEl.value = formatCurrency(sub);
                    subtotalEl.dataset.subtotal = sub;

                    // Check stock
                    checkOrcamentoRowStock(row, servico.materiais, qtde);
                    updateTotals();
                });

                // Initial stock check
                checkOrcamentoRowStock(row, servico.materiais, qtdeValue);
            }
        });

        // Update cliente and other fields from orcamento data
        document.getElementById('cliente_id').value = orcamentoData.cliente_id;
        document.getElementById('taxa_lucro').value = orcamentoData.taxa_lucro;
        document.getElementById('desconto').value = orcamentoData.desconto;
        if (orcamentoData.observacoes) {
            document.getElementById('observacoes').value = orcamentoData.observacoes;
        }

        updateTotals();
    }

    function checkOrcamentoRowStock(row, materiais, qtde) {
        var stockAlert = row.querySelector('.stock-alert');
        var stockAlertDetails = row.querySelector('.stock-alert-details');

        stockAlert.classList.add('d-none');
        stockAlertDetails.classList.add('d-none');
        stockAlertDetails.innerHTML = '';

        if (!materiais || materiais.length === 0) return;

        var alertMessages = [];
        materiais.forEach(function(mat) {
            var qtdeNecessaria = mat.qtde_por_servico * qtde;
            if (qtdeNecessaria > mat.estoque_atual) {
                alertMessages.push(mat.descricao + ': {{ __("needs") }} ' + qtdeNecessaria + ', {{ __("available") }} ' + mat.estoque_atual);
            }
        });

        if (alertMessages.length > 0) {
            stockAlert.classList.remove('d-none');
            stockAlertDetails.classList.remove('d-none');
            stockAlertDetails.innerHTML = '<i class="fa-solid fa-exclamation-circle me-1"></i>' + alertMessages.join('<br><i class="fa-solid fa-exclamation-circle me-1"></i>');
        }
    }

    // Switch back to normal mode
    function switchToNormalMode() {
        isOrcamentoMode = false;

        document.getElementById('servicos-container').classList.remove('d-none');
        document.getElementById('servicos-header').classList.remove('d-none');
        document.getElementById('add-servico-wrapper').classList.remove('d-none');
        document.getElementById('orcamento-servicos-info').classList.add('d-none');
        document.getElementById('orcamento-expirado-alert').classList.add('d-none');

        // Re-enable required on normal selects
        document.querySelectorAll('#servicos-container .servico-select').forEach(function(sel) {
            sel.setAttribute('required', 'required');
        });
        document.querySelectorAll('#servicos-container .qtde-input').forEach(function(inp) {
            inp.setAttribute('required', 'required');
        });

        var orcContainer = document.getElementById('orcamento-servicos-container');
        orcContainer.classList.add('d-none');
        orcContainer.innerHTML = '';

        updateTotals();
    }

    // Handle orcamento selection change
    function onOrcamentoChange() {
        var orcamentoId = document.getElementById('orcamento_id').value;

        if (!orcamentoId) {
            switchToNormalMode();
            return;
        }

        fetch(orcamentoServicosBaseUrl + '/' + orcamentoId)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                switchToOrcamentoMode(data);
            })
            .catch(function(error) {
                console.error('Error loading orcamento services:', error);
                switchToNormalMode();
            });
    }

    // Load services and initialize
    loadServicos().then(function() {
        var orcamentoSelect = document.getElementById('orcamento_id');
        var initialOrcamentoId = orcamentoSelect.value;

        if (initialOrcamentoId && !{{ isset($ordemServico) ? 'true' : 'false' }}) {
            // Coming from orcamento - load in orcamento mode
            onOrcamentoChange();
        } else {
            // Normal mode - populate selects
            document.querySelectorAll('#servicos-container .servico-row').forEach(function(row) {
                var servicoSelect = row.querySelector('.servico-select');
                var selectedId = servicoSelect.value;
                populateServicoSelect(servicoSelect, selectedId);
                bindRowEvents(row);
                updateRowSubtotal(row);
            });
            updateTotals();
        }
    });

    // Orcamento select change handler
    document.getElementById('orcamento_id').addEventListener('change', onOrcamentoChange);

    // Add service button
    document.getElementById('add-servico').addEventListener('click', addServicoRow);

    // Update totals when taxa_lucro or desconto change
    document.getElementById('taxa_lucro').addEventListener('input', updateTotals);
    document.getElementById('desconto').addEventListener('input', updateTotals);

    // Finalization modal (edit page)
    var finalizarModal = document.getElementById('finalizarModal');
    if (finalizarModal) {
        finalizarModal.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) return;

            var osId = trigger.getAttribute('data-os-id');
            if (!osId) return;

            document.getElementById('finalizarLoading').style.display = 'block';
            document.getElementById('finalizarContent').style.display = 'none';

            fetch('/ordem-servico/componentes/' + osId)
                .then(function (response) { return response.json(); })
                .then(function (componentes) {
                    document.getElementById('finalizarLoading').style.display = 'none';
                    document.getElementById('finalizarContent').style.display = 'block';

                    var tbody = document.getElementById('finalizarComponentes');
                    tbody.innerHTML = '';

                    var semMateriais = document.getElementById('finalizarSemMateriais');

                    if (componentes.length === 0) {
                        semMateriais.style.display = 'block';
                        tbody.parentElement.parentElement.style.display = 'none';
                    } else {
                        semMateriais.style.display = 'none';
                        tbody.parentElement.parentElement.style.display = 'block';

                        componentes.forEach(function (comp) {
                            var tr = document.createElement('tr');
                            var estoqueInsuficiente = comp.estoque_atual < comp.qtde_usada;

                            tr.innerHTML = '<td class="small">' + comp.descricao + '</td>' +
                                '<td class="small">' + comp.materia_prima + '</td>' +
                                '<td class="small text-center">' +
                                    comp.estoque_atual +
                                    (estoqueInsuficiente ? ' <i class="fa-solid fa-triangle-exclamation text-danger" title="{{ __("Insufficient stock") }}"></i>' : '') +
                                '</td>' +
                                '<td class="small text-center fw-medium">' + comp.qtde_usada + '</td>' +
                                '<td class="small text-center">' +
                                    '<input type="number" name="perdas[' + comp.componente_id + ']" ' +
                                    'class="form-control form-control-sm text-center perda-input" ' +
                                    'min="0" value="0" style="width: 80px; margin: 0 auto;">' +
                                '</td>' +
                                '<td class="small text-center fw-bold total-deducao">' + comp.qtde_usada + '</td>';
                            tbody.appendChild(tr);

                            var input = tr.querySelector('.perda-input');
                            var totalCell = tr.querySelector('.total-deducao');
                            input.addEventListener('input', function () {
                                var perda = parseInt(this.value) || 0;
                                if (perda < 0) { perda = 0; this.value = 0; }
                                totalCell.textContent = comp.qtde_usada + perda;
                            });
                        });
                    }
                })
                .catch(function () {
                    document.getElementById('finalizarLoading').innerHTML = '<p class="text-danger">{{ __("Error loading components") }}</p>';
                });
        });
    }
});
</script>
@endpush
