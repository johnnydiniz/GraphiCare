@extends('layouts.list')

@section('table')
    @php
        $clientes = $pessoas->filter(fn($p) => $p->cliente !== null);
        $fornecedores = $pessoas->filter(fn($p) => $p->fornecedor !== null);
        $funcionarios = $pessoas->filter(fn($p) => $p->funcionario !== null);
    @endphp

    <div class="p-4 py-3">
        <div class="table-responsive rounded-3 border border-light bg-light">
            {{-- Abas --}}
            <div class="d-flex border-bottom border-light px-4 gap-4 gap-md-8">
                <a class="tab-link d-flex align-items-center gap-2 border-bottom border-primary border-3 pb-3 pt-4 text-decoration-none"
                    href="javascript:void(0)" data-tab="geral">
                    <i class="fa-solid fa-users small text-dark"></i>
                    <span class="small fw-bold text-dark" style="letter-spacing: 0.015em;">{{ __('General') }}</span>
                </a>
                <a class="tab-link d-flex align-items-center gap-2 border-bottom border-3 pb-3 pt-4 text-decoration-none"
                    href="javascript:void(0)" data-tab="cliente"
                    style="border-bottom-color: transparent !important; color: #49739c;">
                    <i class="fa-solid fa-handshake small"></i>
                    <span class="small fw-bold" style="letter-spacing: 0.015em;">{{ __('Customer') }}</span>
                </a>
                <a class="tab-link d-flex align-items-center gap-2 border-bottom border-3 pb-3 pt-4 text-decoration-none"
                    href="javascript:void(0)" data-tab="fornecedor"
                    style="border-bottom-color: transparent !important; color: #49739c;">
                    <i class="fa-solid fa-truck small"></i>
                    <span class="small fw-bold" style="letter-spacing: 0.015em;">{{ __('Provider') }}</span>
                </a>
                <a class="tab-link d-flex align-items-center gap-2 border-bottom border-3 pb-3 pt-4 text-decoration-none"
                    href="javascript:void(0)" data-tab="funcionario"
                    style="border-bottom-color: transparent !important; color: #49739c;">
                    <i class="fa-solid fa-id-badge small"></i>
                    <span class="small fw-bold" style="letter-spacing: 0.015em;">{{ __('Employee') }}</span>
                </a>
            </div>

            {{-- Geral --}}
            <div class="tab-panel" data-tab-panel="geral">
                <x-data-table
                    :columns="[
                        ['label' => 'ID', 'width' => '8%'],
                        ['label' => __('Name'), 'width' => '22%'],
                        ['label' => __('SSN/EIN'), 'width' => '18%'],
                        ['label' => __('Login'), 'width' => '17%'],
                        ['label' => __('Categories'), 'width' => '15%'],
                        ['label' => __('Actions'), 'width' => '20%'],
                    ]"
                    :items="$pessoas"
                    empty-message="Nenhuma pessoa cadastrada">
                    @foreach ($pessoas as $pessoa)
                        <tr class="border-top border-light align-middle" data-status="{{ $pessoa->ativo ? 'ativo' : 'inativo' }}">
                            <td class="text-dark small py-2 ps-4" style="height: 72px;">
                                #{{ $pessoa->id }}
                            </td>
                            <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                {{ $pessoa->nome_social ?? $pessoa->nome_registro }}
                            </td>
                            <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                {{ $pessoa->cpf_cnpj }}
                            </td>
                            <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                {{ $pessoa->login }}
                            </td>
                            <td class="small py-2 ps-4" style="height: 72px;">
                                <div class="d-flex gap-2">
                                    @if($pessoa->cliente)
                                        <i class="fa-solid fa-handshake text-success" title="{{ __('Customer') }}"></i>
                                    @endif
                                    @if($pessoa->fornecedor)
                                        <i class="fa-solid fa-truck text-primary" title="{{ __('Provider') }}"></i>
                                    @endif
                                    @if($pessoa->funcionario)
                                        <i class="fa-solid fa-id-badge text-warning" title="{{ __('Employee') }}"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="text-dark small py-2 ps-4">
                                <x-table-actions
                                    :id="$pessoa->id"
                                    prefix="pessoa"
                                    :view-details="[
                                        __('Name') => ['label' => __('Name'), 'value' => $pessoa->nome_social ?? $pessoa->nome_registro],
                                        __('Legal Name') => ['label' => __('Legal Name'), 'value' => $pessoa->nome_registro],
                                        __('SSN/EIN') => ['label' => __('SSN/EIN'), 'value' => $pessoa->cpf_cnpj],
                                        __('Type') => ['label' => __('Type'), 'value' => $pessoa->tipo == 'fisica' ? __('Individual') : __('Company')],
                                        __('Login') => ['label' => __('Login'), 'value' => $pessoa->login],
                                        __('Birth Date') => ['label' => __('Birth Date'), 'value' => $pessoa->data_nascimento ? \Carbon\Carbon::parse($pessoa->data_nascimento)->format('d/m/Y') : null],
                                        __('Education') => ['label' => __('Education'), 'value' => __(ucfirst(str_replace('_', ' ', $pessoa->escolaridade ?? 'nao_informado')))],
                                        __('Active') => ['label' => __('Active'), 'value' => $pessoa->ativo],
                                    ]"
                                    :view-title="__('Person Details') . ' #' . $pessoa->id"
                                    :edit-route="route('pessoa.editar', $pessoa->id)"
                                    :toggle-route="route('pessoa.toggle-status', $pessoa->id)"
                                    :is-active="$pessoa->ativo"
                                    :delete-route="route('pessoa.excluir', $pessoa->id)"
                                    delete-message="Tem certeza que deseja excluir esta pessoa? Todos os registros relacionados (cliente, fornecedor, funcionário) também serão excluídos." />
                            </td>
                        </tr>
                    @endforeach
                </x-data-table>
            </div>

            {{-- Cliente --}}
            <div class="tab-panel d-none" data-tab-panel="cliente">
                <x-data-table
                    :columns="[
                        ['label' => 'ID', 'width' => '8%'],
                        ['label' => __('Name'), 'width' => '22%'],
                        ['label' => __('Customer Type'), 'width' => '25%'],
                        ['label' => __('Credit Limit'), 'width' => '25%'],
                        ['label' => __('Actions'), 'width' => '20%'],
                    ]"
                    :items="$clientes"
                    empty-message="Nenhum cliente cadastrado">
                    @foreach ($clientes as $pessoa)
                        <tr class="border-top border-light align-middle" data-status="{{ $pessoa->ativo ? 'ativo' : 'inativo' }}">
                            <td class="text-dark small py-2 ps-4" style="height: 72px;">
                                #{{ $pessoa->id }}
                            </td>
                            <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                {{ $pessoa->nome_social ?? $pessoa->nome_registro }}
                            </td>
                            <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                {{ ucfirst($pessoa->cliente->tipo ?? '-') }}
                            </td>
                            <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                {{ $pessoa->cliente->limite_credito ? 'R$ ' . number_format($pessoa->cliente->limite_credito, 2, ',', '.') : '-' }}
                            </td>
                            <td class="text-dark small py-2 ps-4">
                                <x-table-actions
                                    :id="$pessoa->cliente->id"
                                    prefix="cliente-{{ $pessoa->cliente->id }}"
                                    :view-details="[
                                        __('Name') => ['label' => __('Name'), 'value' => $pessoa->nome_social ?? $pessoa->nome_registro],
                                        __('SSN/EIN') => ['label' => __('SSN/EIN'), 'value' => $pessoa->cpf_cnpj],
                                        __('Customer Type') => ['label' => __('Customer Type'), 'value' => __(ucfirst($pessoa->cliente->tipo ?? '-'))],
                                        __('Credit Limit') => ['label' => __('Credit Limit'), 'value' => $pessoa->cliente->limite_credito ? 'R$ ' . number_format($pessoa->cliente->limite_credito, 2, ',', '.') : '-'],
                                        __('Discount Rate') => ['label' => __('Discount Rate'), 'value' => $pessoa->cliente->taxa_desconto ? number_format($pessoa->cliente->taxa_desconto, 2, ',', '.') . '%' : '-'],
                                        __('Active') => ['label' => __('Active'), 'value' => $pessoa->cliente->ativo],
                                    ]"
                                    :view-title="__('Customer Details') . ' #' . $pessoa->cliente->id"
                                    :edit-route="route('pessoa.editar', $pessoa->id)"
                                    :toggle-route="route('cliente.toggle-status', $pessoa->cliente->id)"
                                    :is-active="$pessoa->cliente->ativo"
                                    :delete-route="route('cliente.excluir', $pessoa->cliente->id)"
                                    delete-message="Tem certeza que deseja remover o registro de cliente desta pessoa?" />
                            </td>
                        </tr>
                    @endforeach
                </x-data-table>
            </div>

            {{-- Fornecedor --}}
            <div class="tab-panel d-none" data-tab-panel="fornecedor">
                <x-data-table
                    :columns="[
                        ['label' => 'ID', 'width' => '10%'],
                        ['label' => __('Name'), 'width' => '35%'],
                        ['label' => __('Provider Type'), 'width' => '35%'],
                        ['label' => __('Actions'), 'width' => '20%'],
                    ]"
                    :items="$fornecedores"
                    empty-message="Nenhum fornecedor cadastrado">
                    @foreach ($fornecedores as $pessoa)
                        <tr class="border-top border-light align-middle" data-status="{{ $pessoa->ativo ? 'ativo' : 'inativo' }}">
                            <td class="text-dark small py-2 ps-4" style="height: 72px;">
                                #{{ $pessoa->id }}
                            </td>
                            <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                {{ $pessoa->nome_social ?? $pessoa->nome_registro }}
                            </td>
                            <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                {{ ucfirst(str_replace('_', ' ', $pessoa->fornecedor->tipo ?? '-')) }}
                            </td>
                            <td class="text-dark small py-2 ps-4">
                                <x-table-actions
                                    :id="$pessoa->fornecedor->id"
                                    prefix="fornecedor-{{ $pessoa->fornecedor->id }}"
                                    :view-details="[
                                        __('Name') => ['label' => __('Name'), 'value' => $pessoa->nome_social ?? $pessoa->nome_registro],
                                        __('SSN/EIN') => ['label' => __('SSN/EIN'), 'value' => $pessoa->cpf_cnpj],
                                        __('Provider Type') => ['label' => __('Provider Type'), 'value' => __(ucfirst(str_replace('_', ' ', $pessoa->fornecedor->tipo ?? '-')))],
                                        __('Active') => ['label' => __('Active'), 'value' => $pessoa->fornecedor->ativo],
                                    ]"
                                    :view-title="__('Provider Details') . ' #' . $pessoa->fornecedor->id"
                                    :edit-route="route('pessoa.editar', $pessoa->id)"
                                    :toggle-route="route('fornecedor.toggle-status', $pessoa->fornecedor->id)"
                                    :is-active="$pessoa->fornecedor->ativo"
                                    :delete-route="route('fornecedor.excluir', $pessoa->fornecedor->id)"
                                    delete-message="Tem certeza que deseja remover o registro de fornecedor desta pessoa?" />
                            </td>
                        </tr>
                    @endforeach
                </x-data-table>
            </div>

            {{-- Funcionario --}}
            <div class="tab-panel d-none" data-tab-panel="funcionario">
                <x-data-table
                    :columns="[
                        ['label' => 'ID', 'width' => '10%'],
                        ['label' => __('Name'), 'width' => '35%'],
                        ['label' => __('Position'), 'width' => '35%'],
                        ['label' => __('Actions'), 'width' => '20%'],
                    ]"
                    :items="$funcionarios"
                    empty-message="Nenhum funcionário cadastrado">
                    @foreach ($funcionarios as $pessoa)
                        <tr class="border-top border-light align-middle" data-status="{{ $pessoa->ativo ? 'ativo' : 'inativo' }}">
                            <td class="text-dark small py-2 ps-4" style="height: 72px;">
                                #{{ $pessoa->id }}
                            </td>
                            <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                {{ $pessoa->nome_social ?? $pessoa->nome_registro }}
                            </td>
                            <td class="text-secondary small py-2 ps-4" style="height: 72px;">
                                {{ $pessoa->funcionario->cargo ?? '-' }}
                            </td>
                            <td class="text-dark small py-2 ps-4">
                                <x-table-actions
                                    :id="$pessoa->funcionario->id"
                                    prefix="funcionario-{{ $pessoa->funcionario->id }}"
                                    :view-details="[
                                        __('Name') => ['label' => __('Name'), 'value' => $pessoa->nome_social ?? $pessoa->nome_registro],
                                        __('SSN/EIN') => ['label' => __('SSN/EIN'), 'value' => $pessoa->cpf_cnpj],
                                        __('Position') => ['label' => __('Position'), 'value' => $pessoa->funcionario->cargo ?? '-'],
                                        __('Salary') => ['label' => __('Salary'), 'value' => $pessoa->funcionario->salario ? 'R$ ' . number_format($pessoa->funcionario->salario, 2, ',', '.') : '-'],
                                        __('Active') => ['label' => __('Active'), 'value' => $pessoa->funcionario->ativo],
                                    ]"
                                    :view-title="__('Employee Details') . ' #' . $pessoa->funcionario->id"
                                    :edit-route="route('pessoa.editar', $pessoa->id)"
                                    :toggle-route="route('funcionario.toggle-status', $pessoa->funcionario->id)"
                                    :is-active="$pessoa->funcionario->ativo"
                                    :delete-route="route('funcionario.excluir', $pessoa->funcionario->id)"
                                    delete-message="Tem certeza que deseja remover o registro de funcionário desta pessoa?" />
                            </td>
                        </tr>
                    @endforeach
                </x-data-table>
            </div>
        </div>
    </div>

    <x-confirm-modal />
    <x-view-modal />
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabPanels = document.querySelectorAll('.tab-panel');

        tabLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                var targetTab = this.getAttribute('data-tab');

                tabLinks.forEach(function (l) {
                    l.classList.remove('border-primary');
                    l.style.borderBottomColor = 'transparent';
                    l.style.color = '#49739c';
                    var icon = l.querySelector('i');
                    if (icon) icon.classList.remove('text-dark');
                    var span = l.querySelector('span');
                    if (span) span.classList.remove('text-dark');
                });

                this.classList.add('border-primary');
                this.style.borderBottomColor = '';
                this.style.color = '';
                var activeIcon = this.querySelector('i');
                if (activeIcon) activeIcon.classList.add('text-dark');
                var activeSpan = this.querySelector('span');
                if (activeSpan) activeSpan.classList.add('text-dark');

                tabPanels.forEach(function (panel) {
                    if (panel.getAttribute('data-tab-panel') === targetTab) {
                        panel.classList.remove('d-none');
                    } else {
                        panel.classList.add('d-none');
                    }
                });
            });
        });
    });
</script>
@endpush
