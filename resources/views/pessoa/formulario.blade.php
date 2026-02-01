@extends('layouts.app')

@section('title')
    {{ __($title) }}
@endsection

@section('content')
    @include('layouts.form')
@endsection

@push('scripts')
<script>
window.addEventListener('load', function () {
    var form = document.querySelector('form');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        var tipoCliente = form.querySelector('[name="tipo_cliente"]');
        var tipoFornecedor = form.querySelector('[name="tipo_fornecedor"]');
        var cargo = form.querySelector('[name="cargo"]');
        var salario = form.querySelector('[name="salario"]');

        var hasCliente = tipoCliente && tipoCliente.value && tipoCliente.value !== 'nao_informado';
        var hasFornecedor = tipoFornecedor && tipoFornecedor.value && tipoFornecedor.value !== 'nao_informado';
        var hasFuncionario = (cargo && cargo.value.trim() !== '') || (salario && salario.value.trim() !== '');

        if (!hasCliente && !hasFornecedor && !hasFuncionario) {
            e.preventDefault();
            showToast('{{ __("You must fill in at least one subcategory: Customer, Provider, or Employee.") }}', 'danger');
        }
    });
});
</script>
@endpush
