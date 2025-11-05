<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Resources\WorkspaceResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WorkspaceController
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        return WorkspaceResource::collection($user->workspaces);
    }
}
