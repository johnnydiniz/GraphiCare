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
        :items="$tipoMateriaPrimas"
        empty-message="{{ __('No raw material types registered') }}">
        @foreach ($tipoMateriaPrimas as $tipoMateriaPrima)
            <tr class="border-top border-light align-middle" data-status="{{ $tipoMateriaPrima->ativo ? 'ativo' : 'inativo' }}">
                <td class="text-dark small py-2 ps-4" style="height: 72px;">
                    #{{ $tipoMateriaPrima->id }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $tipoMateriaPrima->descricao }}
                </td>
                <td class="text-dark small py-2 ps-4">
                    <x-table-actions
                        :id="$tipoMateriaPrima->id"
                        prefix="tipo-materia-prima"
                        :view-details="[
                            __('Description') => ['label' => __('Description'), 'value' => $tipoMateriaPrima->descricao],
                            __('Active') => ['label' => __('Active'), 'value' => $tipoMateriaPrima->ativo],
                        ]"
                        :view-title="__('Raw Material Type Details') . ' #' . $tipoMateriaPrima->id"
                        :edit-route="route('tipo-materia-prima.editar', $tipoMateriaPrima->id)"
                        :toggle-route="route('tipo-materia-prima.toggle-status', $tipoMateriaPrima->id)"
                        :is-active="$tipoMateriaPrima->ativo"
                        :delete-route="route('tipo-materia-prima.excluir', $tipoMateriaPrima->id)"
                        :delete-message="__('Are you sure you want to delete this raw material type?')" />
                </td>
            </tr>
        @endforeach
        </x-data-table>
    </div>
</div>

<x-confirm-modal />
<x-view-modal />
@endsection
