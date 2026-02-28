<div class="row g-4">
    @foreach ($fields as $field)
        @if (is_array($route) && $field['type'] == 'password')
            @continue
        @endif
        <div class="col-12 col-md-{{ $field['proportion'] ?? 6 }}">
            @if ($field['type'] != 'checkbox' && $field['type'] != 'radio')
                <label class="form-label text-dark mb-2 fw-medium" for="{{ $field['name'] }}" id="label-{{ $field['name'] }}">
                    <span class="label-text">{{ $field['label'] }}</span>
                    @if (!empty($field['required']))
                        <span class="text-danger">*</span>
                    @endif
                </label>
                @if (!empty($field['action']))
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                        data-bs-target="#{{ $field['action'] }}">
                        <span style="font-size: 12px"><i class="fa-solid fa-plus"></i></span>
                    </button>
                    <!-- Modal -->
                    <div class="modal fade" id="{{ $field['action'] }}" tabindex="-1" role="dialog"
                        aria-labelledby="{{ $field['action'] }}Label" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="{{ $field['action'] }}Label">Cadastro de Tipo de
                                        matéria-prima</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Cancelar">
                                        <span aria-hidden="true"></span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <label class="form-label text-dark mb-2 fw-medium" for="descricao">Descrição<span
                                            class="text-danger">*</span></label>
                                    <div>
                                        <input type="text" id="descricao" name="descricao">
                                    </div>
                                    <hr>
                                    </hr>
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-primary btn-modal"
                                        data-routing="{{ $field['action'] }}"
                                        data-select="#{{ $field['name'] }}">Cadastrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
            @if ($field['type'] == 'text')
                <div>
                    <input id="{{ $field['name'] }}" type="text" name="{{ $field['name'] }}"
                        class="form-control py-2 @error($field['name']) is-invalid @enderror" value="{{ session()->get($field['name']) ?? $field['value'] }}"
                        @if (!empty($field['required'])) required="true" @endif
                        @if (!empty($field['mask'])) data-mask="{{ $field['mask'] }}" @endif
                        autocomplete="{{ $field['name'] }}">
                    @error($field['name'])
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>
            @elseif($field['type'] == 'select')
                <div>
                    <select id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="form-select py-2 @error($field['name']) is-invalid @enderror"
                        @if (!empty($field['required'])) required="true" @endif>
                        @foreach ($field['options'] as $key => $value)
                            <option value="{{ $key }}"
                                {{ $key == session()->get($field['name']) || $field['selected'] == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    @error($field['name'])
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>
            @elseif($field['type'] == 'password')
                <div>
                    <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                        class="form-control py-2 @error($field['name']) is-invalid @enderror" @if (!empty($field['required'])) required="true" @endif
                        autocomplete="{{ $field['name'] }}" autofocus>

                    @error($field['name'])
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>
            @elseif($field['type'] == 'date')
                <div>
                    <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                        class="form-control py-2 @error($field['name']) is-invalid @enderror" value="{{ session()->get($field['name']) ?? $field['value'] }}"
                        @if (!empty($field['required'])) required="true" @endif autocomplete="{{ $field['name'] }}"
                        autofocus>

                    @error($field['name'])
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>
            @elseif($field['type'] == 'textarea')
                <div>
                    <textarea id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="form-control py-2 @error($field['name']) is-invalid @enderror"
                        @if (!empty($field['required'])) required="true" @endif autofocus>{{ session()->get($field['name']) ?? $field['value'] }}</textarea>

                    @error($field['name'])
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>
            @elseif($field['type'] == 'checkbox')
                <div {{ !empty($field['hidden']) ? $field['hidden'] : '' }}>
                    <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                        class="form-check-input @error($field['name']) is-invalid @enderror"
                        {{ session()->get($field['name']) || $field['checked'] ? 'checked' : '' }}
                        @if (!empty($field['required'])) required="true" @endif autofocus>
                    <label class="form-label text-dark mb-2 fw-medium" for="{{ $field['name'] }}">
                        {{ $field['label'] }} </label>
                    @error($field['name'])
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>
            @elseif($field['type'] == 'radio')
                <div>
                    @foreach ($field['options'] as $key => $value)
                        <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                            class="form-check-input @error($field['name']) is-invalid @enderror" value="{{ $key }}"
                            {{ $key == session()->get($field['name']) || $field['selected'] == $key ? 'checked' : '' }}
                            @if (!empty($field['required'])) required="true" @endif autofocus>
                        <label class="form-label text-dark mb-2 fw-medium"
                            for="{{ $field['name'] }}">{{ $value }}</label>
                    @endforeach

                    @error($field['name'])
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>
            @elseif($field['type'] == 'number')
                <div>
                    <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                        class="form-control py-2 @error($field['name']) is-invalid @enderror" value="{{ session()->get($field['name']) ?? $field['value'] }}"
                        @if (!empty($field['required'])) required="true" @endif
                        @if (!empty($field['min'])) min="{{ $field['min'] }}" @endif
                        @if (!empty($field['max'])) max="{{ $field['max'] }}" @endif
                        @if (!empty($field['step'])) step="{{ $field['step'] }}" @endif autofocus>

                    @error($field['name'])
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>
            @elseif($field['type'] == 'file')
                <div>
                    <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                        class="form-control py-2 @error($field['name']) is-invalid @enderror" @if (!empty($field['required'])) required="true" @endif autofocus>

                    @error($field['name'])
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>
            @elseif($field['type'] == 'time')
                <div>
                    <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                        class="form-control py-2 @error($field['name']) is-invalid @enderror" value="{{ session()->get($field['name']) ?? $field['value'] }}"
                        @if (!empty($field['required'])) required="true" @endif autofocus>

                    @error($field['name'])
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror
                </div>
            @endif
        </div>
    @endforeach
</div>

@pushOnce('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- CPF/CNPJ dynamic label and mask switching ---
    var tipoSelect = document.getElementById('tipo');
    var cpfCnpjInput = document.getElementById('cpf_cnpj');
    var cpfCnpjLabel = document.querySelector('#label-cpf_cnpj .label-text');

    if (tipoSelect && cpfCnpjInput) {
        updateCpfCnpjField(tipoSelect.value);
        tipoSelect.addEventListener('change', function () {
            updateCpfCnpjField(this.value);
        });
    }

    function updateCpfCnpjField(tipo) {
        if (!cpfCnpjInput) return;
        var digits = cpfCnpjInput.value.replace(/\D/g, '');

        if (tipo === 'juridica') {
            if (cpfCnpjLabel) cpfCnpjLabel.textContent = 'CNPJ';
            cpfCnpjInput.setAttribute('data-mask', 'cnpj');
            cpfCnpjInput.setAttribute('maxlength', '18');
            cpfCnpjInput.setAttribute('placeholder', '00.000.000/0000-00');
        } else {
            if (cpfCnpjLabel) cpfCnpjLabel.textContent = 'CPF';
            cpfCnpjInput.setAttribute('data-mask', 'cpf');
            cpfCnpjInput.setAttribute('maxlength', '14');
            cpfCnpjInput.setAttribute('placeholder', '000.000.000-00');
        }

        cpfCnpjInput.value = digits;
        applyMask(cpfCnpjInput);
    }

    // --- Initialize all masked inputs ---
    document.querySelectorAll('[data-mask]').forEach(function (input) {
        formatInitialValue(input);
        input.addEventListener('input', function () {
            applyMask(this);
        });
    });

    // --- Strip masks on form submit ---
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            form.querySelectorAll('[data-mask]').forEach(function (input) {
                stripMask(input);
            });
        });
    });

    // --- Format initial value (from database / session) ---
    function formatInitialValue(input) {
        var value = input.value;
        if (!value || value === '') return;

        var maskType = input.getAttribute('data-mask');

        if (maskType === 'currency') {
            var num = parseFloat(value);
            if (!isNaN(num)) {
                input.value = 'R$ ' + num.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        } else if (maskType === 'percent') {
            var num = parseFloat(value);
            if (!isNaN(num)) {
                input.value = num.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + '%';
            }
        } else {
            applyMask(input);
        }
    }

    // --- Apply mask based on data-mask attribute ---
    function applyMask(input) {
        var maskType = input.getAttribute('data-mask');
        var value = input.value;

        switch (maskType) {
            case 'cpf':
                input.value = maskCPF(value);
                break;
            case 'cnpj':
                input.value = maskCNPJ(value);
                break;
            case 'cpf_cnpj':
                input.value = maskCPF(value);
                break;
            case 'currency':
                input.value = maskCurrency(value);
                break;
            case 'percent':
                input.value = maskPercent(value);
                break;
            case 'cep':
                input.value = maskCEP(value);
                break;
        }
    }

    function maskCPF(value) {
        var d = value.replace(/\D/g, '').substring(0, 11);
        return d.replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }

    function maskCNPJ(value) {
        var d = value.replace(/\D/g, '').substring(0, 14);
        return d.replace(/(\d{2})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1/$2')
                .replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    }

    function maskCurrency(value) {
        var digits = value.replace(/\D/g, '');
        if (digits === '') return '';
        var number = parseInt(digits, 10);
        var formatted = (number / 100).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        return 'R$ ' + formatted;
    }

    function maskPercent(value) {
        var digits = value.replace(/\D/g, '');
        if (digits === '') return '';
        var number = parseInt(digits, 10);
        var formatted = (number / 100).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        return formatted + '%';
    }

    function maskCEP(value) {
        var d = value.replace(/\D/g, '').substring(0, 8);
        return d.replace(/(\d{5})(\d)/, '$1-$2');
    }

    // --- Strip mask before form submission ---
    function stripMask(input) {
        var maskType = input.getAttribute('data-mask');
        var value = input.value;

        switch (maskType) {
            case 'cpf':
            case 'cnpj':
            case 'cpf_cnpj':
            case 'cep':
                input.value = value.replace(/\D/g, '');
                break;
            case 'currency':
                input.value = value.replace(/[R$\s.]/g, '').replace(',', '.');
                break;
            case 'percent':
                input.value = value.replace(/[%\s.]/g, '').replace(',', '.');
                break;
        }
    }
});
</script>
@endPushOnce
