<?php

declare(strict_types = 1);

use App\Brain\Chat\Tasks\BroadcastThatMessageWasCreatedTask;
use App\Events\MessageWasCreated;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->workspace = Workspace::factory()->create();
    $this->workspace->users()->attach($this->user);

    $this->channel = Channel::factory()->for($this->workspace)->create();
    $this->channel->users()->attach($this->user);

    $this->message = Message::factory()->for($this->channel)->for($this->user)->create();
});

it('should dispatch MessageWasCreated event when handled', function () {
    Event::fake();

    BroadcastThatMessageWasCreatedTask::dispatchSync([
        'message' => $this->message,
    ]);

    Event::assertDispatched(
        MessageWasCreated::class,
        function (MessageWasCreated $event) {
            return $event->message->id === $this->message->id
                && $event->broadcastOn()[0]->name === 'private-channel.' . $this->message->channel_id;
        }
    );
});
