<?php

use Illuminate\Http\Request;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas públicas
Route::get('/books', [BookController::class, 'index']); // Listar todos os livros com paginação
Route::get('/books/{id}', [BookController::class, 'show']); // Obter um livro específico por ID ou ISBN
Route::get('/books/search', [BookController::class, 'searchBooks']); // Pesquisar livro por nome ou nome do autor
Route::get('/books/{id}/download', [BookController::class, 'download']); // Baixar um livro específico por ID

// Rotas autenticadas para autores
Route::middleware('auth:sanctum')->post('/authors', [AuthorController::class, 'store']);
Route::middleware('auth:sanctum')->group(function () {
Route::post('/books', [BookController::class, 'store']); // Criar um novo livro
Route::put('/books/{id}', [BookController::class, 'update']); // Atualizar um livro por ID
Route::delete('/books/{id}', [BookController::class, 'destroy']); // Deletar um livro por ID
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

});
