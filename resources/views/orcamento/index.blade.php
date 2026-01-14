@extends('layouts.list')

@section('table')
    <!-- Tabela -->
    <div class="p-4 py-3">
        <div class="table-responsive rounded-3 border border-light bg-light">
            <table class="table table-borderless mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="text-dark small fw-medium py-3 ps-4" style="width: 400px;">ID</th>
                        <th class="text-dark small fw-medium py-3 ps-4" style="width: 400px;">
                            {{ __('Customer') }}</th>
                        <th class="text-dark small fw-medium py-3 ps-4" style="width: 240px;">
                            {{ __('Status') }}</th>
                        <th class="text-dark small fw-medium py-3 ps-4" style="width: 400px;">
                            {{ __('Date') }}</th>
                        <th class="text-dark small fw-medium py-3 ps-4" style="width: 400px;">
                            {{ __('Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($orcamentos->isEmpty())
                        <tr>
                            <td colspan="5" class="border-top border-light align-middle text-center">Nenhum
                                orçamento
                                cadastrado</td>
                        </tr>
                    @else
                        @foreach ($orcamentos as $orcamento)
                            <tr class="border-top border-light align-middle">
                                <td class="text-dark small py-2 ps-4" style="height: 72px;">
                                    #{{ $orcamento->id }}</td>
                                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                    {{ $orcamento->cliente->nome }}
                                </td>
                                <td class="small py-2 ps-4" style="height: 72px;">
                                    <button class="btn btn-light w-100 rounded-3 py-1" style="height: 32px;">
                                        <span class="text-truncate fw-medium">Completed</span>
                                    </button>
                                </td>
                                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                    {{ $orcamento->created_at }}</td>
                                <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                    {{ $formatter->formatCurrency($orcamento->valor_final, 'BRL') }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection()
