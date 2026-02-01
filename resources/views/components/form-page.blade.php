@props([
    'title' => '',
    'tabs' => null,
])

<div class="container-fluid py-5 px-4 px-md-5">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between p-4">
                <h1 class="text-dark m-0 fs-2 fw-bold" style="min-width: 288px; letter-spacing: -0.01em;">
                    {{ $title }}
                </h1>
            </div>

            @if($tabs)
                <div class="pb-3">
                    <div class="d-flex border-bottom border-light px-4 gap-4 gap-md-8">
                        @foreach($tabs as $index => $tab)
                            <a class="tab-link d-flex align-items-center gap-2 border-bottom border-3 pb-3 pt-4 text-decoration-none
                                {{ $index === 0 ? 'border-primary' : '' }}"
                                href="javascript:void(0)"
                                data-tab="{{ $tab['key'] }}"
                                style="{{ $index !== 0 ? 'border-bottom-color: transparent !important; color: #49739c;' : '' }}">
                                @if(isset($tab['icon']))
                                    <i class="{{ $tab['icon'] }} small {{ $index === 0 ? 'text-dark' : '' }}"></i>
                                @endif
                                <span class="small fw-bold {{ $index === 0 ? 'text-dark' : '' }}" style="letter-spacing: 0.015em;">
                                    {{ __($tab['label']) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>
</div>

@if($tabs)
    @pushOnce('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tabLinks = document.querySelectorAll('.tab-link');
            var tabPanels = document.querySelectorAll('.tab-panel');

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
    @endPushOnce
@endif
