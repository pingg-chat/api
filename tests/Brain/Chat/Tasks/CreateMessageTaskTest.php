<?php

declare(strict_types = 1);

use App\Brain\Chat\Tasks\CreateMessageTask;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Validation\ValidationException;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->workspace = Workspace::factory()->create();
    $this->workspace->users()->attach($this->user);

    $this->channel = Channel::factory()->for($this->workspace)->create();
    $this->channel->users()->attach($this->user);
});

it('should be able to create a message inside a channel', function () {
    CreateMessageTask::dispatch([
        'channelId' => $this->channel->id,
        'userId'    => $this->user->id,
        'content'   => 'Hello, world!',
    ]);

    assertDatabaseHas('messages', [
        'channel_id' => $this->channel->id,
        'user_id'    => $this->user->id,
        'content'    => 'Hello, world!',
    ]);
});

it('should return a message intance when created', function () {
    $task = CreateMessageTask::dispatchSync([
        'channelId' => $this->channel->id,
        'userId'    => $this->user->id,
        'content'   => 'Hello, world!',
    ]);

    expect($task)
        ->message
        ->toBeInstanceOf(Message::class);
});

it('should be able to send a message_id to answer in a thread', function () {
    $parentMessage = Message::factory()
        ->for($this->channel)
        ->for($this->user)
        ->create();

    $task = CreateMessageTask::dispatchSync([
        'channelId' => $this->channel->id,
        'userId'    => $this->user->id,
        'content'   => 'This is a reply',
        'threadId'  => $parentMessage->id,
    ]);

    assertDatabaseHas('messages', [
        'id'         => $task->message->id,
        'channel_id' => $this->channel->id,
        'user_id'    => $this->user->id,
        'content'    => 'This is a reply',
        'thread_id'  => $parentMessage->id,
    ]);
});

// -----------------------------------------------
// Validations

test('channelId is required', function () {
    expect(fn () => CreateMessageTask::dispatch([
    ]))->toThrow(
        ValidationException::class,
        __('validation.required', ['attribute' => 'channel id'])
    );
});

test('channelId must exist in channels table', function () {
    expect(fn () => CreateMessageTask::dispatch([
        'channelId' => 9999,
    ]))->toThrow(
        ValidationException::class,
        __('validation.exists', ['attribute' => 'channel id'])
    );
});

test('channelId must be an integer', function () {
    expect(fn () => CreateMessageTask::dispatch([
        'channelId' => 'invalid',
    ]))->toThrow(
        ValidationException::class,
        __('validation.integer', ['attribute' => 'channel id'])
    );
});

test('userId is required', function () {
    expect(fn () => CreateMessageTask::dispatch([
        'channelId' => $this->channel->id,
    ]))->toThrow(
        ValidationException::class,
        __('validation.required', ['attribute' => 'user id'])
    );
});

test('userId must exist in users table', function () {
    expect(fn () => CreateMessageTask::dispatch([
        'channelId' => $this->channel->id,
        'userId'    => 9999,
    ]))->toThrow(
        ValidationException::class,
        __('validation.exists', ['attribute' => 'user id'])
    );
});

test('userId must be an integer', function () {
    expect(fn () => CreateMessageTask::dispatch([
        'channelId' => $this->channel->id,
        'userId'    => 'invalid',
    ]))->toThrow(
        ValidationException::class,
        __('validation.integer', ['attribute' => 'user id'])
    );
});

test('userId must belong to the channel', function () {
    $otherUser = User::factory()->create();

    expect(fn () => CreateMessageTask::dispatch([
        'channelId' => $this->channel->id,
        'userId'    => $otherUser->id,
        'content'   => 'Hello',
    ]))->toThrow(
        ValidationException::class,
        __('validation.exists', ['attribute' => 'user id'])
    );
});

test('content is required', function () {
    expect(fn () => CreateMessageTask::dispatch([
        'channelId' => $this->channel->id,
        'userId'    => $this->user->id,
    ]))->toThrow(
        ValidationException::class,
        __('validation.required', ['attribute' => 'content'])
    );
});

test('content must be a string', function () {
    expect(fn () => CreateMessageTask::dispatch([
        'channelId' => $this->channel->id,
        'userId'    => $this->user->id,
        'content'   => 12345,
    ]))->toThrow(
        ValidationException::class,
        __('validation.string', ['attribute' => 'content'])
    );
});

test('threadId must be an integer', function () {
    expect(fn () => CreateMessageTask::dispatch([
        'channelId' => $this->channel->id,
        'userId'    => $this->user->id,
        'content'   => 'Hello',
        'threadId'  => 'invalid',
    ]))->toThrow(
        ValidationException::class,
        __('validation.integer', ['attribute' => 'thread id'])
    );
});

test('threadId must exist in messages table', function () {
    expect(fn () => CreateMessageTask::dispatch([
        'channelId' => $this->channel->id,
        'userId'    => $this->user->id,
        'content'   => 'Hello',
        'threadId'  => 9999,
    ]))->toThrow(
        ValidationException::class,
        __('validation.exists', ['attribute' => 'thread id'])
    );
});

test('threadId must belong to the same channel', function () {
    $otherChannel = Channel::factory()->for($this->workspace)->create();
    $otherChannel->users()->attach($this->user);

    $otherMessage = Message::factory()
        ->for($otherChannel)
        ->for($this->user)
        ->create();

    expect(fn () => CreateMessageTask::dispatch([
        'channelId' => $this->channel->id,
        'userId'    => $this->user->id,
        'content'   => 'Hello',
        'threadId'  => $otherMessage->id,
    ]))->toThrow(
        ValidationException::class,
        __('validation.exists', ['attribute' => 'thread id'])
    );
});
