@props([
    'editRoute' => null,
    'deleteRoute' => null,
    'toggleRoute' => null,
    'isActive' => true,
    'id',
    'prefix' => 'excluir',
    'deleteMessage' => null,
    'toggleMessage' => null,
    'modalId' => 'confirmDeleteModal',
    'viewModalId' => 'viewDetailsModal',
    'viewDetails' => null,
    'viewTitle' => null,
    'customView' => false,
])

<div class="d-flex gap-1">
    @if($viewDetails)
        <button type="button" class="btn btn-sm btn-info"
            data-bs-toggle="modal"
            data-bs-target="#{{ $viewModalId }}"
            data-form-id="{{ $prefix }}-{{ $id }}"
            data-title="{{ $viewTitle ?? __('Details') }}"
            data-details="{{ is_array($viewDetails) ? json_encode($viewDetails) : $viewDetails }}"
            @if($customView) data-custom-view="true" @endif
            title="{{ __('View') }}">
            <span style="font-size: 12px"><i class="fa-solid fa-eye"></i></span>
        </button>
    @endif
    @if($editRoute)
        <a class="btn btn-sm btn-warning" href="{{ $editRoute }}" title="{{ __('Edit') }}">
            <span style="font-size: 12px"><i class="fa-solid fa-user-pen"></i></span>
        </a>
    @endif
    @if($toggleRoute)
        <button type="button" class="btn btn-sm {{ $isActive ? 'btn-secondary' : 'btn-success' }}"
            data-bs-toggle="modal"
            data-bs-target="#{{ $modalId }}"
            data-form-id="{{ $prefix }}-toggle-form-{{ $id }}"
            data-message="{{ $toggleMessage ?? ($isActive ? __('Are you sure you want to deactivate this record?') : __('Are you sure you want to activate this record?')) }}"
            title="{{ $isActive ? __('Deactivate') : __('Activate') }}">
            <span style="font-size: 12px"><i class="fa-solid {{ $isActive ? 'fa-ban' : 'fa-check' }}"></i></span>
        </button>
        <form id="{{ $prefix }}-toggle-form-{{ $id }}"
            action="{{ $toggleRoute }}" method="POST"
            class="d-none">
            @csrf
            @method('PATCH')
        </form>
    @endif
    @if($deleteRoute)
        <button type="button" class="btn btn-sm btn-danger"
            data-bs-toggle="modal"
            data-bs-target="#{{ $modalId }}"
            data-form-id="{{ $prefix }}-form-{{ $id }}"
            @if($deleteMessage) data-message="{{ $deleteMessage }}" @endif
            title="{{ __('Delete') }}">
            <span style="font-size: 12px"><i class="fa-solid fa-trash"></i></span>
        </button>
        <form id="{{ $prefix }}-form-{{ $id }}"
            action="{{ $deleteRoute }}" method="POST"
            class="d-none">
            @csrf
            @method('DELETE')
        </form>
    @endif
</div>
