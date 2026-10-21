<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookReview;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LibraryController extends Controller
{
    public function books(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status', 'all');

        $books = Book::query()
            ->withCount([
                'loans as active_loans_count' => fn ($query) => $query->whereNull('returned_at'),
                'reviews',
            ])
            ->withAvg('reviews', 'rating')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('author', 'like', '%'.$search.'%')
                    ->orWhere('isbn', 'like', '%'.$search.'%');
            }))
            ->when($status === 'available', fn ($query) => $query->whereDoesntHave('loans', fn ($loan) => $loan->whereNull('returned_at')))
            ->when($status === 'borrowed', fn ($query) => $query->whereHas('loans', fn ($loan) => $loan->whereNull('returned_at')))
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        $books->getCollection()->each(fn (Book $book) => $book->append('cover_url'));

        return response()->json($books);
    }

    public function showBook(Book $book): JsonResponse
    {
        $book->loadCount('reviews')->loadAvg('reviews', 'rating')->append('cover_url');
        $reviews = $book->reviews()->with('user:id,name')->latest()->paginate(8);

        return response()->json([
            'book' => $book,
            'reviews' => $reviews,
            'is_borrowed' => $book->loans()->whereNull('returned_at')->exists(),
        ]);
    }

    public function storeReview(Request $request, Book $book): JsonResponse
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'review' => ['required', 'string', 'min:3', 'max:1500'],
        ]);

        $review = BookReview::updateOrCreate(
            ['book_id' => $book->id, 'user_id' => $request->user()->id],
            $data,
        );

        return response()->json(['message' => 'Avaliação salva.', 'review' => $review], 201);
    }

    public function storeLoan(Request $request, Book $book): JsonResponse
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
            return response()->json(['message' => 'Este livro já está emprestado.'], 409);
        }

        return response()->json(['message' => 'Empréstimo registrado.', 'loan' => $loan->load('book')], 201);
    }

    public function loans(Request $request): JsonResponse
    {
        $status = $request->query('status', 'all');

        $loans = Loan::query()
            ->with(['book', 'user:id,name'])
            ->when(! $request->user()->is_admin, fn ($query) => $query->where('user_id', $request->user()->id))
            ->when($status === 'active', fn ($query) => $query->whereNull('returned_at'))
            ->when($status === 'returned', fn ($query) => $query->whereNotNull('returned_at'))
            ->latest('borrowed_at')
            ->paginate(12)
            ->withQueryString();

        return response()->json($loans);
    }

    public function returnLoan(Request $request, Loan $loan): JsonResponse
    {
        abort_unless($request->user()->is_admin || $loan->user_id === $request->user()->id, 403);

        if ($loan->returned_at === null) {
            $loan->update(['returned_at' => now()]);
        }

        return response()->json(['message' => 'Devolução registrada.', 'loan' => $loan->fresh()->load('book')]);
    }

    public function students(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('q', ''));
        $students = User::query()
            ->withCount('loans')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return response()->json($students);
    }

    public function report(): JsonResponse
    {
        return response()->json([
            'stats' => [
                'books' => Book::count(),
                'available' => Book::whereDoesntHave('loans', fn ($query) => $query->whereNull('returned_at'))->count(),
                'active_loans' => Loan::whereNull('returned_at')->count(),
                'overdue' => Loan::whereNull('returned_at')->where('due_at', '<', now())->count(),
                'returned' => Loan::whereNotNull('returned_at')->count(),
                'reviews' => BookReview::count(),
            ],
            'most_borrowed' => Book::withCount('loans')->orderByDesc('loans_count')->orderBy('title')->take(5)->get(),
            'recent_loans' => Loan::with(['user:id,name', 'book'])->latest('borrowed_at')->take(10)->get(),
        ]);
    }

    public function adminRecords(string $resource): JsonResponse
    {
        $model = $this->resourceModel($resource);
        $records = match ($resource) {
            'emprestimos' => $model::query()->with(['book', 'user'])->latest()->paginate(15),
            'avaliacoes' => $model::query()->with(['book', 'user'])->latest()->paginate(15),
            default => $model::query()->latest()->paginate(15),
        };

        return response()->json($records);
    }

    public function showAdminRecord(string $resource, int $record): JsonResponse
    {
        $model = $this->resourceModel($resource);
        $query = $model::query();
        if (in_array($resource, ['emprestimos', 'avaliacoes'], true)) {
            $query->with(['book', 'user']);
        }

        return response()->json(['record' => $query->findOrFail($record)]);
    }

    public function storeAdminRecord(Request $request, string $resource): JsonResponse
    {
        $data = $request->validate($this->resourceRules($resource, $request));
        $model = $this->resourceModel($resource);
        $record = new $model;
        $this->saveAdminRecord($record, $resource, $data);

        return response()->json(['message' => 'Registro criado.', 'record' => $record->fresh()], 201);
    }

    public function updateAdminRecord(Request $request, string $resource, int $record): JsonResponse
    {
        $model = $this->resourceModel($resource);
        $record = $model::query()->findOrFail($record);
        $data = $request->validate($this->resourceRules($resource, $request, $record->getKey()));
        $this->saveAdminRecord($record, $resource, $data);

        return response()->json(['message' => 'Registro atualizado.', 'record' => $record->fresh()], 200);
    }

    public function destroyAdminRecord(Request $request, string $resource, int $record): JsonResponse
    {
        $model = $this->resourceModel($resource);
        $record = $model::query()->findOrFail($record);

        if ($resource === 'usuarios' && $request->user()->is($record)) {
            return response()->json(['message' => 'NÃ£o Ã© permitido excluir a prÃ³pria conta.'], 403);
        }

        if ($resource === 'usuarios' && $record->is_admin && ! User::query()
            ->where('is_admin', true)->where('id', '!=', $record->id)->exists()) {
            return response()->json(['message' => 'O sistema precisa manter pelo menos um administrador.'], 422);
        }

        $record->delete();

        return response()->json(['message' => 'Registro excluÃ­do.']);
    }

    private function resourceModel(string $resource): string
    {
        return match ($resource) {
            'livros' => Book::class,
            'usuarios' => User::class,
            'emprestimos' => Loan::class,
            'avaliacoes' => BookReview::class,
            default => abort(404),
        };
    }

    private function resourceRules(string $resource, Request $request, ?int $recordId = null): array
    {
        return match ($resource) {
            'livros' => [
                'title' => ['required', 'string', 'max:255'],
                'author' => ['required', 'string', 'max:255'],
                'isbn' => ['nullable', 'string', 'max:255', Rule::unique('books', 'isbn')->ignore($recordId)],
                'description' => ['nullable', 'string', 'max:10000'],
            ],
            'usuarios' => [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($recordId)],
                'cpf' => ['required', 'digits:11', Rule::unique('users', 'cpf')->ignore($recordId)],
                'password' => [$recordId === null ? 'required' : 'nullable', 'string', 'min:8'],
                'is_admin' => ['sometimes', 'boolean'],
            ],
            'emprestimos' => [
                'book_id' => ['required', 'integer', 'exists:books,id'],
                'user_id' => ['required', 'integer', 'exists:users,id'],
                'borrowed_at' => ['required', 'date'],
                'due_at' => ['required', 'date', 'after_or_equal:borrowed_at'],
                'returned_at' => ['nullable', 'date', 'after_or_equal:borrowed_at'],
            ],
            'avaliacoes' => [
                'book_id' => ['required', 'integer', 'exists:books,id', Rule::unique('book_reviews', 'book_id')
                    ->where('user_id', $request->integer('user_id'))->ignore($recordId)],
                'user_id' => ['required', 'integer', 'exists:users,id'],
                'rating' => ['required', 'integer', 'between:1,5'],
                'review' => ['required', 'string', 'min:3', 'max:1500'],
            ],
            default => abort(404),
        };
    }

    private function saveAdminRecord(Book|User|Loan|BookReview $record, string $resource, array $data): void
    {
        if ($resource === 'usuarios') {
            if (array_key_exists('is_admin', $data) || ! $record->exists) {
                $record->is_admin = (bool) ($data['is_admin'] ?? false);
            }
            if (blank($data['password'] ?? null)) {
                unset($data['password']);
            }
            if ($record->exists && $record->is(auth()->user())) {
                $record->is_admin = true;
            }
        }

        if ($resource === 'emprestimos') {
            DB::transaction(function () use ($record, $data): void {
                Book::query()->whereKey($data['book_id'])->lockForUpdate()->firstOrFail();
                $active = Loan::query()->where('book_id', $data['book_id'])->whereNull('returned_at')
                    ->when($record->exists, fn ($query) => $query->where('id', '!=', $record->getKey()))
                    ->exists();
                if ($active && blank($data['returned_at'] ?? null)) {
                    throw ValidationException::withMessages(['book_id' => 'Este livro jÃ¡ possui um emprÃ©stimo ativo.']);
                }
                $record->fill($data);
                $record->user_id = $data['user_id'];
                $record->save();
            });

            return;
        }

        $record->fill($data);
        $record->save();
    }

    public function tables(): JsonResponse
    {
        return response()->json(['tables' => collect(Schema::getTableListing(schemaQualified: false))->sort()->values()]);
    }

    public function table(string $table): JsonResponse
    {
        abort_unless(in_array($table, Schema::getTableListing(schemaQualified: false), true), 404);

        $hiddenColumns = [
            'abilities', 'exception', 'failed_job_ids', 'ip_address', 'options', 'owner',
            'password', 'payload', 'remember_token', 'token', 'user_agent', 'value',
        ];
        $columns = array_values(array_diff(
            Schema::getColumnListing($table),
            $hiddenColumns,
            $table === 'sessions' ? ['id'] : [],
        ));

        return response()->json([
            'table' => $table,
            'columns' => $columns,
            'records' => DB::table($table)->select($columns)->simplePaginate(25),
        ]);
    }
}
