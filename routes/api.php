<?php

use App\Http\Controllers\Api\LibraryController;
use App\Http\Controllers\UserApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:5,1')->group(function () {
    Route::post('/usuarios', [UserApiController::class, 'store'])->name('api.usuarios.cadastrar');
    Route::post('/login', [UserApiController::class, 'login'])->name('api.login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (\Illuminate\Http\Request $request) => $request->user())->name('api.user');
    Route::post('/logout', [UserApiController::class, 'logout'])->name('api.logout');

    Route::get('/livros', [LibraryController::class, 'books'])->name('api.livros.listar');
    Route::get('/livros/{book}', [LibraryController::class, 'showBook'])->whereNumber('book')->name('api.livros.ver');
    Route::post('/livros/{book}/avaliacoes', [LibraryController::class, 'storeReview'])->middleware('throttle:10,1')->whereNumber('book')->name('api.livros.avaliacoes.criar');
    Route::post('/livros/{book}/emprestimos', [LibraryController::class, 'storeLoan'])->middleware('throttle:5,1')->whereNumber('book')->name('api.livros.emprestimos.criar');

    Route::get('/emprestimos', [LibraryController::class, 'loans'])->name('api.emprestimos.listar');
    Route::post('/emprestimos/{loan}/devolver', [LibraryController::class, 'returnLoan'])->whereNumber('loan')->name('api.emprestimos.devolver');

    Route::middleware('admin')->group(function () {
        Route::middleware('throttle:5,1')->prefix('usuarios')->controller(UserApiController::class)->group(function () {
            Route::get('/', 'index')->name('api.usuarios.listar');
            Route::get('/simples', 'simple')->name('api.usuarios.listar-simples');
            Route::get('/{user}', 'show')->whereNumber('user')->name('api.usuarios.ver');
            Route::put('/{user}', 'update')->whereNumber('user')->name('api.usuarios.alterar');
            Route::delete('/{user}', 'destroy')->whereNumber('user')->name('api.usuarios.excluir');
        });

        Route::get('/alunos', [LibraryController::class, 'students'])->name('api.alunos.listar');
        Route::get('/relatorios', [LibraryController::class, 'report'])->name('api.relatorios');
        Route::get('/tabelas', [LibraryController::class, 'tables'])->name('api.tabelas.listar');
        Route::get('/tabelas/{table}', [LibraryController::class, 'table'])->name('api.tabelas.ver');

        Route::prefix('admin/recursos/{resource}')
            ->whereIn('resource', ['livros', 'usuarios', 'emprestimos', 'avaliacoes'])
            ->group(function () {
                Route::get('/', [LibraryController::class, 'adminRecords'])->name('api.admin.recursos.listar');
                Route::post('/', [LibraryController::class, 'storeAdminRecord'])->name('api.admin.recursos.criar');
                Route::get('/{record}', [LibraryController::class, 'showAdminRecord'])->whereNumber('record')->name('api.admin.recursos.ver');
                Route::put('/{record}', [LibraryController::class, 'updateAdminRecord'])->whereNumber('record')->name('api.admin.recursos.atualizar');
                Route::delete('/{record}', [LibraryController::class, 'destroyAdminRecord'])->whereNumber('record')->name('api.admin.recursos.excluir');
            });
    });
});
