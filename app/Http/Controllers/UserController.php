<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserController
{
    public function show(): JsonResource
    {
        /** @var User $user */
        $user = Auth::user();

        abort_unless($user, 401);

        return UserResource::make($user);
    }
}
