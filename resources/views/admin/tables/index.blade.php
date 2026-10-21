@extends('layouts.library')

@section('title', 'Tabelas do sistema | Biblioteca Sesi-Tatuí')

@section('content')
    <h1 class="h3 fw-bold mb-2">Tabelas do sistema</h1>
    <p class="text-secondary mb-4">Consulta somente leitura. Senhas, tokens e dados de sessão são ocultados.</p>

    <div class="list-group shadow-sm">
        @foreach ($tables as $table)
            <a href="{{ route('admin.tables.show', $table) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <span class="font-monospace">{{ $table }}</span>
                <span class="btn btn-sm btn-outline-secondary">Consultar</span>
            </a>
        @endforeach
    </div>
@endsection
