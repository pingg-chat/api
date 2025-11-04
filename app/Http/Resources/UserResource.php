<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'icon'     => $this->icon,
            'name'     => $this->name,
            'username' => $this->username,
            'email'    => $this->email,
            $this->mergeWhen($this->connectedWorkspace, [
                'workspace' => WorkspaceResource::make($this->connectedWorkspace),
            ]),
        ];
    }
}
