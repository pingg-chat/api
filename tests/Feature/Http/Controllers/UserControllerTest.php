<?php

declare(strict_types = 1);

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
