<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'short_id'    => $this->short_id,
            'name'        => $this->name,
            'icon'        => $this->icon,
            'description' => $this->description,
            'is_private'  => $this->is_private,
            'is_dm'       => $this->is_dm,
        ];
    }
}
