<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookReview;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LibraryCrudController extends Controller
{
    public function index(string $resource): View
    {
        $definition = $this->definition($resource);
        $model = $definition['model'];

        $records = match ($resource) {
            'emprestimos' => Loan::query()->with(['book', 'user'])->latest()->paginate(15),
            'avaliacoes' => BookReview::query()->with(['book', 'user'])->latest()->paginate(15),
            default => $model::query()->latest()->paginate(15),
        };

        return view('admin.crud.index', compact('resource', 'definition', 'records'));
    }

    public function create(string $resource): View
    {
        $definition = $this->definition($resource);

        return view('admin.crud.form', [
            'resource' => $resource,
            'definition' => $definition,
            'record' => null,
            'options' => $this->options($resource),
        ]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $definition = $this->definition($resource);
        $data = $request->validate($this->rules($resource, $request));
        $model = $definition['model'];
        $record = new $model;

        $this->save($record, $resource, $data);

        return redirect()->route('admin.crud.index', $resource)->with('status', $definition['saved']);
    }

    public function edit(string $resource, int $record): View
    {
        $definition = $this->definition($resource);
        $model = $definition['model'];
        $record = $model::query()->findOrFail($record);

        return view('admin.crud.form', [
            'resource' => $resource,
            'definition' => $definition,
            'record' => $record,
            'options' => $this->options($resource),
        ]);
    }

    public function update(Request $request, string $resource, int $record): RedirectResponse
    {
        $definition = $this->definition($resource);
        $model = $definition['model'];
        $record = $model::query()->findOrFail($record);
        $data = $request->validate($this->rules($resource, $request, $record->getKey()));

        $this->save($record, $resource, $data);

        return redirect()->route('admin.crud.index', $resource)->with('status', $definition['saved']);
    }

    public function destroy(Request $request, string $resource, int $record): RedirectResponse
    {
        $definition = $this->definition($resource);
        $model = $definition['model'];
        $record = $model::query()->findOrFail($record);

        if ($resource === 'usuarios' && $request->user()->is($record)) {
            return back()->with('error', 'Não é permitido excluir a própria conta.');
        }

        if ($resource === 'usuarios' && $record->is_admin && ! User::query()
            ->where('is_admin', true)
            ->where('id', '!=', $record->id)
            ->exists()) {
            return back()->with('error', 'O sistema precisa manter pelo menos um administrador.');
        }

        $record->delete();

        return redirect()->route('admin.crud.index', $resource)->with('status', 'Registro excluído.');
    }

    /**
     * @return array{
     *     title: string,
     *     saved: string,
     *     model: class-string<Model>,
     *     columns: list<array{field: string, label: string}>,
     *     fields: list<array{name: string, label: string, type: string, required?: bool}>
     * }
     */
    private function definition(string $resource): array
    {
        return match ($resource) {
            'livros' => [
                'title' => 'Livros',
                'saved' => 'Livro salvo.',
                'model' => Book::class,
                'columns' => [
                    ['field' => 'title', 'label' => 'Título'],
                    ['field' => 'author', 'label' => 'Autor'],
                    ['field' => 'isbn', 'label' => 'ISBN'],
                ],
                'fields' => [
                    ['name' => 'title', 'label' => 'Título', 'type' => 'text', 'required' => true],
                    ['name' => 'author', 'label' => 'Autor', 'type' => 'text', 'required' => true],
                    ['name' => 'isbn', 'label' => 'ISBN', 'type' => 'text'],
                    ['name' => 'description', 'label' => 'Descrição', 'type' => 'textarea'],
                ],
            ],
            'usuarios' => [
                'title' => 'Usuários',
                'saved' => 'Usuário salvo.',
                'model' => User::class,
                'columns' => [
                    ['field' => 'name', 'label' => 'Nome'],
                    ['field' => 'email', 'label' => 'E-mail'],
                    ['field' => 'cpf', 'label' => 'CPF'],
                    ['field' => 'is_admin', 'label' => 'Administrador'],
                ],
                'fields' => [
                    ['name' => 'name', 'label' => 'Nome', 'type' => 'text', 'required' => true],
                    ['name' => 'email', 'label' => 'E-mail', 'type' => 'email', 'required' => true],
                    ['name' => 'cpf', 'label' => 'CPF (11 números)', 'type' => 'text', 'required' => true],
                    ['name' => 'password', 'label' => 'Senha (deixe em branco para manter a atual)', 'type' => 'password'],
                    ['name' => 'is_admin', 'label' => 'Administrador', 'type' => 'checkbox'],
                ],
            ],
            'emprestimos' => [
                'title' => 'Empréstimos',
                'saved' => 'Empréstimo salvo.',
                'model' => Loan::class,
                'columns' => [
                    ['field' => 'book.title', 'label' => 'Livro'],
                    ['field' => 'user.name', 'label' => 'Usuário'],
                    ['field' => 'borrowed_at', 'label' => 'Retirada'],
                    ['field' => 'due_at', 'label' => 'Devolução prevista'],
                ],
                'fields' => [
                    ['name' => 'book_id', 'label' => 'Livro', 'type' => 'book', 'required' => true],
                    ['name' => 'user_id', 'label' => 'Usuário', 'type' => 'user', 'required' => true],
                    ['name' => 'borrowed_at', 'label' => 'Data do empréstimo', 'type' => 'datetime-local', 'required' => true],
                    ['name' => 'due_at', 'label' => 'Devolução prevista', 'type' => 'datetime-local', 'required' => true],
                    ['name' => 'returned_at', 'label' => 'Data da devolução', 'type' => 'datetime-local'],
                ],
            ],
            'avaliacoes' => [
                'title' => 'Avaliações',
                'saved' => 'Avaliação salva.',
                'model' => BookReview::class,
                'columns' => [
                    ['field' => 'book.title', 'label' => 'Livro'],
                    ['field' => 'user.name', 'label' => 'Usuário'],
                    ['field' => 'rating', 'label' => 'Nota'],
                    ['field' => 'review', 'label' => 'Avaliação'],
                ],
                'fields' => [
                    ['name' => 'book_id', 'label' => 'Livro', 'type' => 'book', 'required' => true],
                    ['name' => 'user_id', 'label' => 'Usuário', 'type' => 'user', 'required' => true],
                    ['name' => 'rating', 'label' => 'Nota (1 a 5)', 'type' => 'number', 'required' => true],
                    ['name' => 'review', 'label' => 'Texto', 'type' => 'textarea', 'required' => true],
                ],
            ],
            default => abort(404),
        };
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function options(string $resource): array
    {
        if (in_array($resource, ['emprestimos', 'avaliacoes'], true)) {
            return [
                'book_id' => Book::query()->orderBy('title')->pluck('title', 'id')->all(),
                'user_id' => User::query()->orderBy('name')->pluck('name', 'id')->all(),
            ];
        }

        return [];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(string $resource, Request $request, ?int $recordId = null): array
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
                'password' => [$recordId === null ? 'required' : 'nullable', 'string', 'min:8', 'confirmed'],
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
                'book_id' => [
                    'required',
                    'integer',
                    'exists:books,id',
                    Rule::unique('book_reviews', 'book_id')
                        ->where('user_id', $request->integer('user_id'))
                        ->ignore($recordId),
                ],
                'user_id' => ['required', 'integer', 'exists:users,id'],
                'rating' => ['required', 'integer', 'between:1,5'],
                'review' => ['required', 'string', 'min:3', 'max:1500'],
            ],
        };
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function save(Model $record, string $resource, array $data): void
    {
        if ($resource === 'usuarios') {
            $record->is_admin = (bool) ($data['is_admin'] ?? false);

            if (blank($data['password'] ?? null)) {
                unset($data['password']);
            }

            if ($record->exists && $record->is(auth()->user())) {
                $record->is_admin = true;
                $data['is_admin'] = true;
            }
        }

        if ($resource === 'emprestimos') {
            DB::transaction(function () use ($record, $data): void {
                Book::query()->whereKey($data['book_id'])->lockForUpdate()->firstOrFail();

                $hasActiveLoan = Loan::query()
                    ->where('book_id', $data['book_id'])
                    ->whereNull('returned_at')
                    ->when($record->exists, fn ($query) => $query->where('id', '!=', $record->getKey()))
                    ->exists();

                if ($hasActiveLoan && blank($data['returned_at'] ?? null)) {
                    throw ValidationException::withMessages([
                        'book_id' => 'Este livro já possui um empréstimo ativo.',
                    ]);
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
}
