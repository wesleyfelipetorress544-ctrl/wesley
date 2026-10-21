@extends('layouts.library')

@section('title', ($record ? 'Editar ' : 'Adicionar ') . $definition['title'] . ' | Biblioteca Sesi-Tatuí')

@section('content')
    <h1 class="h3 fw-bold mb-4">{{ $record ? 'Editar' : 'Adicionar' }}: {{ $definition['title'] }}</h1>

    <form method="POST" action="{{ $record ? route('admin.crud.update', [$resource, $record->getKey()]) : route('admin.crud.store', $resource) }}" class="card border-0 rounded-4 shadow-sm">
        @csrf
        @if ($record)
            @method('PUT')
        @endif

        <div class="card-body p-4">
            @foreach ($definition['fields'] as $field)
                @php
                    $name = $field['name'];
                    $type = $field['type'];
                    $value = $type === 'password' ? '' : old($name, data_get($record, $name));
                    if ($value instanceof \DateTimeInterface) {
                        $value = $value->format('Y-m-d\TH:i');
                    }
                @endphp

                @if ($type === 'checkbox')
                    <input type="hidden" name="{{ $name }}" value="0">
                    <div class="form-check mb-3">
                        <input id="{{ $name }}" name="{{ $name }}" value="1" type="checkbox" class="form-check-input" @checked($value)>
                        <label for="{{ $name }}" class="form-check-label">{{ $field['label'] }}</label>
                    </div>
                @else
                    <div class="mb-3">
                        <label for="{{ $name }}" class="form-label fw-semibold">{{ $field['label'] }}</label>
                        @if ($type === 'textarea')
                            <textarea id="{{ $name }}" name="{{ $name }}" rows="4" class="form-control @error($name) is-invalid @enderror" @required($field['required'] ?? false)>{{ $value }}</textarea>
                        @elseif (in_array($type, ['book', 'user'], true))
                            @php($optionName = $type === 'book' ? 'book_id' : 'user_id')
                            <select id="{{ $name }}" name="{{ $name }}" class="form-select @error($name) is-invalid @enderror" @required($field['required'] ?? false)>
                                <option value="">Selecione</option>
                                @foreach ($options[$optionName] ?? [] as $optionId => $optionLabel)
                                    <option value="{{ $optionId }}" @selected((string) $value === (string) $optionId)>{{ $optionLabel }}</option>
                                @endforeach
                            </select>
                        @else
                            <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $value }}" class="form-control @error($name) is-invalid @enderror" @required($field['required'] ?? false) @if ($type === 'number') min="1" max="5" @endif>
                        @endif
                        @error($name) <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                @endif
            @endforeach

            @if ($resource === 'usuarios')
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label fw-semibold">Confirme a senha</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control">
                </div>
            @endif
        </div>
        <div class="card-footer bg-white border-0 px-4 pb-4 d-flex gap-2">
            <button type="submit" class="btn btn-danger">Salvar</button>
            <a href="{{ route('admin.crud.index', $resource) }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection
