<?php

declare(strict_types = 1);

use App\Brain\Chat\Tasks\CreateChannelTask;
use App\Brain\Chat\Tasks\CreateWorkspaceTask;

test('GET /api/me returns the authenticated user', function () {
    $user = App\Models\User::factory()->create([
        'name'  => 'John Doe',
        'email' => 'joe@doe.com',
    ]);

    api($user)
        ->get(route('api.me'))
        ->assertStatus(200)
        ->assertJson([
            'icon'     => $user->icon,
            'name'     => $user->name,
            'username' => $user->username,
            'email'    => $user->email,
        ]);
});

test('return the connected workspace with the request', function () {
    $user = App\Models\User::factory()->create([
        'name'  => 'John Doe',
        'email' => 'joe@doe.com',
    ]);

    /** @var CreateWorkspaceTask $task */
    $task = CreateWorkspaceTask::dispatchSync([
        'owner_id'    => $user->id,
        'name'        => 'johns_workspace',
        'description' => 'John\'s personal workspace',
        'icon'        => '',
    ]);

    $task->workspace->users()->attach($user->id, ['connected' => true]);

    api($user)
        ->get(route('api.me'))
        ->assertStatus(200)
        ->assertJson([
            'icon'      => $user->icon,
            'name'      => $user->name,
            'username'  => $user->username,
            'email'     => $user->email,
            'workspace' => [
                'id'          => $task->workspace->id,
                'name'        => $task->workspace->name,
                'description' => $task->workspace->description,
                'icon'        => $task->workspace->icon,
            ],
        ]);
});

test('needs to get all channels', function () {
    $user = App\Models\User::factory()->create([
        'name'  => 'John Doe',
        'email' => 'joe@doe.com',
    ]);

    /** @var CreateWorkspaceTask $task */
    $task = CreateWorkspaceTask::dispatchSync([
        'owner_id'    => $user->id,
        'name'        => 'johns_workspace',
        'description' => 'John\'s personal workspace',
        'icon'        => '',
    ]);

    $task->workspace->users()->attach($user->id, ['connected' => true]);

    $publicChannel = CreateChannelTask::dispatchSync([
        'workspace_id' => $task->workspace->id,
        'short_id'     => 'general',
        'icon'         => '',
        'name'         => 'general',
        'description'  => 'General discussion',
        'is_private'   => false,
        'is_dm'        => false,
    ])->channel;

    $channel = CreateChannelTask::dispatchSync([
        'workspace_id' => $task->workspace->id,
        'short_id'     => 'random',
        'icon'         => '',
        'name'         => 'random',
        'description'  => 'Random talks',
        'is_private'   => true,
        'is_dm'        => false,
    ])->channel;

    $user->channels()->attach($channel->id);

    $dm = CreateChannelTask::dispatchSync([
        'workspace_id' => $task->workspace->id,
        'short_id'     => 'dm_jane',
        'icon'         => '',
        'name'         => 'dm_jane',
        'description'  => 'Direct message with Jane',
        'is_private'   => true,
        'is_dm'        => true,
    ])->channel;

    $user->channels()->attach($dm->id);

    api($user)
        ->get(route('api.me'))
        ->assertStatus(200)
        ->assertJson([
            'icon'     => $user->icon,
            'name'     => $user->name,
            'username' => $user->username,
            'email'    => $user->email,

            'workspace' => [
                'id'              => $task->workspace->id,
                'name'            => $task->workspace->name,
                'description'     => $task->workspace->description,
                'icon'            => $task->workspace->icon,
                'public_channels' => [
                    [
                        'id'          => $publicChannel->id,
                        'icon'        => $publicChannel->icon,
                        'short_id'    => $publicChannel->short_id,
                        'name'        => $publicChannel->name,
                        'description' => $publicChannel->description,
                    ],
                ],
            ],

            'channels' => [
                [
                    'id'          => $channel->id,
                    'short_id'    => $channel->short_id,
                    'icon'        => $channel->icon,
                    'name'        => $channel->name,
                    'description' => $channel->description,
                    'is_private'  => $channel->is_private,
                ],
            ],

            'dms' => [
                [
                    'id'          => $dm->id,
                    'short_id'    => $dm->short_id,
                    'icon'        => $dm->icon,
                    'name'        => $dm->name,
                    'description' => $dm->description,
                ],
            ],

        ]);
});
