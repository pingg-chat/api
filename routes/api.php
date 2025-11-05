<?php

declare(strict_types = 1);

use App\Http\Controllers\ChannelController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::get('/me', [UserController::class, 'show'])->name('api.me');
Route::get('/workspaces', [WorkspaceController::class, 'index'])->name('api.workspaces');
Route::post('/channels/{channel}', [ChannelController::class, 'subscribe'])->name('api.channels.subscribe');
