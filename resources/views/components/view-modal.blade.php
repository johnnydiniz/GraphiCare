@props([
    'id' => 'viewDetailsModal',
    'title' => __('Details'),
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="view-modal-content">
                    <div class="d-flex justify-content-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">{{ __('Loading...') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-3 fw-medium px-4" data-bs-dismiss="modal">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>
</div>

@pushOnce('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var viewModal = document.getElementById('{{ $id }}');
    if (!viewModal) return;

    viewModal.addEventListener('show.bs.modal', function (event) {
        var trigger = event.relatedTarget;
        if (!trigger) return;

        // Se tem handler customizado, não processa o conteúdo padrão
        var customView = trigger.getAttribute('data-custom-view');
        if (customView === 'true') return;

        var title = trigger.getAttribute('data-title');
        var details = trigger.getAttribute('data-details');

        var titleEl = viewModal.querySelector('.modal-title');
        if (titleEl && title) {
            titleEl.textContent = title;
        }

        var contentEl = viewModal.querySelector('.view-modal-content');
        if (contentEl && details) {
            try {
                var data = JSON.parse(details);
                var html = '<div class="row g-3">';

                for (var key in data) {
                    if (data.hasOwnProperty(key)) {
                        var value = data[key];
                        var label = key;
                        var displayValue = value;

                        if (typeof value === 'object' && value !== null) {
                            if (value.label) label = value.label;
                            if (value.value !== undefined) displayValue = value.value;
                        }

                        if (displayValue === null || displayValue === '') {
                            displayValue = '<span class="text-muted">-</span>';
                        } else if (typeof displayValue === 'boolean') {
                            displayValue = displayValue
                                ? '<span class="badge bg-success">{{ __("Yes") }}</span>'
                                : '<span class="badge bg-secondary">{{ __("No") }}</span>';
                        }

                        html += '<div class="col-md-6">';
                        html += '<div class="border rounded-3 p-3 h-100">';
                        html += '<small class="text-muted d-block mb-1">' + label + '</small>';
                        html += '<span class="fw-medium">' + displayValue + '</span>';
                        html += '</div>';
                        html += '</div>';
                    }
                }

                html += '</div>';
                contentEl.innerHTML = html;
            } catch (e) {
                contentEl.innerHTML = '<div class="alert alert-danger">{{ __("Error loading details.") }}</div>';
            }
        }
    });

    viewModal.addEventListener('hidden.bs.modal', function () {
        var contentEl = viewModal.querySelector('.view-modal-content');
        if (contentEl) {
            contentEl.innerHTML = '<div class="d-flex justify-content-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">{{ __("Loading...") }}</span></div></div>';
        }
    });
});
</script>
@endPushOnce
