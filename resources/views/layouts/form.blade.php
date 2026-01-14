@if (is_array($route))
    <form method="POST" action="{{ route($route[0], $route[1]) }}">
        @method('PUT')
    @else
        <form method="POST" action="{{ route($route) }}">
@endif
@csrf
@error('db_error')
    <span>{{ $message }}</span>
@enderror

@include('layouts.fields', ['fields' => $fields])

@if (!empty($subfields))
    @include('layouts.fields', ['fields' => $subfields])
@endif

<!-- Botões -->
<div class="col-12">
    <div class="d-flex flex-wrap justify-content-end gap-3 px-4 py-3">
        <button class="btn btn-light px-4 py-2 rounded-3 fw-bold" style="min-width: 84px; height: 40px;">
            {{ __('Cancel') }}
        </button>
        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold text-white"
            style="min-width: 84px; height: 40px;">
            {{ __($btn_label) }}
        </button>
    </div>
</div>
</form>
