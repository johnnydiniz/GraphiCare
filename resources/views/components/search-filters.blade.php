@props([
    'title' => '',
    'statusOptions' => null,
])

@php
    $statusOptions = $statusOptions ?? [
        'ativo' => __('Active'),
        'inativo' => __('Inactive'),
    ];
@endphp

{{-- Search Bar --}}
<div class="p-4 py-3">
    <div class="input-group" style="width:100%">
        <span class="input-group-text bg-light border-0 rounded-start-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#49739c"
                viewBox="0 0 256 256">
                <path
                    d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z">
                </path>
            </svg>
        </span>
        <input type="text" id="searchInput"
            class="form-control form-control-custom border-0 rounded-end-3 flex-1"
            placeholder="{{ __('Search') }} {{ strtolower(__($title)) }}">
    </div>
</div>

{{-- Filters --}}
<div class="d-flex flex-wrap gap-3 p-3 pe-4 align-items-center">
    {{-- Status Dropdown --}}
    <div class="dropdown">
        <button class="btn btn-light d-flex align-items-center gap-2 py-1 px-3 rounded-3"
            style="height: 32px;" type="button" data-bs-toggle="dropdown" aria-expanded="false"
            id="statusFilterBtn">
            <span class="small fw-medium status-label">{{ __('Status') }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                viewBox="0 0 256 256">
                <path
                    d="M213.66,101.66l-80,80a8,8,0,0,1-11.32,0l-80-80A8,8,0,0,1,53.66,90.34L128,164.69l74.34-74.35a8,8,0,0,1,11.32,11.32Z">
                </path>
            </svg>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item status-option active" href="javascript:void(0)" data-status="">
                    {{ __('All') }}
                </a>
            </li>
            @foreach($statusOptions as $value => $label)
                <li>
                    <a class="dropdown-item status-option" href="javascript:void(0)" data-status="{{ $value }}">
                        {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Clear Filters --}}
    <button class="btn btn-outline-secondary align-items-center gap-2 py-1 px-3 rounded-3 small fw-medium"
        style="height: 32px; display: none;" id="clearFiltersBtn">
        <i class="fa-solid fa-xmark"></i>
        {{ __('Clear Filters') }}
    </button>
</div>

@pushOnce('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('searchInput');
    var statusFilterBtn = document.getElementById('statusFilterBtn');
    var clearFiltersBtn = document.getElementById('clearFiltersBtn');
    var statusOptions = document.querySelectorAll('.status-option');
    var selectedStatus = '';

    function applyFilters() {
        var searchText = searchInput.value.toLowerCase().trim();
        var hasFilters = searchText !== '' || selectedStatus !== '';

        clearFiltersBtn.style.display = hasFilters ? 'flex' : 'none';

        var tabPanels = document.querySelectorAll('.tab-panel');
        var containers = tabPanels.length > 0
            ? tabPanels
            : document.querySelectorAll('.table-responsive');

        containers.forEach(function (container) {
            if (!container) return;

            var tbody = container.querySelector('tbody');
            if (!tbody) return;

            var rows = tbody.querySelectorAll('tr:not(.no-results-row)');
            var visibleCount = 0;
            var totalFilterable = 0;

            rows.forEach(function (row) {
                if (row.querySelector('td[colspan]')) {
                    return;
                }

                totalFilterable++;
                var matchesSearch = true;
                var matchesStatus = true;

                if (searchText !== '') {
                    var cells = row.querySelectorAll('td');
                    var rowText = '';
                    cells.forEach(function (cell) {
                        rowText += ' ' + cell.textContent.toLowerCase();
                    });
                    matchesSearch = rowText.indexOf(searchText) !== -1;
                }

                if (selectedStatus !== '') {
                    var rowStatus = row.getAttribute('data-status');
                    matchesStatus = rowStatus === selectedStatus;
                }

                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            var noResultsRow = tbody.querySelector('.no-results-row');

            if (visibleCount === 0 && totalFilterable > 0) {
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.className = 'no-results-row';
                    var colCount = container.querySelectorAll('thead th').length;
                    noResultsRow.innerHTML = '<td colspan="' + colCount + '" class="border-top border-light align-middle text-center text-secondary py-4">{{ __("No results found") }}</td>';
                    tbody.appendChild(noResultsRow);
                }
                noResultsRow.style.display = '';
            } else if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', function () {
        applyFilters();
    });

    statusOptions.forEach(function (option) {
        option.addEventListener('click', function (e) {
            e.preventDefault();
            selectedStatus = this.getAttribute('data-status');

            statusOptions.forEach(function (o) { o.classList.remove('active'); });
            this.classList.add('active');

            var label = statusFilterBtn.querySelector('.status-label');
            if (selectedStatus === '') {
                label.textContent = '{{ __("Status") }}';
                statusFilterBtn.classList.remove('btn-primary');
                statusFilterBtn.classList.add('btn-light');
            } else {
                label.textContent = this.textContent.trim();
                statusFilterBtn.classList.remove('btn-light');
                statusFilterBtn.classList.add('btn-primary');
            }

            applyFilters();
        });
    });

    clearFiltersBtn.addEventListener('click', function () {
        searchInput.value = '';
        selectedStatus = '';

        statusOptions.forEach(function (o) { o.classList.remove('active'); });
        if (statusOptions.length > 0) {
            statusOptions[0].classList.add('active');
        }

        var label = statusFilterBtn.querySelector('.status-label');
        label.textContent = '{{ __("Status") }}';
        statusFilterBtn.classList.remove('btn-primary');
        statusFilterBtn.classList.add('btn-light');

        applyFilters();
    });
});
</script>
@endPushOnce
