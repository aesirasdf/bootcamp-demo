<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// http://localhost:8000/api/register
Route::post('register', [App\Http\Controllers\UserController::class, 'store']);
// http://localhost:8000/api/users
Route::get('users', [App\Http\Controllers\UserController::class, 'index']); 
// http://localhost:8000/api/users/{id}
Route::patch('users/{user}', [App\Http\Controllers\UserController::class, 'update']);
// http://localhost:8000/api/users/{id}
Route::delete('users/{user}', [App\Http\Controllers\UserController::class, 'destroy']); 

Route::prefix('authors')->group(function () {
    Route::get('/', [App\Http\Controllers\AuthorController::class, 'index']);
    Route::get('/paginate/{page}', [App\Http\Controllers\AuthorController::class, 'paginate']);
    Route::post('/', [App\Http\Controllers\AuthorController::class, 'store']);
    Route::patch('/{author}', [App\Http\Controllers\AuthorController::class, 'update']);
    Route::delete('/{author}', [App\Http\Controllers\AuthorController::class, 'destroy']);
});

Route::prefix('books')->group(function () {
    Route::get('/', [App\Http\Controllers\BookController::class, 'index']);
    Route::get('/paginate/{page}', [App\Http\Controllers\BookController::class, 'paginate']);
    Route::post('/', [App\Http\Controllers\BookController::class, 'store']);
    Route::patch('/{book}', [App\Http\Controllers\BookController::class, 'update']);
    Route::delete('/{book}', [App\Http\Controllers\BookController::class, 'destroy']);
});