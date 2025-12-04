<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Workspace;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    public function run(): void
    {
        Workspace::all()
            ->each(function (Workspace $workspace) {
                $channel = Channel::factory()->create(['workspace_id' => $workspace->id]);

                $workspace->owner->channels()->attach($channel->id);

                $channel = Channel::factory()->create(['workspace_id' => $workspace->id]);

                $workspace->owner->channels()->attach($channel->id);
            });
    }
}
