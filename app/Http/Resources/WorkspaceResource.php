<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkspaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'icon'            => $this->icon,
            'name'            => $this->name,
            'description'     => $this->description,
            'public_channels' => ChannelResource::collection(
                $this->channels()
                    ->where('is_private', false)
                    ->where('is_dm', false)->get()
            ),
        ];
    }
}
