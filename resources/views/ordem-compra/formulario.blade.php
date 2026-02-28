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
                    @if(isset($ordemCompra) && $ordemCompra->id)
                        @if($ordemCompra->status === 'recebida')
                            <span class="badge bg-success fs-6">Recebida</span>
                        @else
                            <span class="badge bg-secondary fs-6">Pendente</span>
                        @endif
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ is_array($route) ? route($route[0], $route[1]) : route($route) }}" id="ordem-compra-form">
                @csrf
                @if(is_array($route))
                    @method('PUT')
                @endif

                {{-- Informações da Ordem --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">Informações da Ordem</h5>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="fornecedor_id" class="form-label fw-medium">Fornecedor <span class="text-danger">*</span></label>
                                <select class="form-select py-2 @error('fornecedor_id') is-invalid @enderror" id="fornecedor_id" name="fornecedor_id" required>
                                    <option value="">Selecione um fornecedor...</option>
                                    @foreach($fornecedores as $fornecedor)
                                        <option value="{{ $fornecedor->id }}" {{ old('fornecedor_id', $ordemCompra->fornecedor_id ?? '') == $fornecedor->id ? 'selected' : '' }}>
                                            {{ $fornecedor->pessoa->nome_social ?? $fornecedor->pessoa->nome_registro }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('fornecedor_id')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="nota_fiscal" class="form-label fw-medium">Nota Fiscal</label>
                                <input type="text" class="form-control py-2 @error('nota_fiscal') is-invalid @enderror" id="nota_fiscal" name="nota_fiscal" value="{{ old('nota_fiscal', $ordemCompra->nota_fiscal ?? '') }}">
                                @error('nota_fiscal')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-12 col-md-6">
                                <label for="data_emissao" class="form-label fw-medium">Data de Emissão <span class="text-danger">*</span></label>
                                <input type="date" class="form-control py-2 @error('data_emissao') is-invalid @enderror" id="data_emissao" name="data_emissao" value="{{ old('data_emissao', isset($ordemCompra->data_emissao) ? $ordemCompra->data_emissao->format('Y-m-d') : ($defaultDates['data_emissao'] ?? '')) }}" required>
                                @error('data_emissao')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="data_entrega" class="form-label fw-medium">Data de Entrega</label>
                                <input type="date" class="form-control py-2 @error('data_entrega') is-invalid @enderror" id="data_entrega" name="data_entrega" value="{{ old('data_entrega', isset($ordemCompra->data_entrega) ? $ordemCompra->data_entrega->format('Y-m-d') : '') }}">
                                @error('data_entrega')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-12">
                                <label for="observacoes" class="form-label fw-medium">Observações</label>
                                <textarea class="form-control py-2 @error('observacoes') is-invalid @enderror" id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $ordemCompra->observacoes ?? '') }}</textarea>
                                @error('observacoes')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Itens --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">Itens</h5>

                        {{-- Header row --}}
                        <div class="row g-2 mb-2 d-none d-md-flex" id="itens-header">
                            <div class="col-md-4">
                                <span class="form-label fw-medium small">Matéria-Prima</span>
                            </div>
                            <div class="col-md-2">
                                <span class="form-label fw-medium small">Valor Unitário</span>
                            </div>
                            <div class="col-md-2">
                                <span class="form-label fw-medium small">Quantidade</span>
                            </div>
                            <div class="col-md-3">
                                <span class="form-label fw-medium small">Subtotal</span>
                            </div>
                            <div class="col-md-1"></div>
                        </div>

                        <div id="itens-container">
                            @if(isset($ordemCompra) && $ordemCompra->entradas->count() > 0)
                                @foreach($ordemCompra->entradas as $index => $entrada)
                                    <div class="item-row mb-2" data-index="{{ $index }}">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-12 col-md-4">
                                                <span class="form-label fw-medium small d-md-none">Matéria-Prima</span>
                                                <select class="form-select py-2 mp-select" name="itens[{{ $index }}][materia_prima_id]" required>
                                                    <option value="{{ $entrada->materia_prima_id }}" data-custo="{{ $entrada->materiaPrima->custo_medio ?? 0 }}" selected>{{ $entrada->materiaPrima->descricao ?? '-' }}</option>
                                                </select>
                                            </div>
                                            <div class="col-4 col-md-2">
                                                <span class="form-label fw-medium small d-md-none">Valor Unitário</span>
                                                <input type="number" class="form-control py-2 valor-unitario-input" name="itens[{{ $index }}][valor_unitario]" step="0.01" min="0" value="{{ $entrada->valor_unitario }}" required>
                                            </div>
                                            <div class="col-4 col-md-2">
                                                <span class="form-label fw-medium small d-md-none">Quantidade</span>
                                                <input type="number" class="form-control py-2 qtde-input" name="itens[{{ $index }}][qtde]" min="1" value="{{ $entrada->qtde }}" required>
                                            </div>
                                            <div class="col-4 col-md-3">
                                                <span class="form-label fw-medium small d-md-none">Subtotal</span>
                                                <input type="text" class="form-control py-2 subtotal-display bg-light" value="R$ 0,00" readonly>
                                            </div>
                                            <div class="col-12 col-md-1">
                                                <button type="button" class="btn btn-danger w-100 py-2 remove-item" {{ $index == 0 ? 'disabled' : '' }}>
                                                    <i class="fa-solid fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="item-row mb-2" data-index="0">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-12 col-md-4">
                                            <span class="form-label fw-medium small d-md-none">Matéria-Prima</span>
                                            <select class="form-select py-2 mp-select" name="itens[0][materia_prima_id]" required>
                                                <option value="">Selecione uma matéria-prima...</option>
                                            </select>
                                        </div>
                                        <div class="col-4 col-md-2">
                                            <span class="form-label fw-medium small d-md-none">Valor Unitário</span>
                                            <input type="number" class="form-control py-2 valor-unitario-input" name="itens[0][valor_unitario]" step="0.01" min="0" value="0" required>
                                        </div>
                                        <div class="col-4 col-md-2">
                                            <span class="form-label fw-medium small d-md-none">Quantidade</span>
                                            <input type="number" class="form-control py-2 qtde-input" name="itens[0][qtde]" min="1" value="1" required>
                                        </div>
                                        <div class="col-4 col-md-3">
                                            <span class="form-label fw-medium small d-md-none">Subtotal</span>
                                            <input type="text" class="form-control py-2 subtotal-display bg-light" value="R$ 0,00" readonly>
                                        </div>
                                        <div class="col-12 col-md-1">
                                            <button type="button" class="btn btn-danger w-100 py-2 remove-item" disabled>
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-success px-4" id="add-item">
                                <i class="fa-solid fa-plus me-2"></i>Adicionar Item
                            </button>
                        </div>

                        @error('itens')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Resumo --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">Resumo</h5>

                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Valor Total</h5>
                            <h3 class="text-primary fw-bold mb-0" id="valor-total">R$ 0,00</h3>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="{{ route('ordem-compra.index') }}" class="btn btn-light px-4 py-2">Cancelar</a>
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
    var itemIndex = {{ isset($ordemCompra) ? $ordemCompra->entradas->count() : 1 }};
    var materiasPrimasUrl = '{{ route("ordem-compra.materias-primas") }}';
    var materiasPrimasData = [];

    function formatCurrency(value) {
        return 'R$ ' + parseFloat(value).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function loadMateriasPrimas() {
        return fetch(materiasPrimasUrl)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                materiasPrimasData = data;
                return data;
            });
    }

    function populateMpSelect(select, selectedId) {
        var options = '<option value="">Selecione uma matéria-prima...</option>';
        materiasPrimasData.forEach(function(item) {
            var isSelected = item.id == selectedId ? 'selected' : '';
            options += '<option value="' + item.id + '" data-custo="' + item.custo_medio + '" ' + isSelected + '>' + item.descricao + '</option>';
        });
        select.innerHTML = options;
    }

    function updateRowSubtotal(row) {
        var valorInput = row.querySelector('.valor-unitario-input');
        var qtdeInput = row.querySelector('.qtde-input');
        var subtotalDisplay = row.querySelector('.subtotal-display');

        var valor = parseFloat(valorInput.value) || 0;
        var qtde = parseInt(qtdeInput.value) || 1;
        var subtotal = valor * qtde;

        subtotalDisplay.value = formatCurrency(subtotal);
        subtotalDisplay.dataset.subtotal = subtotal;
    }

    function updateTotal() {
        var total = 0;
        document.querySelectorAll('#itens-container .item-row').forEach(function(row) {
            var subtotalDisplay = row.querySelector('.subtotal-display');
            total += parseFloat(subtotalDisplay.dataset.subtotal) || 0;
        });
        document.getElementById('valor-total').textContent = formatCurrency(total);
    }

    function bindRowEvents(row) {
        var mpSelect = row.querySelector('.mp-select');
        var valorInput = row.querySelector('.valor-unitario-input');
        var qtdeInput = row.querySelector('.qtde-input');
        var removeBtn = row.querySelector('.remove-item');

        mpSelect.addEventListener('change', function() {
            var selected = mpSelect.options[mpSelect.selectedIndex];
            var custo = selected && selected.dataset.custo ? parseFloat(selected.dataset.custo) : 0;
            valorInput.value = custo.toFixed(2);
            updateRowSubtotal(row);
            updateTotal();
        });

        valorInput.addEventListener('input', function() {
            updateRowSubtotal(row);
            updateTotal();
        });

        qtdeInput.addEventListener('input', function() {
            if (this.value < 1) this.value = 1;
            updateRowSubtotal(row);
            updateTotal();
        });

        removeBtn.addEventListener('click', function() {
            row.remove();
            updateRemoveButtons();
            updateTotal();
        });
    }

    function updateRemoveButtons() {
        var rows = document.querySelectorAll('#itens-container .item-row');
        rows.forEach(function(row) {
            var removeBtn = row.querySelector('.remove-item');
            removeBtn.disabled = rows.length === 1;
        });
    }

    function addItemRow() {
        var container = document.getElementById('itens-container');
        var newRow = document.createElement('div');
        newRow.className = 'item-row mb-2';
        newRow.dataset.index = itemIndex;

        newRow.innerHTML = '<div class="row g-2 align-items-center">' +
            '<div class="col-12 col-md-4">' +
                '<span class="form-label fw-medium small d-md-none">Matéria-Prima</span>' +
                '<select class="form-select py-2 mp-select" name="itens[' + itemIndex + '][materia_prima_id]" required>' +
                    '<option value="">Selecione uma matéria-prima...</option>' +
                '</select>' +
            '</div>' +
            '<div class="col-4 col-md-2">' +
                '<span class="form-label fw-medium small d-md-none">Valor Unitário</span>' +
                '<input type="number" class="form-control py-2 valor-unitario-input" name="itens[' + itemIndex + '][valor_unitario]" step="0.01" min="0" value="0" required>' +
            '</div>' +
            '<div class="col-4 col-md-2">' +
                '<span class="form-label fw-medium small d-md-none">Quantidade</span>' +
                '<input type="number" class="form-control py-2 qtde-input" name="itens[' + itemIndex + '][qtde]" min="1" value="1" required>' +
            '</div>' +
            '<div class="col-4 col-md-3">' +
                '<span class="form-label fw-medium small d-md-none">Subtotal</span>' +
                '<input type="text" class="form-control py-2 subtotal-display bg-light" value="R$ 0,00" data-subtotal="0" readonly>' +
            '</div>' +
            '<div class="col-12 col-md-1">' +
                '<button type="button" class="btn btn-danger w-100 py-2 remove-item">' +
                    '<i class="fa-solid fa-minus"></i>' +
                '</button>' +
            '</div>' +
        '</div>';

        container.appendChild(newRow);
        populateMpSelect(newRow.querySelector('.mp-select'), null);
        bindRowEvents(newRow);
        updateRemoveButtons();
        itemIndex++;
    }

    loadMateriasPrimas().then(function() {
        document.querySelectorAll('#itens-container .item-row').forEach(function(row) {
            var mpSelect = row.querySelector('.mp-select');
            var selectedId = mpSelect.value;
            populateMpSelect(mpSelect, selectedId);
            bindRowEvents(row);
            updateRowSubtotal(row);
        });
        updateTotal();
    });

    document.getElementById('add-item').addEventListener('click', addItemRow);
});
</script>
@endpush
