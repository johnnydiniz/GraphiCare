@extends('layouts.list')

@section('table')
    @php
        $formatCurrency = function($value) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        };
        $orcamentosData = $orcamentos->map(function($o) use ($formatCurrency) {
            // Calcular consumido por servico
            $consumido = [];
            foreach ($o->ordensServico as $os) {
                foreach ($os->servicos as $s) {
                    $consumido[$s->id] = ($consumido[$s->id] ?? 0) + ($s->pivot->qtde ?? 1);
                }
            }

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
                'expirado' => $o->validade && $o->validade->isPast(),
                'observacoes' => $o->observacoes ?? '-',
                'ativo' => $o->ativo,
                'ordens_count' => $o->ordensServico->count(),
                'servicos' => $o->servicos->map(function($s) use ($formatCurrency, $consumido) {
                    $qtdeOrc = $s->pivot->qtde ?? 1;
                    $qtdeCons = $consumido[$s->id] ?? 0;
                    return [
                        'descricao' => $s->descricao,
                        'qtde' => $qtdeOrc,
                        'qtde_consumida' => $qtdeCons,
                        'qtde_remanescente' => max(0, $qtdeOrc - $qtdeCons),
                        'custo_unitario' => $formatCurrency($s->custo_estimado),
                        'subtotal' => $formatCurrency($s->custo_estimado * $qtdeOrc)
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
                    @if($orcamento->validade && $orcamento->validade->isPast())
                        <span class="badge bg-danger ms-1">{{ __('Expired') }}</span>
                    @endif
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $orcamento->created_at ? $orcamento->created_at->format('d/m/Y') : '-' }}
                </td>
                <td class="text-dark small py-2 ps-4">
                    <div class="d-flex gap-1 align-items-center">
                        @php
                            $consumidoOrc = [];
                            foreach ($orcamento->ordensServico as $os) {
                                foreach ($os->servicos as $s) {
                                    $consumidoOrc[$s->id] = ($consumidoOrc[$s->id] ?? 0) + ($s->pivot->qtde ?? 1);
                                }
                            }
                            $todosEsgotados = $orcamento->servicos->count() > 0 && $orcamento->servicos->every(function($s) use ($consumidoOrc) {
                                return ($consumidoOrc[$s->id] ?? 0) >= ($s->pivot->qtde ?? 1);
                            });
                        @endphp
                        {{-- Print Dropdown --}}
                        <div class="dropdown d-inline">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Print') }}">
                                <span style="font-size: 12px"><i class="fa-solid fa-print"></i></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item small" href="{{ route('orcamento.print-cliente', $orcamento->id) }}" target="_blank"><i class="fa-solid fa-user me-2"></i>{{ __('Client View') }}</a></li>
                                <li><a class="dropdown-item small" href="{{ route('orcamento.print-admin', $orcamento->id) }}" target="_blank"><i class="fa-solid fa-user-tie me-2"></i>{{ __('Administrative View') }}</a></li>
                            </ul>
                        </div>
                        @if($todosEsgotados)
                            <button class="btn btn-sm btn-secondary" disabled title="{{ __('All services already consumed') }}">
                                <span style="font-size: 12px"><i class="fa-solid fa-file-circle-plus"></i></span>
                            </button>
                        @else
                            <a class="btn btn-sm btn-primary" href="{{ route('ordem-servico.inserir', ['orcamento_id' => $orcamento->id]) }}" title="{{ __('Generate Order') }}">
                                <span style="font-size: 12px"><i class="fa-solid fa-file-circle-plus"></i></span>
                            </a>
                        @endif
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
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Validity") }}</small><span class="fw-medium">' + orcamento.validade;
            if (orcamento.expirado) {
                html += ' <span class="badge bg-danger">{{ __("Expired") }}</span>';
            }
            html += '</span></div></div>';
            if (orcamento.ordens_count > 0) {
                html += '<div class="col-md-12"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Orders") }}</small><span class="fw-medium">' + orcamento.ordens_count + ' {{ __("order(s) generated") }}</span></div></div>';
            }
            if (orcamento.observacoes && orcamento.observacoes !== '-') {
                html += '<div class="col-md-12"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Notes") }}</small><span class="fw-medium">' + orcamento.observacoes + '</span></div></div>';
            }
            html += '</div>';

            html += '<h6 class="fw-bold mb-3">{{ __("Services") }}</h6>';
            if (orcamento.servicos.length > 0) {
                html += '<div class="table-responsive"><table class="table table-sm table-bordered mb-0">';
                html += '<thead class="bg-light"><tr><th class="small">#</th><th class="small">{{ __("Service") }}</th><th class="small text-center">{{ __("Qty") }}</th><th class="small text-center">{{ __("Consumed") }}</th><th class="small text-center">{{ __("Remaining") }}</th><th class="small text-end">{{ __("Unit Cost") }}</th><th class="small text-end">{{ __("Subtotal") }}</th></tr></thead>';
                html += '<tbody>';
                orcamento.servicos.forEach(function (s, index) {
                    var rowClass = s.qtde_remanescente <= 0 ? ' class="table-secondary"' : '';
                    var remanBadge = s.qtde_remanescente <= 0 ? '<span class="badge bg-secondary">{{ __("Consumed") }}</span>' : s.qtde_remanescente;
                    html += '<tr' + rowClass + '><td class="small">' + (index + 1) + '</td><td class="small">' + s.descricao + '</td><td class="small text-center">' + s.qtde + '</td><td class="small text-center">' + s.qtde_consumida + '</td><td class="small text-center">' + remanBadge + '</td><td class="small text-end">' + s.custo_unitario + '</td><td class="small text-end">' + s.subtotal + '</td></tr>';
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
