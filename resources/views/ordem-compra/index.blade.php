@extends('layouts.list')

@section('table')
    @php
        $formatCurrency = function($value) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        };
        $ordensData = $ordensCompra->map(function($oc) use ($formatCurrency) {
            return [
                'id' => $oc->id,
                'fornecedor' => $oc->fornecedor->pessoa->nome_social ?? $oc->fornecedor->pessoa->nome_registro ?? '-',
                'status' => $oc->status,
                'itens' => $oc->entradas->count(),
                'valor_total' => $formatCurrency($oc->valor_total),
                'nota_fiscal' => $oc->nota_fiscal ?? '-',
                'data_emissao' => $oc->data_emissao ? $oc->data_emissao->format('d/m/Y') : '-',
                'data_entrega' => $oc->data_entrega ? $oc->data_entrega->format('d/m/Y') : '-',
                'observacoes' => $oc->observacoes ?? '-',
                'ativo' => $oc->ativo,
                'entradas' => $oc->entradas->map(function($e) use ($formatCurrency) {
                    return [
                        'materia_prima' => $e->materiaPrima->descricao ?? '-',
                        'qtde' => $e->qtde,
                        'valor_unitario' => $formatCurrency($e->valor_unitario),
                        'subtotal' => $formatCurrency($e->valor_unitario * $e->qtde),
                    ];
                }),
            ];
        });
    @endphp

<div class="p-4 py-3">
    <div class="table-responsive rounded-3 border border-light bg-light">
        <x-data-table
        :columns="[
            ['label' => 'ID', 'width' => '5%'],
            ['label' => 'Fornecedor', 'width' => '18%'],
            ['label' => 'Status', 'width' => '10%'],
            ['label' => 'Itens', 'width' => '6%'],
            ['label' => 'Valor Total', 'width' => '11%'],
            ['label' => 'Nota Fiscal', 'width' => '10%'],
            ['label' => 'Data Emissão', 'width' => '10%'],
            ['label' => 'Ações', 'width' => '30%'],
        ]"
        :items="$ordensCompra"
        empty-message="Nenhuma ordem de compra cadastrada">
        @foreach ($ordensCompra as $oc)
            <tr class="border-top border-light align-middle" data-status="{{ $oc->ativo ? 'ativo' : 'inativo' }}">
                <td class="text-dark small py-2 ps-4" style="height: 72px;">
                    #{{ $oc->id }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $oc->fornecedor->pessoa->nome_social ?? $oc->fornecedor->pessoa->nome_registro ?? '-' }}
                </td>
                <td class="small py-2 ps-4" style="height: 72px;">
                    @if($oc->status === 'recebida')
                        <span class="badge bg-success">Recebida</span>
                    @else
                        <span class="badge bg-secondary">Pendente</span>
                    @endif
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $oc->entradas->count() }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $formatCurrency($oc->valor_total) }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $oc->nota_fiscal ?? '-' }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $oc->data_emissao ? $oc->data_emissao->format('d/m/Y') : '-' }}
                </td>
                <td class="text-dark small py-2 ps-4">
                    <div class="d-flex gap-1 align-items-center">
                        @if($oc->status === 'pendente')
                            <button type="button" class="btn btn-sm btn-success btn-receber"
                                data-bs-toggle="modal"
                                data-bs-target="#receberBoletoModal"
                                data-oc-id="{{ $oc->id }}"
                                data-oc-valor="{{ $formatCurrency($oc->valor_total) }}"
                                title="Receber">
                                <span style="font-size: 12px"><i class="fa-solid fa-truck-ramp-box"></i></span>
                            </button>
                        @else
                            <button class="btn btn-sm btn-secondary" disabled title="Recebida">
                                <span style="font-size: 12px"><i class="fa-solid fa-check"></i></span>
                            </button>
                        @endif
                        <x-table-actions
                            :id="$oc->id"
                            prefix="ordem-compra"
                            :view-details="[
                                'Fornecedor' => ['label' => 'Fornecedor', 'value' => $oc->fornecedor->pessoa->nome_social ?? $oc->fornecedor->pessoa->nome_registro ?? '-'],
                                'Ativo' => ['label' => 'Ativo', 'value' => $oc->ativo],
                            ]"
                            :view-title="'Detalhes da Ordem de Compra #' . $oc->id"
                            :custom-view="true"
                            :edit-route="$oc->status !== 'recebida' ? route('ordem-compra.editar', $oc->id) : null"
                            :toggle-route="route('ordem-compra.toggle-status', $oc->id)"
                            :is-active="$oc->ativo"
                            :delete-route="$oc->status !== 'recebida' ? route('ordem-compra.excluir', $oc->id) : null"
                            :delete-message="'Tem certeza que deseja excluir esta ordem de compra?'" />
                    </div>
                </td>
            </tr>
        @endforeach
        </x-data-table>
    </div>
</div>

<x-confirm-modal />
<x-view-modal />

{{-- Receber + Boleto Modal --}}
<div class="modal fade" id="receberBoletoModal" tabindex="-1" aria-labelledby="receberBoletoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="receberBoletoForm" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="receberBoletoModalLabel">Receber Ordem de Compra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Ao receber a ordem de compra, o estoque será atualizado e um boleto será gerado automaticamente. Preencha os dados do boleto abaixo.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium text-muted">Valor do Boleto</label>
                        <p class="fw-bold fs-5 text-primary mb-0" id="receberBoletoValor"></p>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label for="boleto_data_emissao" class="form-label fw-medium">Data de Emissão <span class="text-danger">*</span></label>
                            <input type="date" class="form-control py-2" id="boleto_data_emissao" name="boleto_data_emissao" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label for="boleto_data_vencimento" class="form-label fw-medium">Data de Vencimento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control py-2" id="boleto_data_vencimento" name="boleto_data_vencimento" value="{{ now()->addDays(30)->format('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="boleto_observacoes" class="form-label fw-medium">Observações</label>
                        <textarea class="form-control py-2" id="boleto_observacoes" name="boleto_observacoes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-truck-ramp-box me-1"></i> Receber e Gerar Boleto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.addEventListener('load', function () {
    var ordensData = @json($ordensData);

    var viewModal = document.getElementById('viewDetailsModal');
    if (viewModal) {
        viewModal.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) return;

            var formId = trigger.getAttribute('data-form-id');
            if (!formId || !formId.includes('ordem-compra')) return;

            var match = formId.match(/ordem-compra-(\d+)/);
            if (!match) return;

            var ocId = parseInt(match[1]);
            var oc = ordensData.find(function (o) {
                return o.id === ocId;
            });
            if (!oc) return;

            var titleEl = viewModal.querySelector('.modal-title');
            titleEl.textContent = 'Detalhes da Ordem de Compra #' + oc.id;

            var statusBadge = '';
            if (oc.status === 'recebida') {
                statusBadge = '<span class="badge bg-success">Recebida</span>';
            } else {
                statusBadge = '<span class="badge bg-secondary">Pendente</span>';
            }

            var contentEl = viewModal.querySelector('.view-modal-content');
            var html = '<div class="row g-3 mb-4">';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Fornecedor</small><span class="fw-medium">' + oc.fornecedor + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Status</small>' + statusBadge + '</div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Ativo</small><span class="badge ' + (oc.ativo ? 'bg-success' : 'bg-secondary') + '">' + (oc.ativo ? 'Sim' : 'Não') + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Nota Fiscal</small><span class="fw-medium">' + oc.nota_fiscal + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Data de Emissão</small><span class="fw-medium">' + oc.data_emissao + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Data de Entrega</small><span class="fw-medium">' + oc.data_entrega + '</span></div></div>';
            html += '<div class="col-md-12"><div class="border rounded-3 p-3 h-100 bg-primary bg-opacity-10"><small class="text-muted d-block mb-1">Valor Total</small><span class="fw-bold fs-5 text-primary">' + oc.valor_total + '</span></div></div>';
            if (oc.observacoes && oc.observacoes !== '-') {
                html += '<div class="col-md-12"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Observações</small><span class="fw-medium">' + oc.observacoes + '</span></div></div>';
            }
            html += '</div>';

            html += '<h6 class="fw-bold mb-3">Itens</h6>';
            if (oc.entradas.length > 0) {
                html += '<div class="table-responsive"><table class="table table-sm table-bordered mb-0">';
                html += '<thead class="bg-light"><tr><th class="small">#</th><th class="small">Matéria-Prima</th><th class="small text-center">Qtde</th><th class="small text-end">Valor Unitário</th><th class="small text-end">Subtotal</th></tr></thead>';
                html += '<tbody>';
                oc.entradas.forEach(function (e, index) {
                    html += '<tr><td class="small">' + (index + 1) + '</td><td class="small">' + e.materia_prima + '</td><td class="small text-center">' + e.qtde + '</td><td class="small text-end">' + e.valor_unitario + '</td><td class="small text-end">' + e.subtotal + '</td></tr>';
                });
                html += '</tbody></table></div>';
            } else {
                html += '<p class="text-muted mb-0">Nenhum item cadastrado</p>';
            }

            contentEl.innerHTML = html;
        });
    }

    // Receber + Boleto modal
    var receberModal = document.getElementById('receberBoletoModal');
    if (receberModal) {
        receberModal.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) return;

            var ocId = trigger.getAttribute('data-oc-id');
            var ocValor = trigger.getAttribute('data-oc-valor');

            var form = document.getElementById('receberBoletoForm');
            form.action = '/ordem-compra/receber/' + ocId;

            document.getElementById('receberBoletoValor').textContent = ocValor;
            document.getElementById('boleto_observacoes').value = '';
        });
    }
});
</script>
@endpush
