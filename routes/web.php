<?php

use App\Http\Controllers\Admin\DatabaseTableController;
use App\Http\Controllers\Admin\LibraryCrudController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.attempt');
Route::get('/cadastro', [AuthController::class, 'showRegistration'])->name('register');
Route::post('/cadastro', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.store');

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1')->name('admin.login.attempt');

Route::middleware('auth')->group(function () {
    Route::get('/biblioteca', [AuthController::class, 'library'])->name('biblioteca');

    Route::get('/livros', [BookController::class, 'index'])->name('books.index');
    Route::get('/livros/{book}', [BookController::class, 'show'])->name('books.show');
    Route::post('/livros/{book}/avaliacoes', [BookController::class, 'storeReview'])->middleware('throttle:10,1')->name('books.reviews.store');
    Route::post('/livros/{book}/emprestimos', [BookController::class, 'storeLoan'])->middleware('throttle:5,1')->name('books.loans.store');

    Route::get('/emprestimos', [LoanController::class, 'index'])->name('loans.index');
    Route::post('/emprestimos/{loan}/devolver', [LoanController::class, 'returnBook'])->name('loans.return');

    Route::middleware('admin')->group(function () {
        Route::get('/alunos', [StudentController::class, 'index'])->name('students.index');
        Route::get('/relatorios', [ReportController::class, 'index'])->name('reports.index');

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/tabelas', [DatabaseTableController::class, 'index'])->name('tables.index');
            Route::get('/tabelas/{table}', [DatabaseTableController::class, 'show'])->name('tables.show');

            Route::prefix('gerenciar/{resource}')->name('crud.')->whereIn('resource', [
                'livros',
                'usuarios',
                'emprestimos',
                'avaliacoes',
            ])->group(function () {
                Route::get('/', [LibraryCrudController::class, 'index'])->name('index');
                Route::get('/criar', [LibraryCrudController::class, 'create'])->name('create');
                Route::post('/', [LibraryCrudController::class, 'store'])->name('store');
                Route::get('/{record}/editar', [LibraryCrudController::class, 'edit'])->whereNumber('record')->name('edit');
                Route::put('/{record}', [LibraryCrudController::class, 'update'])->whereNumber('record')->name('update');
                Route::delete('/{record}', [LibraryCrudController::class, 'destroy'])->whereNumber('record')->name('destroy');
            });
        });
    });

    Route::post('/sair', [AuthController::class, 'logout'])->name('logout');
});
