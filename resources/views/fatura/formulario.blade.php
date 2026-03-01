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

            <form method="POST" action="{{ is_array($route) ? route($route[0], $route[1]) : route($route) }}" id="fatura-form">
                @csrf
                @if(is_array($route))
                    @method('PUT')
                @endif

                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">Dados da Fatura</h5>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="ordem_servico_id" class="form-label fw-medium">Ordem de Serviço <span class="text-danger">*</span></label>
                                <select class="form-select py-2 @error('ordem_servico_id') is-invalid @enderror" id="ordem_servico_id" name="ordem_servico_id" required>
                                    <option value="">Selecione uma ordem de serviço...</option>
                                    @if(isset($fatura) && $fatura->ordemServico)
                                        <option value="{{ $fatura->ordem_servico_id }}" selected>
                                            OS #{{ $fatura->ordem_servico_id }} - {{ $fatura->ordemServico->cliente->pessoa->nome_social ?? $fatura->ordemServico->cliente->pessoa->nome_registro ?? '-' }} - R$ {{ number_format($fatura->ordemServico->valor_final, 2, ',', '.') }}
                                        </option>
                                    @endif
                                </select>
                                @error('ordem_servico_id')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="valor" class="form-label fw-medium">Valor (R$) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control py-2 @error('valor') is-invalid @enderror" id="valor" name="valor" min="0" value="{{ old('valor', $fatura->valor ?? '') }}" required>
                                @error('valor')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-12 col-md-6">
                                <label for="data_emissao" class="form-label fw-medium">Data de Emissão <span class="text-danger">*</span></label>
                                <input type="date" class="form-control py-2 @error('data_emissao') is-invalid @enderror" id="data_emissao" name="data_emissao" value="{{ old('data_emissao', isset($fatura->data_emissao) ? $fatura->data_emissao->format('Y-m-d') : ($defaultDates['data_emissao'] ?? '')) }}" required>
                                @error('data_emissao')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="data_vencimento" class="form-label fw-medium">Data de Vencimento <span class="text-danger">*</span></label>
                                <input type="date" class="form-control py-2 @error('data_vencimento') is-invalid @enderror" id="data_vencimento" name="data_vencimento" value="{{ old('data_vencimento', isset($fatura->data_vencimento) ? $fatura->data_vencimento->format('Y-m-d') : ($defaultDates['data_vencimento'] ?? '')) }}" required>
                                @error('data_vencimento')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-12">
                                <label for="observacoes" class="form-label fw-medium">Observações</label>
                                <textarea class="form-control py-2 @error('observacoes') is-invalid @enderror" id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $fatura->observacoes ?? '') }}</textarea>
                                @error('observacoes')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="{{ route('fatura.index') }}" class="btn btn-light px-4 py-2">Cancelar</a>
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
    var ordensServicoUrl = '{{ route("fatura.ordens-servico") }}';
    var ordensServicoData = [];
    var osSelect = document.getElementById('ordem_servico_id');
    var valorInput = document.getElementById('valor');

    function loadOrdensServico() {
        return fetch(ordensServicoUrl)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                ordensServicoData = data;
                return data;
            });
    }

    function populateOsSelect(selectedId) {
        var options = '<option value="">Selecione uma ordem de serviço...</option>';
        ordensServicoData.forEach(function(item) {
            var isSelected = item.id == selectedId ? 'selected' : '';
            options += '<option value="' + item.id + '" data-valor="' + item.valor_final + '" ' + isSelected + '>' + item.label + '</option>';
        });
        osSelect.innerHTML = options;
    }

    function updateValor() {
        var selectedOption = osSelect.options[osSelect.selectedIndex];
        if (osSelect.value && selectedOption && selectedOption.dataset.valor) {
            valorInput.value = selectedOption.dataset.valor;
        }
    }

    osSelect.addEventListener('change', updateValor);

    loadOrdensServico().then(function() {
        var selectedId = osSelect.value || '{{ old("ordem_servico_id", $fatura->ordem_servico_id ?? "") }}';
        populateOsSelect(selectedId);
    });
});
</script>
@endpush
