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
            ['label' => 'ID', 'width' => '10%'],
            ['label' => __('Description'), 'width' => '40%'],
            ['label' => __('Base Cost'), 'width' => '20%'],
            ['label' => __('Actions'), 'width' => '30%'],
        ]"
        :items="$equipamentosOperacionais"
        empty-message="{{ __('No operational equipments registered') }}">
        @foreach ($equipamentosOperacionais as $equipamentoOperacional)
            <tr class="border-top border-light align-middle" data-status="{{ $equipamentoOperacional->ativo ? 'ativo' : 'inativo' }}">
                <td class="text-dark small py-2 ps-4" style="height: 72px;">
                    #{{ $equipamentoOperacional->id }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $equipamentoOperacional->descricao }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $formatCurrency($equipamentoOperacional->custo) }}
                </td>
                <td class="text-dark small py-2 ps-4">
                    <x-table-actions
                        :id="$equipamentoOperacional->id"
                        prefix="equipamento-operacional"
                        :view-details="[
                            __('Description') => ['label' => __('Description'), 'value' => $equipamentoOperacional->descricao],
                            __('Base Cost') => ['label' => __('Base Cost'), 'value' => $formatCurrency($equipamentoOperacional->custo)],
                            __('Active') => ['label' => __('Active'), 'value' => $equipamentoOperacional->ativo],
                        ]"
                        :view-title="__('Operational Equipment Details') . ' #' . $equipamentoOperacional->id"
                        :edit-route="route('equipamento-operacional.editar', $equipamentoOperacional->id)"
                        :toggle-route="route('equipamento-operacional.toggle-status', $equipamentoOperacional->id)"
                        :is-active="$equipamentoOperacional->ativo"
                        :delete-route="route('equipamento-operacional.excluir', $equipamentoOperacional->id)"
                        :delete-message="__('Are you sure you want to delete this operational equipment?')" />
                </td>
            </tr>
        @endforeach
        </x-data-table>
    </div>
</div>

<x-confirm-modal />
<x-view-modal />
@endsection
