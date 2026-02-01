@extends('layouts.list')

@section('table')
<div class="p-4 py-3">
    <div class="table-responsive rounded-3 border border-light bg-light">
        <x-data-table
        :columns="[
            ['label' => 'ID', 'width' => '8%'],
            ['label' => __('Type'), 'width' => '15%'],
            ['label' => __('Description'), 'width' => '30%'],
            ['label' => __('Raw Material') . ' / ' . __('Operational Equipment'), 'width' => '27%'],
            ['label' => __('Actions'), 'width' => '20%'],
        ]"
        :items="$componentesServico"
        empty-message="{{ __('No service components registered') }}">
        @foreach ($componentesServico as $componenteServico)
            <tr class="border-top border-light align-middle" data-status="{{ $componenteServico->ativo ? 'ativo' : 'inativo' }}">
                <td class="text-dark small py-2 ps-4" style="height: 72px;">
                    #{{ $componenteServico->id }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $componenteServico->tipo == 'material' ? __('Material') : __('Equipment') }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $componenteServico->descricao }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    @if($componenteServico->tipo == 'material')
                        {{ $componenteServico->materiaPrima->tipoMateriaPrima->descricao ?? '' }} {{ $componenteServico->materiaPrima->descricao ?? '-' }}
                    @else
                        {{ $componenteServico->equipamentoOperacional->descricao ?? '-' }}
                    @endif
                </td>
                <td class="text-dark small py-2 ps-4">
                    <x-table-actions
                        :id="$componenteServico->id"
                        prefix="componente-servico"
                        :view-details="[
                            __('Type') => ['label' => __('Type'), 'value' => $componenteServico->tipo == 'material' ? __('Material') : __('Equipment')],
                            __('Description') => ['label' => __('Description'), 'value' => $componenteServico->descricao],
                            __('Raw Material') => ['label' => __('Raw Material'), 'value' => ($componenteServico->materiaPrima->tipoMateriaPrima->descricao ?? '') . ' ' . ($componenteServico->materiaPrima->descricao ?? '-')],
                            __('Operational Equipment') => ['label' => __('Operational Equipment'), 'value' => $componenteServico->equipamentoOperacional->descricao ?? '-'],
                            __('Active') => ['label' => __('Active'), 'value' => $componenteServico->ativo],
                        ]"
                        :view-title="__('Service Component Details') . ' #' . $componenteServico->id"
                        :edit-route="route('componente-servico.editar', $componenteServico->id)"
                        :toggle-route="route('componente-servico.toggle-status', $componenteServico->id)"
                        :is-active="$componenteServico->ativo"
                        :delete-route="route('componente-servico.excluir', $componenteServico->id)"
                        :delete-message="__('Are you sure you want to delete this service component?')" />
                </td>
            </tr>
        @endforeach
        </x-data-table>
    </div>
</div>

<x-confirm-modal />
<x-view-modal />
@endsection
