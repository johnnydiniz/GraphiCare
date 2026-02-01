@props([
    'columns' => [],
    'items' => null,
    'emptyMessage' => 'Nenhum registro cadastrado',
])

<table class="table table-borderless mb-0">
    <thead class="bg-light">
        <tr>
            @foreach($columns as $column)
                <th class="text-dark small fw-medium py-3 ps-4"
                    @if(isset($column['width'])) style="width: {{ $column['width'] }};" @endif>
                    {{ $column['label'] }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @if($items !== null && count($items) === 0)
            <tr>
                <td colspan="{{ count($columns) }}" class="border-top border-light align-middle text-center py-4">
                    {{ $emptyMessage }}
                </td>
            </tr>
        @else
            {{ $slot }}
        @endif
    </tbody>
</table>
