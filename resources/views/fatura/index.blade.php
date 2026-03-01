@extends('layouts.list')

@section('table')
    @php
        $formatCurrency = function($value) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        };
        $faturasData = $faturas->map(function($fatura) use ($formatCurrency) {
            return [
                'id' => $fatura->id,
                'os_id' => $fatura->ordem_servico_id,
                'cliente' => $fatura->ordemServico->cliente->pessoa->nome_social ?? $fatura->ordemServico->cliente->pessoa->nome_registro ?? '-',
                'valor' => $formatCurrency($fatura->valor),
                'status' => $fatura->status,
                'data_emissao' => $fatura->data_emissao ? $fatura->data_emissao->format('d/m/Y') : '-',
                'data_vencimento' => $fatura->data_vencimento ? $fatura->data_vencimento->format('d/m/Y') : '-',
                'observacoes' => $fatura->observacoes ?? '-',
            ];
        });
    @endphp

<div class="p-4 py-3">
    <div class="table-responsive rounded-3 border border-light bg-light">
        <x-data-table
        :columns="[
            ['label' => 'ID', 'width' => '5%'],
            ['label' => 'OS#', 'width' => '7%'],
            ['label' => 'Cliente', 'width' => '18%'],
            ['label' => 'Valor', 'width' => '11%'],
            ['label' => 'Status', 'width' => '10%'],
            ['label' => 'Emissão', 'width' => '10%'],
            ['label' => 'Vencimento', 'width' => '10%'],
            ['label' => 'Ações', 'width' => '29%'],
        ]"
        :items="$faturas"
        empty-message="Nenhuma fatura cadastrada">
        @foreach ($faturas as $fatura)
            <tr class="border-top border-light align-middle">
                <td class="text-dark small py-2 ps-4" style="height: 72px;">
                    #{{ $fatura->id }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    #{{ $fatura->ordem_servico_id }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $fatura->ordemServico->cliente->pessoa->nome_social ?? $fatura->ordemServico->cliente->pessoa->nome_registro ?? '-' }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $formatCurrency($fatura->valor) }}
                </td>
                <td class="small py-2 ps-4" style="height: 72px;">
                    @if($fatura->status === 'paga')
                        <span class="badge bg-success">Paga</span>
                    @elseif($fatura->status === 'vencida')
                        <span class="badge bg-danger">Vencida</span>
                    @else
                        <span class="badge bg-warning text-dark">Pendente</span>
                    @endif
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $fatura->data_emissao ? $fatura->data_emissao->format('d/m/Y') : '-' }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $fatura->data_vencimento ? $fatura->data_vencimento->format('d/m/Y') : '-' }}
                </td>
                <td class="text-dark small py-2 ps-4">
                    <div class="d-flex gap-1 align-items-center">
                        @if($fatura->status !== 'paga')
                            <form id="fatura-pagar-form-{{ $fatura->id }}" action="{{ route('fatura.marcar-paga', $fatura->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('PATCH')
                            </form>
                            <button type="button" class="btn btn-sm btn-success"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmDeleteModal"
                                data-form-id="fatura-pagar-form-{{ $fatura->id }}"
                                data-message="Tem certeza que deseja marcar esta fatura como paga?"
                                title="Marcar como Paga">
                                <span style="font-size: 12px"><i class="fa-solid fa-check-double"></i></span>
                            </button>
                        @else
                            <button class="btn btn-sm btn-secondary" disabled title="Paga">
                                <span style="font-size: 12px"><i class="fa-solid fa-check"></i></span>
                            </button>
                        @endif
                        <x-table-actions
                            :id="$fatura->id"
                            prefix="fatura"
                            :view-details="[
                                'Cliente' => ['label' => 'Cliente', 'value' => $fatura->ordemServico->cliente->pessoa->nome_social ?? $fatura->ordemServico->cliente->pessoa->nome_registro ?? '-'],
                            ]"
                            :view-title="'Detalhes da Fatura #' . $fatura->id"
                            :custom-view="true"
                            :edit-route="route('fatura.editar', $fatura->id)"
                            :delete-route="route('fatura.excluir', $fatura->id)"
                            :delete-message="'Tem certeza que deseja excluir esta fatura?'" />
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
    var faturasData = @json($faturasData);

    var viewModal = document.getElementById('viewDetailsModal');
    if (viewModal) {
        viewModal.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) return;

            var formId = trigger.getAttribute('data-form-id');
            if (!formId || !formId.includes('fatura')) return;

            var match = formId.match(/fatura-(\d+)/);
            if (!match) return;

            var faturaId = parseInt(match[1]);
            var fatura = faturasData.find(function (f) {
                return f.id === faturaId;
            });
            if (!fatura) return;

            var titleEl = viewModal.querySelector('.modal-title');
            titleEl.textContent = 'Detalhes da Fatura #' + fatura.id;

            var statusBadge = '';
            if (fatura.status === 'paga') {
                statusBadge = '<span class="badge bg-success">Paga</span>';
            } else if (fatura.status === 'vencida') {
                statusBadge = '<span class="badge bg-danger">Vencida</span>';
            } else {
                statusBadge = '<span class="badge bg-warning text-dark">Pendente</span>';
            }

            var contentEl = viewModal.querySelector('.view-modal-content');
            var html = '<div class="row g-3 mb-4">';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Ordem de Serviço</small><span class="fw-medium">#' + fatura.os_id + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Cliente</small><span class="fw-medium">' + fatura.cliente + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Status</small>' + statusBadge + '</div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Data de Emissão</small><span class="fw-medium">' + fatura.data_emissao + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Data de Vencimento</small><span class="fw-medium">' + fatura.data_vencimento + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100 bg-primary bg-opacity-10"><small class="text-muted d-block mb-1">Valor</small><span class="fw-bold fs-5 text-primary">' + fatura.valor + '</span></div></div>';
            if (fatura.observacoes && fatura.observacoes !== '-') {
                html += '<div class="col-md-12"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Observações</small><span class="fw-medium">' + fatura.observacoes + '</span></div></div>';
            }
            html += '</div>';

            contentEl.innerHTML = html;
        });
    }
});
</script>
@endpush
