<div class="container-fluid py-5 px-4 px-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="d-flex flex-wrap justify-content-between mb-4">
                <h1 class="text-dark m-0 fs-2 fw-bold">{{ __($title) }}</h1>
            </div>

            @if (is_array($route))
                <form method="POST" action="{{ route($route[0], $route[1]) }}" id="resource-form">
                    @method('PUT')
            @else
                <form method="POST" action="{{ route($route) }}" id="resource-form">
            @endif
                @csrf

                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        @if (isset($fieldGroups) && count($fieldGroups) > 0)
                            {{-- Abas para grupos de campos --}}
                            <ul class="nav nav-tabs mb-4" role="tablist">
                                @foreach ($fieldGroups as $index => $group)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $loop->first ? 'active' : '' }} text-nowrap"
                                                data-tab="{{ $group['key'] }}"
                                                type="button"
                                                role="tab">
                                            {{ __($group['label'] ?? $group['key']) }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            @foreach ($fieldGroups as $group)
                                <div class="tab-panel {{ !$loop->first ? 'd-none' : '' }}" data-tab-panel="{{ $group['key'] }}">
                                    @include('layouts.fields', ['fields' => $group['fields']])
                                </div>
                            @endforeach
                        @else
                            @include('layouts.fields', ['fields' => $fields])

                            @if (!empty($subfields))
                                <hr class="my-4">
                                @include('layouts.fields', ['fields' => $subfields])
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Botões --}}
                @php
                    $resourcePrefix = explode('.', is_array($route) ? $route[0] : $route)[0];
                @endphp
                <div class="d-flex gap-3 justify-content-end">
                    <a href="{{ route($resourcePrefix . '.index') }}" class="btn btn-light px-4 py-2 rounded-3 fw-medium">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-medium text-white">
                        {{ __($btn_label) }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script de tabs - sempre carregado quando há fieldGroups --}}
@if (isset($fieldGroups) && count($fieldGroups) > 0)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tab switching
    document.querySelectorAll('[data-tab]').forEach(function(button) {
        button.addEventListener('click', function() {
            var tabKey = this.getAttribute('data-tab');

            // Update active tab button
            document.querySelectorAll('[data-tab]').forEach(function(btn) {
                btn.classList.remove('active');
            });
            this.classList.add('active');

            // Show corresponding panel
            document.querySelectorAll('[data-tab-panel]').forEach(function(panel) {
                panel.classList.add('d-none');
            });
            var targetPanel = document.querySelector('[data-tab-panel="' + tabKey + '"]');
            if (targetPanel) {
                targetPanel.classList.remove('d-none');
            }
        });
    });
});
</script>
@endpush
@endif

{{-- Script de erros de validação --}}
@if ($errors->any())
@push('scripts')
<script>
window.addEventListener('load', function () {
    // Mostrar toast com mensagem de erro
    var errorCount = {{ $errors->count() }};
    var message = errorCount === 1
        ? '{{ __("There is 1 field with error. Please check the form.") }}'
        : '{{ __("There are :count fields with errors. Please check the form.") }}'.replace(':count', errorCount);
    showToast(message, 'danger');

    // Encontrar primeiro campo com erro
    var firstInvalidField = document.querySelector('.is-invalid');
    if (firstInvalidField) {
        // Verificar se está em uma aba
        var tabPanel = firstInvalidField.closest('[data-tab-panel]');
        if (tabPanel) {
            var tabKey = tabPanel.getAttribute('data-tab-panel');
            // Ativar a aba correspondente
            var tabButton = document.querySelector('[data-tab="' + tabKey + '"]');
            if (tabButton) {
                tabButton.click();
            }
        }

        // Focar no campo
        firstInvalidField.focus();
        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>
@endpush
@endif
