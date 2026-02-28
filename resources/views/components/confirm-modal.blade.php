@props([
    'id' => 'confirmDeleteModal',
    'title' => __('Confirm Action'),
    'message' => __('Are you sure you want to delete this record? This action cannot be undone.'),
    'confirmLabel' => __('Delete'),
    'confirmClass' => 'btn-danger',
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-secondary">
                <p class="confirm-modal-message mb-0">{{ $message }}</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-3 fw-medium px-4" data-bs-dismiss="modal">
                    {{ __('Cancel') }}
                </button>
                <button type="button" class="btn {{ $confirmClass }} rounded-3 fw-medium px-4 confirm-delete-btn">
                    {{ $confirmLabel }}
                </button>
            </div>
        </div>
    </div>
</div>

@pushOnce('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.modal.fade').forEach(function (modal) {
        var confirmBtn = modal.querySelector('.confirm-delete-btn');
        if (!confirmBtn) return;

        var targetFormId = null;

        modal.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) return;

            targetFormId = trigger.getAttribute('data-form-id');

            var message = trigger.getAttribute('data-message');
            if (message) {
                var messageEl = modal.querySelector('.confirm-modal-message');
                if (messageEl) messageEl.textContent = message;
            }
        });

        confirmBtn.addEventListener('click', function () {
            if (targetFormId) {
                var form = document.getElementById(targetFormId);
                if (form) form.submit();
            }
        });
    });
});
</script>
@endPushOnce
