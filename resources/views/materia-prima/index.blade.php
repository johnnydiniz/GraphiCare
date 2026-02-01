@extends('layouts.list')

@section('table')
    @php
        $formatCurrency = function($value) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        };
    @endphp

<div class="p-4 py-3">
    <div class="table-responsive rounded-3 border border-light bg-light">
        <x-data-table
        :columns="[
            ['label' => 'ID', 'width' => '8%'],
            ['label' => __('Type'), 'width' => '15%'],
            ['label' => __('Description'), 'width' => '20%'],
            ['label' => __('Average Cost'), 'width' => '12%'],
            ['label' => __('Current Stock'), 'width' => '12%'],
            ['label' => __('Min. Stock'), 'width' => '10%'],
            ['label' => __('Actions'), 'width' => '23%'],
        ]"
        :items="$materiasPrimas"
        empty-message="{{ __('No raw materials registered') }}">
        @foreach ($materiasPrimas as $materiaPrima)
            <tr class="border-top border-light align-middle" data-status="{{ $materiaPrima->ativo ? 'ativo' : 'inativo' }}">
                <td class="text-dark small py-2 ps-4" style="height: 72px;">
                    #{{ $materiaPrima->id }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $materiaPrima->tipoMateriaPrima->descricao ?? '-' }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $materiaPrima->descricao }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $formatCurrency($materiaPrima->custo_medio) }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $materiaPrima->estoque_atual }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $materiaPrima->estoque_minimo ?? '-' }}
                </td>
                <td class="text-dark small py-2 ps-4">
                    <x-table-actions
                            :id="$materiaPrima->id"
                            prefix="materia-prima"
                            :view-details="[
                                __('Type') => ['label' => __('Type'), 'value' => $materiaPrima->tipoMateriaPrima->descricao ?? '-'],
                                __('Description') => ['label' => __('Description'), 'value' => $materiaPrima->descricao],
                                __('Average Cost') => ['label' => __('Average Cost'), 'value' => $formatCurrency($materiaPrima->custo_medio)],
                                __('Current Stock') => ['label' => __('Current Stock'), 'value' => $materiaPrima->estoque_atual],
                                __('Minimum Stock') => ['label' => __('Minimum Stock'), 'value' => $materiaPrima->estoque_minimo ?? '-'],
                                __('Stock Alert') => ['label' => __('Stock Alert'), 'value' => $materiaPrima->aviso_estoque],
                                __('Active') => ['label' => __('Active'), 'value' => $materiaPrima->ativo],
                            ]"
                            :view-title="__('Raw Material Details') . ' #' . $materiaPrima->id"
                            :edit-route="route('materia-prima.editar', $materiaPrima->id)"
                            :toggle-route="route('materia-prima.toggle-status', $materiaPrima->id)"
                            :is-active="$materiaPrima->ativo"
                            :delete-route="route('materia-prima.excluir', $materiaPrima->id)"
                            :delete-message="__('Are you sure you want to delete this raw material?')" />
                </td>
            </tr>
        @endforeach
        </x-data-table>
    </div>
</div>

<x-confirm-modal />
<x-view-modal />
@endsection
