@extends('layouts.library')

@section('title', 'Livros | Biblioteca Sesi-Tatuí')

@section('content')
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3">
        <div>
            <h1 class="h3 fw-bold mb-1">Livros Disponíveis</h1>
            <p class="small text-secondary mb-0"><span class="fw-bold text-dark">{{ $books->total() }}</span> {{ $books->total() === 1 ? 'livro encontrado' : 'livros encontrados' }}</p>
        </div>
        <form class="w-100 w-lg-25" role="search" method="GET" action="{{ route('books.index') }}">
            <label for="book-search" class="visually-hidden">Buscar livros</label>
            <input id="book-search" name="q" type="search" class="form-control form-control-sm" placeholder="Buscar por título, autor ou ISBN..." value="{{ $search }}">
        </form>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-3" aria-label="Filtrar livros">
        <a href="{{ route('books.index', ['q' => $search]) }}" class="btn btn-sm rounded-pill px-3 {{ $status === 'all' ? 'btn-danger' : 'btn-light border' }}" @if ($status === 'all') style="--bs-btn-bg: #ed1c24; --bs-btn-border-color: #ed1c24;" @endif>Todos</a>
        <a href="{{ route('books.index', ['q' => $search, 'status' => 'available']) }}" class="btn btn-sm rounded-pill px-3 {{ $status === 'available' ? 'btn-danger' : 'btn-light border' }}" @if ($status === 'available') style="--bs-btn-bg: #ed1c24; --bs-btn-border-color: #ed1c24;" @endif>Disponíveis</a>
        <a href="{{ route('books.index', ['q' => $search, 'status' => 'borrowed']) }}" class="btn btn-sm rounded-pill px-3 {{ $status === 'borrowed' ? 'btn-danger' : 'btn-light border' }}" @if ($status === 'borrowed') style="--bs-btn-bg: #ed1c24; --bs-btn-border-color: #ed1c24;" @endif>Emprestados</a>
    </div>

    @if ($books->isEmpty())
        <div class="alert alert-light border">Nenhum livro encontrado para essa busca.</div>
    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3">
            @foreach ($books as $book)
                <div class="col">
                    <article class="card h-100 border-0 rounded-4 shadow-sm">
                        <div class="card-body p-3 p-lg-4">
                            <a href="{{ route('books.show', $book) }}" class="text-decoration-none text-dark">
                                <div class="ratio rounded-3 overflow-hidden mb-3" style="--bs-aspect-ratio: 133.25%;">
                                    @if ($book->cover_url)
                                        <img src="{{ $book->cover_url }}" alt="Capa de {{ $book->title }}" class="w-100 h-100 object-fit-contain bg-body-secondary" loading="lazy">
                                    @else
                                        <div class="bg-body-secondary d-flex align-items-center justify-content-center text-secondary">
                                            <i class="bi bi-book-half display-4" aria-hidden="true"></i>
                                        </div>
                                    @endif
                                </div>
                                <h2 class="fs-5 fw-bold mb-1">{{ $book->title }}</h2>
                            </a>
                            <p class="text-secondary fw-semibold mb-2">{{ $book->author }}</p>
                            <p class="fs-6 text-secondary mb-3">{{ $book->description ?: 'Descrição indisponível.' }}</p>
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <span class="badge rounded-pill {{ $book->active_loans_count ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success-emphasis' }}">{{ $book->active_loans_count ? 'Emprestado' : 'Disponível' }}</span>
                                <a href="{{ route('books.show', $book) }}" class="small link-danger text-decoration-none fw-semibold">Ver detalhes</a>
                            </div>
                            <div class="small text-secondary mt-2">
                                @if ($book->reviews_count)
                                    {{ number_format((float) $book->reviews_avg_rating, 1, ',', '.') }} <i class="bi bi-star-fill text-warning" aria-hidden="true"></i> · {{ $book->reviews_count }} {{ $book->reviews_count === 1 ? 'avaliação' : 'avaliações' }}
                                @else
                                    Ainda sem avaliações
                                @endif
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
        <div class="mt-3">{{ $books->links() }}</div>
    @endif
@endsection