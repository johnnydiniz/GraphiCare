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

            <form method="POST" action="{{ is_array($route) ? route($route[0], $route[1]) : route($route) }}" id="perda-quebra-form">
                @csrf
                @if(is_array($route))
                    @method('PUT')
                @endif

                {{-- Registrar Perda/Quebra --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">Registrar Perda/Quebra</h5>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="materia_prima_id" class="form-label fw-medium">Matéria-Prima <span class="text-danger">*</span></label>
                                <select class="form-select py-2 @error('materia_prima_id') is-invalid @enderror" id="materia_prima_id" name="materia_prima_id" required>
                                    <option value="">Selecione uma matéria-prima...</option>
                                    @if(isset($perdaQuebra) && $perdaQuebra->materiaPrima)
                                        <option value="{{ $perdaQuebra->materia_prima_id }}" selected>{{ $perdaQuebra->materiaPrima->descricao }}</option>
                                    @endif
                                </select>
                                @error('materia_prima_id')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="qtde" class="form-label fw-medium">Quantidade <span class="text-danger">*</span></label>
                                <input type="number" class="form-control py-2 @error('qtde') is-invalid @enderror" id="qtde" name="qtde" min="1" value="{{ old('qtde', $perdaQuebra->qtde ?? 1) }}" required>
                                @error('qtde')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-12 col-md-6">
                                <label for="motivo" class="form-label fw-medium">Motivo <span class="text-danger">*</span></label>
                                <select class="form-select py-2 @error('motivo') is-invalid @enderror" id="motivo" name="motivo" required>
                                    <option value="">Selecione um motivo...</option>
                                    <option value="quebra" {{ old('motivo', $perdaQuebra->motivo ?? '') == 'quebra' ? 'selected' : '' }}>Quebra</option>
                                    <option value="desperdicio" {{ old('motivo', $perdaQuebra->motivo ?? '') == 'desperdicio' ? 'selected' : '' }}>Desperdício</option>
                                    <option value="vencimento" {{ old('motivo', $perdaQuebra->motivo ?? '') == 'vencimento' ? 'selected' : '' }}>Vencimento</option>
                                    <option value="ajuste" {{ old('motivo', $perdaQuebra->motivo ?? '') == 'ajuste' ? 'selected' : '' }}>Ajuste de Inventário</option>
                                    <option value="outro" {{ old('motivo', $perdaQuebra->motivo ?? '') == 'outro' ? 'selected' : '' }}>Outro</option>
                                </select>
                                @error('motivo')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="data" class="form-label fw-medium">Data <span class="text-danger">*</span></label>
                                <input type="date" class="form-control py-2 @error('data') is-invalid @enderror" id="data" name="data" value="{{ old('data', isset($perdaQuebra->data) ? $perdaQuebra->data->format('Y-m-d') : ($defaultDate ?? '')) }}" required>
                                @error('data')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-12">
                                <label for="observacoes" class="form-label fw-medium">Observações</label>
                                <textarea class="form-control py-2 @error('observacoes') is-invalid @enderror" id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $perdaQuebra->observacoes ?? '') }}</textarea>
                                @error('observacoes')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info de estoque --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4" id="estoque-info-card" style="display: none;">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">Informações de Estoque</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted">Matéria-Prima:</span>
                                <span class="fw-medium" id="info-mp-descricao">-</span>
                            </div>
                            <div>
                                <span class="text-muted">Estoque Atual:</span>
                                <span class="fw-bold fs-5 text-primary" id="info-mp-estoque">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="{{ route('perda-quebra.index') }}" class="btn btn-light px-4 py-2">Cancelar</a>
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
    var materiasPrimasUrl = '{{ route("perda-quebra.materias-primas") }}';
    var materiasPrimasData = [];
    var mpSelect = document.getElementById('materia_prima_id');
    var estoqueCard = document.getElementById('estoque-info-card');
    var infoDescricao = document.getElementById('info-mp-descricao');
    var infoEstoque = document.getElementById('info-mp-estoque');

    function loadMateriasPrimas() {
        return fetch(materiasPrimasUrl)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                materiasPrimasData = data;
                return data;
            });
    }

    function populateMpSelect(selectedId) {
        var options = '<option value="">Selecione uma matéria-prima...</option>';
        materiasPrimasData.forEach(function(item) {
            var isSelected = item.id == selectedId ? 'selected' : '';
            options += '<option value="' + item.id + '" data-estoque="' + item.estoque_atual + '"  ' + isSelected + '>' + item.descricao + ' (Estoque: ' + item.estoque_atual + ')</option>';
        });
        mpSelect.innerHTML = options;
    }

    function updateEstoqueInfo() {
        var selectedOption = mpSelect.options[mpSelect.selectedIndex];
        if (mpSelect.value && selectedOption) {
            var mp = materiasPrimasData.find(function(item) { return item.id == mpSelect.value; });
            if (mp) {
                infoDescricao.textContent = mp.descricao;
                infoEstoque.textContent = mp.estoque_atual;
                estoqueCard.style.display = 'block';
                return;
            }
        }
        estoqueCard.style.display = 'none';
    }

    mpSelect.addEventListener('change', updateEstoqueInfo);

    loadMateriasPrimas().then(function() {
        var selectedId = mpSelect.value || '{{ old("materia_prima_id", $perdaQuebra->materia_prima_id ?? "") }}';
        populateMpSelect(selectedId);
        updateEstoqueInfo();
    });
});
</script>
@endpush
