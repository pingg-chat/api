<?php

declare(strict_types = 1);

use App\Brain\Chat\Processes\SubscribeToAChannelProcess;
use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    $this->user      = User::factory()->create();
    $this->workspace = Workspace::factory()->create();
    $this->user->workspaces()->attach($this->workspace->id);
    $this->channel = Channel::factory()->create(['workspace_id' => $this->workspace->id]);
});

test('make sure we are calling the subscribe process', function () {
    Bus::fake();

    api($this->user)
        ->post(route('api.channels.subscribe', ['channel' => $this->channel->id]))
        ->assertStatus(204);

    Bus::assertDispatched(SubscribeToAChannelProcess::class);
});
