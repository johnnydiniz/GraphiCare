<div class="row g-4 px-4 py-2">
    @foreach ($fields as $field)
        <div class="col-12 col-md-{{ $field['proportion'] ?? 6 }}">
            @if (is_array($route) && $field['type'] == 'password')
                @continue
            @endif
            @if ($field['type'] != 'checkbox' && $field['type'] != 'radio')
                <label class="form-label text-dark mb-2 fw-medium" for="{{ $field['name'] }}"> {{ $field['label'] }}
                    @if (!empty($field['required']))
                        <span class="text-danger">*</span>
                    @endif
                </label>
                @if (!empty($field['action']))
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                        data-bs-target="#{{ $field['action'] }}">
                        <span style="font-size: 12px"><i class="fa-solid fa-plus"></i></span>
                    </button>
                    <!-- Modal -->
                    <div class="modal fade" id="{{ $field['action'] }}" tabindex="-1" role="dialog"
                        aria-labelledby="{{ $field['action'] }}Label" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="{{ $field['action'] }}Label">Cadastro de Tipo de
                                        matéria-prima</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Cancelar">
                                        <span aria-hidden="true"></span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <label class="form-label text-dark mb-2 fw-medium" for="descricao">Descrição<span
                                            class="text-danger">*</span></label>
                                    <div>
                                        <input type="text" id="descricao" name="descricao">
                                    </div>
                                    <hr>
                                    </hr>
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-primary btn-modal"
                                        data-routing="{{ $field['action'] }}"
                                        data-select="#{{ $field['name'] }}">Cadastrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
            @if ($field['type'] == 'text')
                <div>
                    <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                        class="form-control py-2" value="{{ session()->get($field['name']) ?? $field['value'] }}"
                        @if (!empty($field['required'])) required="true" @endif autocomplete="{{ $field['name'] }}"
                        autofocus>
                    @error($field['name'])
                        <span>{{ $message }}</span>
                    @enderror
                </div>
            @elseif($field['type'] == 'select')
                <div>
                    <select id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="form-select py-2"
                        @if (!empty($field['required'])) required="true" @endif>
                        @foreach ($field['options'] as $key => $value)
                            <option value="{{ $key }}"
                                {{ $key == session()->get($field['name']) || $field['selected'] == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    @error($field['name'])
                        <span>{{ $message }}</span>
                    @enderror
                </div>
            @elseif($field['type'] == 'password')
                <div>
                    <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                        class="form-control py-2" @if (!empty($field['required'])) required="true" @endif
                        autocomplete="{{ $field['name'] }}" autofocus>

                    @error($field['name'])
                        <span>{{ $message }}</span>
                    @enderror
                </div>
            @elseif($field['type'] == 'date')
                <div>
                    <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                        class="form-control py-2" value="{{ session()->get($field['name']) ?? $field['value'] }}"
                        @if (!empty($field['required'])) required="true" @endif autocomplete="{{ $field['name'] }}"
                        autofocus>

                    @error($field['name'])
                        <span>{{ $message }}</span>
                    @enderror
                </div>
            @elseif($field['type'] == 'textarea')
                <div>
                    <textarea id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="form-control py-2"
                        @if (!empty($field['required'])) required="true" @endif autofocus>{{ session()->get($field['name']) ?? $field['value'] }}</textarea>

                    @error($field['name'])
                        <span>{{ $message }}</span>
                    @enderror
                </div>
            @elseif($field['type'] == 'checkbox')
                <div {{ !empty($field['hidden']) ? $field['hidden'] : '' }}>
                    <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                        class="form-check-input"
                        {{ session()->get($field['name']) || $field['checked'] ? 'checked' : '' }}
                        @if (!empty($field['required'])) required="true" @endif autofocus>
                    <label class="form-label text-dark mb-2 fw-medium" for="{{ $field['name'] }}">
                        {{ $field['label'] }} </label>
                    @error($field['name'])
                        <span>{{ $message }}</span>
                    @enderror
                </div>
            @elseif($field['type'] == 'radio')
                <div>
                    @foreach ($field['options'] as $key => $value)
                        <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                            class="form-check-input" value="{{ $key }}"
                            {{ $key == session()->get($field['name']) || $field['selected'] == $key ? 'checked' : '' }}
                            @if (!empty($field['required'])) required="true" @endif autofocus>
                        <label class="form-label text-dark mb-2 fw-medium"
                            for="{{ $field['name'] }}">{{ $value }}</label>
                    @endforeach

                    @error($field['name'])
                        <span>{{ $message }}</span>
                    @enderror
                </div>
            @elseif($field['type'] == 'number')
                <div>
                    <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                        class="form-control py-2" value="{{ session()->get($field['name']) ?? $field['value'] }}"
                        @if (!empty($field['required'])) required="true" @endif
                        @if (!empty($field['min'])) min="{{ $field['min'] }}" @endif
                        @if (!empty($field['max'])) max="{{ $field['max'] }}" @endif
                        @if (!empty($field['step'])) step="{{ $field['step'] }}" @endif autofocus>

                    @error($field['name'])
                        <span>{{ $message }}</span>
                    @enderror
                </div>
            @elseif($field['type'] == 'file')
                <div>
                    <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                        class="form-control py-2" @if (!empty($field['required'])) required="true" @endif autofocus>

                    @error($field['name'])
                        <span>{{ $message }}</span>
                    @enderror
                </div>
            @elseif($field['type'] == 'time')
                <div>
                    <input id="{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                        class="form-control py-2" value="{{ session()->get($field['name']) ?? $field['value'] }}"
                        @if (!empty($field['required'])) required="true" @endif autofocus>

                    @error($field['name'])
                        <span>{{ $message }}</span>
                    @enderror
                </div>
            @endif
        </div>
    @endforeach
</div>
