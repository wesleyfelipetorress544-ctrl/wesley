@extends('layouts.library')

@section('title', 'Relatórios | Biblioteca Sesi-Tatuí')

@section('content')
    <h1 class="h4 fw-bold mb-1">Relatórios</h1>
    <p class="small text-secondary mb-4">Resumo do acervo e das atividades da biblioteca.</p>

    <div class="row row-cols-2 row-cols-xl-3 g-3 mb-4">
        <div class="col"><div class="card h-100 border-0 rounded-4 shadow-sm"><div class="card-body"><div class="small text-secondary fw-semibold">Livros no acervo</div><div class="display-6 fw-bold">{{ $stats['books'] }}</div></div></div></div>
        <div class="col"><div class="card h-100 border-0 rounded-4 shadow-sm"><div class="card-body"><div class="small text-secondary fw-semibold">Disponíveis</div><div class="display-6 fw-bold text-success">{{ $stats['available'] }}</div></div></div></div>
        <div class="col"><div class="card h-100 border-0 rounded-4 shadow-sm"><div class="card-body"><div class="small text-secondary fw-semibold">Empréstimos ativos</div><div class="display-6 fw-bold text-danger">{{ $stats['active_loans'] }}</div></div></div></div>
        <div class="col"><div class="card h-100 border-0 rounded-4 shadow-sm"><div class="card-body"><div class="small text-secondary fw-semibold">Em atraso</div><div class="display-6 fw-bold text-warning">{{ $stats['overdue'] }}</div></div></div></div>
        <div class="col"><div class="card h-100 border-0 rounded-4 shadow-sm"><div class="card-body"><div class="small text-secondary fw-semibold">Devolvidos</div><div class="display-6 fw-bold">{{ $stats['returned'] }}</div></div></div></div>
        <div class="col"><div class="card h-100 border-0 rounded-4 shadow-sm"><div class="card-body"><div class="small text-secondary fw-semibold">Avaliações</div><div class="display-6 fw-bold">{{ $stats['reviews'] }}</div></div></div></div>
    </div>

    <div class="row g-3">
        <section class="col-lg-5">
            <div class="card h-100 border-0 rounded-4 shadow-sm">
                <div class="card-header bg-white fw-semibold">Livros mais emprestados</div>
                <ol class="list-group list-group-numbered list-group-flush">
                    @forelse ($mostBorrowed as $book)
                        <li class="list-group-item d-flex justify-content-between gap-2"><span>{{ $book->title }}</span><span class="badge text-bg-light">{{ $book->loans_count }}</span></li>
                    @empty
                        <li class="list-group-item text-secondary">Ainda não há empréstimos registrados.</li>
                    @endforelse
                </ol>
            </div>
        </section>
        <section class="col-lg-7">
            <div class="card h-100 border-0 rounded-4 shadow-sm">
                <div class="card-header bg-white fw-semibold">Empréstimos recentes</div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light"><tr><th>Livro</th><th>Aluno</th><th>Data</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse ($recentLoans as $loan)
                                <tr><td>{{ $loan->book->title }}</td><td>{{ $loan->user->name }}</td><td>{{ $loan->borrowed_at->format('d/m/Y') }}</td><td>{{ $loan->returned_at ? 'Devolvido' : 'Ativo' }}</td></tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-secondary py-3">Nenhum empréstimo registrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection