@extends('layouts.library')

@section('title', 'Empr&eacute;stimos | Biblioteca Sesi-Tatu&iacute;')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="h4 fw-bold mb-1">Hist&oacute;rico de empr&eacute;stimos</h1>
            <p class="small text-secondary mb-0">Registro de empr&eacute;stimos e devolu&ccedil;&otilde;es da biblioteca.</p>
        </div>
        <div class="btn-group btn-group-sm" role="group" aria-label="Filtrar empr&eacute;stimos">
            <a href="{{ route('loans.index') }}" class="btn {{ $status === 'all' ? 'btn-danger' : 'btn-outline-secondary' }}" @if ($status === 'all') style="--bs-btn-bg: #ed1c24; --bs-btn-border-color: #ed1c24;" @endif>Todos</a>
            <a href="{{ route('loans.index', ['status' => 'active']) }}" class="btn {{ $status === 'active' ? 'btn-danger' : 'btn-outline-secondary' }}" @if ($status === 'active') style="--bs-btn-bg: #ed1c24; --bs-btn-border-color: #ed1c24;" @endif>Ativos</a>
            <a href="{{ route('loans.index', ['status' => 'returned']) }}" class="btn {{ $status === 'returned' ? 'btn-danger' : 'btn-outline-secondary' }}" @if ($status === 'returned') style="--bs-btn-bg: #ed1c24; --bs-btn-border-color: #ed1c24;" @endif>Devolvidos</a>
        </div>
    </div>

    @if ($loans->isEmpty())
        <div class="card border-0 shadow-sm"><div class="card-body text-secondary">Ainda n&atilde;o h&aacute; empr&eacute;stimos nesta categoria. Encontre um livro e registre um empr&eacute;stimo.</div></div>
    @else
        <div class="table-responsive bg-white rounded-4 border-0 shadow-sm">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Livro</th><th>Aluno</th><th>Retirada</th><th>Devolu&ccedil;&atilde;o prevista</th><th>Devolvido em</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @foreach ($loans as $loan)
                        <tr>
                            <td><a href="{{ route('books.show', $loan->book) }}" class="link-dark fw-semibold">{{ $loan->book->title }}</a><div class="small text-secondary fw-semibold">{{ $loan->book->author }}</div></td>
                            <td>{{ $loan->user->name }}</td>
                            <td>{{ $loan->borrowed_at->format('d/m/Y') }}</td>
                            <td>{{ $loan->due_at->format('d/m/Y') }}</td>
                            <td>@if ($loan->returned_at) {{ $loan->returned_at->format('d/m/Y') }} @else &mdash; @endif</td>
                            <td>
                                @if ($loan->returned_at)
                                    <span class="badge bg-success-subtle text-success-emphasis">Devolvido</span>
                                @elseif ($loan->due_at->isPast())
                                    <span class="badge bg-danger text-white">Em atraso</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis">Em andamento</span>
                                @endif
                            </td>
                            <td>
                                @if (!$loan->returned_at)
                                    <form method="POST" action="{{ route('loans.return', $loan) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit" style="--bs-btn-color: #ed1c24; --bs-btn-border-color: #ed1c24; --bs-btn-hover-color: #fff; --bs-btn-hover-bg: #ed1c24; --bs-btn-hover-border-color: #ed1c24;">Registrar devolu&ccedil;&atilde;o</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $loans->links() }}</div>
    @endif
@endsection