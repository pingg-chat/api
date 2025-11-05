<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (empty($this->user)) {
            $this->load('user');
        }

        return [
            'id'         => $this->id,
            'content'    => $this->content,
            'created_at' => $this->created_at, // TODO: timezone handling?
            'user'       => [
                'id'       => $this->user->id,
                'name'     => $this->user->name,
                'username' => $this->user->username,
                'icon'     => $this->user->icon,
            ],
            'thread_id' => $this->thread_id,
        ];
    }
}
