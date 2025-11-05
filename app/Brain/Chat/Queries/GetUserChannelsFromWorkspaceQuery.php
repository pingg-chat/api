<?php

declare(strict_types = 1);

namespace App\Brain\Chat\Queries;

use App\Models\User;
use App\Models\Workspace;
use Brain\Query;
use Illuminate\Support\Collection;
use stdClass;

class GetUserChannelsFromWorkspaceQuery extends Query
{
    public function __construct(
        private readonly User $user,
        private readonly ?Workspace $workspace = null,
        private readonly bool $dm = false,
    ) {
        //
    }

    public function handle(): Collection | stdClass
    {
        if (! $this->workspace) {
            return collect([]);
        }

        return $this->user
            ->channels()
            ->select([
                'id',
                'short_id',
                'icon',
                'name',
                'description',
                'is_private',
                'is_dm',
            ])
            ->where('workspace_id', $this->workspace->id)
            ->where('is_dm', $this->dm)
            ->getQuery()
            ->get();
    }
}
