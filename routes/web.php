<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\ImportController::class, 'index'])->name('home');
Route::post('/import/upload', [\App\Http\Controllers\ImportController::class, 'upload'])->name('upload');
Route::get('/data', [\App\Http\Controllers\DataController::class, 'index'])->name('data');
