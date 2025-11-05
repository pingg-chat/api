<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use App\Brain\Chat\Queries\GetUserChannelsFromWorkspaceQuery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $channels = GetUserChannelsFromWorkspaceQuery::run(
            user: $this->resource,
            workspace: $this->connectedWorkspace,
        );

        $dms = GetUserChannelsFromWorkspaceQuery::run(
            user: $this->resource,
            workspace: $this->connectedWorkspace,
            dm: true,
        );

        return [
            'id'       => $this->id,
            'icon'     => $this->icon,
            'name'     => $this->name,
            'username' => $this->username,
            'email'    => $this->email,

            $this->mergeWhen($this->connectedWorkspace, [
                'workspace' => WorkspaceResource::make($this->connectedWorkspace),
            ]),

            $this->mergeWhen($channels->isNotEmpty(), [
                'channels' => ChannelResource::collection($channels),
            ]),

            $this->mergeWhen($dms->isNotEmpty(), [
                'dms' => ChannelResource::collection($dms),
            ]),

        ];
    }
}
