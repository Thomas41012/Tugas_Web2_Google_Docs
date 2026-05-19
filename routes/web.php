<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentEditController;
use App\Http\Controllers\DocumentRevisionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('documents.index')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::post('/documents/{document}/sync', [DocumentController::class, 'sync'])->name('documents.sync');
    Route::post('/documents/{document}/cursor', [DocumentController::class, 'cursor'])->name('documents.cursor');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    Route::get('/documents/{document}/history', [DocumentRevisionController::class, 'index'])->name('documents.history');
    Route::get('/documents/{document}/history/{revision}', [DocumentRevisionController::class, 'show'])->name('documents.revision');
    Route::post('/documents/{document}/history/{revision}/restore', [DocumentRevisionController::class, 'restore'])->name('documents.restore');

    Route::get('/documents/{document}/activity', [DocumentEditController::class, 'index'])->name('documents.activity');
});
