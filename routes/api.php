<?php

declare(strict_types = 1);

use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::get('/me', [UserController::class, 'show'])->name('api.me');
Route::get('/workspaces', [WorkspaceController::class, 'index'])->name('api.workspaces');
