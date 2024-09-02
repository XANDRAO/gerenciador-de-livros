<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CepController;
use App\Http\Controllers\AuthorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aqui você pode registrar as rotas da sua aplicação. Essas rotas são carregadas
| pelo RouteServiceProvider e serão atribuídas ao grupo de middleware "web".
|
*/

// Rota para a página inicial
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rotas públicas para livros
//Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/search', [BookController::class, 'searchBooks'])->name('books.search');
//Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');
//Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
//Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
//Route::post('/books', [BookController::class, 'store'])->name('books.store');
////Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
//Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
//Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
//Route::post('/books', [BookController::class, 'store'])->name('books.store');


Route::post('/login', [AuthController::class, 'login'])->name('login.post');


Route::resource('authors', AuthorController::class);
Route::resource('books', BookController::class);


//Route::get('/books/{id}/download', [BookController::class, 'download'])->name('books.download');

// Rotas públicas para autores
//Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
//Route::get('/authors/{id}', [AuthorController::class, 'show'])->name('authors.show');

// Rota para consultar CEP
Route::get('/cep/{cep}', [CepController::class, 'index'])->name('cep.index');

// Rotas autenticadas para livros
//Route::middleware('auth:sanctum')->group(function () {
    //Route::put('/books/{id}', [BookController::class, 'update'])->name('books.update');
    //Route::delete('/books/{id}', [BookController::class, 'destroy'])->name('books.destroy');
//});

// Rotas autenticadas para autores
Route::middleware('auth:sanctum')->group(function () {
   // Route::put('/authors/{id}', [AuthorController::class, 'update'])->name('authors.update');
    //Route::delete('/authors/{id}', [AuthorController::class, 'destroy'])->name('authors.destroy');
    //Route::post('/authors', [AuthorController::class, 'store'])->name('authors.store');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Rotas de autenticação
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [AuthController::class, 'register'])->name('register');

