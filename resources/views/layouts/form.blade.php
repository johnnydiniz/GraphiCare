@if(is_array($route))
<form method="POST" action="{{ route($route[0], $route[1]) }}">
    @method('PUT')
    @else
    <form method="POST" action="{{ route($route) }}">
        @endif
        @csrf
        @error('db_error')
        <span>{{ $message }}</span>
        @enderror
        @foreach ($fields as $field)
        <div>
            @if(is_array($route) && $field['type'] == 'password')
            @continue
            @endif
            @if($field['type'] != 'checkbox' && $field['type'] != 'radio')
            <label for="{{ $field['name'] }}"> {{ $field['label'] }} @if(!empty($field['required']))<span class="text-danger">*</span>@endif</label>
            @endif
            @if($field['type'] == 'text')
            <div>
                <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}" value="{{ session()->get($field['name']) ?? $field['value'] }}" @if (!empty($field['required'])) required="true" @endif autocomplete="{{ $field['name'] }}" autofocus>
                @error($field["name"])
                <span>{{ $message }}</span>
                @enderror
            </div>
            @elseif($field['type'] == 'select')
            <div>
                <select id="{{ $field['name'] }}" name="{{ $field['name'] }}" @if (!empty($field['required'])) required="true" @endif>
                    @foreach ($field['options'] as $key => $value)
                    <option value="{{ $key }}" {{ ($key == session()->get($field['name']) || $field['selected'] == $key) ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                    @endforeach
                </select>
                @if(!empty($field['action']))
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#{{ $field['action'] }}">
                    <span style="font-size: 12px"><i class="fa-solid fa-plus"></i></span>
                </button>
                <!-- Modal -->
                <div class="modal fade" id="{{ $field['action'] }}" tabindex="-1" role="dialog" aria-labelledby="{{ $field['action'] }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="{{ $field['action'] }}Label">Cadastro de Tipo de matéria-prima</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancelar">
                                    <span aria-hidden="true"></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <label for="descricao">Descrição<span class="text-danger">*</span></label>
                                <div>
                                    <input type="text" id="descricao" name="descricao">
                                </div>
                                <hr></hr>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-primary btn-modal" data-routing="{{ $field['action'] }}" data-select="#{{ $field['name'] }}">Cadastrar</button>                                
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @error($field["name"])
                <span>{{ $message }}</span>
                @enderror
            </div>
            @elseif($field['type'] == 'password')
            <div>
                <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}" @if (!empty($field['required'])) required="true" @endif autocomplete="{{ $field['name'] }}" autofocus>

                @error($field["name"])
                <span>{{ $message }}</span>
                @enderror
            </div>
            @elseif($field['type'] == 'date')
            <div>
                <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}" value="{{ session()->get($field['name']) ?? $field['value'] }}" @if (!empty($field['required'])) required="true" @endif autocomplete="{{ $field['name'] }}" autofocus>

                @error($field["name"])
                <span>{{ $message }}</span>
                @enderror
            </div>
            @elseif($field['type'] == 'textarea')
            <div>
                <textarea id="{{ $field['name'] }}" name="{{ $field['name'] }}" @if (!empty($field['required'])) required="true" @endif autofocus>{{ session()->get($field['name']) ?? $field['value'] }}</textarea>

                @error($field["name"])
                <span>{{ $message }}</span>
                @enderror
            </div>
            @elseif($field['type'] == 'checkbox')
            <div {{  !empty($field['hidden']) ? $field['hidden'] : '' }}>
                <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}" {{ (session()->get($field['name'])  || $field['checked']) ? 'checked' : '' }} @if (!empty($field['required'])) required="true" @endif autofocus>
                <label for="{{ $field['name'] }}"> {{ $field['label'] }} </label>
                @error($field["name"])
                <span>{{ $message }}</span>
                @enderror
            </div>
            @elseif($field['type'] == 'radio')
            <div>
                @foreach ($field['options'] as $key => $value)
                <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}" value="{{ $key }}" {{ ($key == session()->get($field['name']) || $field['selected'] == $key) ? 'checked' : '' }} @if (!empty($field['required'])) required="true" @endif autofocus>
                <label for="{{ $field['name'] }}">{{ $value }}</label>
                @endforeach

                @error($field["name"])
                <span>{{ $message }}</span>
                @enderror
            </div>
            @elseif($field['type'] == 'number')
            <div>
                <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}" value="{{ session()->get($field['name']) ?? $field['value'] }}" @if (!empty($field['required'])) required="true" @endif autofocus>

                @error($field["name"])
                <span>{{ $message }}</span>
                @enderror
            </div>
            @elseif($field['type'] == 'file')
            <div>
                <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}" @if (!empty($field['required'])) required="true" @endif autofocus>

                @error($field["name"])
                <span>{{ $message }}</span>
                @enderror
            </div>
            @endif
        </div>
        @endforeach

        <div>
            <div>
                <button type="submit">
                    {{ $btn_label }}
                </button>
            </div>
        </div>
    </form>
