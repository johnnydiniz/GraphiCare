<nav class="navbar navbar-expand-lg bg-white border-bottom border-light py-3 px-4">
    <div class="container-fluid">
        <!-- Logo e Nome da Empresa -->
        <div class="d-flex align-items-center me-5">
            <div class="d-flex align-items-center justify-content-center gap-2 text-dark">
                <div class="rounded-circle border d-flex align-items-center justify-content-center logo-icon">
                    <img src="{{ asset('imgs/logo.png') }}" alt="Printer">
                </div>
                <h2 class="m-0 fs-5 fw-bold" style="letter-spacing: -0.015em;">GraphiCare</h2>
            </div>
        </div>

        <!-- Itens de Navegação -->
        <div class="d-none d-lg-flex align-items-center gap-5 me-auto">
            <a class="text-dark text-decoration-none small fw-medium" href="{{ route('home') }}"><i
                    class="fa-solid fa-house"></i> {{ __('Home') }}</a>
            <a class="text-dark text-decoration-none small fw-medium" href="{{ route('orcamento.index') }}"><i
                    class="fa-solid fa-file-contract"></i> {{ __('Quotes') }}</a>
            <a class="text-dark text-decoration-none small fw-medium" href="{{ route('ordem-servico.index') }}"><i
                    class="fa-solid fa-print"></i> {{ __('Orders') }}</a>
            <div class="dropdown">
                <a class="text-dark text-decoration-none small fw-medium dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-warehouse"></i> Estoque
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item small" href="{{ route('ordem-compra.index') }}"><i class="fa-solid fa-cart-shopping me-2"></i>Compras</a></li>
                    <li><a class="dropdown-item small" href="{{ route('materia-prima.index') }}"><i class="fa-solid fa-boxes-stacked me-2"></i>{{ __('Inventory') }}</a></li>
                    <li><a class="dropdown-item small" href="{{ route('perda-quebra.index') }}"><i class="fa-solid fa-triangle-exclamation me-2"></i>Perdas</a></li>
                </ul>
            </div>
            <div class="dropdown">
                <a class="text-dark text-decoration-none small fw-medium dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-coins"></i> Financeiro
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item small" href="{{ route('fluxo-caixa.index') }}"><i class="fa-solid fa-chart-line me-2"></i>Fluxo de Caixa</a></li>
                    <li><a class="dropdown-item small" href="{{ route('fatura.index') }}"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Faturas</a></li>
                    <li><a class="dropdown-item small" href="{{ route('boleto.index') }}"><i class="fa-solid fa-barcode me-2"></i>Boletos</a></li>
                </ul>
            </div>
            <div class="dropdown">
                <a class="text-dark text-decoration-none small fw-medium dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-folder-open"></i> Cadastros
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item small" href="{{ route('pessoa.index') }}"><i class="fa-solid fa-people-group me-2"></i>{{ __('People') }}</a></li>
                    <li><a class="dropdown-item small" href="{{ route('servico.index') }}"><i class="fa-solid fa-clipboard me-2"></i>{{ __('Services') }}</a></li>
                    <li><a class="dropdown-item small" href="{{ route('equipamento-operacional.index') }}"><i class="fa-solid fa-toolbox me-2"></i>{{ __('Machinery') }}</a></li>
                </ul>
            </div>
            <div class="dropdown">
                <a class="text-dark text-decoration-none small fw-medium dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-square-poll-horizontal"></i> {{ __('Reports') }}
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item small" href="{{ route('relatorio.ordens') }}"><i class="fa-solid fa-print me-2"></i>Ordens de Serviço</a></li>
                    <li><a class="dropdown-item small" href="{{ route('relatorio.financeiro') }}"><i class="fa-solid fa-coins me-2"></i>Financeiro</a></li>
                    <li><a class="dropdown-item small" href="{{ route('relatorio.estoque') }}"><i class="fa-solid fa-warehouse me-2"></i>Estoque</a></li>
                </ul>
            </div>
        </div>

        <!-- Notificações e Avatar -->
        <div class="d-flex align-items-center gap-4">
            <!-- Sino de Notificações -->
            <div class="dropdown">
                <a class="text-dark text-decoration-none position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-bell fs-5"></i>
                    @if($notificacoesCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill {{ ($temVencido || $temAlerta) ? 'bg-danger' : 'bg-warning text-dark' }}" style="font-size: 0.65rem;">
                            {{ $notificacoesCount }}
                        </span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" style="width: 340px; max-height: 400px; overflow-y: auto;">
                    @if($notificacoes->count() > 0)
                        <li class="dropdown-header fw-bold text-dark">Vencimentos</li>
                        @foreach($notificacoes as $notif)
                            <li>
                                <a class="dropdown-item small py-2" href="{{ route($notif['rota']) }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid {{ $notif['icone'] }} {{ $notif['vencido'] ? 'text-danger' : 'text-warning' }}"></i>
                                        <div class="flex-grow-1">
                                            <div class="fw-medium">{{ $notif['referencia'] }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                R$ {{ number_format($notif['valor'], 2, ',', '.') }}
                                                — {{ $notif['data_vencimento']->format('d/m/Y') }}
                                            </div>
                                        </div>
                                        @if($notif['vencido'])
                                            <span class="badge bg-danger">Vencido</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ $notif['dias'] }}d</span>
                                        @endif
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    @endif
                    @if(count($alertasEstoque) > 0)
                        @if($notificacoes->count() > 0)
                            <li><hr class="dropdown-divider"></li>
                        @endif
                        <li class="dropdown-header fw-bold text-dark">Estoque Crítico</li>
                        @foreach($alertasEstoque as $alerta)
                            <li>
                                <a class="dropdown-item small py-2" href="{{ route('materia-prima.index') }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-boxes-stacked text-danger"></i>
                                        <div class="flex-grow-1">
                                            <div class="fw-medium">{{ $alerta['referencia'] }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                Atual: {{ $alerta['estoque_atual'] }} / Mín: {{ $alerta['estoque_minimo'] }}
                                            </div>
                                        </div>
                                        <span class="badge bg-danger">Baixo</span>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    @endif
                    @if($notificacoes->count() === 0 && count($alertasEstoque) === 0)
                        <li class="text-center text-muted small py-3">Nenhuma notificação</li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item small text-center fw-medium text-primary" href="{{ route('fluxo-caixa.index') }}">
                            <i class="fa-solid fa-chart-line me-1"></i> Ver Fluxo de Caixa
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Avatar com Iniciais -->
            <div class="dropdown">
                <a class="text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px; background-color: #4a5568; color: #fff; font-size: 0.85rem; font-weight: 600;">
                        @php
                            $words = explode(' ', Auth::user()->nome_registro);
                            $initials = strtoupper(substr($words[0], 0, 1));
                            if (count($words) > 1) {
                                $initials .= strtoupper(substr(end($words), 0, 1));
                            }
                        @endphp
                        {{ $initials }}
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li class="dropdown-header fw-bold text-dark">{{ Auth::user()->nome_registro }}</li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item small" href="{{ route('perfil.index') }}">
                            <i class="fa-solid fa-user me-2"></i>Perfil
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('sair') }}">
                            @csrf
                            <button type="submit" class="dropdown-item small">
                                <i class="fa-solid fa-right-from-bracket me-2"></i>Sair
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
