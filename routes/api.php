<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CepController;
use App\Http\Controllers\AuthorController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);
Route::get('/books/search', [BookController::class, 'searchBooks']);
Route::get('/books/{id}/download', [BookController::class, 'download']);
Route::get('/cep', CepController::class, 'index');

// Rotas autenticadas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/books', [BookController::class, 'store']);
    Route::put('/books/{id}', [BookController::class, 'update']);
    Route::delete('/books/{id}', [BookController::class, 'destroy']);
    Route::post('/authors', [AuthorController::class, 'store']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
});

// Rotas de autenticação
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

