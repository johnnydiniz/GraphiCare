@extends('layouts.list')

@section('table')
    @php
        $formatCurrency = function($value) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        };
        $servicosData = $servicos->map(function($s) use ($formatCurrency) {
            return [
                'id' => $s->id,
                'descricao' => $s->descricao,
                'ativo' => $s->ativo,
                'custo_estimado' => $formatCurrency($s->custo_estimado),
                'componentes' => $s->componenteServico->map(function($c) use ($formatCurrency) {
                    $qtde = $c->pivot->qtde ?? 1;
                    $custoBase = $c->custo_operacional ?? 0; // Custo base do componente
                    $custoOperacionalAdicional = $c->pivot->custo_operacional ?? 0; // Custo adicional
                    $custoTotal = ($custoBase * $qtde) + $custoOperacionalAdicional;
                    return [
                        'tipo' => $c->tipo == 'material' ? __('Material') : __('Equipment'),
                        'descricao' => $c->descricao,
                        'qtde' => $qtde,
                        'custo' => $formatCurrency($custoTotal)
                    ];
                })
            ];
        });
    @endphp

<div class="p-4 py-3">
    <div class="table-responsive rounded-3 border border-light bg-light">
        <x-data-table
        :columns="[
            ['label' => 'ID', 'width' => '8%'],
            ['label' => __('Description'), 'width' => '35%'],
            ['label' => __('Components'), 'width' => '12%'],
            ['label' => __('Estimated Cost'), 'width' => '20%'],
            ['label' => __('Actions'), 'width' => '25%'],
        ]"
        :items="$servicos"
        empty-message="{{ __('No services registered') }}">
        @foreach ($servicos as $servico)
            <tr class="border-top border-light align-middle" data-status="{{ $servico->ativo ? 'ativo' : 'inativo' }}">
                <td class="text-dark small py-2 ps-4" style="height: 72px;">
                    #{{ $servico->id }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $servico->descricao }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $servico->componenteServico->count() }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $formatCurrency($servico->custo_estimado) }}
                </td>
                <td class="text-dark small py-2 ps-4">
                    <x-table-actions
                        :id="$servico->id"
                        prefix="servico"
                        :view-details="[
                            __('Description') => ['label' => __('Description'), 'value' => $servico->descricao],
                            __('Estimated Cost') => ['label' => __('Estimated Cost'), 'value' => $formatCurrency($servico->custo_estimado)],
                            __('Active') => ['label' => __('Active'), 'value' => $servico->ativo],
                        ]"
                        :view-title="__('Service Details') . ' #' . $servico->id"
                        :custom-view="true"
                        :edit-route="route('servico.editar', $servico->id)"
                        :toggle-route="route('servico.toggle-status', $servico->id)"
                        :is-active="$servico->ativo"
                        :delete-route="route('servico.excluir', $servico->id)"
                        :delete-message="__('Are you sure you want to delete this service?')"/>
                </td>
            </tr>
        @endforeach
        </x-data-table>
    </div>
</div>

<x-confirm-modal/>
<x-view-modal/>
@endsection

@push('scripts')
    <script>
        window.addEventListener('load', function () {
            // Preparar dados dos serviços para visualização detalhada
            var servicosData = @json($servicosData);

            // Override do modal de visualização para serviços
            var viewModal = document.getElementById('viewDetailsModal');
            if (viewModal) {
                viewModal.addEventListener('show.bs.modal', function (event) {
                    var trigger = event.relatedTarget;
                    if (!trigger) return;

                    var formId = trigger.getAttribute('data-form-id');
                    if (!formId || !formId.includes('servico')) return;

                    // Encontrar o ID do serviço
                    var match = formId.match(/servico-(\d+)/);
                    if (!match) return;

                    var servicoId = parseInt(match[1]);
                    var servico = servicosData.find(function (s) {
                        return s.id === servicoId;
                    });
                    if (!servico) return;

                    var titleEl = viewModal.querySelector('.modal-title');
                    titleEl.textContent = '{{ __("Service Details") }} #' + servico.id;

                    var contentEl = viewModal.querySelector('.view-modal-content');
                    var html = '<div class="row g-3 mb-4">';
                    html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Description") }}</small><span class="fw-medium">' + servico.descricao + '</span></div></div>';
                    html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Estimated Cost") }}</small><span class="fw-medium">' + servico.custo_estimado + '</span></div></div>';
                    html += '<div class="col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">{{ __("Active") }}</small><span class="badge ' + (servico.ativo ? 'bg-success' : 'bg-secondary') + '">' + (servico.ativo ? '{{ __("Yes") }}' : '{{ __("No") }}') + '</span></div></div>';
                    html += '</div>';

                    html += '<h6 class="fw-bold mb-3">{{ __("Service Components") }}</h6>';
                    if (servico.componentes.length > 0) {
                        html += '<div class="table-responsive"><table class="table table-sm table-bordered mb-0">';
                        html += '<thead class="bg-light"><tr><th class="small">#</th><th class="small">{{ __("Type") }}</th><th class="small">{{ __("Description") }}</th><th class="small text-center">{{ __("Qty") }}</th><th class="small text-end">{{ __("Cost") }}</th></tr></thead>';
                        html += '<tbody>';
                        servico.componentes.forEach(function (c, index) {
                            html += '<tr><td class="small">' + (index + 1) + '</td><td class="small">' + c.tipo + '</td><td class="small">' + c.descricao + '</td><td class="small text-center">' + c.qtde + '</td><td class="small text-end">' + c.custo + '</td></tr>';
                        });
                        html += '</tbody></table></div>';
                    } else {
                        html += '<p class="text-muted mb-0">{{ __("No service components registered") }}</p>';
                    }

                    contentEl.innerHTML = html;
                });
            }
        });
    </script>
@endpush
