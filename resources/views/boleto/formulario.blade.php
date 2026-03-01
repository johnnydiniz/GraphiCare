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
                </div>
            </div>

            <form method="POST" action="{{ is_array($route) ? route($route[0], $route[1]) : route($route) }}" id="boleto-form">
                @csrf
                @if(is_array($route))
                    @method('PUT')
                @endif

                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">Dados do Boleto</h5>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="ordem_compra_id" class="form-label fw-medium">Ordem de Compra <span class="text-danger">*</span></label>
                                <select class="form-select py-2 @error('ordem_compra_id') is-invalid @enderror" id="ordem_compra_id" name="ordem_compra_id" required>
                                    <option value="">Selecione uma ordem de compra...</option>
                                    @if(isset($boleto) && $boleto->ordemCompra)
                                        <option value="{{ $boleto->ordem_compra_id }}" selected>
                                            OC #{{ $boleto->ordem_compra_id }} - {{ $boleto->ordemCompra->fornecedor->pessoa->nome_social ?? $boleto->ordemCompra->fornecedor->pessoa->nome_registro ?? '-' }} - R$ {{ number_format($boleto->ordemCompra->valor_total, 2, ',', '.') }}
                                        </option>
                                    @endif
                                </select>
                                @error('ordem_compra_id')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="valor" class="form-label fw-medium">Valor (R$) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control py-2 @error('valor') is-invalid @enderror" id="valor" name="valor" min="0" value="{{ old('valor', $boleto->valor ?? '') }}" required>
                                @error('valor')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-12 col-md-6">
                                <label for="data_emissao" class="form-label fw-medium">Data de Emissão <span class="text-danger">*</span></label>
                                <input type="date" class="form-control py-2 @error('data_emissao') is-invalid @enderror" id="data_emissao" name="data_emissao" value="{{ old('data_emissao', isset($boleto->data_emissao) ? $boleto->data_emissao->format('Y-m-d') : ($defaultDates['data_emissao'] ?? '')) }}" required>
                                @error('data_emissao')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="data_vencimento" class="form-label fw-medium">Data de Vencimento <span class="text-danger">*</span></label>
                                <input type="date" class="form-control py-2 @error('data_vencimento') is-invalid @enderror" id="data_vencimento" name="data_vencimento" value="{{ old('data_vencimento', isset($boleto->data_vencimento) ? $boleto->data_vencimento->format('Y-m-d') : ($defaultDates['data_vencimento'] ?? '')) }}" required>
                                @error('data_vencimento')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-12">
                                <label for="observacoes" class="form-label fw-medium">Observações</label>
                                <textarea class="form-control py-2 @error('observacoes') is-invalid @enderror" id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $boleto->observacoes ?? '') }}</textarea>
                                @error('observacoes')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="{{ route('boleto.index') }}" class="btn btn-light px-4 py-2">Cancelar</a>
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
    var ordensCompraUrl = '{{ route("boleto.ordens-compra") }}';
    var ordensCompraData = [];
    var ocSelect = document.getElementById('ordem_compra_id');
    var valorInput = document.getElementById('valor');

    function loadOrdensCompra() {
        return fetch(ordensCompraUrl)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                ordensCompraData = data;
                return data;
            });
    }

    function populateOcSelect(selectedId) {
        var options = '<option value="">Selecione uma ordem de compra...</option>';
        ordensCompraData.forEach(function(item) {
            var isSelected = item.id == selectedId ? 'selected' : '';
            options += '<option value="' + item.id + '" data-valor="' + item.valor_total + '" ' + isSelected + '>' + item.label + '</option>';
        });
        ocSelect.innerHTML = options;
    }

    function updateValor() {
        var selectedOption = ocSelect.options[ocSelect.selectedIndex];
        if (ocSelect.value && selectedOption && selectedOption.dataset.valor) {
            valorInput.value = selectedOption.dataset.valor;
        }
    }

    ocSelect.addEventListener('change', updateValor);

    loadOrdensCompra().then(function() {
        var selectedId = ocSelect.value || '{{ old("ordem_compra_id", $boleto->ordem_compra_id ?? "") }}';
        populateOcSelect(selectedId);
    });
});
</script>
@endpush
