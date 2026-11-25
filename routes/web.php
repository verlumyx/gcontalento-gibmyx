<?php

use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('web.home');

Route::prefix('panel')->name('web.')->group(function () {
    Route::prefix('books')->name('books.')->group(function () {
        Route::view('/', 'books.index')->name('index');
        Route::view('/create', 'books.create')->name('create');
        Route::get('/{book}/edit', fn (Book $book) => view('books.edit', compact('book')))
            ->whereNumber('book')
            ->name('edit');
    });

    Route::prefix('categories')->name('categories.')->group(function () {
        Route::view('/', 'categories.index')->name('index');
        Route::view('/create', 'categories.create')->name('create');
        Route::get('/{category}/edit', fn (Category $category) => view('categories.edit', compact('category')))
            ->whereNumber('category')
            ->name('edit');
    });
});
