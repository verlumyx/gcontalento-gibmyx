<?php

use App\Http\Controllers\BooksController;
use App\Http\Controllers\CategoriesController;
use Illuminate\Support\Facades\Route;

// Rutas de la API REST (sin prefijo /api, middleware: api)

Route::get('/books', [BooksController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BooksController::class, 'show'])->whereNumber('book')->name('books.show');
Route::post('/books', [BooksController::class, 'create'])->name('books.create');
Route::match(['put', 'patch'], '/books/{book}', [BooksController::class, 'update'])->whereNumber('book')->name('books.update');
Route::delete('/books/{book}', [BooksController::class, 'delete'])->whereNumber('book')->name('books.delete');

Route::get('/categories', [CategoriesController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoriesController::class, 'show'])->whereNumber('category')->name('categories.show');
Route::post('/categories', [CategoriesController::class, 'create'])->name('categories.create');
Route::match(['put', 'patch'], '/categories/{category}', [CategoriesController::class, 'update'])->whereNumber('category')->name('categories.update');
Route::delete('/categories/{category}', [CategoriesController::class, 'delete'])->whereNumber('category')->name('categories.delete');
