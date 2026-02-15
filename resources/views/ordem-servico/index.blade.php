@extends('layouts.list')

@section('table')
    @php
        $formatCurrency = function($value) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        };
        $ordensData = $ordensServico->map(function($os) use ($formatCurrency) {
            return [
                'id' => $os->id,
                'cliente' => $os->cliente->pessoa->nome_social ?? $os->cliente->pessoa->nome_registro ?? '-',
                'custo_final' => $formatCurrency($os->custo_final),
                'taxa_lucro' => number_format($os->taxa_lucro ?? 0, 2, ',', '.') . '%',
                'desconto' => number_format($os->desconto ?? 0, 2, ',', '.') . '%',
                'valor_final' => $formatCurrency($os->valor_final),
                'tipo_entrega' => $os->tipo_entrega == 'entrega' ? __('Delivery') : __('Pickup'),
                'data_inicio' => $os->data_inicio ? $os->data_inicio->format('d/m/Y') : '-',
                'data_fim' => $os->data_fim ? $os->data_fim->format('d/m/Y') : '-',
                'data_entrega' => $os->data_entrega ? $os->data_entrega->format('d/m/Y') : '-',
                'observacoes' => $os->observacoes ?? '-',
                'ativo' => $os->ativo,
                'status' => $os->status,
                'orcamento_id' => $os->orcamento_id,
                'servicos' => $os->servicos->map(function($s) use ($formatCurrency) {
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
            ['label' => 'ID', 'width' => '5%'],
            ['label' => __('Customer'), 'width' => '16%'],
            ['label' => __('Status'), 'width' => '10%'],
            ['label' => __('Services'), 'width' => '7%'],
            ['label' => __('Final Value'), 'width' => '11%'],
            ['label' => __('Delivery'), 'width' => '9%'],
            ['label' => __('Quote'), 'width' => '7%'],
            ['label' => __('Start Date'), 'width' => '9%'],
            ['label' => __('Actions'), 'width' => '26%'],
        ]"
        :items="$ordensServico"
        empty-message="{{ __('No orders registered') }}">
        @foreach ($ordensServico as $os)
            <tr class="border-top border-light align-middle" data-status="{{ $os->ativo ? 'ativo' : 'inativo' }}">
                <td class="text-dark small py-2 ps-4" style="height: 72px;">
                    #{{ $os->id }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $os->cliente->pessoa->nome_social ?? $os->cliente->pessoa->nome_registro ?? '-' }}
                </td>
                <td class="small py-2 ps-4" style="height: 72px;">
                    @if($os->status === 'finalizada')
                        <span class="badge bg-success">{{ __('Finished') }}</span>
                    @elseif($os->status === 'em_andamento')
                        <span class="badge bg-warning text-dark">{{ __('In Progress') }}</span>
                    @else
                        <span class="badge bg-secondary">{{ __('Pending') }}</span>
                    @endif
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $os->servicos->count() }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $formatCurrency($os->valor_final) }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $os->tipo_entrega == 'entrega' ? __('Delivery') : __('Pickup') }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    @if($os->orcamento_id)
                        <a href="{{ route('orcamento.editar', $os->orcamento_id) }}" class="text-primary">#{{ $os->orcamento_id }}</a>
                    @else
                        -
                    @endif
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $os->data_inicio ? $os->data_inicio->format('d/m/Y') : '-' }}
                </td>
                <td class="text-dark small py-2 ps-4">
                    <div class="d-flex gap-1 align-items-center">
                        {{-- Print Dropdown --}}
                        <div class="dropdown d-inline">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Print') }}">
                                <span style="font-size: 12px"><i class="fa-solid fa-print"></i></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item small" href="{{ route('ordem-servico.print-producao', $os->id) }}" target="_blank"><i class="fa-solid fa-industry me-2"></i>{{ __('Production View') }}</a></li>
                                <li><a class="dropdown-item small" href="{{ route('ordem-servico.print-admin', $os->id) }}" target="_blank"><i class="fa-solid fa-user-tie me-2"></i>{{ __('Administrative View') }}</a></li>
                            </ul>
                        </div>
                        @if($os->status === 'pendente')
                            <form action="{{ route('ordem-servico.iniciar', $os->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success" title="{{ __('Start') }}">
                                    <span style="font-size: 12px"><i class="fa-solid fa-play"></i></span>
                                </button>
                            </form>
                        @elseif($os->status === 'em_andamento')
                            <button type="button" class="btn btn-sm btn-danger btn-finalizar"
                                data-os-id="{{ $os->id }}"
                                data-bs-toggle="modal"
                                data-bs-target="#finalizarModal"
                                title="{{ __('Finish') }}">
                                <span style="font-size: 12px"><i class="fa-solid fa-flag-checkered"></i></span>
                            </button>
                        @else
                            <button class="btn btn-sm btn-secondary" disabled title="{{ __('Finished') }}">
                                <span style="font-size: 12px"><i class="fa-solid fa-check"></i></span>
                            </button>
                        @endif
                        <x-table-actions
                            :id="$os->id"
                            prefix="ordem-servico"
                            :view-details="[
                                __('Customer') => ['label' => __('Customer'), 'value' => $os->cliente->pessoa->nome_social ?? $os->cliente->pessoa->nome_registro ?? '-'],
                                __('Active') => ['label' => __('Active'), 'value' => $os->ativo],
                            ]"
                            :view-title="__('Order Details') . ' #' . $os->id"
                            :custom-view="true"
                            :edit-route="route('ordem-servico.editar', $os->id)"
                            :toggle-route="route('ordem-servico.toggle-status', $os->id)"
                            :is-active="$os->ativo"
                            :delete-route="route('ordem-servico.excluir', $os->id)"
                            :delete-message="__('Are you sure you want to delete this order?')" />
                    </div>
                </td>
            </tr>
        @endforeach
        </x-data-table>
    </div>
</div>

{{-- Finalization Modal --}}
<div class="modal fade" id="finalizarModal" tabindex="-1" aria-labelledby="finalizarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="finalizarForm" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="finalizarModalLabel">{{ __('Finish Order') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        {{ __('Review the materials used in this order. If there was any loss or breakage, enter the quantity in the corresponding field.') }}
                    </div>

                    <div id="finalizarLoading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">{{ __('Loading...') }}</span>
                        </div>
                        <p class="mt-2 text-muted">{{ __('Loading...') }}</p>
                    </div>

                    <div id="finalizarContent" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="small">{{ __('Component') }}</th>
                                        <th class="small">{{ __('Raw Material') }}</th>
                                        <th class="small text-center">{{ __('Current Stock') }}</th>
                                        <th class="small text-center">{{ __('Qty Used') }}</th>
                                        <th class="small text-center" style="width: 130px;">{{ __('Loss/Breakage') }}</th>
                                        <th class="small text-center">{{ __('Total Deduction') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="finalizarComponentes">
                                </tbody>
                            </table>
                        </div>
                        <div id="finalizarSemMateriais" class="text-muted text-center py-3" style="display: none;">
                            {{ __('No material components in this order.') }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger" id="btnConfirmarFinalizar">
                        <i class="fa-solid fa-flag-checkered me-1"></i> {{ __('Finish Order') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<x-confirm-modal />
<x-view-modal />
@endsection

@push('scripts')
<script>
window.addEventListener('load', function () {
    var ordensData = @json($ordensData);

    // View modal
    var viewModal = document.getElementById('viewDetailsModal');
    if (viewModal) {
        viewModal.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) return;

            var formId = trigger.getAttribute('data-form-id');
            if (!formId || !formId.includes('ordem-servico')) return;

            var match = formId.match(/ordem-servico-(\d+)/);
            if (!match) return;

            var osId = parseInt(match[1]);
            var os = ordensData.find(function (o) {
                return o.id === osId;
            });
            if (!os) return;

            var titleEl = viewModal.querySelector('.modal-title');
            titleEl.textContent = '{{ __("Order Details") }} #' + os.id;

            var statusBadge = '';
            if (os.status === 'finalizada') {
                statusBadge = '<span class="badge bg-success">{{ __("Finished") }}</span>';
            } else if (os.status === 'em_andamento') {
                statusBadge = '<span class="badge bg-warning text-dark">{{ __("In Progress") }}</span>';
            } else {
                statusBadge = '<span class="badge bg-secondary">{{ __("Pending") }}</span>';
            }

            var contentEl = viewModal.querySelector('.view-modal-content');
            var html = '<div class="row g-3 mb-4">';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Customer") }}</small><span class="fw-medium">' + os.cliente + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Status") }}</small>' + statusBadge + '</div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Active") }}</small><span class="badge ' + (os.ativo ? 'bg-success' : 'bg-secondary') + '">' + (os.ativo ? '{{ __("Yes") }}' : '{{ __("No") }}') + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Total Cost") }}</small><span class="fw-medium">' + os.custo_final + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Profit Rate") }}</small><span class="fw-medium text-success">' + os.taxa_lucro + '</span></div></div>';
            html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Discount") }}</small><span class="fw-medium text-danger">' + os.desconto + '</span></div></div>';
            html += '<div class="col-md-12"><div class="border rounded-3 p-3 h-100 bg-primary bg-opacity-10"><small class="text-muted d-block mb-1">{{ __("Final Value") }}</small><span class="fw-bold fs-5 text-primary">' + os.valor_final + '</span></div></div>';
            html += '<div class="col-md-3"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Delivery Type") }}</small><span class="fw-medium">' + os.tipo_entrega + '</span></div></div>';
            html += '<div class="col-md-3"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Start Date") }}</small><span class="fw-medium">' + os.data_inicio + '</span></div></div>';
            html += '<div class="col-md-3"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("End Date") }}</small><span class="fw-medium">' + os.data_fim + '</span></div></div>';
            html += '<div class="col-md-3"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Delivery Date") }}</small><span class="fw-medium">' + os.data_entrega + '</span></div></div>';
            if (os.orcamento_id) {
                html += '<div class="col-md-12"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Quote") }}</small><span class="fw-medium">#' + os.orcamento_id + '</span></div></div>';
            }
            if (os.observacoes && os.observacoes !== '-') {
                html += '<div class="col-md-12"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Notes") }}</small><span class="fw-medium">' + os.observacoes + '</span></div></div>';
            }
            html += '</div>';

            html += '<h6 class="fw-bold mb-3">{{ __("Services") }}</h6>';
            if (os.servicos.length > 0) {
                html += '<div class="table-responsive"><table class="table table-sm table-bordered mb-0">';
                html += '<thead class="bg-light"><tr><th class="small">#</th><th class="small">{{ __("Service") }}</th><th class="small text-center">{{ __("Qty") }}</th><th class="small text-end">{{ __("Unit Cost") }}</th><th class="small text-end">{{ __("Subtotal") }}</th></tr></thead>';
                html += '<tbody>';
                os.servicos.forEach(function (s, index) {
                    html += '<tr><td class="small">' + (index + 1) + '</td><td class="small">' + s.descricao + '</td><td class="small text-center">' + s.qtde + '</td><td class="small text-end">' + s.custo_unitario + '</td><td class="small text-end">' + s.subtotal + '</td></tr>';
                });
                html += '</tbody></table></div>';
            } else {
                html += '<p class="text-muted mb-0">{{ __("No services registered") }}</p>';
            }

            contentEl.innerHTML = html;
        });
    }

    // Finalization modal
    var finalizarModal = document.getElementById('finalizarModal');
    if (finalizarModal) {
        finalizarModal.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) return;

            var osId = trigger.getAttribute('data-os-id');
            if (!osId) return;

            // Set form action
            var form = document.getElementById('finalizarForm');
            form.action = '/ordem-servico/finalizar/' + osId;

            // Show loading, hide content
            document.getElementById('finalizarLoading').style.display = 'block';
            document.getElementById('finalizarContent').style.display = 'none';

            // Fetch components
            fetch('/ordem-servico/componentes/' + osId)
                .then(function (response) { return response.json(); })
                .then(function (componentes) {
                    document.getElementById('finalizarLoading').style.display = 'none';
                    document.getElementById('finalizarContent').style.display = 'block';

                    var tbody = document.getElementById('finalizarComponentes');
                    tbody.innerHTML = '';

                    var semMateriais = document.getElementById('finalizarSemMateriais');

                    if (componentes.length === 0) {
                        semMateriais.style.display = 'block';
                        tbody.parentElement.parentElement.style.display = 'none';
                    } else {
                        semMateriais.style.display = 'none';
                        tbody.parentElement.parentElement.style.display = 'block';

                        componentes.forEach(function (comp) {
                            var tr = document.createElement('tr');
                            var estoqueInsuficiente = comp.estoque_atual < comp.qtde_usada;

                            tr.innerHTML = '<td class="small">' + comp.descricao + '</td>' +
                                '<td class="small">' + comp.materia_prima + '</td>' +
                                '<td class="small text-center">' +
                                    comp.estoque_atual +
                                    (estoqueInsuficiente ? ' <i class="fa-solid fa-triangle-exclamation text-danger" title="{{ __("Insufficient stock") }}"></i>' : '') +
                                '</td>' +
                                '<td class="small text-center fw-medium">' + comp.qtde_usada + '</td>' +
                                '<td class="small text-center">' +
                                    '<input type="number" name="perdas[' + comp.componente_id + ']" ' +
                                    'class="form-control form-control-sm text-center perda-input" ' +
                                    'min="0" value="0" style="width: 80px; margin: 0 auto;">' +
                                '</td>' +
                                '<td class="small text-center fw-bold total-deducao">' + comp.qtde_usada + '</td>';
                            tbody.appendChild(tr);

                            // Update total deduction on loss input change
                            var input = tr.querySelector('.perda-input');
                            var totalCell = tr.querySelector('.total-deducao');
                            input.addEventListener('input', function () {
                                var perda = parseInt(this.value) || 0;
                                if (perda < 0) { perda = 0; this.value = 0; }
                                totalCell.textContent = comp.qtde_usada + perda;
                            });
                        });
                    }
                })
                .catch(function () {
                    document.getElementById('finalizarLoading').innerHTML = '<p class="text-danger">{{ __("Error loading components") }}</p>';
                });
        });
    }
});
</script>
@endpush
