@extends('layouts.list')

@section('table')
    @php
        $formatCurrency = function($value) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        };
        $boletosData = $boletos->map(function($boleto) use ($formatCurrency) {
            return [
                'id' => $boleto->id,
                'oc_id' => $boleto->ordem_compra_id,
                'fornecedor' => $boleto->ordemCompra->fornecedor->pessoa->nome_social ?? $boleto->ordemCompra->fornecedor->pessoa->nome_registro ?? '-',
                'valor' => $formatCurrency($boleto->valor),
                'status' => $boleto->status,
                'data_emissao' => $boleto->data_emissao ? $boleto->data_emissao->format('d/m/Y') : '-',
                'data_vencimento' => $boleto->data_vencimento ? $boleto->data_vencimento->format('d/m/Y') : '-',
                'observacoes' => $boleto->observacoes ?? '-',
            ];
        });
    @endphp

<div class="p-4 py-3">
    <div class="table-responsive rounded-3 border border-light bg-light">
        <x-data-table
        :columns="[
            ['label' => 'ID', 'width' => '5%'],
            ['label' => 'OC#', 'width' => '7%'],
            ['label' => 'Fornecedor', 'width' => '18%'],
            ['label' => 'Valor', 'width' => '11%'],
            ['label' => 'Status', 'width' => '10%'],
            ['label' => 'Emissão', 'width' => '10%'],
            ['label' => 'Vencimento', 'width' => '10%'],
            ['label' => 'Ações', 'width' => '29%'],
        ]"
        :items="$boletos"
        empty-message="Nenhum boleto cadastrado">
        @foreach ($boletos as $boleto)
            <tr class="border-top border-light align-middle">
                <td class="text-dark small py-2 ps-4" style="height: 72px;">
                    #{{ $boleto->id }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    #{{ $boleto->ordem_compra_id }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $boleto->ordemCompra->fornecedor->pessoa->nome_social ?? $boleto->ordemCompra->fornecedor->pessoa->nome_registro ?? '-' }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $formatCurrency($boleto->valor) }}
                </td>
                <td class="small py-2 ps-4" style="height: 72px;">
                    @if($boleto->status === 'pago')
                        <span class="badge bg-success">Pago</span>
                    @elseif($boleto->status === 'vencido')
                        <span class="badge bg-danger">Vencido</span>
                    @else
                        <span class="badge bg-warning text-dark">Pendente</span>
                    @endif
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $boleto->data_emissao ? $boleto->data_emissao->format('d/m/Y') : '-' }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $boleto->data_vencimento ? $boleto->data_vencimento->format('d/m/Y') : '-' }}
                </td>
                <td class="text-dark small py-2 ps-4">
                    <div class="d-flex gap-1 align-items-center">
                        @if($boleto->status !== 'pago')
                            <form id="boleto-pagar-form-{{ $boleto->id }}" action="{{ route('boleto.marcar-pago', $boleto->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('PATCH')
                            </form>
                            <button type="button" class="btn btn-sm btn-success"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmDeleteModal"
                                data-form-id="boleto-pagar-form-{{ $boleto->id }}"
                                data-message="Tem certeza que deseja marcar este boleto como pago?"
                                title="Marcar como Pago">
                                <span style="font-size: 12px"><i class="fa-solid fa-check-double"></i></span>
                            </button>
                        @else
                            <button class="btn btn-sm btn-secondary" disabled title="Pago">
                                <span style="font-size: 12px"><i class="fa-solid fa-check"></i></span>
                            </button>
                        @endif
                        <x-table-actions
                            :id="$boleto->id"
                            prefix="boleto"
                            :view-details="[
                                'Fornecedor' => ['label' => 'Fornecedor', 'value' => $boleto->ordemCompra->fornecedor->pessoa->nome_social ?? $boleto->ordemCompra->fornecedor->pessoa->nome_registro ?? '-'],
                            ]"
                            :view-title="'Detalhes do Boleto #' . $boleto->id"
                            :custom-view="true"
                            :edit-route="route('boleto.editar', $boleto->id)"
                            :delete-route="route('boleto.excluir', $boleto->id)"
                            :delete-message="'Tem certeza que deseja excluir este boleto?'" />
                    </div>
                </td>
            </tr>
        @endforeach
        </x-data-table>
    </div>
</div>

<x-confirm-modal />
<x-view-modal />
@endsection

@push('scripts')
<script>
window.addEventListener('load', function () {
    var boletosData = @json($boletosData);

    var viewModal = document.getElementById('viewDetailsModal');
    if (viewModal) {
        viewModal.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) return;

            var formId = trigger.getAttribute('data-form-id');
            if (!formId || !formId.includes('boleto')) return;

            var match = formId.match(/boleto-(\d+)/);
            if (!match) return;

            var boletoId = parseInt(match[1]);
            var boleto = boletosData.find(function (b) {
                return b.id === boletoId;
            });
            if (!boleto) return;

            var titleEl = viewModal.querySelector('.modal-title');
            titleEl.textContent = 'Detalhes do Boleto #' + boleto.id;

            var statusBadge = '';
            if (boleto.status === 'pago') {
                statusBadge = '<span class="badge bg-success">Pago</span>';
            } else if (boleto.status === 'vencido') {
                statusBadge = '<span class="badge bg-danger">Vencido</span>';
            } else {
                statusBadge = '<span class="badge bg-warning text-dark">Pendente</span>';
            }

            var contentEl = viewModal.querySelector('.view-modal-content');
            var html = '<div class="row g-3 mb-4">';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Ordem de Compra</small><span class="fw-medium">#' + boleto.oc_id + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Fornecedor</small><span class="fw-medium">' + boleto.fornecedor + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Status</small>' + statusBadge + '</div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Data de Emissão</small><span class="fw-medium">' + boleto.data_emissao + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Data de Vencimento</small><span class="fw-medium">' + boleto.data_vencimento + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100 bg-primary bg-opacity-10"><small class="text-muted d-block mb-1">Valor</small><span class="fw-bold fs-5 text-primary">' + boleto.valor + '</span></div></div>';
            if (boleto.observacoes && boleto.observacoes !== '-') {
                html += '<div class="col-md-12"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Observações</small><span class="fw-medium">' + boleto.observacoes + '</span></div></div>';
            }
            html += '</div>';

            contentEl.innerHTML = html;
        });
    }
});
</script>
@endpush
