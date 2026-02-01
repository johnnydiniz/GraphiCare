@extends('layouts.list')

@section('table')
<div class="p-4 py-3">
    <div class="table-responsive rounded-3 border border-light bg-light">
        <x-data-table
        :columns="[
            ['label' => 'ID', 'width' => '15%'],
            ['label' => __('Description'), 'width' => '55%'],
            ['label' => __('Actions'), 'width' => '30%'],
        ]"
        :items="$tipoContatos"
        empty-message="{{ __('No contact types registered') }}">
        @foreach ($tipoContatos as $tipoContato)
            <tr class="border-top border-light align-middle" data-status="{{ $tipoContato->ativo ? 'ativo' : 'inativo' }}">
                <td class="text-dark small py-2 ps-4" style="height: 72px;">
                    #{{ $tipoContato->id }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $tipoContato->descricao }}
                </td>
                <td class="text-dark small py-2 ps-4">
                    <x-table-actions
                        :id="$tipoContato->id"
                        prefix="tipo-contato"
                        :view-details="[
                            __('Description') => ['label' => __('Description'), 'value' => $tipoContato->descricao],
                            __('Active') => ['label' => __('Active'), 'value' => $tipoContato->ativo],
                        ]"
                        :view-title="__('Contact Type Details') . ' #' . $tipoContato->id"
                        :edit-route="route('tipo-contato.editar', $tipoContato->id)"
                        :toggle-route="route('tipo-contato.toggle-status', $tipoContato->id)"
                        :is-active="$tipoContato->ativo"
                        :delete-route="route('tipo-contato.excluir', $tipoContato->id)"
                        :delete-message="__('Are you sure you want to delete this contact type?')" />
                </td>
            </tr>
        @endforeach
        </x-data-table>
    </div>
</div>

<x-confirm-modal />
<x-view-modal />
@endsection
