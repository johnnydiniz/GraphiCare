<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Laravel') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item dropdown">
                    <a id="cadastrosDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        Cadastros
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cadastrosDropdown">
                        <a class="dropdown-item nav-link" href="{{ route('pessoa.index') }}">Pessoas</a>
                        <a class="dropdown-item nav-link" href="{{ route('servico.index') }}">Serviços</a>
                        <a class="dropdown-item nav-link" href="{{ route('materia-prima.index') }}">Matéria-prima</a>
                        <a class="dropdown-item nav-link" href="{{ route('orcamento.index') }}">Orçamentos</a>
                        <a class="dropdown-item nav-link" href="{{ route('cliente.index') }}">Ordens de serviços</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item nav-link" href="{{ route('componente-servico.index') }}">Componentes de Serviços</a>
                        <a class="dropdown-item nav-link" href="{{ route('tipo-materia-prima.index') }}">Tipo de Matéria-prima</a>
                        <a class="dropdown-item nav-link" href="{{ route('tipo-contato.index') }}">Tipo de Contato</a>

                        
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a id="movimentacoesDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        Movimentações
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="movimentacoesDropdown">
                        <a class="dropdown-item nav-link" href="{{ route('cliente.index') }}">Entradas</a>
                        <a class="dropdown-item nav-link" href="{{ route('cliente.index') }}">Saídas</a>
                        <a class="dropdown-item nav-link" href="{{ route('cliente.index') }}">Perdas/Quebras</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a id="relatoriosDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        Relatórios
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="relatoriosDropdown">
                        <a class="dropdown-item nav-link" href="{{ route('cliente.index') }}">Estoque</a>
                        <a class="dropdown-item nav-link" href="{{ route('cliente.index') }}">Financeiro</a>
                    </div>
                </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <button class="btn btn-primary mx-1 my-sm-0" type="button"> + Orçamento</button>
                </li>
                <li class="nav-item">
                    <button class="btn btn-primary mx-1 my-sm-0" type="button"> + Ordem de serviço</button>
                </li>
                <!-- Authentication Links -->
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->nome_social }}
                    </a>

                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('sair') }}" onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('sair') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
