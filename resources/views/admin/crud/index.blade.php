@extends('layouts.library')

@section('title', 'Gerenciar ' . $definition['title'] . ' | Biblioteca Sesi-Tatuí')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Gerenciar {{ strtolower($definition['title']) }}</h1>
            <p class="small text-secondary mb-0">{{ $records->total() }} registros</p>
        </div>
        <a href="{{ route('admin.crud.create', $resource) }}" class="btn btn-danger">Adicionar</a>
    </div>

    <div class="table-responsive bg-white rounded-4 shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    @foreach ($definition['columns'] as $column)
                        <th>{{ $column['label'] }}</th>
                    @endforeach
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr>
                        @foreach ($definition['columns'] as $column)
                            @php($value = data_get($record, $column['field']))
                            <td>
                                @if (is_bool($value))
                                    {{ $value ? 'Sim' : 'Não' }}
                                @elseif ($value instanceof \DateTimeInterface)
                                    {{ $value->format('d/m/Y H:i') }}
                                @else
                                    {{ \Illuminate\Support\Str::limit((string) $value, 100) ?: '—' }}
                                @endif
                            </td>
                        @endforeach
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.crud.edit', [$resource, $record->getKey()]) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                            <form method="POST" action="{{ route('admin.crud.destroy', [$resource, $record->getKey()]) }}" class="d-inline" onsubmit="return confirm('Excluir este registro?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" @disabled($resource === 'usuarios' && auth()->user()->is($record))>Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($definition['columns']) + 1 }}" class="text-center text-secondary py-4">Nenhum registro encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $records->links() }}</div>
@endsection
