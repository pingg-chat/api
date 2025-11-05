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

// ------------
// Messages

test('get all messages from the channel', function () {
    $messages = App\Models\Message::factory()
        ->count(3)
        ->for($this->channel, 'channel')
        ->for($this->user, 'user')
        ->create();

    api($this->user)
        ->get(route('api.channels.messages', ['channel' => $this->channel->id]))
        ->assertStatus(200)
        ->assertJsonCount(3)
        ->assertJsonFragment(['id' => $messages[0]->id])
        ->assertJsonFragment(['id' => $messages[1]->id])
        ->assertJsonFragment(['id' => $messages[2]->id]);
});

test('i need to be able to get messages from a thread', function () {
    $thread = App\Models\Message::factory()
        ->for($this->channel, 'channel')
        ->for($this->user, 'user')
        ->create();

    $threadMessages = App\Models\Message::factory()
        ->count(2)
        ->for($this->channel, 'channel')
        ->for($this->user, 'user')
        ->create([
            'thread_id' => $thread->id,
        ]);

    // Non-thread messages
    App\Models\Message::factory()
        ->count(2)
        ->for($this->channel, 'channel')
        ->for($this->user, 'user')
        ->create();

    api($this->user)
        ->get(route('api.channels.messages', [
            'channel' => $this->channel->id,
            'thread'  => $thread->id,
        ]))
        ->assertStatus(200)
        ->assertJsonCount(2)
        ->assertJsonFragment(['id' => $threadMessages[0]->id])
        ->assertJsonFragment(['id' => $threadMessages[1]->id]);
});
