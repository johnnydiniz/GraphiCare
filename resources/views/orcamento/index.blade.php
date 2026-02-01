@extends('layouts.list')

@section('table')
    @php
        $formatCurrency = function($value) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        };
        $orcamentosData = $orcamentos->map(function($o) use ($formatCurrency) {
            return [
                'id' => $o->id,
                'cliente' => $o->cliente->pessoa->nome_social ?? $o->cliente->pessoa->nome_registro ?? '-',
                'custo_final' => $formatCurrency($o->custo_final),
                'taxa_lucro' => number_format($o->taxa_lucro ?? 0, 2, ',', '.') . '%',
                'desconto' => number_format($o->desconto ?? 0, 2, ',', '.') . '%',
                'valor_final' => $formatCurrency($o->valor_final),
                'previsao_inicio' => $o->previsao_inicio ? $o->previsao_inicio->format('d/m/Y') : '-',
                'previsao_entrega' => $o->previsao_entrega ? $o->previsao_entrega->format('d/m/Y') : '-',
                'validade' => $o->validade ? $o->validade->format('d/m/Y') : '-',
                'observacoes' => $o->observacoes ?? '-',
                'ativo' => $o->ativo,
                'servicos' => $o->servicos->map(function($s) use ($formatCurrency) {
                    return [
                        'descricao' => $s->descricao,
                        'qtde' => $s->pivot->qtde ?? 1,
                        'custo_unitario' => $formatCurrency($s->custo_estimado),
                        'subtotal' => $formatCurrency($s->custo_estimado * ($s->pivot->qtde ?? 1))
                    ];
                })
            ];
        });
    @endphp

<div class="p-4 py-3">
    <div class="table-responsive rounded-3 border border-light bg-light">
        <x-data-table
        :columns="[
            ['label' => 'ID', 'width' => '6%'],
            ['label' => __('Customer'), 'width' => '22%'],
            ['label' => __('Services'), 'width' => '10%'],
            ['label' => __('Final Value'), 'width' => '15%'],
            ['label' => __('Validity'), 'width' => '12%'],
            ['label' => __('Created'), 'width' => '12%'],
            ['label' => __('Actions'), 'width' => '23%'],
        ]"
        :items="$orcamentos"
        empty-message="{{ __('No quotes registered') }}">
        @foreach ($orcamentos as $orcamento)
            <tr class="border-top border-light align-middle" data-status="{{ $orcamento->ativo ? 'ativo' : 'inativo' }}">
                <td class="text-dark small py-2 ps-4" style="height: 72px;">
                    #{{ $orcamento->id }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $orcamento->cliente->pessoa->nome_social ?? $orcamento->cliente->pessoa->nome_registro ?? '-' }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $orcamento->servicos->count() }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $formatCurrency($orcamento->valor_final) }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $orcamento->validade ? $orcamento->validade->format('d/m/Y') : '-' }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $orcamento->created_at ? $orcamento->created_at->format('d/m/Y') : '-' }}
                </td>
                <td class="text-dark small py-2 ps-4">
                    <x-table-actions
                        :id="$orcamento->id"
                        prefix="orcamento"
                        :view-details="[
                            __('Customer') => ['label' => __('Customer'), 'value' => $orcamento->cliente->pessoa->nome_social ?? $orcamento->cliente->pessoa->nome_registro ?? '-'],
                            __('Active') => ['label' => __('Active'), 'value' => $orcamento->ativo],
                        ]"
                        :view-title="__('Quote Details') . ' #' . $orcamento->id"
                        :custom-view="true"
                        :edit-route="route('orcamento.editar', $orcamento->id)"
                        :toggle-route="route('orcamento.toggle-status', $orcamento->id)"
                        :is-active="$orcamento->ativo"
                        :delete-route="route('orcamento.excluir', $orcamento->id)"
                        :delete-message="__('Are you sure you want to delete this quote?')" />
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
    var orcamentosData = @json($orcamentosData);

    var viewModal = document.getElementById('viewDetailsModal');
    if (viewModal) {
        viewModal.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) return;

            var formId = trigger.getAttribute('data-form-id');
            if (!formId || !formId.includes('orcamento')) return;

            var match = formId.match(/orcamento-(\d+)/);
            if (!match) return;

            var orcamentoId = parseInt(match[1]);
            var orcamento = orcamentosData.find(function (o) {
                return o.id === orcamentoId;
            });
            if (!orcamento) return;

            var titleEl = viewModal.querySelector('.modal-title');
            titleEl.textContent = '{{ __("Quote Details") }} #' + orcamento.id;

            var contentEl = viewModal.querySelector('.view-modal-content');
            var html = '<div class="row g-3 mb-4">';
            html += '<div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Customer") }}</small><span class="fw-medium">' + orcamento.cliente + '</span></div></div>';
            html += '<div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Active") }}</small><span class="badge ' + (orcamento.ativo ? 'bg-success' : 'bg-secondary') + '">' + (orcamento.ativo ? '{{ __("Yes") }}' : '{{ __("No") }}') + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Total Cost") }}</small><span class="fw-medium">' + orcamento.custo_final + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Profit Rate") }}</small><span class="fw-medium text-success">' + orcamento.taxa_lucro + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Discount") }}</small><span class="fw-medium text-danger">' + orcamento.desconto + '</span></div></div>';
            html += '<div class="col-md-12"><div class="border rounded-3 p-3 h-100 bg-primary bg-opacity-10"><small class="text-muted d-block mb-1">{{ __("Final Value") }}</small><span class="fw-bold fs-5 text-primary">' + orcamento.valor_final + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Expected Start") }}</small><span class="fw-medium">' + orcamento.previsao_inicio + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Expected Delivery") }}</small><span class="fw-medium">' + orcamento.previsao_entrega + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Validity") }}</small><span class="fw-medium">' + orcamento.validade + '</span></div></div>';
            if (orcamento.observacoes && orcamento.observacoes !== '-') {
                html += '<div class="col-md-12"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Notes") }}</small><span class="fw-medium">' + orcamento.observacoes + '</span></div></div>';
            }
            html += '</div>';

            html += '<h6 class="fw-bold mb-3">{{ __("Services") }}</h6>';
            if (orcamento.servicos.length > 0) {
                html += '<div class="table-responsive"><table class="table table-sm table-bordered mb-0">';
                html += '<thead class="bg-light"><tr><th class="small">#</th><th class="small">{{ __("Service") }}</th><th class="small text-center">{{ __("Qty") }}</th><th class="small text-end">{{ __("Unit Cost") }}</th><th class="small text-end">{{ __("Subtotal") }}</th></tr></thead>';
                html += '<tbody>';
                orcamento.servicos.forEach(function (s, index) {
                    html += '<tr><td class="small">' + (index + 1) + '</td><td class="small">' + s.descricao + '</td><td class="small text-center">' + s.qtde + '</td><td class="small text-end">' + s.custo_unitario + '</td><td class="small text-end">' + s.subtotal + '</td></tr>';
                });
                html += '</tbody></table></div>';
            } else {
                html += '<p class="text-muted mb-0">{{ __("No services registered") }}</p>';
            }

            contentEl.innerHTML = html;
        });
    }
});
</script>
@endpush
