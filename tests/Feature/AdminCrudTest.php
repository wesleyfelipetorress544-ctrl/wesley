<?php

use App\Models\Book;
use App\Models\BookReview;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('lets an administrator create update and delete books', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->post(route('admin.crud.store', 'livros'), [
        'title' => 'Livro novo',
        'author' => 'Autora',
        'isbn' => '9780000000001',
        'description' => 'Descrição',
    ])->assertRedirect(route('admin.crud.index', 'livros'));

    $book = Book::query()->where('isbn', '9780000000001')->firstOrFail();
    $this->assertModelExists($book);

    $this->put(route('admin.crud.update', ['livros', $book->id]), [
        'title' => 'Livro editado',
        'author' => 'Autora',
        'isbn' => '9780000000001',
        'description' => 'Descrição nova',
    ])->assertRedirect(route('admin.crud.index', 'livros'));

    expect($book->fresh()->title)->toBe('Livro editado');

    $this->delete(route('admin.crud.destroy', ['livros', $book->id]))
        ->assertRedirect(route('admin.crud.index', 'livros'));
    $this->assertModelMissing($book);
});

it('lets an administrator manage users while keeping passwords hashed', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->post(route('admin.crud.store', 'usuarios'), [
        'name' => 'Aluna',
        'email' => 'aluna@example.test',
        'cpf' => '12345678901',
        'password' => 'senha-segura-123',
        'password_confirmation' => 'senha-segura-123',
    ])->assertRedirect(route('admin.crud.index', 'usuarios'));

    $user = User::query()->where('email', 'aluna@example.test')->firstOrFail();
    expect(Hash::check('senha-segura-123', $user->password))->toBeTrue();

    $this->get(route('admin.crud.edit', ['usuarios', $user->id]))
        ->assertOk()
        ->assertDontSee($user->password);

    $this->put(route('admin.crud.update', ['usuarios', $user->id]), [
        'name' => 'Aluna editada',
        'email' => 'aluna@example.test',
        'cpf' => '12345678901',
        'password' => '',
    ])->assertRedirect(route('admin.crud.index', 'usuarios'));

    expect($user->fresh()->name)->toBe('Aluna editada')
        ->and(Hash::check('senha-segura-123', $user->fresh()->password))->toBeTrue();

    $this->delete(route('admin.crud.destroy', ['usuarios', $user->id]))
        ->assertRedirect(route('admin.crud.index', 'usuarios'));
    $this->assertModelMissing($user);
});

it('prevents an administrator from deleting their own account', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->delete(route('admin.crud.destroy', ['usuarios', $admin->id]))
        ->assertRedirect()
        ->assertSessionHas('error', 'Não é permitido excluir a própria conta.');

    $this->assertModelExists($admin);
});

it('lets an administrator create update and delete loans', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $borrower = User::factory()->create();
    $book = Book::query()->create(['title' => 'Livro', 'author' => 'Autora']);

    $this->actingAs($admin)->post(route('admin.crud.store', 'emprestimos'), [
        'book_id' => $book->id,
        'user_id' => $borrower->id,
        'borrowed_at' => '2026-10-01T10:00',
        'due_at' => '2026-10-15T10:00',
        'returned_at' => '',
    ])->assertRedirect(route('admin.crud.index', 'emprestimos'));

    $loan = Loan::query()->firstOrFail();
    expect($loan->user_id)->toBe($borrower->id);

    $this->put(route('admin.crud.update', ['emprestimos', $loan->id]), [
        'book_id' => $book->id,
        'user_id' => $borrower->id,
        'borrowed_at' => '2026-10-01T10:00',
        'due_at' => '2026-10-15T10:00',
        'returned_at' => '2026-10-14T10:00',
    ])->assertRedirect(route('admin.crud.index', 'emprestimos'));

    expect($loan->fresh()->returned_at?->toDateTimeString())->toBe('2026-10-14 10:00:00');

    $this->delete(route('admin.crud.destroy', ['emprestimos', $loan->id]))
        ->assertRedirect(route('admin.crud.index', 'emprestimos'));
    $this->assertModelMissing($loan);
});

it('rejects an active loan when the book is already borrowed', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $borrower = User::factory()->create();
    $book = Book::query()->create(['title' => 'Livro emprestado', 'author' => 'Autora']);
    $borrower->loans()->create([
        'book_id' => $book->id,
        'borrowed_at' => '2026-10-01 10:00:00',
        'due_at' => '2026-10-15 10:00:00',
    ]);

    $this->actingAs($admin)->post(route('admin.crud.store', 'emprestimos'), [
        'book_id' => $book->id,
        'user_id' => $admin->id,
        'borrowed_at' => '2026-10-02T10:00',
        'due_at' => '2026-10-16T10:00',
        'returned_at' => '',
    ])->assertSessionHasErrors('book_id');

    expect(Loan::query()->where('book_id', $book->id)->count())->toBe(1);
});

it('lets an administrator create update and delete reviews', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $reader = User::factory()->create();
    $book = Book::query()->create(['title' => 'Livro', 'author' => 'Autora']);

    $this->actingAs($admin)->post(route('admin.crud.store', 'avaliacoes'), [
        'book_id' => $book->id,
        'user_id' => $reader->id,
        'rating' => 4,
        'review' => 'Boa leitura.',
    ])->assertRedirect(route('admin.crud.index', 'avaliacoes'));

    $review = BookReview::query()->firstOrFail();
    $this->put(route('admin.crud.update', ['avaliacoes', $review->id]), [
        'book_id' => $book->id,
        'user_id' => $reader->id,
        'rating' => 5,
        'review' => 'Excelente leitura.',
    ])->assertRedirect(route('admin.crud.index', 'avaliacoes'));

    expect($review->fresh()->rating)->toBe(5);

    $this->delete(route('admin.crud.destroy', ['avaliacoes', $review->id]))
        ->assertRedirect(route('admin.crud.index', 'avaliacoes'));
    $this->assertModelMissing($review);
});

it('renders list create and edit pages for each library resource', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $reader = User::factory()->create();
    $book = Book::query()->create(['title' => 'Livro das telas', 'author' => 'Autora']);
    $loan = $reader->loans()->create([
        'book_id' => $book->id,
        'borrowed_at' => '2026-10-01 10:00:00',
        'due_at' => '2026-10-15 10:00:00',
    ]);
    $review = BookReview::query()->create([
        'book_id' => $book->id,
        'user_id' => $reader->id,
        'rating' => 5,
        'review' => 'Excelente.',
    ]);

    $this->actingAs($admin);

    foreach (['livros', 'usuarios', 'emprestimos', 'avaliacoes'] as $resource) {
        $this->get(route('admin.crud.index', $resource))->assertOk();
        $this->get(route('admin.crud.create', $resource))->assertOk();
    }

    $this->get(route('admin.crud.edit', ['livros', $book->id]))->assertOk();
    $this->get(route('admin.crud.edit', ['usuarios', $reader->id]))->assertOk();
    $this->get(route('admin.crud.edit', ['emprestimos', $loan->id]))->assertOk();
    $this->get(route('admin.crud.edit', ['avaliacoes', $review->id]))->assertOk();
    $this->get(route('admin.tables.index'))->assertOk();
});

it('restricts the crud pages to administrators', function () {
    $student = User::factory()->create();

    $this->actingAs($student)
        ->get(route('admin.crud.index', 'livros'))
        ->assertRedirect(route('books.index'));
});

it('lets administrators consult all tables without exposing passwords', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $secret = 'password-hidden-from-table-list';
    DB::table('cache')->insert([
        'key' => 'cache-test',
        'value' => $secret,
        'expiration' => now()->addHour()->timestamp,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.tables.show', 'cache'))
        ->assertOk()
        ->assertSee('cache-test')
        ->assertDontSee($secret);

    $this->get(route('admin.tables.show', 'users'))
        ->assertOk()
        ->assertDontSee($admin->getAuthPassword())
        ->assertDontSee('remember_token');
});

it('returns not found for unknown database tables and crud resources', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->get(route('admin.tables.show', 'unknown_table'))->assertNotFound();
    $this->get('/admin/gerenciar/unknown')->assertNotFound();
});
