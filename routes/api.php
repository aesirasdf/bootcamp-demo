<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// http://localhost:8000/api/login
Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);
// http://localhost:8000/api/register
Route::post('register', [App\Http\Controllers\AuthController::class, 'register']);
// http://localhost:8000/api/getInfo
Route::middleware(['auth:api'])->get('info', [App\Http\Controllers\AuthController::class, 'getInfo']);

Route::prefix("users")->middleware(['auth:api'])->group(function () {
    // http://localhost:8000/api/users
    Route::post('/', [App\Http\Controllers\UserController::class, 'store']);
    // http://localhost:8000/api/users
    Route::get('/', [App\Http\Controllers\UserController::class, 'index']); 
    // http://localhost:8000/api/users/{id}
    Route::patch('/{user}', [App\Http\Controllers\UserController::class, 'update']);
    // http://localhost:8000/api/users/{id}
    Route::delete('/{user}', [App\Http\Controllers\UserController::class, 'destroy']); 
});

Route::prefix('authors')->group(function () {
    Route::get('/', [App\Http\Controllers\AuthorController::class, 'index']);
    Route::get('/paginate/{page}', [App\Http\Controllers\AuthorController::class, 'paginate']);
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/', [App\Http\Controllers\AuthorController::class, 'store']);
        Route::patch('/{author}', [App\Http\Controllers\AuthorController::class, 'update']);
        Route::delete('/{author}', [App\Http\Controllers\AuthorController::class, 'destroy']);
    });
});


Route::prefix('books')->group(function () {
    Route::get('/', [App\Http\Controllers\BookController::class, 'index']);
    Route::get('/paginate/{page}', [App\Http\Controllers\BookController::class, 'paginate']);
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/', [App\Http\Controllers\BookController::class, 'store']);
        Route::patch('/{book}', [App\Http\Controllers\BookController::class, 'update']);
        Route::delete('/{book}', [App\Http\Controllers\BookController::class, 'destroy']);
    });
});

Route::prefix('genres')->group(function () {
    Route::get('/', [App\Http\Controllers\GenreController::class, 'index']);
    Route::get('/paginate/{page}', [App\Http\Controllers\GenreController::class, 'paginate']);
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/', [App\Http\Controllers\GenreController::class, 'store']);
        Route::patch('{genre}', [App\Http\Controllers\GenreController::class, 'update']);
        Route::delete('{genre}', [App\Http\Controllers\GenreController::class, 'destroy']);
    });
    
});


Route::prefix('customers')->group(function () {
    Route::middleware(['auth:api'])->group(function () {
        Route::get('/', [App\Http\Controllers\CustomerController::class, 'index']);
        Route::get('/paginate/{page}', [App\Http\Controllers\CustomerController::class, 'paginate']);
        Route::post('/', [App\Http\Controllers\CustomerController::class, 'store']);
        Route::patch('{customer}', [App\Http\Controllers\CustomerController::class, 'update']);
        Route::delete('{customer}', [App\Http\Controllers\CustomerController::class, 'destroy']);
    });
    
});

Route::prefix('loans')->middleware(['auth:api'])->group(function () {
    Route::post('/', [App\Http\Controllers\LoanController::class, "store"]);
    Route::get('/', [App\Http\Controllers\LoanController::class, "index"]);
    Route::get('/{loan}', [App\Http\Controllers\LoanController::class, "view"]);
    Route::patch('/{loan}', [App\Http\Controllers\LoanController::class, "update"]);
    Route::delete('/{loan}', [App\Http\Controllers\LoanController::class, "destroy"]);
});