@extends('layouts.app')

@section('title')
{{ $title }}
@endsection

@section('content')
<div class="container-fluid py-5 px-4 px-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="d-flex flex-wrap justify-content-between mb-4">
                <h1 class="text-dark m-0 fs-2 fw-bold">{{ $title }}</h1>
            </div>

            <form method="POST" action="{{ is_array($route) ? route($route[0], $route[1]) : route($route) }}" id="orcamento-form">
                @csrf
                @if(is_array($route))
                    @method('PUT')
                @endif

                {{-- Quote Information --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">{{ __('Quote Information') }}</h5>

                        @if(isset($orcamento) && $orcamento->id)
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="ativo" name="ativo" value="1" {{ $orcamento->ativo ? 'checked' : '' }}>
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
                                        <option value="{{ $cliente->id }}" {{ old('cliente_id', $orcamento->cliente_id ?? '') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->pessoa->nome_social ?? $cliente->pessoa->nome_registro }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cliente_id')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-6 col-md-3">
                                <label for="taxa_lucro" class="form-label fw-medium">{{ __('Profit Rate') }} (%)</label>
                                <input type="number" class="form-control py-2 @error('taxa_lucro') is-invalid @enderror" id="taxa_lucro" name="taxa_lucro" value="{{ old('taxa_lucro', $orcamento->taxa_lucro ?? 0) }}" step="0.01" min="0">
                                @error('taxa_lucro')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-6 col-md-3">
                                <label for="desconto" class="form-label fw-medium">{{ __('Discount') }} (%)</label>
                                <input type="number" class="form-control py-2 @error('desconto') is-invalid @enderror" id="desconto" name="desconto" value="{{ old('desconto', $orcamento->desconto ?? 0) }}" step="0.01" min="0" max="100">
                                @error('desconto')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-12 col-md-4">
                                <label for="previsao_inicio" class="form-label fw-medium">{{ __('Expected Start') }}</label>
                                <input type="date" class="form-control py-2 @error('previsao_inicio') is-invalid @enderror" id="previsao_inicio" name="previsao_inicio" value="{{ old('previsao_inicio', isset($orcamento->previsao_inicio) ? $orcamento->previsao_inicio->format('Y-m-d') : ($defaultDates['previsao_inicio'] ?? '')) }}">
                                @error('previsao_inicio')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="previsao_entrega" class="form-label fw-medium">{{ __('Expected Delivery') }}</label>
                                <input type="date" class="form-control py-2 @error('previsao_entrega') is-invalid @enderror" id="previsao_entrega" name="previsao_entrega" value="{{ old('previsao_entrega', isset($orcamento->previsao_entrega) ? $orcamento->previsao_entrega->format('Y-m-d') : '') }}">
                                @error('previsao_entrega')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="validade" class="form-label fw-medium">{{ __('Validity') }}</label>
                                <input type="date" class="form-control py-2 @error('validade') is-invalid @enderror" id="validade" name="validade" value="{{ old('validade', isset($orcamento->validade) ? $orcamento->validade->format('Y-m-d') : ($defaultDates['validade'] ?? '')) }}">
                                @error('validade')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-12">
                                <label for="observacoes" class="form-label fw-medium">{{ __('Notes') }}</label>
                                <textarea class="form-control py-2 @error('observacoes') is-invalid @enderror" id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $orcamento->observacoes ?? '') }}</textarea>
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

                        {{-- Header row --}}
                        <div class="row g-2 mb-2 d-none d-md-flex">
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
                            @if(isset($orcamento) && $orcamento->servicos->count() > 0)
                                @foreach($orcamento->servicos as $index => $servico)
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

                        <div class="d-flex justify-content-end mb-3">
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
                    <a href="{{ route('orcamento.index') }}" class="btn btn-light px-4 py-2">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary px-4 py-2">{{ $btn_label }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.addEventListener('load', function() {
    var servicoIndex = {{ isset($orcamento) ? $orcamento->servicos->count() : 1 }};
    var servicosUrl = '{{ route("orcamento.servicos") }}';
    var servicosData = [];

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

        // Hide alerts by default
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

        // Check stock availability
        checkStockAvailability(row);
    }

    function updateTotals() {
        var custoTotal = 0;
        document.querySelectorAll('.servico-row').forEach(function(row) {
            var subtotalDisplay = row.querySelector('.subtotal-display');
            custoTotal += parseFloat(subtotalDisplay.dataset.subtotal) || 0;
        });

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
        var rows = document.querySelectorAll('.servico-row');
        rows.forEach(function(row, index) {
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

    // Load services and initialize
    loadServicos().then(function() {
        document.querySelectorAll('.servico-row').forEach(function(row) {
            var servicoSelect = row.querySelector('.servico-select');
            var selectedId = servicoSelect.value;

            // Populate select with all services
            populateServicoSelect(servicoSelect, selectedId);

            bindRowEvents(row);
            updateRowSubtotal(row);
        });
        updateTotals();
    });

    // Add service button
    document.getElementById('add-servico').addEventListener('click', addServicoRow);

    // Update totals when taxa_lucro or desconto change
    document.getElementById('taxa_lucro').addEventListener('input', updateTotals);
    document.getElementById('desconto').addEventListener('input', updateTotals);
});
</script>
@endpush
