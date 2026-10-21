@extends('layouts.library')

@section('title', 'Consultar tabela | Biblioteca Sesi-Tatuí')

@section('content')
    <h1 class="h3 fw-bold mb-2">Tabela: <span class="font-monospace">{{ $table }}</span></h1>
    <p class="text-secondary mb-4">Consulta somente leitura. Campos confidenciais não são exibidos.</p>

    <div class="table-responsive bg-white rounded-4 shadow-sm">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>@foreach ($columns as $column)<th class="text-nowrap">{{ $column }}</th>@endforeach</tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr>
                        @foreach ($columns as $column)
                            <td class="text-break" style="max-width: 24rem">{{ \Illuminate\Support\Str::limit((string) ($record->{$column} ?? ''), 200) }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ max(count($columns), 1) }}" class="text-center text-secondary py-4">Nenhum registro nesta tabela.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $records->links() }}</div>
    <a href="{{ route('admin.tables.index') }}" class="btn btn-link link-secondary px-0">Voltar às tabelas</a>
@endsection
