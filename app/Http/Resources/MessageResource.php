<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'content'    => $this->content,
            'created_at' => $this->created_at, // TODO: timezone handling?
            'user'       => [
                'id'       => $this->user_id,
                'name'     => $this->user_name,
                'username' => $this->user_username,
                'icon'     => $this->user_icon,
            ],
            'thread_id' => $this->thread_id,
        ];
    }
}
