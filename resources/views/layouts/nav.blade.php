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
            <a class="text-dark text-decoration-none small fw-medium" href="{{ route('home') }}"><i
                    class="fa-solid fa-print"></i> {{ __('Orders') }}</a>
            <a class="text-dark text-decoration-none small fw-medium" href="{{ route('pessoa.index') }}"><i
                    class="fa-solid fa-people-group"></i> {{ __('People') }}</a>
            <a class="text-dark text-decoration-none small fw-medium" href="{{ route('servico.index') }}"><i
                    class="fa-solid fa-clipboard"></i> {{ __('Services') }}</a>
            <a class="text-dark text-decoration-none small fw-medium" href="{{ route('materia-prima.index') }}"><i
                    class="fa-solid fa-boxes-stacked"></i> {{ __('Inventory') }}</a>
            <a class="text-dark text-decoration-none small fw-medium" href="{{ route('equipamento-operacional.index') }}"><i
                    class="fa-solid fa-toolbox"></i> {{ __('Machinery') }}</a>
            <a class="text-dark text-decoration-none small fw-medium" href="{{ route('home') }}"><i
                    class="fa-solid fa-square-poll-horizontal"></i> {{ __('Reports') }}</a>
        </div>

        <!-- Configurações e Avatar -->
        <div class="d-flex align-items-center gap-4">
            <a class="text-dark text-decoration-none small fw-medium" href="{{ route('home') }}"><i
                    class="fa-solid fa-gears"></i> {{ __('Settings') }}</a>
            <!-- Avatar -->
            <a class="text-dark text-decoration-none small fw-medium" href="{{ route('home') }}">
                <div class="rounded-circle overflow-hidden"
                    style="width: 40px; height: 40px; background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCzkehLwdOwFIvR47ve53EgzDuFZmWUx1kINKGEfd7I9fJsundC2rIrAUCX01tDKfQCBxSvKEFA7wiW1pZ4tq_ciOPpxVko3NTK4NRC4wyvquY82ynCZAjFLtA5axPDIT2XggKPEkfe4i3KHJ7v7cCETqXJTTGS4tlJpIiz-DLDtynSKn8r7Ht8HeDst3r4r-Fa11RYVdJRDVoZhZVYdqdlOjBKHGdEc0jeeHZ13mnHP6VoDy2aQ1aYbYTdBUa8KMaNKOj6XuJMoKdG'); background-size: cover; background-position: center;">
                </div>
            </a>
        </div>
    </div>
</nav>
