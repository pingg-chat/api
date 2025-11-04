<?php

declare(strict_types = 1);

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/me', [UserController::class, 'show'])->name('api.me');
