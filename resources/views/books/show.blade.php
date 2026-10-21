@extends('layouts.library')

@section('title', $book->title.' | Biblioteca Sesi-Tatuí')

@section('content')
    <a href="{{ route('books.index') }}" class="link-danger text-decoration-none small">← Voltar aos livros</a>

    <div class="card border-0 shadow-sm rounded-4 mt-3">
        <div class="card-body p-4 p-lg-5">
            <div class="row g-4 align-items-start">
                <div class="col-12 col-sm-4 col-lg-3">
                    <div class="ratio rounded-4 overflow-hidden shadow-sm" style="--bs-aspect-ratio: 133.25%;">
                        @if ($book->cover_url)
                            <img src="{{ $book->cover_url }}" alt="Capa de {{ $book->title }}" class="w-100 h-100 object-fit-contain bg-body-secondary">
                        @else
                            <div class="bg-body-secondary d-flex align-items-center justify-content-center text-secondary">
                                <i class="bi bi-book-half display-3" aria-hidden="true"></i>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col">
                    <h1 class="h2 fw-bold mb-2">{{ $book->title }}</h1>
                    <p class="fs-5 text-secondary fw-semibold mb-3">{{ $book->author }}</p>
                    @if ($book->isbn)
                        <p class="small text-secondary mb-3"><strong>ISBN:</strong> {{ $book->isbn }}</p>
                    @endif
                    <p class="fs-5 lh-lg mb-4">{{ $book->description ?: 'Ainda não há uma descrição cadastrada para este livro.' }}</p>
                    <p class="text-secondary mb-0">
                        @if ($book->reviews_count)
                            Nota média: <strong>{{ number_format((float) $book->reviews_avg_rating, 1, ',', '.') }} de 5</strong> ({{ $book->reviews_count }} avaliações)
                        @else
                            Este livro ainda não recebeu avaliações.
                        @endif
                    </p>
                </div>
                <aside class="col-12 col-lg-auto">
                    @if ($isBorrowed)
                        <span class="badge bg-warning-subtle text-warning-emphasis p-2">Emprestado no momento</span>
                    @else
                        <form method="POST" action="{{ route('books.loans.store', $book) }}">
                            @csrf
                            <button class="btn btn-danger" type="submit" style="--bs-btn-bg: #ed1c24; --bs-btn-border-color: #ed1c24; --bs-btn-hover-bg: #ed1c24; --bs-btn-hover-border-color: #ed1c24;">Solicitar empréstimo</button>
                        </form>
                        <p class="form-text">Prazo padrão: 14 dias.</p>
                    @endif
                </aside>
            </div>
            @error('book') <div class="alert alert-warning mt-3 mb-0">{{ $message }}</div> @enderror
        </div>
    </div>

    <section id="avaliacoes" class="mt-4">
        <h2 class="h5 fw-bold">Avaliações</h2>
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-light border border-danger text-danger">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('books.reviews.store', $book) }}" class="card card-body border-0 rounded-4 shadow-sm p-4 mb-3">
            @csrf
            <label for="rating" class="form-label fw-semibold">Sua nota</label>
            <select class="form-select mb-3" id="rating" name="rating" required>
                <option value="">Escolha de 1 a 5 estrelas</option>
                @for ($rating = 5; $rating >= 1; $rating--)
                    <option value="{{ $rating }}" @selected((string) old('rating') === (string) $rating)>{{ $rating }} {{ $rating === 1 ? 'estrela' : 'estrelas' }}</option>
                @endfor
            </select>
            <label for="review" class="form-label fw-semibold">Comentário</label>
            <textarea class="form-control mb-3" id="review" name="review" rows="3" maxlength="1500" required>{{ old('review') }}</textarea>
            <button class="btn btn-outline-danger align-self-start" type="submit" style="--bs-btn-color: #ed1c24; --bs-btn-border-color: #ed1c24; --bs-btn-hover-color: #fff; --bs-btn-hover-bg: #ed1c24; --bs-btn-hover-border-color: #ed1c24;">Publicar avaliação</button>
        </form>

        @forelse ($reviews as $review)
            <article class="card card-body border-0 rounded-4 shadow-sm p-4 mb-3">
                <div class="d-flex justify-content-between gap-2">
                    <strong>{{ $review->user->name }}</strong>
                    <span class="text-warning" aria-label="{{ $review->rating }} de 5 estrelas">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                </div>
                <p class="mb-1 mt-2">{{ $review->review }}</p>
                <small class="text-secondary">{{ $review->created_at->format('d/m/Y') }}</small>
            </article>
        @empty
            <p class="text-secondary">Seja a primeira pessoa a avaliar este livro.</p>
        @endforelse
        {{ $reviews->links() }}
    </section>
@endsection