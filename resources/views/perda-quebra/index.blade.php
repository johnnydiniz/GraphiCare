@extends('layouts.list')

@section('table')
    @php
        $motivoLabels = [
            'quebra' => 'Quebra',
            'desperdicio' => 'Desperdício',
            'vencimento' => 'Vencimento',
            'ajuste' => 'Ajuste de Inventário',
            'outro' => 'Outro',
        ];
        $motivoBadges = [
            'quebra' => 'bg-danger',
            'desperdicio' => 'bg-warning text-dark',
            'vencimento' => 'bg-warning text-dark',
            'ajuste' => 'bg-info',
            'outro' => 'bg-secondary',
        ];
        $perdasData = $perdasQuebras->map(function($pq) use ($motivoLabels) {
            return [
                'id' => $pq->id,
                'materia_prima' => $pq->materiaPrima->descricao ?? '-',
                'qtde' => $pq->qtde,
                'motivo' => $motivoLabels[$pq->motivo] ?? $pq->motivo,
                'data' => $pq->data ? $pq->data->format('d/m/Y') : '-',
                'observacoes' => $pq->observacoes ?? '-',
            ];
        });
    @endphp

<div class="p-4 py-3">
    <div class="table-responsive rounded-3 border border-light bg-light">
        <x-data-table
        :columns="[
            ['label' => 'ID', 'width' => '5%'],
            ['label' => 'Matéria-Prima', 'width' => '25%'],
            ['label' => 'Quantidade', 'width' => '10%'],
            ['label' => 'Motivo', 'width' => '15%'],
            ['label' => 'Data', 'width' => '12%'],
            ['label' => 'Ações', 'width' => '33%'],
        ]"
        :items="$perdasQuebras"
        empty-message="Nenhuma perda/quebra cadastrada">
        @foreach ($perdasQuebras as $pq)
            <tr class="border-top border-light align-middle" data-status="ativo">
                <td class="text-dark small py-2 ps-4" style="height: 72px;">
                    #{{ $pq->id }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $pq->materiaPrima->descricao ?? '-' }}
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $pq->qtde }}
                </td>
                <td class="small py-2 ps-4" style="height: 72px;">
                    <span class="badge {{ $motivoBadges[$pq->motivo] ?? 'bg-secondary' }}">
                        {{ $motivoLabels[$pq->motivo] ?? $pq->motivo }}
                    </span>
                </td>
                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                    {{ $pq->data ? $pq->data->format('d/m/Y') : '-' }}
                </td>
                <td class="text-dark small py-2 ps-4">
                    <x-table-actions
                        :id="$pq->id"
                        prefix="perda-quebra"
                        :view-details="[
                            'Matéria-Prima' => ['label' => 'Matéria-Prima', 'value' => $pq->materiaPrima->descricao ?? '-'],
                        ]"
                        :view-title="'Detalhes da Perda/Quebra #' . $pq->id"
                        :custom-view="true"
                        :edit-route="route('perda-quebra.editar', $pq->id)"
                        :delete-route="route('perda-quebra.excluir', $pq->id)"
                        :delete-message="'Tem certeza que deseja excluir esta perda/quebra? O estoque será revertido.'" />
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
    var perdasData = @json($perdasData);

    var viewModal = document.getElementById('viewDetailsModal');
    if (viewModal) {
        viewModal.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) return;

            var formId = trigger.getAttribute('data-form-id');
            if (!formId || !formId.includes('perda-quebra')) return;

            var match = formId.match(/perda-quebra-(\d+)/);
            if (!match) return;

            var pqId = parseInt(match[1]);
            var pq = perdasData.find(function (p) {
                return p.id === pqId;
            });
            if (!pq) return;

            var titleEl = viewModal.querySelector('.modal-title');
            titleEl.textContent = 'Detalhes da Perda/Quebra #' + pq.id;

            var contentEl = viewModal.querySelector('.view-modal-content');
            var html = '<div class="row g-3 mb-4">';
            html += '<div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Matéria-Prima</small><span class="fw-medium">' + pq.materia_prima + '</span></div></div>';
            html += '<div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Quantidade</small><span class="fw-medium">' + pq.qtde + '</span></div></div>';
            html += '<div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Motivo</small><span class="fw-medium">' + pq.motivo + '</span></div></div>';
            html += '<div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Data</small><span class="fw-medium">' + pq.data + '</span></div></div>';
            if (pq.observacoes && pq.observacoes !== '-') {
                html += '<div class="col-md-12"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block mb-1">Observações</small><span class="fw-medium">' + pq.observacoes + '</span></div></div>';
            }
            html += '</div>';

            contentEl.innerHTML = html;
        });
    }
});
</script>
@endpush
