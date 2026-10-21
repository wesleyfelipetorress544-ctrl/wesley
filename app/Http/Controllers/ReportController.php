<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $stats = [
            'books' => Book::count(),
            'available' => Book::whereDoesntHave('loans', fn ($query) => $query->whereNull('returned_at'))->count(),
            'active_loans' => Loan::whereNull('returned_at')->count(),
            'overdue' => Loan::whereNull('returned_at')->where('due_at', '<', now())->count(),
            'returned' => Loan::whereNotNull('returned_at')->count(),
            'reviews' => \App\Models\BookReview::count(),
        ];

        $mostBorrowed = Book::withCount('loans')->orderByDesc('loans_count')->orderBy('title')->take(5)->get();
        $recentLoans = Loan::with(['user', 'book'])->latest('borrowed_at')->take(10)->get();

        return view('reports.index', compact('stats', 'mostBorrowed', 'recentLoans'));
    }
}