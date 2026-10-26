<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GiftController;

Route::get('/', [GiftController::class, 'index'])->name('gift');
Route::post('/login', [GiftController::class, 'verify'])->name('login.verify');
Route::get('/inicio', [GiftController::class, 'dashboard'])->name('dashboard');
Route::get('/regalo', [GiftController::class, 'board'])->name('gift.board');
