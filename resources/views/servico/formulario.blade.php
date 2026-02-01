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

            <form method="POST" action="{{ is_array($route) ? route($route[0], $route[1]) : route($route) }}" id="servico-form">
                @csrf
                @if(is_array($route))
                    @method('PUT')
                @endif

                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">{{ __('Service Information') }}</h5>

                        @if(isset($servico) && $servico->id)
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="ativo" name="ativo" value="1" {{ $servico->ativo ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativo">{{ __('Active') }}</label>
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="descricao" class="form-label fw-medium">{{ __('Description') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control py-2 @error('descricao') is-invalid @enderror" id="descricao" name="descricao" value="{{ old('descricao', $servico->descricao ?? '') }}" required>
                            @error('descricao')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">{{ __('Service Components') }}</h5>

                        {{-- Header row --}}
                        <div class="row g-2 mb-2 d-none d-md-flex">
                            <div class="col-md-2">
                                <span class="form-label fw-medium small">{{ __('Type') }}</span>
                            </div>
                            <div class="col-md-2">
                                <span class="form-label fw-medium small">{{ __('Component') }}</span>
                            </div>
                            <div class="col-md-1">
                                <span class="form-label fw-medium small">{{ __('Qty') }}</span>
                            </div>
                            <div class="col-md-2">
                                <span class="form-label fw-medium small">{{ __('Base Cost') }}</span>
                            </div>
                            <div class="col-md-2">
                                <span class="form-label fw-medium small">{{ __('Op. Cost') }}</span>
                            </div>
                            <div class="col-md-2">
                                <span class="form-label fw-medium small">{{ __('Total') }}</span>
                            </div>
                            <div class="col-md-1"></div>
                        </div>

                        <div id="componentes-container">
                            @if(isset($servico) && $servico->componenteServico->count() > 0)
                                @foreach($servico->componenteServico as $index => $componente)
                                    <div class="componente-row row g-2 mb-2 align-items-center" data-index="{{ $index }}">
                                        <div class="col-6 col-md-2">
                                            <span class="form-label fw-medium small d-md-none">{{ __('Type') }}</span>
                                            <select class="form-select py-2 tipo-select" name="componentes[{{ $index }}][tipo]" required>
                                                <option value="">{{ __('Select...') }}</option>
                                                <option value="material" {{ $componente->tipo == 'material' ? 'selected' : '' }}>{{ __('Material') }}</option>
                                                <option value="equipamento" {{ $componente->tipo == 'equipamento' ? 'selected' : '' }}>{{ __('Equipment') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <span class="form-label fw-medium small d-md-none">{{ __('Component') }}</span>
                                            <select class="form-select py-2 componente-select" name="componentes[{{ $index }}][id]" required>
                                                <option value="{{ $componente->id }}" data-custo-base="{{ $componente->custo_operacional ?? 0 }}" selected>{{ $componente->descricao }}</option>
                                            </select>
                                        </div>
                                        <div class="col-3 col-md-1">
                                            <span class="form-label fw-medium small d-md-none">{{ __('Qty') }}</span>
                                            <input type="number" class="form-control py-2 qtde-input" name="componentes[{{ $index }}][qtde]" min="1" value="{{ $componente->pivot->qtde ?? 1 }}" required>
                                        </div>
                                        <div class="col-4 col-md-2">
                                            <span class="form-label fw-medium small d-md-none">{{ __('Base Cost') }}</span>
                                            <input type="text" class="form-control py-2 custo-base-display bg-light" value="R$ 0,00" readonly>
                                        </div>
                                        <div class="col-5 col-md-2">
                                            <span class="form-label fw-medium small d-md-none">{{ __('Op. Cost') }}</span>
                                            <input type="text" class="form-control py-2 custo-operacional-input" data-value="{{ $componente->pivot->custo_operacional ?? 0 }}" value="R$ {{ number_format($componente->pivot->custo_operacional ?? 0, 2, ',', '.') }}">
                                            <input type="hidden" class="custo-operacional-hidden" name="componentes[{{ $index }}][custo_operacional]" value="{{ $componente->pivot->custo_operacional ?? 0 }}">
                                        </div>
                                        <div class="col-8 col-md-2">
                                            <span class="form-label fw-medium small d-md-none">{{ __('Total') }}</span>
                                            <input type="text" class="form-control py-2 custo-display bg-light" value="R$ 0,00" readonly>
                                        </div>
                                        <div class="col-4 col-md-1">
                                            <button type="button" class="btn btn-danger w-100 py-2 remove-componente" {{ $index == 0 ? 'disabled' : '' }}>
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="componente-row row g-2 mb-2 align-items-center" data-index="0">
                                    <div class="col-6 col-md-2">
                                        <span class="form-label fw-medium small d-md-none">{{ __('Type') }}</span>
                                        <select class="form-select py-2 tipo-select" name="componentes[0][tipo]" required>
                                            <option value="">{{ __('Select...') }}</option>
                                            <option value="material">{{ __('Material') }}</option>
                                            <option value="equipamento">{{ __('Equipment') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <span class="form-label fw-medium small d-md-none">{{ __('Component') }}</span>
                                        <select class="form-select py-2 componente-select" name="componentes[0][id]" required disabled>
                                            <option value="">{{ __('Select a type first') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-3 col-md-1">
                                        <span class="form-label fw-medium small d-md-none">{{ __('Qty') }}</span>
                                        <input type="number" class="form-control py-2 qtde-input" name="componentes[0][qtde]" min="1" value="1" required>
                                    </div>
                                    <div class="col-4 col-md-2">
                                        <span class="form-label fw-medium small d-md-none">{{ __('Base Cost') }}</span>
                                        <input type="text" class="form-control py-2 custo-base-display bg-light" value="R$ 0,00" readonly>
                                    </div>
                                    <div class="col-5 col-md-2">
                                        <span class="form-label fw-medium small d-md-none">{{ __('Op. Cost') }}</span>
                                        <input type="text" class="form-control py-2 custo-operacional-input" data-value="0" value="R$ 0,00">
                                        <input type="hidden" class="custo-operacional-hidden" name="componentes[0][custo_operacional]" value="0">
                                    </div>
                                    <div class="col-8 col-md-2">
                                        <span class="form-label fw-medium small d-md-none">{{ __('Total') }}</span>
                                        <input type="text" class="form-control py-2 custo-display bg-light" value="R$ 0,00" readonly>
                                    </div>
                                    <div class="col-4 col-md-1">
                                        <button type="button" class="btn btn-danger w-100 py-2 remove-componente" disabled>
                                            <i class="fa-solid fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-success px-4" id="add-componente">
                                <i class="fa-solid fa-plus me-2"></i>{{ __('Add Component') }}
                            </button>
                        </div>

                        @error('componentes')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold mb-0">{{ __('Estimated Total') }}</h5>
                            <h4 class="text-success fw-bold mb-0" id="total-estimado">R$ 0,00</h4>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="{{ route('servico.index') }}" class="btn btn-light px-4 py-2">{{ __('Cancel') }}</a>
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
    var componenteIndex = {{ isset($servico) ? $servico->componenteServico->count() : 1 }};
    var componentesUrl = '{{ route("componente-servico.por-tipo") }}';

    function formatCurrency(value) {
        return 'R$ ' + parseFloat(value).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function parseCurrency(value) {
        if (typeof value === 'number') return value;
        return parseFloat(value.replace(/[R$\s.]/g, '').replace(',', '.')) || 0;
    }

    function applyCurrencyMask(input) {
        input.addEventListener('focus', function() {
            var value = parseCurrency(this.value);
            this.value = value.toFixed(2).replace('.', ',');
            this.select();
        });

        input.addEventListener('blur', function() {
            var value = parseCurrency(this.value);
            this.value = formatCurrency(value);
            this.dataset.value = value;
            // Atualizar o campo hidden
            var hidden = this.parentElement.querySelector('.custo-operacional-hidden');
            if (hidden) hidden.value = value;
        });

        input.addEventListener('input', function() {
            // Permitir apenas números e vírgula
            this.value = this.value.replace(/[^0-9,]/g, '');
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.blur();
            }
        });
    }

    function updateRowCost(row) {
        var componenteSelect = row.querySelector('.componente-select');
        var qtdeInput = row.querySelector('.qtde-input');
        var custoOperacionalInput = row.querySelector('.custo-operacional-input');
        var custoBaseDisplay = row.querySelector('.custo-base-display');
        var custoDisplay = row.querySelector('.custo-display');

        var selected = componenteSelect.options[componenteSelect.selectedIndex];
        var custoBase = selected && selected.dataset.custoBase ? parseFloat(selected.dataset.custoBase) : 0;
        var qtde = parseInt(qtdeInput.value) || 1;
        var custoOperacional = parseFloat(custoOperacionalInput.dataset.value) || 0;

        // Atualizar custo base (multiplicado pela quantidade)
        var custoBaseTotal = custoBase * qtde;
        custoBaseDisplay.value = formatCurrency(custoBaseTotal);

        // Calcular total (custo base * qtde + custo operacional)
        var custoTotal = custoBaseTotal + custoOperacional;

        custoDisplay.value = formatCurrency(custoTotal);
        custoDisplay.dataset.custoTotal = custoTotal;
    }

    function updateTotal() {
        var total = 0;
        document.querySelectorAll('.componente-row').forEach(function(row) {
            var custoDisplay = row.querySelector('.custo-display');
            total += parseFloat(custoDisplay.dataset.custoTotal) || 0;
        });
        document.getElementById('total-estimado').textContent = formatCurrency(total);
    }

    function loadComponentes(tipoSelect) {
        var row = tipoSelect.closest('.componente-row');
        var componenteSelect = row.querySelector('.componente-select');
        var custoBaseDisplay = row.querySelector('.custo-base-display');
        var custoDisplay = row.querySelector('.custo-display');
        var tipo = tipoSelect.value;

        if (!tipo) {
            componenteSelect.innerHTML = '<option value="">{{ __("Select a type first") }}</option>';
            componenteSelect.disabled = true;
            custoBaseDisplay.value = 'R$ 0,00';
            custoDisplay.value = 'R$ 0,00';
            custoDisplay.dataset.custoTotal = 0;
            updateTotal();
            return;
        }

        componenteSelect.disabled = true;
        componenteSelect.innerHTML = '<option value="">{{ __("Loading...") }}</option>';

        fetch(componentesUrl + '?tipo=' + tipo)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                var options = '<option value="">{{ __("Select...") }}</option>';
                data.forEach(function(item) {
                    options += '<option value="' + item.id + '" data-custo-base="' + item.custo_base + '">' + item.descricao + '</option>';
                });
                componenteSelect.innerHTML = options;
                componenteSelect.disabled = false;
                custoBaseDisplay.value = 'R$ 0,00';
                custoDisplay.value = 'R$ 0,00';
                custoDisplay.dataset.custoTotal = 0;
                updateTotal();
            })
            .catch(function(error) {
                componenteSelect.innerHTML = '<option value="">{{ __("Error loading") }}</option>';
                if (typeof showToast === 'function') {
                    showToast('{{ __("Error loading components") }}', 'danger');
                }
            });
    }

    function bindRowEvents(row) {
        var tipoSelect = row.querySelector('.tipo-select');
        var componenteSelect = row.querySelector('.componente-select');
        var qtdeInput = row.querySelector('.qtde-input');
        var custoOperacionalInput = row.querySelector('.custo-operacional-input');
        var removeBtn = row.querySelector('.remove-componente');

        // Aplicar máscara de moeda no custo operacional
        applyCurrencyMask(custoOperacionalInput);

        tipoSelect.addEventListener('change', function() {
            loadComponentes(this);
        });

        componenteSelect.addEventListener('change', function() {
            updateRowCost(row);
            updateTotal();
        });

        qtdeInput.addEventListener('input', function() {
            if (this.value < 1) this.value = 1;
            updateRowCost(row);
            updateTotal();
        });

        custoOperacionalInput.addEventListener('blur', function() {
            updateRowCost(row);
            updateTotal();
        });

        removeBtn.addEventListener('click', function() {
            row.remove();
            updateRemoveButtons();
            updateTotal();
        });
    }

    function updateRemoveButtons() {
        var rows = document.querySelectorAll('.componente-row');
        rows.forEach(function(row, index) {
            var removeBtn = row.querySelector('.remove-componente');
            removeBtn.disabled = rows.length === 1;
        });
    }

    function addComponenteRow() {
        var container = document.getElementById('componentes-container');
        var newRow = document.createElement('div');
        newRow.className = 'componente-row row g-2 mb-2 align-items-center';
        newRow.dataset.index = componenteIndex;

        newRow.innerHTML = `
            <div class="col-6 col-md-2">
                <span class="form-label fw-medium small d-md-none">{{ __('Type') }}</span>
                <select class="form-select py-2 tipo-select" name="componentes[${componenteIndex}][tipo]" required>
                    <option value="">{{ __('Select...') }}</option>
                    <option value="material">{{ __('Material') }}</option>
                    <option value="equipamento">{{ __('Equipment') }}</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <span class="form-label fw-medium small d-md-none">{{ __('Component') }}</span>
                <select class="form-select py-2 componente-select" name="componentes[${componenteIndex}][id]" required disabled>
                    <option value="">{{ __('Select a type first') }}</option>
                </select>
            </div>
            <div class="col-3 col-md-1">
                <span class="form-label fw-medium small d-md-none">{{ __('Qty') }}</span>
                <input type="number" class="form-control py-2 qtde-input" name="componentes[${componenteIndex}][qtde]" min="1" value="1" required>
            </div>
            <div class="col-4 col-md-2">
                <span class="form-label fw-medium small d-md-none">{{ __('Base Cost') }}</span>
                <input type="text" class="form-control py-2 custo-base-display bg-light" value="R$ 0,00" readonly>
            </div>
            <div class="col-5 col-md-2">
                <span class="form-label fw-medium small d-md-none">{{ __('Op. Cost') }}</span>
                <input type="text" class="form-control py-2 custo-operacional-input" data-value="0" value="R$ 0,00">
                <input type="hidden" class="custo-operacional-hidden" name="componentes[${componenteIndex}][custo_operacional]" value="0">
            </div>
            <div class="col-8 col-md-2">
                <span class="form-label fw-medium small d-md-none">{{ __('Total') }}</span>
                <input type="text" class="form-control py-2 custo-display bg-light" value="R$ 0,00" data-custo-total="0" readonly>
            </div>
            <div class="col-4 col-md-1">
                <button type="button" class="btn btn-danger w-100 py-2 remove-componente">
                    <i class="fa-solid fa-minus"></i>
                </button>
            </div>
        `;

        container.appendChild(newRow);
        bindRowEvents(newRow);
        updateRemoveButtons();
        componenteIndex++;
    }

    // Initialize existing rows
    document.querySelectorAll('.componente-row').forEach(function(row) {
        bindRowEvents(row);

        // Load components for rows with pre-selected tipo (edit mode)
        var tipoSelect = row.querySelector('.tipo-select');
        if (tipoSelect.value) {
            var componenteSelect = row.querySelector('.componente-select');
            var selectedId = componenteSelect.value;

            fetch(componentesUrl + '?tipo=' + tipoSelect.value)
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    var options = '<option value="">{{ __("Select...") }}</option>';
                    data.forEach(function(item) {
                        var isSelected = item.id == selectedId ? 'selected' : '';
                        options += '<option value="' + item.id + '" data-custo-base="' + item.custo_base + '" ' + isSelected + '>' + item.descricao + '</option>';
                    });
                    componenteSelect.innerHTML = options;
                    componenteSelect.disabled = false;

                    updateRowCost(row);
                    updateTotal();
                });
        }
    });

    // Add component button
    document.getElementById('add-componente').addEventListener('click', addComponenteRow);

    updateTotal();
});
</script>
@endpush
