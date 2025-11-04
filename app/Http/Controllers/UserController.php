<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function show(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
