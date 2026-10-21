<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status', 'all');

        $books = Book::query()
            ->withCount([
                'loans as active_loans_count' => fn ($query) => $query->whereNull('returned_at'),
                'reviews',
            ])
            ->withAvg('reviews', 'rating')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%'.$search.'%')
                        ->orWhere('author', 'like', '%'.$search.'%')
                        ->orWhere('isbn', 'like', '%'.$search.'%');
                });
            })
            ->when($status === 'available', fn ($query) => $query->whereDoesntHave('loans', fn ($loan) => $loan->whereNull('returned_at')))
            ->when($status === 'borrowed', fn ($query) => $query->whereHas('loans', fn ($loan) => $loan->whereNull('returned_at')))
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        return view('biblioteca', compact('books', 'search', 'status'));
    }

    public function show(Book $book): View
    {
        $book->loadCount('reviews')->loadAvg('reviews', 'rating');
        $reviews = $book->reviews()->with('user')->latest()->paginate(8);
        $isBorrowed = $book->loans()->whereNull('returned_at')->exists();

        return view('books.show', compact('book', 'reviews', 'isBorrowed'));
    }

    public function storeReview(Request $request, Book $book): RedirectResponse
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'review' => ['required', 'string', 'min:3', 'max:1500'],
        ]);

        BookReview::updateOrCreate(
            ['book_id' => $book->id, 'user_id' => $request->user()->id],
            $data,
        );

        return redirect()->to(route('books.show', $book).'#avaliacoes')
            ->with('status', 'Sua avaliação foi salva.');
    }

    public function storeLoan(Request $request, Book $book): RedirectResponse
    {
        $loan = DB::transaction(function () use ($request, $book) {
            $lockedBook = Book::query()->whereKey($book->id)->lockForUpdate()->firstOrFail();

            if ($lockedBook->loans()->whereNull('returned_at')->exists()) {
                return null;
            }

            return $request->user()->loans()->create([
                'book_id' => $lockedBook->id,
                'borrowed_at' => now(),
                'due_at' => now()->addDays(14),
            ]);
        });

        if ($loan === null) {
            return back()->withErrors(['book' => 'Este livro já está emprestado.']);
        }

        return redirect()->route('loans.index')->with(
            'status',
            'Empréstimo registrado. Devolução prevista para '.$loan->due_at->format('d/m/Y').'.',
        );
    }
}