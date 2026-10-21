<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');

        $loans = Loan::query()
            ->with(['book', 'user'])
            ->when($status === 'active', fn ($query) => $query->whereNull('returned_at'))
            ->when($status === 'returned', fn ($query) => $query->whereNotNull('returned_at'))
            ->latest('borrowed_at')
            ->paginate(12)
            ->withQueryString();

        return view('loans.index', compact('loans', 'status'));
    }

    public function returnBook(Loan $loan): RedirectResponse
    {
        if ($loan->returned_at === null) {
            $loan->update(['returned_at' => now()]);
        }

        return back()->with('status', 'Devolucao registrada.');
    }
}