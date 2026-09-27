<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\MagazineController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MagazineController::class, 'home'])->name('home');
Route::get('/articles', [MagazineController::class, 'index'])->name('articles.index');
Route::get('/articles/{article:slug}', [MagazineController::class, 'show'])->name('articles.show');
Route::get('/category/{category:slug}', [MagazineController::class, 'category'])->name('categories.show');
Route::get('/category/{path}', [MagazineController::class, 'categoryPath'])->where('path', '.*')->name('categories.path');
Route::post('/newsletter', [MagazineController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/dashboard', fn () => view('magazine.dashboard'))->middleware('auth:web')->name('dashboard');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminController::class, 'login'])->name('login');
    Route::post('/login', [AdminController::class, 'authenticate'])->name('authenticate');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    Route::middleware('auth:admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/articles/create', [AdminController::class, 'create'])->name('articles.create');
        Route::get('/articles/{article}/edit', [AdminController::class, 'edit'])->name('articles.edit');
        Route::post('/articles', [AdminController::class, 'store'])->name('articles.store');
        Route::post('/uploads/editor-image', [AdminController::class, 'uploadEditorImage'])->name('uploads.editor-image');
        Route::put('/articles/{article}', [AdminController::class, 'update'])->name('articles.update');
        Route::delete('/articles/{article}', [AdminController::class, 'destroy'])->name('articles.destroy');
        Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
        Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
        Route::get('/newsletter', [AdminController::class, 'newsletter'])->name('newsletter.index');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    });
});

require __DIR__.'/auth.php';
